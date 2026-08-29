<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\MentorActivityLog;
use App\Models\MentorApplication;
use App\Models\Progress;
use App\Models\Session;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $mentor = $user->mentor;
        $mentorId = $mentor ? $mentor->id : null;

        // Data Lamaran Rekrutmen Calon Guru (Jika Masih dalam Masa Seleksi)
        $mentorApplication = MentorApplication::with(['testSessions', 'documents'])
            ->where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->latest()
            ->first();

        $isRecruitmentMode = false;
        if ($mentorApplication && $mentorApplication->status !== 'approved') {
            $isRecruitmentMode = true;
        } elseif ($mentor && ! $mentor->is_active && $mentor->status !== 'probation' && $mentor->status !== 'active') {
            $isRecruitmentMode = true;
        }

        $activeTestSession = $mentorApplication
            ? $mentorApplication->testSessions->whereIn('status', ['in_progress', 'scheduled'])->first()
            : null;

        $completedTestSessions = $mentorApplication
            ? $mentorApplication->testSessions->where('status', 'completed')->all()
            : [];

        // Students (Deduplicated - Active Paid Only)
        $students = ($mentorId && ! $isRecruitmentMode)
            ? Student::where(function ($q) use ($mentor) {
                $q->whereHas('mentors', fn ($m) => $m->where('mentors.id', $mentor->id)->where('mentor_student.is_active', true))
                    ->orWhereHas('enrollments', fn ($e) => $e->where('mentor_id', $mentor->id)->where('status', EnrollmentStatus::ACTIVE->value));
            })->with(['user', 'parent.user', 'programs'])->get()
            : collect();

        // Statistics
        $todaySessionsCount = ($mentorId && ! $isRecruitmentMode)
            ? Session::where('mentor_id', $mentorId)->whereDate('date', today())->count()
            : 0;

        $activeStudentsCount = $students->count();

        $upcomingSessionsCount = ($mentorId && ! $isRecruitmentMode)
            ? Session::where('mentor_id', $mentorId)
                ->whereDate('date', '>', today())
                ->whereDate('date', '<=', today()->addDays(7))
                ->count()
            : 0;

        $avgTajwid = ($mentorId && ! $isRecruitmentMode)
            ? round(Progress::where('mentor_id', $mentorId)->avg('nilai_tajwid') ?? 0, 1)
            : 0;

        // Today's schedule with confirmation
        $todaySessions = ($mentorId && ! $isRecruitmentMode)
            ? Session::with(['student.user', 'student.parent.user', 'confirmation'])
                ->where('mentor_id', $mentorId)
                ->whereDate('date', today())
                ->orderBy('time', 'asc')
                ->get()
            : collect();

        $recentProgress = ($mentorId && ! $isRecruitmentMode)
            ? Progress::with(['student.user'])
                ->where('mentor_id', $mentorId)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        // 📊 Chart Data (Monthly Progress Counts & Avg Tajwid for past 6 months)
        $chartLabels = [];
        $chartProgressCounts = [];
        $chartAvgTajwid = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthLabel = $monthDate->translatedFormat('M Y');
            $chartLabels[] = $monthLabel;

            if ($mentorId && ! $isRecruitmentMode) {
                $monthProgress = Progress::where('mentor_id', $mentorId)
                    ->whereYear('created_at', $monthDate->year)
                    ->whereMonth('created_at', $monthDate->month);

                $chartProgressCounts[] = (clone $monthProgress)->count();
                $chartAvgTajwid[] = round((clone $monthProgress)->avg('nilai_tajwid') ?? 0, 1);
            } else {
                $chartProgressCounts[] = 0;
                $chartAvgTajwid[] = 0;
            }
        }

        // ⚠️ Alert: Santri dengan Nilai Tajwid / Fluent Terendah (< 70 atau terkecil)
        $lowProgressStudents = collect();
        if ($mentorId && ! $isRecruitmentMode && $students->isNotEmpty()) {
            $lowProgressStudents = $students->map(function ($student) use ($mentorId) {
                $avg = Progress::where('mentor_id', $mentorId)
                    ->where('student_id', $student->id)
                    ->avg('nilai_tajwid');

                $student->avg_tajwid_score = $avg !== null ? round($avg, 1) : null;

                return $student;
            })->filter(function ($student) {
                return $student->avg_tajwid_score !== null && $student->avg_tajwid_score <= 75;
            })->sortBy('avg_tajwid_score')->values()->take(5);
        }

        // 🕒 Activity Feed Mentor
        $recentActivities = $mentorId
            ? MentorActivityLog::where('mentor_id', $mentorId)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('mentor.dashboard', compact(
            'isRecruitmentMode',
            'mentorApplication',
            'activeTestSession',
            'completedTestSessions',
            'todaySessionsCount',
            'activeStudentsCount',
            'upcomingSessionsCount',
            'avgTajwid',
            'todaySessions',
            'students',
            'recentProgress',
            'chartLabels',
            'chartProgressCounts',
            'chartAvgTajwid',
            'lowProgressStudents',
            'recentActivities'
        ));
    }

    public function profile(): View
    {
        $user = auth()->user();
        $mentor = $user->mentor;
        $recentActivities = $mentor
            ? MentorActivityLog::where('mentor_id', $mentor->id)->latest()->take(10)->get()
            : collect();

        return view('mentor.profile', compact('user', 'mentor', 'recentActivities'));
    }
}
