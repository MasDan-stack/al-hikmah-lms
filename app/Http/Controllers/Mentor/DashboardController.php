<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\MentorActivityLog;
use App\Models\Progress;
use App\Models\Session;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $mentor = $user->mentor;

        $mentorId = $mentor ? $mentor->id : null;

        // Statistics
        $todaySessionsCount = $mentorId
            ? Session::where('mentor_id', $mentorId)->whereDate('date', today())->count()
            : 0;

        $activeStudentsCount = $mentorId
            ? $mentor->students()->count()
            : 0;

        $upcomingSessionsCount = $mentorId
            ? Session::where('mentor_id', $mentorId)
                ->whereDate('date', '>', today())
                ->whereDate('date', '<=', today()->addDays(7))
                ->count()
            : 0;

        $avgTajwid = $mentorId
            ? round(Progress::where('mentor_id', $mentorId)->avg('nilai_tajwid') ?? 0, 1)
            : 0;

        // Today's schedule
        $todaySessions = $mentorId
            ? Session::with(['student.user'])
                ->where('mentor_id', $mentorId)
                ->whereDate('date', today())
                ->orderBy('time', 'asc')
                ->get()
            : collect();

        // Students & recent progress
        $students = $mentorId
            ? $mentor->students()->with(['user'])->get()
            : collect();

        $recentProgress = $mentorId
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

            if ($mentorId) {
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
        if ($mentorId && $students->isNotEmpty()) {
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
