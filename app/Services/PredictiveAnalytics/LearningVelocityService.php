<?php

namespace App\Services\PredictiveAnalytics;

use App\Models\Progress;
use App\Models\Student;
use App\Models\StudentLearningVelocity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LearningVelocityService
{
    public const TARGET_VELOCITY = 5.0; // Target default: 5 ayat/hari aktif

    public function snapshotDailyVelocities(): void
    {
        $today = now()->toDateString();

        Student::with([
            'progress' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            },
        ])
            ->whereHas('enrollments', function ($q) {
                $q->where('status', 'active');
            })
            ->chunk(100, function ($students) use ($today) {
                foreach ($students as $student) {
                    $velocityData = $this->calculateVelocity($student);

                    StudentLearningVelocity::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'calculation_date' => $today,
                        ],
                        $velocityData
                    );
                }
            });

        Cache::forget('learning_velocities_all');
        Cache::forget('predictive_dashboard_summary');
    }

    public function calculateAllVelocities(?int $programId = null): Collection
    {
        $cacheKey = $programId ? "learning_velocities_prog_{$programId}" : 'learning_velocities_all';

        return Cache::remember($cacheKey, 3600, function () use ($programId) {
            $students = Student::with([
                'enrollments.program',
                'progress' => function ($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                },
            ])
                ->whereHas('enrollments', function ($q) use ($programId) {
                    $q->where('status', 'active');
                    if ($programId) {
                        $q->where('program_id', $programId);
                    }
                })
                ->get();

            return $students->map(function ($student) {
                $velocity = $this->calculateVelocity($student);

                return array_merge($velocity, ['student' => $student]);
            })->sortByDesc('velocity_ayat_per_day')->values();
        });
    }

    public function calculateVelocity(Student $student): array
    {
        $rawProgresses = $student->relationLoaded('progress')
            ? $student->progress
            : $student->progress()->where('created_at', '>=', now()->subDays(30))->get();

        $progresses = $rawProgresses->filter(function ($prog) {
            $kat = strtolower($prog->kategori ?? '');

            return in_array($kat, ['ziyadah', 'tahfidz', 'tahsin', 'hifz', 'setoran', 'hafalan']) || empty($kat);
        });

        $totalAyat = 0;
        $uniqueDays = [];

        foreach ($progresses as $prog) {
            if ($prog->surah_start == $prog->surah_end && ! is_null($prog->ayat_end) && ! is_null($prog->ayat_start) && $prog->ayat_end >= $prog->ayat_start) {
                $totalAyat += ($prog->ayat_end - $prog->ayat_start + 1);
            } elseif (! is_null($prog->ayat_end) && ! is_null($prog->ayat_start) && $prog->ayat_end >= $prog->ayat_start) {
                $totalAyat += ($prog->ayat_end - $prog->ayat_start + 1);
            } else {
                $totalAyat += 5;
            }

            if ($prog->created_at) {
                $uniqueDays[$prog->created_at->format('Y-m-d')] = true;
            }
        }

        $daysActive = count($uniqueDays);
        $velocity = $daysActive > 0 ? ($totalAyat / $daysActive) : 0;
        $targetAchievement = self::TARGET_VELOCITY > 0 ? ($velocity / self::TARGET_VELOCITY) * 100 : 0;

        $status = 'no_data';
        if ($daysActive > 0) {
            if ($targetAchievement >= 100) {
                $status = 'excellent';
            } elseif ($targetAchievement >= 80) {
                $status = 'good';
            } elseif ($targetAchievement >= 50) {
                $status = 'moderate';
            } else {
                $status = 'slow';
            }
        }

        $totalAyatKhatam = 6236;
        $allProgressCount = Progress::where('student_id', $student->id)->count();
        $ayatDoneSoFar = $allProgressCount * 7;

        $ayatRemaining = max(0, $totalAyatKhatam - $ayatDoneSoFar);

        $estimatedDaysRemaining = null;
        $projectedCompletionDate = null;

        if ($velocity > 0) {
            $effectiveVelocity = max(0.5, $velocity);
            $estimatedDaysRemaining = (int) ceil($ayatRemaining / $effectiveVelocity);
            $projectedCompletionDate = now()->addDays($estimatedDaysRemaining)->toDateString();
        }

        return [
            'velocity_ayat_per_day' => round($velocity, 2),
            'target_velocity' => self::TARGET_VELOCITY,
            'velocity_status' => $status,
            'target_achievement_percent' => round(min(200, $targetAchievement), 2),
            'days_active' => $daysActive,
            'total_ayat_30d' => $totalAyat,
            'estimated_days_remaining' => $estimatedDaysRemaining,
            'projected_completion_date' => $projectedCompletionDate,
            'percentile_rank' => 50.0,
        ];
    }
}
