<?php

namespace App\Services;

use App\Models\LeaderboardSnapshot;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LeaderboardService
{
    /**
     * Mengambil daftar peringkat leaderboard dengan caching 1 jam
     */
    public function getLeaderboard(string $category = 'overall', int $limit = 50): Collection
    {
        $cacheKey = "leaderboard_{$category}_{$limit}";

        return Cache::remember($cacheKey, 3600, function () use ($category, $limit) {
            return $this->calculateLeaderboard($category, $limit);
        });
    }

    /**
     * Menghitung peringkat leaderboard secara real-time dari database
     */
    public function calculateLeaderboard(string $category = 'overall', int $limit = 50): Collection
    {
        $query = Student::with(['user', 'juzProgress', 'earnedBadges']);

        switch ($category) {
            case 'anak':
                $query->where('age', '<=', 12);
                break;
            case 'dewasa':
                $query->where('age', '>', 12);
                break;
            case 'streak':
                $query->orderBy('current_streak', 'desc')->orderBy('total_points', 'desc');
                break;
            default: // overall
                $query->orderBy('total_points', 'desc')->orderBy('current_streak', 'desc');
                break;
        }

        if ($category !== 'streak') {
            $query->orderBy('total_points', 'desc');
        }

        $students = $query->take($limit)->get();
        $ranked = collect();
        $rank = 1;

        foreach ($students as $student) {
            $displayName = $student->getDisplayName();
            if ($student->privacy_leaderboard) {
                $displayName = 'Santri #'.$student->id;
            }

            $totalAyat = $student->juzProgress->sum('ayat_hafal');
            $totalMutqin = $student->juzProgress->where('status', 'mutqin')->count();

            $ranked->push((object) [
                'rank' => $rank,
                'student_id' => $student->id,
                'student_name' => $displayName,
                'raw_name' => $student->getDisplayName(),
                'avatar' => $student->user?->avatar ?? null,
                'age' => $student->age,
                'gender' => $student->gender,
                'total_points' => $student->total_points ?: 0,
                'current_streak' => $student->current_streak ?: 0,
                'longest_streak' => $student->longest_streak ?: 0,
                'total_ayat' => $totalAyat,
                'total_juz_mutqin' => $totalMutqin,
                'badge_count' => $student->earnedBadges->count(),
                'privacy_leaderboard' => (bool) ($student->privacy_leaderboard ?? false),
                'is_current_user' => auth()->check() && auth()->user()->id === $student->user_id,
            ]);

            $rank++;
        }

        return $ranked;
    }

    /**
     * Menghapus cache seluruh kategori leaderboard
     */
    public function invalidateCache(): void
    {
        $categories = ['overall', 'anak', 'dewasa', 'streak', 'per_juz'];
        foreach ($categories as $cat) {
            Cache::forget("leaderboard_{$cat}_50");
            Cache::forget("leaderboard_{$cat}_100");
            Cache::forget("leaderboard_{$cat}_10");
        }
    }

    /**
     * Mengambil snapshot peringkat untuk riwayat
     */
    public function snapshot(string $periodType = 'weekly'): void
    {
        $categories = ['overall', 'anak', 'dewasa', 'streak'];
        $start = now()->startOfWeek()->toDateString();
        $end = now()->endOfWeek()->toDateString();

        foreach ($categories as $cat) {
            $data = $this->calculateLeaderboard($cat, 100);

            foreach ($data as $entry) {
                LeaderboardSnapshot::create([
                    'period_type' => $periodType,
                    'period_start' => $start,
                    'period_end' => $end,
                    'category' => $cat,
                    'student_id' => $entry->student_id,
                    'rank_position' => $entry->rank,
                    'total_points' => $entry->total_points,
                    'total_ayat' => $entry->total_ayat,
                    'total_juz_mutqin' => $entry->total_juz_mutqin,
                    'current_streak' => $entry->current_streak,
                    'trend' => 'stable',
                ]);
            }
        }
    }
}
