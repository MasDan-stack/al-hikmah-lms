<?php

namespace App\Actions\Mentors;

use App\Enums\EnrollmentStatus;
use App\Events\StudentAssignedToMentor;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignStudentAction
{
    /**
     * Eksekusi alokasi santri dengan jaminan atomic concurrency & pessimistic locking.
     */
    public function execute(int $mentorId, int $studentId, string $day, ?string $time = null): void
    {
        DB::transaction(function () use ($mentorId, $studentId, $day, $time) {
            // 1. Lock row mentor sebagai single source of truth untuk concurrency
            $mentor = Mentor::where('id', $mentorId)->lockForUpdate()->firstOrFail();
            $student = Student::findOrFail($studentId);

            if (! $mentor->is_active) {
                throw ValidationException::withMessages([
                    'mentor_id' => 'Mentor sedang dalam status tidak aktif.',
                ]);
            }

            // 2. Lock ketersediaan mentor pada hari yang dipilih
            $availability = MentorAvailability::where('mentor_id', $mentor->id)
                ->where('day', $day)
                ->lockForUpdate()
                ->first();

            // Jika belum ada konfigurasi khusus, gunakan default kuota mentor
            $maxQuota = $availability?->max_students ?? $mentor->default_max_students_per_day ?? 5;
            $isAvailable = $availability ? $availability->isAvailable() : true;

            if (! $isAvailable) {
                throw ValidationException::withMessages([
                    'day' => 'Mentor libur atau tidak membuka bimbingan pada hari yang dipilih.',
                ]);
            }

            // 3. Hitung kuota real-time di dalam transaksi yang terkunci
            $currentCount = DB::table('mentor_student')
                ->where('mentor_id', $mentor->id)
                ->where('day_assigned', $day)
                ->where('is_active', true)
                ->count();

            if ($currentCount >= $maxQuota) {
                throw ValidationException::withMessages([
                    'day' => "Kuota mengajar untuk hari {$day} sudah penuh ({$currentCount}/{$maxQuota}).",
                ]);
            }

            // 4. Validasi pencegahan jadwal bentrok santri di hari yang sama
            $existsSameDay = DB::table('mentor_student')
                ->where('student_id', $student->id)
                ->where('day_assigned', $day)
                ->where('is_active', true)
                ->exists();

            if ($existsSameDay) {
                throw ValidationException::withMessages([
                    'student_id' => 'Santri ini sudah memiliki jadwal belajar aktif pada hari yang sama.',
                ]);
            }

            $timeFormatted = $time ? (strlen($time) === 5 ? $time.':00' : $time) : null;

            // 5. Attach via Eloquent Relation
            $mentor->students()->attach($student->id, [
                'day_assigned' => $day,
                'time_assigned' => $timeFormatted,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 6. Sinkronisasi permohonan enrollment santri jika ada
            $waitingEnrollment = Enrollment::where('student_id', $student->id)
                ->whereIn('status', [
                    EnrollmentStatus::WAITING_ADMIN->value,
                    EnrollmentStatus::WAITING_PARENT->value,
                    EnrollmentStatus::CONFIRMED->value,
                ])
                ->whereNull('mentor_id')
                ->latest()
                ->first();

            if ($waitingEnrollment) {
                $waitingEnrollment->update([
                    'mentor_id' => $mentor->id,
                    'offered_days' => [$day],
                    'offered_time' => $timeFormatted ? substr($timeFormatted, 0, 5) : null,
                ]);
            }

            // 7. Dispatch Domain Event untuk proses asynchronous di background
            event(new StudentAssignedToMentor($mentor, $student, $day, $timeFormatted));
        });
    }
}
