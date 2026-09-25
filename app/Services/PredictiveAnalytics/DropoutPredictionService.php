<?php

namespace App\Services\PredictiveAnalytics;

use App\Models\Payment;
use App\Models\Progress;
use App\Models\SessionConfirmation;
use App\Models\Student;
use App\Models\StudentDropoutPrediction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DropoutPredictionService
{
    public const WEIGHT_ATTENDANCE = 0.35;

    public const WEIGHT_PAYMENT = 0.30;

    public const WEIGHT_PROGRESS = 0.20;

    public const WEIGHT_ENGAGEMENT = 0.15;

    public function __construct(
        protected PrescriptiveInsightService $insightService
    ) {}

    public function snapshotDailyPredictions(): void
    {
        $today = now()->toDateString();

        Student::with([
            'parent.user',
            'enrollments.program',
            'progress' => function ($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            },
            'payments' => function ($q) {
                $q->where('status', 'paid')->where('created_at', '>=', now()->subDays(90));
            },
        ])
            ->whereHas('enrollments', function ($q) {
                $q->where('status', 'active');
            })
            ->chunk(100, function ($students) use ($today) {
                $predictionsData = [];

                foreach ($students as $student) {
                    $riskData = $this->calculateRiskScore($student);

                    StudentDropoutPrediction::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'prediction_date' => $today,
                        ],
                        [
                            'risk_score' => $riskData['risk_score'],
                            'risk_level' => $riskData['risk_level'],
                            'attendance_score' => $riskData['attendance_score'],
                            'payment_score' => $riskData['payment_score'],
                            'progress_score' => $riskData['progress_score'],
                            'engagement_score' => $riskData['engagement_score'],
                            'risk_factors' => $riskData['risk_factors'],
                            'recommendations' => $riskData['recommendations'],
                        ]
                    );

                    // Update the student table with latest info
                    $student->update([
                        'last_dropout_prediction_at' => $today,
                        'dropout_risk_level' => $riskData['risk_level'],
                        'dropout_risk_score' => $riskData['risk_score'],
                    ]);
                }
            });

        $this->invalidateCache();
    }

    public function calculateAllRisks(?int $programId = null): Collection
    {
        $cacheKey = $programId ? "dropout_risks_all_prog_{$programId}" : 'dropout_risks_all';

        return Cache::remember($cacheKey, 3600, function () use ($programId) {
            // Eager Loading optimal mencegah N+1
            $students = Student::with([
                'parent.user',
                'enrollments.program',
                'progress' => function ($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                },
                'payments' => function ($q) {
                    $q->where('status', 'paid')->where('created_at', '>=', now()->subDays(90));
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
                $risk = $this->calculateRiskScore($student);

                return array_merge($risk, ['student' => $student]);
            })->sortByDesc('risk_score')->values();
        });
    }

    public function calculateRiskScore(Student $student): array
    {
        $attScore = $this->calculateAttendanceScore($student);
        $payScore = $this->calculatePaymentScore($student);
        $progScore = $this->calculateProgressScore($student);
        $engScore = $this->calculateEngagementScore($student);

        $totalScore = ($attScore * self::WEIGHT_ATTENDANCE) +
                      ($payScore * self::WEIGHT_PAYMENT) +
                      ($progScore * self::WEIGHT_PROGRESS) +
                      ($engScore * self::WEIGHT_ENGAGEMENT);

        $totalScore = round(min(100.0, max(0.0, $totalScore)), 1);
        $riskLevel = $this->determineRiskLevel($totalScore);
        $factors = $this->identifyRiskFactors($attScore, $payScore, $progScore, $engScore);

        $insights = $this->insightService->generateInsights($student, [
            'risk_level' => $riskLevel,
            'risk_score' => $totalScore,
            'risk_factors' => $factors,
        ]);

        return [
            'risk_score' => $totalScore,
            'risk_level' => $riskLevel,
            'attendance_score' => $attScore,
            'payment_score' => $payScore,
            'progress_score' => $progScore,
            'engagement_score' => $engScore,
            'risk_factors' => $factors,
            'recommendations' => $insights['actions'] ?? [],
        ];
    }

    private function calculateAttendanceScore(Student $student): float
    {
        try {
            $last30Days = Carbon::now()->subDays(30);
            $totalSessions = SessionConfirmation::whereIn('session_id', function ($q) use ($student, $last30Days) {
                $q->select('id')->from('learning_sessions')
                    ->where('student_id', $student->id)
                    ->where('date', '>=', $last30Days);
            })->count();

            if ($totalSessions === 0) {
                return 25.0; // Santri baru
            }

            $presentSessions = SessionConfirmation::whereIn('session_id', function ($q) use ($student, $last30Days) {
                $q->select('id')->from('learning_sessions')
                    ->where('student_id', $student->id)
                    ->where('date', '>=', $last30Days);
            })->where('status', 'hadir')->count();

            $rate = ($presentSessions / max(1, $totalSessions)) * 100;
            if ($rate >= 90) {
                return 0.0;
            }
            if ($rate >= 75) {
                return 25.0;
            }
            if ($rate >= 60) {
                return 60.0;
            }
            if ($rate >= 40) {
                return 85.0;
            }

            return 100.0;
        } catch (\Exception $e) {
            Log::error("Error calculating attendance score for student {$student->id}: ".$e->getMessage());

            return 50.0; // Safe default
        }
    }

    private function calculatePaymentScore(Student $student): float
    {
        $lastPayment = Payment::where('student_id', $student->id)
            ->where('status', 'paid')
            ->latest('payment_date')
            ->first();

        if (! $lastPayment) {
            return 30.0;
        }

        $date = $lastPayment->payment_date ?? $lastPayment->created_at;
        $days = Carbon::parse($date)->diffInDays(now());
        if ($days <= 30) {
            return 0.0;
        }
        if ($days <= 45) {
            return 40.0;
        }
        if ($days <= 60) {
            return 75.0;
        }

        return 100.0;
    }

    private function calculateProgressScore(Student $student): float
    {
        $lastProgress = Progress::where('student_id', $student->id)->latest('created_at')->first();
        if (! $lastProgress) {
            return 40.0;
        }

        $days = Carbon::parse($lastProgress->created_at)->diffInDays(now());
        if ($days <= 7) {
            return 0.0;
        }
        if ($days <= 14) {
            return 35.0;
        }
        if ($days <= 30) {
            return 70.0;
        }

        return 100.0;
    }

    private function calculateEngagementScore(Student $student): float
    {
        if (method_exists($student, 'mentorFeedbacks')) {
            $lastFeedback = $student->mentorFeedbacks()->latest('created_at')->first();
            if (! $lastFeedback) {
                return 40.0;
            }

            $days = Carbon::parse($lastFeedback->created_at)->diffInDays(now());
            if ($days <= 14) {
                return 0.0;
            }
            if ($days <= 30) {
                return 50.0;
            }

            return 100.0;
        }

        return 40.0;
    }

    public function invalidateCache(): void
    {
        Cache::forget('dropout_risks_all');
        Cache::forget('learning_velocities_all');
        Cache::forget('predictive_dashboard_summary');
    }

    private function determineRiskLevel(float $score): string
    {
        if ($score >= 75.0) {
            return 'critical';
        }
        if ($score >= 55.0) {
            return 'high';
        }
        if ($score >= 30.0) {
            return 'medium';
        }

        return 'low';
    }

    private function identifyRiskFactors(float $att, float $pay, float $prog, float $eng): array
    {
        $factors = [];
        if ($att >= 60.0) {
            $factors[] = 'Presensi rendah (< 70%)';
        }
        if ($pay >= 40.0) {
            $factors[] = 'Pembayaran menunggak > 30 hari';
        }
        if ($prog >= 35.0) {
            $factors[] = 'Progres hafalan stagnan > 14 hari';
        }
        if ($eng >= 50.0) {
            $factors[] = 'Wali santri pasif / jarang feedback';
        }

        return empty($factors) ? ['Aktivitas belajar terpantau normal'] : $factors;
    }
}
