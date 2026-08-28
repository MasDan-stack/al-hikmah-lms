<?php

namespace App\Services;

use App\Models\HifzTarget;
use App\Models\Mentor;
use App\Models\MentorLeave;
use App\Models\Program;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Support\Collection;

class StaffAnalyticsService
{
    /**
     * Dapatkan ringkasan metrik SDM & Guru Pembimbing
     */
    public function getStaffSummary(): array
    {
        $today = today();
        $totalMentors = Mentor::count();
        $activeMentors = Mentor::where('is_active', true)->count();

        // Guru Cuti Hari Ini
        $mentorsOnLeaveToday = MentorLeave::where('leave_date', $today)
            ->where('status', 'approved')
            ->count();

        // Total Santri Aktif & Rasio Guru:Santri
        $totalActiveStudents = Student::whereHas('programs', fn ($q) => $q->where('student_program.status', 'active'))->count();
        if ($totalActiveStudents === 0) {
            $totalActiveStudents = Student::count();
        }

        $ratio = $activeMentors > 0 ? round($totalActiveStudents / $activeMentors, 1) : 0;
        $ratioString = "1 : {$ratio}";

        // Mentor Overload (>40 santri)
        $overloadMentorsCount = Mentor::where('is_active', true)
            ->withCount(['students' => function ($q) {
                $q->where('mentor_student.is_active', true);
            }])
            ->having('students_count', '>', 40)
            ->count();

        return [
            'total_mentors' => $totalMentors,
            'active_mentors' => $activeMentors,
            'mentors_on_leave_today' => $mentorsOnLeaveToday,
            'total_active_students' => $totalActiveStudents,
            'mentor_student_ratio' => $ratioString,
            'ratio_value' => $ratio,
            'overload_mentors_count' => $overloadMentorsCount,
        ];
    }

    /**
     * Dapatkan daftar beban kerja seluruh mentor
     */
    public function getMentorWorkloadList(): Collection
    {
        $today = today();
        $leavesToday = MentorLeave::where('leave_date', $today)
            ->where('status', 'approved')
            ->pluck('mentor_id')
            ->toArray();

        return Mentor::with(['user', 'availabilities'])
            ->withCount([
                'students as active_students_count' => function ($q) {
                    $q->where('mentor_student.is_active', true);
                },
            ])
            ->get()
            ->map(function ($mentor) use ($leavesToday) {
                $studentCount = $mentor->active_students_count ?? 0;

                // Status Beban Kerja
                if ($studentCount > 40) {
                    $capacityStatus = 'overload';
                    $statusLabel = 'Overload (>40)';
                    $badgeClass = 'bg-danger text-white';
                } elseif ($studentCount >= 31) {
                    $capacityStatus = 'busy';
                    $statusLabel = 'Hampir Penuh (31-40)';
                    $badgeClass = 'bg-warning text-dark';
                } else {
                    $capacityStatus = 'normal';
                    $statusLabel = 'Optimal (≤30)';
                    $badgeClass = 'bg-success text-white';
                }

                // Sesi Mengajar Selesai
                $completedSessionsCount = Session::where('mentor_id', $mentor->id)
                    ->where('status', 'completed')
                    ->count();

                $totalSessionsCount = Session::where('mentor_id', $mentor->id)->count();
                $attendanceRate = $totalSessionsCount > 0 ? round(($completedSessionsCount / $totalSessionsCount) * 100, 1) : 100.0;

                // Target Santri yang Berhasil Diselesaikan
                $completedTargetsCount = HifzTarget::where('mentor_id', $mentor->user_id)
                    ->where('status', 'completed')
                    ->count();

                return [
                    'id' => $mentor->id,
                    'user_id' => $mentor->user_id,
                    'name' => $mentor->getDisplayName(),
                    'email' => $mentor->user?->email ?? '-',
                    'phone' => $mentor->user?->phone ?? '-',
                    'specialization' => $mentor->specialization ?? 'Umum',
                    'rating' => (float) ($mentor->rating ?? 5.0),
                    'is_active' => (bool) $mentor->is_active,
                    'is_on_leave_today' => in_array($mentor->id, $leavesToday),
                    'active_students_count' => $studentCount,
                    'capacity_status' => $capacityStatus,
                    'status_label' => $statusLabel,
                    'badge_class' => $badgeClass,
                    'completed_sessions_count' => $completedSessionsCount,
                    'attendance_rate' => $attendanceRate,
                    'completed_targets_count' => $completedTargetsCount,
                ];
            })
            ->sortByDesc('active_students_count')
            ->values();
    }

    /**
     * Dapatkan daftar mentor dengan performa terbaik (Top Performing Mentors)
     */
    public function getTopPerformingMentors(int $limit = 5): Collection
    {
        return $this->getMentorWorkloadList()
            ->sortByDesc(function ($m) {
                return ($m['rating'] * 100) + ($m['completed_targets_count'] * 10) + $m['attendance_rate'];
            })
            ->take($limit)
            ->values();
    }

    /**
     * Dapatkan distribusi beban mengajar per program
     */
    public function getWorkloadDistributionByProgram(): array
    {
        $programs = Program::withCount([
            'students as active_students_count' => function ($q) {
                $q->where('student_program.status', 'active');
            },
        ])->get();

        $labels = [];
        $series = [];

        foreach ($programs as $program) {
            $labels[] = $program->name;
            $series[] = $program->active_students_count;
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }
}
