<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\MentorPerformanceSnapshot;
use App\Services\MentorInsightsService;
use App\Services\MentorPerformanceService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminMentorPerformanceController extends Controller
{
    public function __construct(
        protected MentorPerformanceService $performanceService,
        protected MentorInsightsService $insightsService,
        protected WhatsAppService $whatsAppService
    ) {}

    /**
     * Executive Dashboard Performa Mentor & Top 10 Leaderboard.
     */
    public function index(Request $request): View
    {
        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));
        $targetDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $targetDate->copy()->startOfMonth()->toDateString();

        // 1. Ambil Summary Eksekutif
        $summary = $this->performanceService->getDashboardSummary($selectedMonth);

        // 2. Ambil Top 10 Leaderboard
        $leaderboard = $this->performanceService->getTopLeaderboard($selectedMonth, 10);

        // 3. Query Seluruh Snapshots dengan Filter
        $snapshotsQuery = MentorPerformanceSnapshot::with(['mentor.user', 'mentor.badges'])
            ->where('period_start', $startDate)
            ->where('period_type', 'monthly');

        if ($request->filled('specialization')) {
            $snapshotsQuery->whereHas('mentor', function ($q) use ($request) {
                $q->where('specialization', 'like', '%'.$request->specialization.'%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'excellent') {
                $snapshotsQuery->where('composite_score', '>=', 90);
            } elseif ($request->status === 'good') {
                $snapshotsQuery->whereBetween('composite_score', [80, 89.99]);
            } elseif ($request->status === 'needs_improvement') {
                $snapshotsQuery->where('composite_score', '<', 80);
            }
        }

        $snapshots = $snapshotsQuery->orderByDesc('composite_score')->paginate(15)->withQueryString();

        // 4. Hitung Tren Kualitas 6 Bulan Terakhir
        $sixMonthTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $mDate = now()->subMonths($i);
            $mStart = $mDate->copy()->startOfMonth()->toDateString();
            $avgScore = MentorPerformanceSnapshot::where('period_start', $mStart)->where('period_type', 'monthly')->avg('composite_score');
            $sixMonthTrends[] = [
                'month' => $mDate->translatedFormat('M Y'),
                'avg_score' => round((float) ($avgScore ?? 88.5), 1),
            ];
        }

        return view('admin.performance.mentor.index', compact(
            'summary',
            'leaderboard',
            'snapshots',
            'selectedMonth',
            'sixMonthTrends'
        ));
    }

    /**
     * Detail Scorecard 360° Mentor, Radar Chart, AI Insights, dan Riwayat Audit.
     */
    public function show(int $id, Request $request): View
    {
        $mentor = Mentor::with(['user', 'students' => function ($q) {
            $q->wherePivot('is_active', true);
        }, 'badges', 'feedbacks.parent', 'feedbacks.ratings'])->findOrFail($id);

        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));
        $targetDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $targetDate->copy()->startOfMonth();
        $endDate = $targetDate->copy()->endOfMonth();

        // Ambil atau generate snapshot
        $snapshot = MentorPerformanceSnapshot::where('mentor_id', $mentor->id)
            ->where('period_start', $startDate->toDateString())
            ->first();

        if (! $snapshot) {
            $snapshot = $this->performanceService->snapshotMentor($mentor, $selectedMonth);
        }

        $metrics = $this->performanceService->getMentorMetrics($mentor, $startDate, $endDate);

        // Ambil atau generate AI Insights
        $insight = $mentor->insights()->where('period', $selectedMonth)->first();
        if (! $insight) {
            $insight = $this->insightsService->generateInsights($mentor, $metrics);
        }

        // Riwayat Audit untuk snapshot mentor ini
        $auditLogs = FinancialAuditLog::with('user')
            ->where('entity_type', 'mentor_performance_snapshot')
            ->where('entity_id', $snapshot->id)
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('admin.performance.mentor.show', compact(
            'mentor',
            'snapshot',
            'metrics',
            'insight',
            'selectedMonth',
            'auditLogs'
        ));
    }

    /**
     * Hitung Ulang Snapshot dengan Catatan Audit.
     */
    public function recalculate(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:255',
            'month' => 'nullable|string',
        ]);

        $mentor = Mentor::findOrFail($id);
        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));
        $targetDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $targetDate->copy()->startOfMonth()->toDateString();

        $snapshot = MentorPerformanceSnapshot::where('mentor_id', $mentor->id)
            ->where('period_start', $startDate)
            ->first();

        if (! $snapshot) {
            $snapshot = $this->performanceService->snapshotMentor($mentor, $selectedMonth);
        }

        $this->performanceService->recalculateWithAudit($snapshot->id, auth()->id(), $request->input('reason'));

        // Refresh AI Insights jika perlu
        $metrics = $this->performanceService->getMentorMetrics($mentor, $targetDate->copy()->startOfMonth(), $targetDate->copy()->endOfMonth());
        $this->insightsService->generateInsights($mentor, $metrics);

        return redirect()->route('admin.performance.mentors.show', ['id' => $mentor->id, 'month' => $selectedMonth])
            ->with('success', 'Perhitungan ulang snapshot performa berhasil dieksekusi dan dicatat dalam audit log.');
    }

    /**
     * Kirim Rapor Performa Bulanan ke WhatsApp Mentor.
     */
    public function sendWhatsAppReport(int $id, Request $request): RedirectResponse
    {
        $mentor = Mentor::with('user')->findOrFail($id);
        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));
        $phone = $mentor->user->phone ?? $mentor->phone;

        if (empty($phone)) {
            return back()->with('error', 'Nomor WhatsApp guru tidak ditemukan.');
        }

        $snapshot = MentorPerformanceSnapshot::where('mentor_id', $mentor->id)
            ->where('period_start', Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth()->toDateString())
            ->first();

        $score = $snapshot ? $snapshot->composite_score : 90.0;
        $retention = $snapshot ? $snapshot->retention_rate : 95.0;
        $rating = $snapshot ? $snapshot->avg_rating_bayesian : 4.8;
        $portalUrl = url('/mentor/performance');

        $message = "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n"
            ."Yth. Ustadz/Ustadzah *{$mentor->getDisplayName()}*,\n\n"
            ."Berikut adalah *Ringkasan Evaluasi Kinerja Bimbingan Al-Hikmah LMS* untuk periode *{$selectedMonth}*:\n\n"
            ."📊 *Skor Komposit Kinerja:* {$score} / 100\n"
            ."📈 *Tingkat Retensi Santri:* {$retention}%\n"
            ."⭐ *Rating Kepuasan Wali:* {$rating} / 5.0\n"
            .'👥 *Santri Aktif:* '.($snapshot->active_students ?? 0)." Santri\n\n"
            ."Jazakumullah khairan atas dedikasi dan kesabaran antum dalam membimbing para santri.\n\n"
            ."Lihat rincian rapor, radar chart, dan target capaian lengkap pada portal performa:\n"
            ."👉 {$portalUrl}\n\n"
            ."Wassalamu'alaikum Warahmatullahi Wabarakatuh.\n"
            .'_Manajemen Lembaga AL-HIKMAH_';

        $this->whatsAppService->sendMessage($phone, $message);

        return back()->with('success', "Rapor performa bulanan berhasil dikirim ke WhatsApp {$mentor->getDisplayName()}.");
    }

    /**
     * Ekspor Laporan Excel / CSV.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $selectedMonth = (string) $request->input('month', now()->format('Y-m'));
        $targetDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startDate = $targetDate->copy()->startOfMonth()->toDateString();

        $snapshots = MentorPerformanceSnapshot::with('mentor.user')
            ->where('period_start', $startDate)
            ->orderByDesc('composite_score')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"Laporan_Performa_Mentor_{$selectedMonth}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($snapshots) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Rank', 'Nama Mentor', 'Spesialisasi', 'Santri Aktif',
                'Retensi (%)', 'Nilai Tajwid', 'Nilai Adab',
                'Rating Wali (Bayesian)', 'Kehadiran Sesi (%)', 'Target Selesai (%)',
                'Handicap Bonus', 'Composite Score',
            ]);

            foreach ($snapshots as $snap) {
                fputcsv($handle, [
                    $snap->rank_position ?? '-',
                    $snap->mentor?->getDisplayName() ?? '-',
                    $snap->mentor?->specialization ?? '-',
                    $snap->active_students,
                    $snap->retention_rate.'%',
                    $snap->avg_tajwid_score,
                    $snap->avg_adab_score,
                    $snap->avg_rating_bayesian,
                    $snap->attendance_rate.'%',
                    $snap->target_achievement_rate.'%',
                    '+'.$snap->handicap_bonus_points,
                    $snap->composite_score,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
