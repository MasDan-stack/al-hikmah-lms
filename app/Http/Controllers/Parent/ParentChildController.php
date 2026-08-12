<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\Student;
use Illuminate\View\View;

class ParentChildController extends Controller
{
    public function index(): View
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent
            ? $parent->students()->with(['user', 'mentors.user'])->get()
            : collect();

        return view('parent.children.index', compact('children'));
    }

    public function show(int $id): View
    {
        $parent = auth()->user()->parentProfile;
        $child = Student::with(['user', 'mentors.user'])->findOrFail($id);

        if (! $parent || $child->parent_id !== $parent->id) {
            abort(403, 'Akses data anak ditolak.');
        }

        $progresses = Progress::with(['mentor.user', 'session'])
            ->where('student_id', $child->id)
            ->latest()
            ->get();

        // Data Grafik Bulanan untuk anak ini (6 bulan terakhir)
        $chartLabels = [];
        $chartProgressCounts = [];
        $chartAvgTajwid = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $chartLabels[] = $monthDate->translatedFormat('M Y');

            $monthProgress = Progress::where('student_id', $child->id)
                ->whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month);

            $chartProgressCounts[] = (clone $monthProgress)->count();
            $chartAvgTajwid[] = round((clone $monthProgress)->avg('nilai_tajwid') ?? 0, 1);
        }

        return view('parent.children.show', compact(
            'child',
            'progresses',
            'chartLabels',
            'chartProgressCounts',
            'chartAvgTajwid'
        ));
    }

    public function exportReport(int $id)
    {
        $parent = auth()->user()->parentProfile;
        $child = Student::with(['user', 'mentors.user'])->findOrFail($id);

        if (! $parent || $child->parent_id !== $parent->id) {
            abort(403, 'Akses data anak ditolak.');
        }

        $progresses = Progress::with(['mentor.user'])
            ->where('student_id', $child->id)
            ->latest()
            ->get();

        $avgTajwid = round($progresses->avg('nilai_tajwid') ?? 0, 1);
        $avgFluent = round($progresses->avg('nilai_fluent') ?? 0, 1);

        return view('parent.children.report', compact('child', 'progresses', 'avgTajwid', 'avgFluent'));
    }
}
