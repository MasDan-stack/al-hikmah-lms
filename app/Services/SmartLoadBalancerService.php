<?php

namespace App\Services;

use App\Models\Mentor;

class SmartLoadBalancerService
{
    /**
     * Menghitung rasio beban aktif terhadap kuota maksimal.
     */
    public function evaluateMentorCapacity(Mentor $mentor): array
    {
        $profile = $mentor->loadBalanceProfile;

        if (! $profile) {
            $maxStudents = $mentor->default_max_students_per_day * 5; // Default assumption for 5 days

            return [
                'current' => $mentor->students()->wherePivot('is_active', true)->count(),
                'max' => $maxStudents > 0 ? $maxStudents : 25,
                'ratio' => 0.0,
            ];
        }

        $current = $profile->current_active_students;
        $max = $profile->max_active_students;

        // Recalculate current just to be safe
        $actualCurrent = $mentor->students()->wherePivot('is_active', true)->count();
        if ($actualCurrent !== $current) {
            $profile->update(['current_active_students' => $actualCurrent]);
            $current = $actualCurrent;
        }

        $ratio = $max > 0 ? ($current / $max) * 100 : 100;

        return [
            'current' => $current,
            'max' => $max,
            'ratio' => round($ratio, 2),
        ];
    }

    /**
     * Menghitung skor indeks risiko kelelahan berdasarkan parameter
     */
    public function calculateBurnoutIndex(Mentor $mentor): float
    {
        $profile = $mentor->loadBalanceProfile;
        if (! $profile) {
            return 0.0;
        }

        $capacity = $this->evaluateMentorCapacity($mentor);
        $slotRatio = $capacity['ratio'];

        // Mock values for "Penurunan Rating 30 Hari" and "Jam Mengajar Berturut-turut"
        // Since we don't have full performance models loaded in this scope right now
        $ratingDropPercentage = 0; // e.g. dropped by 5% -> 5.0
        $consecutiveHoursScore = 0; // e.g. 5 hours straight -> 10.0

        $index = ($slotRatio * 0.50) + ($ratingDropPercentage * 0.30) + ($consecutiveHoursScore * 0.20);

        // Update profile
        $level = 'low';
        if ($index > 80) {
            $level = 'critical';
        } elseif ($index > 60) {
            $level = 'high';
        } elseif ($index > 40) {
            $level = 'medium';
        }

        $profile->update([
            'burnout_risk_score' => round($index, 2),
            'burnout_level' => $level,
        ]);

        return round($index, 2);
    }

    /**
     * Mengembalikan nilai true jika guru dilarang menerima santri baru sementara waktu.
     */
    public function isThrottledFromNewStudents(Mentor $mentor): bool
    {
        $profile = $mentor->loadBalanceProfile;
        if (! $profile) {
            return false;
        }

        if ($profile->is_throttled) {
            return true;
        }

        if ($profile->coaching_cooldown_until && $profile->coaching_cooldown_until->isFuture()) {
            return true;
        }

        $capacity = $this->evaluateMentorCapacity($mentor);
        if ($capacity['ratio'] >= 100) {
            return true; // Kapasitas penuh
        }

        return false;
    }

    /**
     * Menetapkan masa pembinaan dan mematikan rekomendasi otomatis.
     */
    public function applyCoachingCooldown(Mentor $mentor, int $days, string $reason): void
    {
        $profile = $mentor->loadBalanceProfile;

        if (! $profile) {
            $profile = $mentor->loadBalanceProfile()->create([
                'coaching_cooldown_until' => now()->addDays($days),
                'cooldown_reason' => $reason,
                'is_throttled' => true,
            ]);

            return;
        }

        $profile->update([
            'coaching_cooldown_until' => now()->addDays($days),
            'cooldown_reason' => $reason,
            'is_throttled' => true,
        ]);
    }
}
