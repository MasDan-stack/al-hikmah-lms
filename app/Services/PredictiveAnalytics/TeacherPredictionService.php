<?php

namespace App\Services\PredictiveAnalytics;

use App\Models\Mentor;
use App\Models\MentorPerformanceSnapshot;
use Illuminate\Support\Collection;

class TeacherPredictionService
{
    public const SLOPE_CRITICAL_THRESHOLD = -3.5;

    public const SCORE_WARNING_THRESHOLD = 70.0;

    public function snapshotDailyPredictions(): void
    {
        $today = now()->toDateString();

        Mentor::where('status', 'active')
            ->chunk(50, function ($mentors) use ($today) {
                foreach ($mentors as $mentor) {
                    $prediction = $this->predictMentorPerformance($mentor);

                    $mentor->update([
                        'last_coaching_alert_at' => $today,
                        'coaching_needed' => $prediction['coaching_needed'],
                        'coaching_urgency' => $prediction['coaching_urgency'],
                    ]);
                }
            });
    }

    public function predictMentorPerformance(Mentor $mentor): array
    {
        // 1. Ambil 6 snapshot bulanan terakhir mentor
        $snapshots = MentorPerformanceSnapshot::where('mentor_id', $mentor->id)
            ->where('period_type', 'monthly')
            ->orderBy('period_start', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $k = $snapshots->count();

        if ($k >= 2) {
            $sumT = 0;
            $sumS = 0;
            $sumTS = 0;
            $sumT2 = 0;

            for ($t = 0; $t < $k; $t++) {
                $score = (float) ($snapshots[$t]->composite_score ?? 80.0);
                $sumT += $t;
                $sumS += $score;
                $sumTS += ($t * $score);
                $sumT2 += ($t * $t);
            }

            $denominator = ($k * $sumT2) - ($sumT * $sumT);
            $slope = $denominator != 0 ? (($k * $sumTS) - ($sumT * $sumS)) / $denominator : 0.0;

            $currentScore = (float) ($snapshots->last()->composite_score ?? 80.0);
            $predictedScore = max(0.0, min(100.0, $currentScore + ($slope * 3)));
        } else {
            // Fallback jika riwayat snapshot belum cukup
            $rating = (float) ($mentor->rating ?? 4.5);
            $currentScore = $rating * 20.0; // Konversi ke skala 100
            $slope = 0.0;
            $predictedScore = $currentScore;
        }

        $coachingNeeded = false;
        $urgency = 'low';

        if ($slope < self::SLOPE_CRITICAL_THRESHOLD || $predictedScore < 60.0) {
            $coachingNeeded = true;
            $urgency = 'critical';
        } elseif ($predictedScore < self::SCORE_WARNING_THRESHOLD || $slope < -2.0) {
            $coachingNeeded = true;
            $urgency = 'high';
        } elseif ($predictedScore < 80.0 || (float) ($mentor->rating ?? 5.0) < 4.5) {
            $coachingNeeded = true;
            $urgency = 'medium';
        }

        return [
            'mentor_id' => $mentor->id,
            'current_score' => round($currentScore, 1),
            'slope' => round($slope, 2),
            'predicted_score' => round($predictedScore, 1),
            'coaching_needed' => $coachingNeeded,
            'coaching_urgency' => $urgency,
        ];
    }

    public function getMentorsNeedingCoaching(): Collection
    {
        return Mentor::with(['user', 'students'])
            ->where('status', 'active')
            ->where('coaching_needed', true)
            ->orderByRaw("FIELD(coaching_urgency, 'critical', 'high', 'medium', 'low')")
            ->get();
    }
}
