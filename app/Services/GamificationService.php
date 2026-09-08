<?php

namespace App\Services;

use App\Models\GamificationPoint;
use App\Models\HifzTarget;
use App\Models\Progress;
use App\Models\Student;

class GamificationService
{
    public function __construct(
        protected StreakTrackerService $streakTrackerService,
        protected HifzProgressService $hifzProgressService,
        protected BadgeEvaluatorService $badgeEvaluatorService,
        protected LeaderboardService $leaderboardService
    ) {}

    /**
     * Berikan poin gamifikasi ke santri dan catat transaksinya
     */
    public function awardPoints(
        Student $student,
        int $points,
        string $activityType,
        $sourceIdOrDescription = null,
        ?string $description = null
    ): GamificationPoint {
        $sourceId = is_numeric($sourceIdOrDescription) ? (int) $sourceIdOrDescription : null;
        $desc = is_string($sourceIdOrDescription) ? $sourceIdOrDescription : $description;

        $log = GamificationPoint::create([
            'student_id' => $student->id,
            'points' => $points,
            'activity_type' => $activityType,
            'source_type' => $activityType,
            'source_id' => $sourceId,
            'description' => $desc,
        ]);

        $student->increment('total_points', $points);

        return $log;
    }

    public function handleNewProgress(Progress $progress): void
    {
        $this->processProgress($progress);
    }

    /**
     * Memproses progres hafalan baru dari mutaba'ah untuk memicu seluruh sistem gamifikasi
     */
    public function processProgress(Progress $progress): void
    {
        $student = $progress->student;
        if (! $student) {
            return;
        }

        // 1. Hitung jumlah ayat terhafal
        $ayatCount = 1;
        if ($progress->ayat_start && $progress->ayat_end) {
            $ayatCount = max(1, abs($progress->ayat_end - $progress->ayat_start) + 1);
        }

        // 2. Beri poin standar: 10 poin per ayat
        $pointsEarned = $ayatCount * 10;

        // Bonus jika nilai adab & tajwid sempurna
        if ($progress->nilai_tajwid >= 90) {
            $pointsEarned += 20;
        }
        if ($progress->nilai_adab >= 90) {
            $pointsEarned += 20;
        }

        $this->awardPoints(
            $student,
            $pointsEarned,
            'ayat_hafal',
            $progress->id,
            "Setoran {$progress->kategori}: {$progress->surah_start} ({$ayatCount} ayat)"
        );

        // 3. Update Streak harian
        $this->streakTrackerService->track($student);

        // 4. Update Progres Juz
        $targetJuz = $progress->juz ?: ($progress->juz_number ?: 30);
        $this->hifzProgressService->calculateJuzProgress($student, $targetJuz);

        // 5. Cek target hari ini yang cocok dan tandai selesai
        $todayTarget = HifzTarget::where('student_id', $student->id)
            ->where('target_date', now()->toDateString())
            ->where('status', 'pending')
            ->first();

        if ($todayTarget) {
            $this->completeTarget($todayTarget);
        }

        // 6. Evaluasi perolehan badge
        $this->badgeEvaluatorService->evaluate($student, $progress);

        // 7. Invalidasi cache leaderboard
        $this->leaderboardService->invalidateCache();
    }

    /**
     * Tandai target hafalan sebagai selesai dan berikan reward poin
     */
    public function completeTarget(HifzTarget $target): void
    {
        if ($target->status === 'completed') {
            return;
        }

        $target->status = 'completed';
        $target->completed_at = now();
        $target->save();

        if ($target->student) {
            $this->awardPoints(
                $target->student,
                50,
                'target_completed',
                $target->id,
                'Menyelesaikan target harian: '.($target->surah_name ?: 'Target Harian')
            );

            $this->badgeEvaluatorService->evaluate($target->student);
        }
    }
}
