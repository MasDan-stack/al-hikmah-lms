<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AhpCriteriaConfig;
use App\Models\AhpEvaluationSnapshot;
use App\Services\DecisionSupport\AhpRankingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAhpRankingController extends Controller
{
    public function __construct(
        protected AhpRankingService $ahpService
    ) {}

    /**
     * Tampilan Utama Dashboard SPK Guru Teladan AHP.
     */
    public function index(Request $request): View
    {
        $selectedMonth = $request->query('month', now()->format('Y-m'));

        // Evaluasi dan ranking bulan terpilih
        $evaluation = $this->ahpService->evaluateAndRank($selectedMonth);
        $matrix = $this->ahpService->getActivePairwiseMatrix();
        $criteriaDefs = AhpRankingService::CRITERIA_KEYS;

        // Data Radar Chart Top 3
        $radarCategories = [
            'Disiplin (C1)',
            'Pedagogi (C2)',
            'Akhlak (C3)',
            'Kepuasan Wali (C4)',
            'Keaktifan (C5)',
        ];

        $radarSeries = [];
        foreach ($evaluation['podium'] as $podiumMentor) {
            $radarSeries[] = [
                'name' => $podiumMentor['mentor_name'],
                'data' => [
                    (float) $podiumMentor['c1_discipline'],
                    (float) $podiumMentor['c2_pedagogy'],
                    (float) $podiumMentor['c3_morals'],
                    (float) $podiumMentor['c4_satisfaction'],
                    (float) $podiumMentor['c5_involvement'],
                ],
            ];
        }

        return view('admin.mentors.ahp-ranking.index', compact(
            'evaluation',
            'selectedMonth',
            'matrix',
            'criteriaDefs',
            'radarCategories',
            'radarSeries'
        ));
    }

    /**
     * Simpan Matriks Perbandingan Berpasangan Baru (Setelah Uji Konsistensi).
     */
    public function updateMatrix(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'matrix' => ['required', 'array'],
        ]);

        $calculated = $this->ahpService->calculateWeights($validated['matrix']);

        if (! $calculated['is_consistent']) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'is_consistent' => false,
                    'cr' => $calculated['cr'],
                    'cr_percent' => $calculated['cr_percent'],
                    'message' => "Matriks perbandingan tidak konsisten (CR = {$calculated['cr_percent']}% > 10%). Mohon tinjau kembali perbandingan antar kriteria.",
                ], 422);
            }

            return back()->with('error', "Matriks perbandingan tidak konsisten (CR = {$calculated['cr_percent']}% > 10%).");
        }

        // Simpan konfigurasi kriteria ke database
        foreach (AhpRankingService::CRITERIA_KEYS as $key => $meta) {
            AhpCriteriaConfig::updateOrCreate(
                ['criteria_key' => $key],
                [
                    'criteria_name' => $meta['name'],
                    'description' => $meta['desc'],
                    'weight' => $calculated['weights'][$key] ?? $meta['default_weight'],
                    'pairwise_values' => $validated['matrix'][$key] ?? [],
                    'is_active' => true,
                ]
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_consistent' => true,
                'cr' => $calculated['cr'],
                'cr_percent' => $calculated['cr_percent'],
                'weights' => $calculated['weights'],
                'message' => 'Konfigurasi matriks AHP berhasil disimpan dan konsisten.',
            ]);
        }

        return back()->with('success', 'Konfigurasi matriks AHP berhasil diperbarui.');
    }

    /**
     * Poin B: Simulasi Bobot Matriks Tanpa Menyimpan Permanen.
     */
    public function simulate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'matrix' => ['required', 'array'],
            'month' => ['required', 'string'],
        ]);

        $simulation = $this->ahpService->simulateWeights($validated['matrix'], $validated['month']);

        return response()->json([
            'success' => true,
            'is_consistent' => $simulation['is_consistent'],
            'cr' => $simulation['cr'],
            'cr_percent' => $simulation['cr_percent'],
            'simulated_weights' => $simulation['simulated_weights']['weights'],
            'rank_diffs' => $simulation['rank_diffs'],
        ]);
    }

    /**
     * Poin A: Update Catatan Khusus Mentor dari Admin.
     */
    public function updateNotes(Request $request, int $snapshotId): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $snapshot = AhpEvaluationSnapshot::findOrFail($snapshotId);
        $snapshot->update([
            'admin_notes' => $validated['admin_notes'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan khusus mentor berhasil disimpan.',
                'admin_notes' => $snapshot->admin_notes,
            ]);
        }

        return back()->with('success', 'Catatan khusus mentor berhasil diperbarui.');
    }

    /**
     * Poin C: Eksekusi Pengumuman & Notifikasi WhatsApp ke Guru.
     */
    public function announce(Request $request, string $month): JsonResponse|RedirectResponse
    {
        $result = $this->ahpService->sendAnnouncementNotifications($month);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Cetak Lembar Resmi Surat Keputusan (SK) & Rekomendasi Bonus Siap Cetak A4.
     */
    public function printSk(Request $request, string $month): View
    {
        $evaluation = $this->ahpService->evaluateAndRank($month);
        $targetDate = Carbon::createFromFormat('Y-m', $month);

        // Generate SK Number formal
        $skNumber = sprintf('SK-%04d/YAH-LMS/GURU-TELADAN/%s', $targetDate->month, $targetDate->year);

        return view('admin.mentors.ahp-ranking.print-sk', compact(
            'evaluation',
            'month',
            'skNumber',
            'targetDate'
        ));
    }
}
