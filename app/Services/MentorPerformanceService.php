<?php

namespace App\Services;

use App\Models\FinancialAuditLog;
use App\Models\HifzTarget;
use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\MentorPerformanceSnapshot;
use App\Models\Progress;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MentorPerformanceService
{
    public const BAYESIAN_C = 5;      // Minimum sample size threshold

    public const BAYESIAN_M = 4.5;    // Baseline institutional average rating

    /**
     * Hitung metrik performa komposit mentor dengan Dynamic Weighting, Bayesian Smoothing, dan Handicap Multiplier.
     */
    public function getMentorMetrics(Mentor $mentor, Carbon $startDate, Carbon $endDate): array
    {
        // 1. Retention Rate & Dropout Rate
        $totalStudents = $mentor->students()->count();
        $activeStudents = $mentor->students()->wherePivot('is_active', true)->count();
        $retentionRate = $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 2) : 100.0;
        $dropoutRate = max(0.0, round(100.0 - $retentionRate, 2));

        // 2. Kualitas Akademik (Tajwid & Adab)
        $progressQuery = Progress::where('mentor_id', $mentor->id)
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

        $avgTajwidRaw = $progressQuery->avg('nilai_tajwid');
        $avgAdabRaw = $progressQuery->avg('nilai_adab');

        $avgTajwid = (float) ($avgTajwidRaw !== null ? $avgTajwidRaw : 85.0);
        $avgAdab = (float) ($avgAdabRaw !== null ? $avgAdabRaw : 90.0);

        // 3. Sesi & Kepatuhan Kehadiran
        $sessionsQuery = Session::where('mentor_id', $mentor->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

        $totalSessions = (int) $sessionsQuery->count();
        $completedSessions = (int) (clone $sessionsQuery)->where('status', 'completed')->count();
        $attendanceRate = $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 100.0;

        // 4. Kepuasan Wali Santri & Bayesian Rating Average Smoothing
        $feedbackQuery = MentorFeedback::where('mentor_id', $mentor->id)
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

        $totalFeedback = (int) $feedbackQuery->count();
        $rawAvgRating = $totalFeedback > 0 ? (float) $feedbackQuery->avg('overall_rating') : (float) ($mentor->rating ?? 5.0);
        $rawAvgRating = round($rawAvgRating, 2);

        // Bayesian Formula: (C * m + sum(R)) / (C + n)
        $sumRatings = (float) ($feedbackQuery->sum('overall_rating') ?? ($totalFeedback * $rawAvgRating));
        if ($totalFeedback === 0) {
            // Jika belum ada feedback baru di periode ini, gunakan rating mentor saat ini sebagai fallback
            $sumRatings = (float) (($mentor->rating ?? 4.8) * 1);
            $effectiveReviews = 1;
        } else {
            $effectiveReviews = $totalFeedback;
        }

        $bayesianRating = round(((self::BAYESIAN_C * self::BAYESIAN_M) + $sumRatings) / (self::BAYESIAN_C + $effectiveReviews), 2);
        $satisfactionScore = round(($bayesianRating / 5.0) * 100, 2);

        // 5. Pencapaian Target Kurikulum
        $mentorUserId = $mentor->user_id ?? $mentor->id;
        $targetsQuery = HifzTarget::where('mentor_id', $mentorUserId)
            ->whereBetween('target_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $totalTargets = (int) $targetsQuery->count();
        $completedTargets = (int) (clone $targetsQuery)->where('status', 'completed')->count();
        $targetRate = $totalTargets > 0 ? round(($completedTargets / $totalTargets) * 100, 2) : 85.0;

        // 6. Program-Based Dynamic Weighting
        $specialization = strtolower((string) ($mentor->specialization ?? 'tahsin'));
        $isLanguageOrBasic = str_contains($specialization, 'arab') || str_contains($specialization, 'nol') || str_contains($specialization, 'iqra');

        if ($isLanguageOrBasic) {
            // Bobot Tajwid 0%, Target Kurikulum 25%, Adab 15%, Retensi 25%, Sesi 20%, Kepuasan 15%
            $academicQualityScore = round($avgAdab, 2);
            $compositeScore = ($retentionRate * 0.25) +
                              ($academicQualityScore * 0.15) +
                              ($targetRate * 0.25) +
                              ($attendanceRate * 0.20) +
                              ($satisfactionScore * 0.15);
        } else {
            // Program Standar: Tajwid 15%, Adab 10%, Retensi 25%, Sesi 20%, Kepuasan 20%, Target 10%
            $academicQualityScore = round(($avgTajwid * 0.60) + ($avgAdab * 0.40), 2);
            $compositeScore = ($retentionRate * 0.25) +
                              ($academicQualityScore * 0.25) +
                              ($attendanceRate * 0.20) +
                              ($satisfactionScore * 0.20) +
                              ($targetRate * 0.10);
        }

        // 7. Student Handicap Multiplier (Bonus Kesabaran Membina Santri Rendah/Butuh Bimbingan)
        $handicapStudentsCount = Progress::where('mentor_id', $mentor->id)
            ->where('nilai_tajwid', '<', 65)
            ->distinct('student_id')
            ->count('student_id');

        $handicapBonus = min(6.0, round($handicapStudentsCount * 1.5, 2));
        $finalCompositeScore = min(100.0, max(0.0, round($compositeScore + $handicapBonus, 2)));

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'retention_rate' => $retentionRate,
            'dropout_rate' => $dropoutRate,
            'avg_tajwid_score' => round($avgTajwid, 1),
            'avg_adab_score' => round($avgAdab, 1),
            'academic_quality_score' => $academicQualityScore,
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'attendance_rate' => $attendanceRate,
            'avg_rating_raw' => $rawAvgRating,
            'avg_rating_bayesian' => $bayesianRating,
            'total_feedback' => $totalFeedback,
            'target_achievement_rate' => $targetRate,
            'handicap_bonus_points' => $handicapBonus,
            'composite_score' => $finalCompositeScore,
        ];
    }

    /**
     * Hitung & Simpan Snapshot Kinerja Satu Mentor untuk Periode Bulanan.
     */
    public function snapshotMentor(Mentor $mentor, ?string $periodMonth = null, bool $forceRecalculate = false): MentorPerformanceSnapshot
    {
        $targetDate = $periodMonth ? Carbon::createFromFormat('Y-m', $periodMonth) : now();
        $startDate = $targetDate->copy()->startOfMonth();
        $endDate = $targetDate->copy()->endOfMonth();

        $metrics = $this->getMentorMetrics($mentor, $startDate, $endDate);

        $snapshot = MentorPerformanceSnapshot::updateOrCreate(
            [
                'mentor_id' => $mentor->id,
                'period_type' => 'monthly',
                'period_start' => $startDate->toDateString(),
                'period_end' => $endDate->toDateString(),
            ],
            [
                'total_students' => $metrics['total_students'],
                'active_students' => $metrics['active_students'],
                'retention_rate' => $metrics['retention_rate'],
                'dropout_rate' => $metrics['dropout_rate'],
                'avg_tajwid_score' => $metrics['avg_tajwid_score'],
                'avg_adab_score' => $metrics['avg_adab_score'],
                'academic_quality_score' => $metrics['academic_quality_score'],
                'total_sessions' => $metrics['total_sessions'],
                'completed_sessions' => $metrics['completed_sessions'],
                'attendance_rate' => $metrics['attendance_rate'],
                'avg_rating_raw' => $metrics['avg_rating_raw'],
                'avg_rating_bayesian' => $metrics['avg_rating_bayesian'],
                'total_feedback_count' => $metrics['total_feedback'],
                'target_achievement_rate' => $metrics['target_achievement_rate'],
                'handicap_bonus_points' => $metrics['handicap_bonus_points'],
                'composite_score' => $metrics['composite_score'],
                'is_locked' => true,
                'calculated_at' => now(),
            ]
        );

        // Update cache tagar/key jika ada
        Cache::forget("mentor_composite_score_{$mentor->id}_{$startDate->format('Y-m')}");

        return $snapshot;
    }

    /**
     * Hitung & Snapshot Seluruh Mentor Aktif.
     */
    public function snapshotAllMentors(?string $periodMonth = null): int
    {
        $mentors = Mentor::where('is_active', true)->get();
        $count = 0;

        foreach ($mentors as $mentor) {
            $this->snapshotMentor($mentor, $periodMonth);
            $count++;
        }

        // Hitung ulang rank position untuk semua snapshot di periode ini
        $targetDate = $periodMonth ? Carbon::createFromFormat('Y-m', $periodMonth) : now();
        $startDate = $targetDate->copy()->startOfMonth()->toDateString();

        $snapshots = MentorPerformanceSnapshot::where('period_start', $startDate)
            ->where('period_type', 'monthly')
            ->orderByDesc('composite_score')
            ->get();

        $rank = 1;
        foreach ($snapshots as $snap) {
            $snap->update(['rank_position' => $rank++]);
        }

        // Clear dashboard cache
        $monthKey = $targetDate->format('Y-m');
        Cache::forget("mentor_performance_dashboard_summary_{$monthKey}");
        Cache::forget("top_mentors_leaderboard_{$monthKey}");

        return $count;
    }

    /**
     * Ambil Ringkasan Dashboard Eksekutif Admin dengan Cache 300 detik.
     */
    public function getDashboardSummary(?string $periodMonth = null): array
    {
        $month = $periodMonth ?? now()->format('Y-m');
        $cacheKey = "mentor_performance_dashboard_summary_{$month}";

        return Cache::remember($cacheKey, 300, function () use ($month) {
            $targetDate = Carbon::createFromFormat('Y-m', $month);
            $startDate = $targetDate->copy()->startOfMonth()->toDateString();

            $snapshots = MentorPerformanceSnapshot::with('mentor.user')
                ->where('period_start', $startDate)
                ->where('period_type', 'monthly')
                ->get();

            if ($snapshots->isEmpty()) {
                // Generate jika belum ada
                $this->snapshotAllMentors($month);
                $snapshots = MentorPerformanceSnapshot::with('mentor.user')
                    ->where('period_start', $startDate)
                    ->where('period_type', 'monthly')
                    ->get();
            }

            $totalMentors = Mentor::where('is_active', true)->count();
            $avgRating = round((float) ($snapshots->avg('avg_rating_bayesian') ?? 4.8), 2);
            $avgRetention = round((float) ($snapshots->avg('retention_rate') ?? 95.0), 2);
            $avgComposite = round((float) ($snapshots->avg('composite_score') ?? 90.0), 2);
            $totalActiveStudents = (int) $snapshots->sum('active_students');

            return [
                'period' => $month,
                'total_mentors' => $totalMentors,
                'avg_rating' => $avgRating,
                'avg_retention' => $avgRetention,
                'avg_composite' => $avgComposite,
                'total_active_students' => $totalActiveStudents,
                'snapshots_count' => $snapshots->count(),
            ];
        });
    }

    /**
     * Ambil Top 10 Leaderboard Guru dengan Cache 1800 detik.
     */
    public function getTopLeaderboard(?string $periodMonth = null, int $limit = 10): Collection
    {
        $month = $periodMonth ?? now()->format('Y-m');
        $cacheKey = "top_mentors_leaderboard_{$month}_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($month, $limit) {
            $targetDate = Carbon::createFromFormat('Y-m', $month);
            $startDate = $targetDate->copy()->startOfMonth()->toDateString();

            return MentorPerformanceSnapshot::with('mentor.user')
                ->where('period_start', $startDate)
                ->where('period_type', 'monthly')
                ->orderByDesc('composite_score')
                ->take($limit)
                ->get();
        });
    }

    /**
     * Hitung Ulang Snapshot dengan Catatan Audit Trail Finansial.
     */
    public function recalculateWithAudit(int $snapshotId, int $adminUserId, string $reason): MentorPerformanceSnapshot
    {
        $snapshot = MentorPerformanceSnapshot::with('mentor')->findOrFail($snapshotId);
        $oldScore = $snapshot->composite_score;

        $startDate = Carbon::parse($snapshot->period_start);
        $endDate = Carbon::parse($snapshot->period_end);

        $metrics = $this->getMentorMetrics($snapshot->mentor, $startDate, $endDate);

        $snapshot->update([
            'total_students' => $metrics['total_students'],
            'active_students' => $metrics['active_students'],
            'retention_rate' => $metrics['retention_rate'],
            'dropout_rate' => $metrics['dropout_rate'],
            'avg_tajwid_score' => $metrics['avg_tajwid_score'],
            'avg_adab_score' => $metrics['avg_adab_score'],
            'academic_quality_score' => $metrics['academic_quality_score'],
            'total_sessions' => $metrics['total_sessions'],
            'completed_sessions' => $metrics['completed_sessions'],
            'attendance_rate' => $metrics['attendance_rate'],
            'avg_rating_raw' => $metrics['avg_rating_raw'],
            'avg_rating_bayesian' => $metrics['avg_rating_bayesian'],
            'total_feedback_count' => $metrics['total_feedback'],
            'target_achievement_rate' => $metrics['target_achievement_rate'],
            'handicap_bonus_points' => $metrics['handicap_bonus_points'],
            'composite_score' => $metrics['composite_score'],
            'calculated_at' => now(),
        ]);

        // Catat ke FinancialAuditLog
        FinancialAuditLog::create([
            'user_id' => $adminUserId,
            'action' => 'recalculate_mentor_performance',
            'entity_type' => 'mentor_performance_snapshot',
            'entity_id' => $snapshot->id,
            'old_values' => ['composite_score' => $oldScore],
            'new_values' => [
                'composite_score' => $snapshot->composite_score,
                'reason' => $reason,
                'period' => $snapshot->period_start.' to '.$snapshot->period_end,
            ],
            'created_at' => now(),
        ]);

        // Clear Caches
        $monthKey = $startDate->format('Y-m');
        Cache::forget("mentor_performance_dashboard_summary_{$monthKey}");
        Cache::forget("top_mentors_leaderboard_{$monthKey}_10");
        Cache::forget("mentor_composite_score_{$snapshot->mentor_id}_{$monthKey}");

        return $snapshot;
    }

    /**
     * Hitung Bayesian Rating Smoothing untuk seorang mentor.
     */
    public function computeBayesianRating(int $mentorId, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $feedbackQuery = MentorFeedback::where('mentor_id', $mentorId);
        if ($startDate && $endDate) {
            $feedbackQuery->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
        }

        $totalFeedback = (int) $feedbackQuery->count();
        if ($totalFeedback === 0) {
            return (float) self::BAYESIAN_M;
        }

        $sumRatings = (float) $feedbackQuery->sum('overall_rating');

        return round(((self::BAYESIAN_C * self::BAYESIAN_M) + $sumRatings) / (self::BAYESIAN_C + $totalFeedback), 2);
    }

    /**
     * Dapatkan matriks bobot dinamis berdasarkan spesialisasi program.
     */
    public function getDynamicWeights(string $specialization): array
    {
        $spec = strtolower($specialization);
        $isLanguageOrBasic = str_contains($spec, 'arab') || str_contains($spec, 'nol') || str_contains($spec, 'iqra');

        if ($isLanguageOrBasic) {
            return [
                'retention' => 0.25,
                'tajwid' => 0.0,
                'adab' => 0.15,
                'curriculum_milestones' => 0.25,
                'attendance' => 0.20,
                'satisfaction' => 0.15,
            ];
        }

        return [
            'retention' => 0.25,
            'tajwid' => 0.15,
            'adab' => 0.10,
            'curriculum_milestones' => 0.10,
            'attendance' => 0.20,
            'satisfaction' => 0.20,
        ];
    }

    /**
     * Hitung bonus handicap kesabaran santri.
     */
    public function calculateHandicapBonus(int $mentorId): float
    {
        $handicapStudentsCount = Progress::where('mentor_id', $mentorId)
            ->where('nilai_tajwid', '<', 65)
            ->distinct('student_id')
            ->count('student_id');

        if ($handicapStudentsCount === 0) {
            // Cek santri usia balita/anak-anak < 6 tahun yang dibina
            $mentor = Mentor::find($mentorId);
            $youngCount = $mentor ? $mentor->students()->where('age', '<=', 6)->count() : 0;

            return min(6.0, round($youngCount * 1.5, 2));
        }

        return min(6.0, round($handicapStudentsCount * 1.5, 2));
    }

    /**
     * Hitung dan simpan snapshot berdasarkan ID mentor.
     */
    public function calculateAndSaveSnapshot(int $mentorId, ?string $periodMonth = null): MentorPerformanceSnapshot
    {
        $mentor = Mentor::findOrFail($mentorId);

        return $this->snapshotMentor($mentor, $periodMonth);
    }
}
