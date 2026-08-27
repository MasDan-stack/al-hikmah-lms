<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Models\Badge;
use App\Models\GamificationPoint;
use App\Models\HifzTarget;
use App\Models\JuzProgress;
use App\Models\Progress;
use App\Models\SessionConfirmation;
use App\Models\Student;
use App\Models\StudentBadge;

class BadgeEvaluatorService
{
    /**
     * Evaluasi seluruh kriteria badge untuk santri dan berikan badge baru jika memenuhi syarat
     *
     * @return array<Badge> Daftar badge baru yang berhasil diraih
     */
    public function evaluate(Student $student, ?Progress $latestProgress = null): array
    {
        $earnedBadgeIds = StudentBadge::where('student_id', $student->id)->pluck('badge_id')->toArray();
        $availableBadges = Badge::where('is_active', true)->whereNotIn('id', $earnedBadgeIds)->get();

        $newlyEarned = [];

        // Hitung agregat data santri
        $totalProgressCount = Progress::where('student_id', $student->id)->count();
        $totalAyatHafal = JuzProgress::where('student_id', $student->id)->sum('ayat_hafal');
        $totalMutqinCount = JuzProgress::where('student_id', $student->id)->where('status', 'mutqin')->count();
        $currentStreak = $student->current_streak ?: 0;
        $completedTargetsCount = HifzTarget::where('student_id', $student->id)->where('status', 'completed')->count();
        $adabMumtazCount = Progress::where('student_id', $student->id)->where('nilai_adab', '>=', 90)->count();
        $morningSetoranCount = Progress::where('student_id', $student->id)
            ->whereTime('created_at', '<=', '11:00:00')
            ->count();
        $confirmedSessionsCount = SessionConfirmation::whereHas('session', fn ($q) => $q->where('student_id', $student->id))
            ->where('parent_confirmed', true)
            ->count();

        foreach ($availableBadges as $badge) {
            $isEligible = false;
            $criteria = $badge->criteria_json ?? [];
            $type = $criteria['type'] ?? $badge->code;

            switch ($badge->code) {
                case 'B01': // Penyemai Qur'an
                    $isEligible = $totalProgressCount >= 1;
                    break;
                case 'B02': // Pembaca Setia (100 ayat)
                    $isEligible = $totalAyatHafal >= 100;
                    break;
                case 'B03': // Bintang Juz (1 Juz Mutqin)
                    $isEligible = $totalMutqinCount >= 1;
                    break;
                case 'B04': // Hilal Tahfidz (5 Juz Mutqin)
                    $isEligible = $totalMutqinCount >= 5;
                    break;
                case 'B05': // Matahari Qur'an (10 Juz Mutqin)
                    $isEligible = $totalMutqinCount >= 10;
                    break;
                case 'B06': // Hafidz/ah Qur'an (30 Juz Mutqin)
                    $isEligible = $totalMutqinCount >= 30;
                    break;
                case 'B07': // Istiqomah 7
                    $isEligible = $currentStreak >= 7;
                    break;
                case 'B08': // Istiqomah 30
                    $isEligible = $currentStreak >= 30;
                    break;
                case 'B09': // Istiqomah 100
                    $isEligible = $currentStreak >= 100;
                    break;
                case 'B10': // Pemanah Tepat (7 target selesai)
                    $isEligible = $completedTargetsCount >= 7;
                    break;
                case 'B12': // Adab Mulia (10x Mumtaz)
                    $isEligible = $adabMumtazCount >= 10;
                    break;
                case 'B13': // Tilawah Pagi (30x pagi)
                    $isEligible = $morningSetoranCount >= 30;
                    break;
                case 'B14': // Kolaborasi Keluarga (5x konfirmasi wali)
                    $isEligible = $confirmedSessionsCount >= 5;
                    break;
                case 'B15': // Mutqin Master (3 Juz Mutqin)
                    $isEligible = $totalMutqinCount >= 3;
                    break;
            }

            if ($isEligible) {
                $this->awardBadge($student, $badge);
                $newlyEarned[] = $badge;
            }
        }

        return $newlyEarned;
    }

    /**
     * Berikan badge kepada santri beserta bonus poin dan notifikasi
     */
    public function awardBadge(Student $student, Badge $badge, array $triggerData = []): StudentBadge
    {
        $studentBadge = StudentBadge::firstOrCreate(
            [
                'student_id' => $student->id,
                'badge_id' => $badge->id,
            ],
            [
                'earned_at' => now(),
                'trigger_data' => $triggerData ?: ['awarded_at' => now()->toIso8601String()],
                'announced_to_parent' => false,
            ]
        );

        // Tambah poin bonus badge
        if ($badge->points_reward > 0) {
            GamificationPoint::create([
                'student_id' => $student->id,
                'points' => $badge->points_reward,
                'source_type' => 'badge',
                'source_id' => $badge->id,
                'description' => "Bonus Lencana: {$badge->name}",
            ]);

            $student->increment('total_points', $badge->points_reward);
        }

        // Kirim notifikasi ucapan selamat ke wali santri
        $parentUserId = $student->parent?->user_id;
        if ($parentUserId) {
            NotificationService::send(
                $parentUserId,
                "Lencana Baru Diraih! 🎖️ {$badge->name}",
                "Barakallah! Ananda {$student->getDisplayName()} telah berhasil meraih lencana *{$badge->name}* ({$badge->description}).",
                NotificationType::SUCCESS,
                route('parent.dashboard'),
                'badge',
                true
            );
            $studentBadge->update(['announced_to_parent' => true]);
        }

        return $studentBadge;
    }

    public function evaluateOnProgress(Student $student, ?Progress $progress = null): array
    {
        return $this->evaluate($student, $progress);
    }

    public function evaluateOnStreak(Student $student, int $streak): array
    {
        return $this->evaluate($student);
    }

    public function evaluateOnJuzCompletion(Student $student, int $juz, bool $isMutqin = false): array
    {
        return $this->evaluate($student);
    }
}
