<?php

namespace App\Services;

use App\Models\Student;
use Carbon\Carbon;

class StreakTrackerService
{
    /**
     * Memperbarui streak setoran harian santri
     */
    public function track(Student $student, ?Carbon $date = null): int
    {
        $today = $date ? $date->copy()->startOfDay() : now()->startOfDay();
        $lastDate = $student->last_setoran_date ? Carbon::parse($student->last_setoran_date)->startOfDay() : null;

        if ($lastDate && $lastDate->equalTo($today)) {
            // Sudah tercatat hari ini, streak tidak berubah
            return $student->current_streak;
        }

        if ($lastDate && $lastDate->equalTo($today->copy()->subDay())) {
            // Berurutan dari kemarin
            $newStreak = ($student->current_streak ?: 0) + 1;
        } else {
            // Reset atau mulai baru
            $newStreak = 1;
        }

        $student->current_streak = $newStreak;
        $student->longest_streak = max($student->longest_streak ?: 0, $newStreak);
        $student->last_setoran_date = $today->toDateString();
        $student->save();

        return $newStreak;
    }

    public function recordActivity(Student $student, ?Carbon $date = null): int
    {
        return $this->track($student, $date);
    }
}
