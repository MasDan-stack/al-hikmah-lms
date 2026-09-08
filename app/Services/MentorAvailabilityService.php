<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Events\StudentAssignedToMentor;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MentorAvailabilityService
{
    /**
     * Mengambil seluruh ketersediaan mentor untuk matriks admin.
     */
    public function getAllAvailabilities(array $filters = []): Collection
    {
        $query = Mentor::with([
            'user',
            'availabilities',
            'students' => function ($q) {
                $q->wherePivot('is_active', true)->with(['enrollments.program']);
            },
        ])->where('is_active', true);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($filters['slot']) && $filters['slot'] !== 'all' && is_numeric($filters['slot'])) {
            $slotNum = (int) $filters['slot'];
            $dayFilter = $filters['day'] ?? null;

            $query->whereHas('availabilities', function ($q) use ($slotNum, $dayFilter) {
                $q->where('is_available', true)
                    ->whereJsonContains('slot_numbers', $slotNum);
                if ($dayFilter && $dayFilter !== 'all') {
                    $dayKey = MentorAvailability::INDONESIAN_TO_ENGLISH[$dayFilter] ?? $dayFilter;
                    $q->where('day', $dayKey);
                }
            });
        }

        return $query->get();
    }

    /**
     * Mengambil ketersediaan per mentor diindeks berdasarkan hari.
     */
    public function getMentorAvailability(int $mentorId): \Illuminate\Support\Collection
    {
        return MentorAvailability::where('mentor_id', $mentorId)
            ->get()
            ->keyBy('day');
    }

    /**
     * Mengambil daftar mentor yang membuka slot tertentu dan masih memiliki sisa kuota.
     */
    public function getAvailableMentors(string $day, int $slotNumber): \Illuminate\Support\Collection
    {
        $dayKey = MentorAvailability::INDONESIAN_TO_ENGLISH[strtolower($day)] ?? strtolower($day);

        $mentors = Mentor::with(['user', 'availabilities' => function ($q) use ($dayKey) {
            $q->where('day', $dayKey);
        }])->where('is_active', true)->get();

        // Hitung jumlah santri aktif di slot & hari tersebut
        $studentCounts = DB::table('mentor_student')
            ->select('mentor_id', DB::raw('count(*) as total'))
            ->where('day_assigned', $dayKey)
            ->where('slot_number', $slotNumber)
            ->where('is_active', true)
            ->groupBy('mentor_id')
            ->pluck('total', 'mentor_id');

        return $mentors->filter(function ($mentor) use ($slotNumber, $studentCounts) {
            $availability = $mentor->availabilities->first();
            if (! $availability || ! $availability->is_available || $availability->is_holiday) {
                return false;
            }

            if (! $availability->hasSlot($slotNumber)) {
                return false;
            }

            // Sistem privat 1-on-1: Mentor hanya tersedia jika belum ada santri di slot tersebut
            $currentLoad = $studentCounts[$mentor->id] ?? 0;

            return $currentLoad === 0;
        })->values();
    }

    /**
     * Menyimpan pilihan angka slot 0-6 mentor.
     */
    public function saveAvailability(int $mentorId, array $data): array
    {
        return DB::transaction(function () use ($mentorId, $data) {
            $results = [];
            $maxStudents = $data['max_students'] ?? 5;
            $daysOrder = MentorAvailability::DAYS_ORDER;

            foreach ($daysOrder as $dayKey) {
                // Cari data dari key 'days' atau 'availability'
                $isHolidayExplicit = ! empty($data['days'][$dayKey]['is_holiday']);
                $holidayNotes = $data['days'][$dayKey]['notes'] ?? null;
                $rawSlots = $data['days'][$dayKey]['slots'] ?? $data['availability'][$dayKey] ?? [];

                // Konversi string/array ke list angka integer 0-6 yang valid
                $slotNumbers = collect((array) $rawSlots)
                    ->filter(fn ($s) => is_numeric($s) && (int) $s >= 0 && (int) $s <= 6)
                    ->map(fn ($s) => (int) $s)
                    ->unique()
                    ->sort()
                    ->values()
                    ->toArray();

                if ($isHolidayExplicit) {
                    $slotNumbers = [];
                    $isAvailable = false;
                    $isHoliday = true;
                } else {
                    $isAvailable = ! empty($slotNumbers);
                    $isHoliday = false;
                    $holidayNotes = null;
                }

                // Set jam mulai & selesai representatif
                $firstSlot = ! empty($slotNumbers) ? $slotNumbers[0] : null;
                $lastSlot = ! empty($slotNumbers) ? end($slotNumbers) : null;
                $startTime = $firstSlot !== null ? (MentorAvailability::SLOT_MAP[$firstSlot]['time'].':00') : '08:00:00';
                $endTime = $lastSlot !== null ? (MentorAvailability::SLOT_MAP[$lastSlot]['time'].':00') : '16:00:00';

                $availability = MentorAvailability::updateOrCreate(
                    [
                        'mentor_id' => $mentorId,
                        'day' => $dayKey,
                    ],
                    [
                        'slot_numbers' => $slotNumbers,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'max_students' => $maxStudents,
                        'is_available' => $isAvailable,
                        'is_holiday' => $isHoliday,
                        'notes' => $isHoliday ? ($holidayNotes ?? 'Hari Bebas Mentor') : null,
                    ]
                );

                $results[$dayKey] = $availability;
            }

            return $results;
        });
    }

    /**
     * Parser pintar: Mengonversi teks chat WhatsApp menjadi array slot harian.
     */
    public function parseWhatsAppFormat(string $rawText): array
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $rawText));
        $parsed = [];

        $dayAliases = [
            'senin' => 'monday',
            'selasa' => 'tuesday',
            'rabu' => 'wednesday',
            'kamis' => 'thursday',
            'jumat' => 'friday',
            "jum'at" => 'friday',
            'sabtu' => 'saturday',
            'minggu' => 'sunday',
            'ahad' => 'sunday',
        ];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with(strtolower($line), 'nama')) {
                continue;
            }

            if (preg_match('/^([a-zA-Z\'\`]+)\s*[:=\-]\s*(.*)$/i', $line, $matches)) {
                $dayInput = strtolower(trim($matches[1]));
                $slotsInput = trim($matches[2]);

                $englishDay = $dayAliases[$dayInput] ?? null;
                if ($englishDay) {
                    preg_match_all('/[0-6]/', $slotsInput, $slotMatches);
                    $slots = array_map('intval', array_unique($slotMatches[0] ?? []));
                    sort($slots);
                    $parsed[$englishDay] = $slots;
                }
            }
        }

        return $parsed;
    }

    /**
     * Menghasilkan teks ringkas format WhatsApp dari jadwal mentor.
     */
    public function exportToWhatsAppFormat(int $mentorId): string
    {
        $mentor = Mentor::findOrFail($mentorId);
        $availabilities = MentorAvailability::where('mentor_id', $mentorId)->get()->keyBy('day');

        $out = "Nama : {$mentor->getDisplayName()}\n\n";

        $dayDisplay = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => "Jum'at",
            'saturday' => 'Sabtu',
            'sunday' => 'Ahad',
        ];

        foreach (MentorAvailability::DAYS_ORDER as $dayKey) {
            $avail = $availabilities->get($dayKey);
            $slots = $avail?->slot_numbers ?? [];
            $slotsStr = ! empty($slots) ? implode(' ', $slots) : '-';
            $out .= "{$dayDisplay[$dayKey]} : {$slotsStr}\n";
        }

        $out .= "\nKeterangan Angka Slot:\n";
        foreach (MentorAvailability::SLOT_MAP as $num => $info) {
            $out .= "{$num}. {$info['time']} ({$info['desc']})\n";
        }

        return trim($out);
    }

    /**
     * Mendapatkan daftar santri yang belum memiliki guru.
     */
    public function getUnassignedStudents(): Collection
    {
        return Student::with([
            'parent.user',
            'enrollments' => function ($q) {
                $q->with('program')->latest();
            },
        ])
            ->where(function ($q) {
                $q->whereDoesntHave('mentors', function ($mq) {
                    $mq->where('mentor_student.is_active', true);
                })
                    ->orWhereHas('enrollments', function ($eq) {
                        $eq->whereIn('status', [
                            EnrollmentStatus::WAITING_ADMIN->value,
                            EnrollmentStatus::WAITING_PARENT->value,
                            EnrollmentStatus::CONFIRMED->value,
                        ])->whereNull('mentor_id');
                    });
            })
            ->get();
    }

    /**
     * Eksekusi alokasi santri dengan penguncian atomik dan anti-bentrok.
     */
    public function assignStudent(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            $mentorId = (int) $data['mentor_id'];
            $studentId = (int) $data['student_id'];
            $dayInput = $data['day'] ?? $data['day_of_week'] ?? 'monday';
            $day = MentorAvailability::INDONESIAN_TO_ENGLISH[strtolower($dayInput)] ?? strtolower($dayInput);
            $slotNumber = (int) ($data['slot_number'] ?? $data['slot'] ?? 1);
            $programId = ! empty($data['program_id']) ? (int) $data['program_id'] : null;
            $notes = $data['notes'] ?? null;

            $mentor = Mentor::where('id', $mentorId)->lockForUpdate()->firstOrFail();
            $student = Student::findOrFail($studentId);

            if (! $mentor->is_active) {
                throw ValidationException::withMessages([
                    'mentor_id' => 'Mentor sedang dalam status tidak aktif.',
                ]);
            }

            $availability = MentorAvailability::where('mentor_id', $mentor->id)
                ->where('day', $day)
                ->lockForUpdate()
                ->first();

            if (! $availability || ! $availability->is_available || $availability->is_holiday) {
                throw ValidationException::withMessages([
                    'day' => "Mentor libur / tidak membuka jadwal pada hari {$day}.",
                ]);
            }

            if (! $availability->hasSlot($slotNumber)) {
                $slotTime = MentorAvailability::SLOT_MAP[$slotNumber]['time'] ?? $slotNumber;
                throw ValidationException::withMessages([
                    'slot_number' => "Mentor tidak membuka Slot {$slotNumber} ({$slotTime} WIB) di hari yang dipilih.",
                ]);
            }

            // Cek kuota per-slot jam spesifik (Sistem Privat 1-on-1: Maksimal 1 santri per slot)
            $currentLoad = DB::table('mentor_student')
                ->where('mentor_id', $mentor->id)
                ->where('day_assigned', $day)
                ->where('slot_number', $slotNumber)
                ->where('is_active', true)
                ->lockForUpdate()
                ->count();

            if ($currentLoad >= 1) {
                $dayName = MentorAvailability::DAYS[$day] ?? ucfirst($day);
                $slotTime = MentorAvailability::SLOT_MAP[$slotNumber]['time'] ?? 'jam tersebut';
                throw ValidationException::withMessages([
                    'slot_number' => "Jadwal bentrok! Ustadz/ah {$mentor->getDisplayName()} sudah memiliki santri bimbingan privat (1-on-1) pada hari {$dayName} Slot {$slotNumber} ({$slotTime} WIB).",
                ]);
            }

            // Cek pencegahan bentrok santri di hari dan slot yang sama
            $existsSameSlot = DB::table('mentor_student')
                ->where('student_id', $student->id)
                ->where('day_assigned', $day)
                ->where('slot_number', $slotNumber)
                ->where('is_active', true)
                ->exists();

            if ($existsSameSlot) {
                throw ValidationException::withMessages([
                    'student_id' => 'Santri ini sudah memiliki jadwal aktif pada hari dan jam slot yang sama.',
                ]);
            }

            $slotTime = MentorAvailability::SLOT_MAP[$slotNumber]['time'] ?? '08:00';

            // Simpan alokasi ke pivot
            DB::table('mentor_student')->insert([
                'mentor_id' => $mentor->id,
                'student_id' => $student->id,
                'program_id' => $programId,
                'day_assigned' => $day,
                'slot_number' => $slotNumber,
                'time_assigned' => $slotTime.':00',
                'time_label' => $slotTime,
                'notes' => $notes,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sinkronisasi Enrollment santri jika ada
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
                    'offered_time' => $slotTime,
                ]);
            }

            // Dispatch Domain Event
            event(new StudentAssignedToMentor($mentor, $student, $day, $slotTime.':00'));

            Log::info('Santri dialokasikan ke mentor [Slot System]', [
                'mentor_id' => $mentor->id,
                'student_id' => $student->id,
                'day' => $day,
                'slot' => $slotNumber,
            ]);

            return true;
        });
    }
}
