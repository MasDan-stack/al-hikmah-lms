<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function export(Request $request): View
    {
        $user = auth()->user();
        $mentor = $user->mentor;

        if (! $mentor) {
            abort(403, 'Akses khusus mentor');
        }

        // Summary Statistics
        $totalSessions = Session::where('mentor_id', $mentor->id)->count();
        $completedSessions = Session::where('mentor_id', $mentor->id)->where('status', 'completed')->count();
        $activeStudentsCount = $mentor->students()->count();

        $avgFluent = round(Progress::where('mentor_id', $mentor->id)->avg('nilai_fluent') ?? 0, 1);
        $avgTajwid = round(Progress::where('mentor_id', $mentor->id)->avg('nilai_tajwid') ?? 0, 1);

        // Students list with progress summary
        $students = $mentor->students()->with(['user'])->get()->map(function ($student) use ($mentor) {
            $studentProgresses = Progress::where('student_id', $student->id)
                ->where('mentor_id', $mentor->id)
                ->get();

            $student->total_records = $studentProgresses->count();
            $student->avg_tajwid = round($studentProgresses->avg('nilai_tajwid') ?? 0, 1);
            $student->latest_progress = $studentProgresses->sortByDesc('created_at')->first();

            return $student;
        });

        // Recent progress records
        $recentProgresses = Progress::with(['student.user'])
            ->where('mentor_id', $mentor->id)
            ->latest()
            ->take(20)
            ->get();

        return view('mentor.reports.export', compact(
            'mentor',
            'user',
            'totalSessions',
            'completedSessions',
            'activeStudentsCount',
            'avgFluent',
            'avgTajwid',
            'students',
            'recentProgresses'
        ));
    }
}
