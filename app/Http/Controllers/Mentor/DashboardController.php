<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
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

        return view('mentor.dashboard', compact(
            'todaySessionsCount',
            'activeStudentsCount',
            'upcomingSessionsCount',
            'avgTajwid',
            'todaySessions',
            'students',
            'recentProgress'
        ));
    }

    public function profile(): View
    {
        $user = auth()->user();
        $mentor = $user->mentor;

        return view('mentor.profile', compact('user', 'mentor'));
    }
}
