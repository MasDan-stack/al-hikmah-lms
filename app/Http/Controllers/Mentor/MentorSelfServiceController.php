<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\MentorGoal;
use App\Models\MentorPerformanceSnapshot;
use App\Models\MentorSelfAssessment;
use App\Services\MentorInsightsService;
use App\Services\MentorPerformanceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorSelfServiceController extends Controller
{
    public function __construct(
        protected MentorPerformanceService $performanceService,
        protected MentorInsightsService $insightsService
    ) {}

    /**
     * Portal Kinerja Mandiri Guru Pembimbing (My Performance Dashboard).
     */
    public function myPerformance(Request $request): View
    {
        $user = auth()->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();

        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));
        $targetDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $targetDate->copy()->startOfMonth();
        $endDate = $targetDate->copy()->endOfMonth();

        // 1. Ambil atau generate snapshot performa pribadi
        $snapshot = MentorPerformanceSnapshot::where('mentor_id', $mentor->id)
            ->where('period_start', $startDate->toDateString())
            ->first();

        if (! $snapshot) {
            $snapshot = $this->performanceService->snapshotMentor($mentor, $selectedMonth);
        }

        $metrics = $this->performanceService->getMentorMetrics($mentor, $startDate, $endDate);

        // 2. Ambil atau generate AI Prescriptive Insights
        $insight = $mentor->insights()->where('period', $selectedMonth)->first();
        if (! $insight) {
            $insight = $this->insightsService->generateInsights($mentor, $metrics);
        }

        // 3. Hitung Posisi Persentil Relatif di Lembaga
        $allScores = MentorPerformanceSnapshot::where('period_start', $startDate->toDateString())
            ->where('period_type', 'monthly')
            ->pluck('composite_score')
            ->sort()
            ->values();

        $totalMentors = $allScores->count();
        $belowCount = $allScores->filter(fn ($s) => $s < $snapshot->composite_score)->count();
        $percentile = $totalMentors > 1 ? round(($belowCount / ($totalMentors - 1)) * 100) : 100;

        // 4. Target Capaian Aktif
        $goals = MentorGoal::where('mentor_id', $mentor->id)
            ->where('period', $selectedMonth)
            ->get();

        // 5. Evaluasi Diri
        $selfAssessment = MentorSelfAssessment::where('mentor_id', $mentor->id)
            ->where('period', $selectedMonth)
            ->first();

        // 6. Lencana
        $badges = $mentor->badges()->get();

        return view('mentor.performance.index', compact(
            'mentor',
            'snapshot',
            'metrics',
            'insight',
            'percentile',
            'goals',
            'selfAssessment',
            'badges',
            'selectedMonth'
        ));
    }

    /**
     * Halaman Kelola Target Capaian Bulanan (Goal Setting).
     */
    public function goals(Request $request): View
    {
        $user = auth()->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));

        $goals = MentorGoal::where('mentor_id', $mentor->id)
            ->where('period', $selectedMonth)
            ->latest()
            ->get();

        return view('mentor.performance.goals', compact('mentor', 'goals', 'selectedMonth'));
    }

    /**
     * Simpan Target Baru / 1-Click Adopt AI Recommendation.
     */
    public function storeGoal(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'goal_type' => 'required|in:rating,retention,tajwid_score,attendance,target_completion',
            'target_value' => 'required|numeric|min:1|max:100',
            'period' => 'nullable|string',
        ]);

        $period = $validated['period'] ?? now()->format('Y-m');

        // Cari nilai saat ini sesuai tipe goal
        $startDate = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $period)->endOfMonth();
        $metrics = $this->performanceService->getMentorMetrics($mentor, $startDate, $endDate);

        $currentVal = match ($validated['goal_type']) {
            'rating' => (float) $metrics['avg_rating_bayesian'],
            'retention' => (float) $metrics['retention_rate'],
            'tajwid_score' => (float) $metrics['avg_tajwid_score'],
            'attendance' => (float) $metrics['attendance_rate'],
            'target_completion' => (float) $metrics['target_achievement_rate'],
            default => 0.0,
        };

        MentorGoal::create([
            'mentor_id' => $mentor->id,
            'goal_type' => $validated['goal_type'],
            'title' => $validated['title'],
            'target_value' => $validated['target_value'],
            'current_value' => $currentVal,
            'period' => $period,
            'status' => 'in_progress',
            'achieved_at' => null,
        ]);

        return back()->with('success', 'Target capaian bimbingan berhasil ditambahkan ke daftar aktif.');
    }

    /**
     * Halaman Formulir Evaluasi Diri (Self-Assessment).
     */
    public function selfAssessment(Request $request): View
    {
        $user = auth()->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();
        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));

        $assessment = MentorSelfAssessment::where('mentor_id', $mentor->id)
            ->where('period', $selectedMonth)
            ->first();

        return view('mentor.performance.self-assessment', compact('mentor', 'assessment', 'selectedMonth'));
    }

    /**
     * Simpan Formulir Evaluasi Diri.
     */
    public function storeSelfAssessment(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $mentor = Mentor::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'period' => 'required|string',
            'strengths' => 'required|string|min:5',
            'weaknesses' => 'nullable|string',
            'challenges' => 'nullable|string',
            'action_plan' => 'required|string|min:5',
            'self_score' => 'nullable|numeric',
        ]);

        $weaknesses = $validated['weaknesses'] ?? $validated['challenges'] ?? '-';

        MentorSelfAssessment::updateOrCreate(
            [
                'mentor_id' => $mentor->id,
                'period' => $validated['period'],
            ],
            [
                'strengths' => $validated['strengths'],
                'weaknesses' => $weaknesses,
                'action_plan' => $validated['action_plan'],
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('mentor.performance.index', ['month' => $validated['period']])
            ->with('success', 'Formulir refleksi diri dan rencana peningkatan mutu berhasil disimpan.');
    }
}
