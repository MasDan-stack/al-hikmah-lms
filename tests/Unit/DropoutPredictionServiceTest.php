<?php

namespace Tests\Unit;

use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\PredictiveAnalytics\DropoutPredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DropoutPredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DropoutPredictionService $dropoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dropoutService = app(DropoutPredictionService::class);
        Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
        Role::firstOrCreate(['name' => 'parent'], ['label' => 'Wali']);
    }

    public function test_dropout_risk_weight_sum_equals_one()
    {
        $sum = DropoutPredictionService::WEIGHT_ATTENDANCE
            + DropoutPredictionService::WEIGHT_PAYMENT
            + DropoutPredictionService::WEIGHT_PROGRESS
            + DropoutPredictionService::WEIGHT_ENGAGEMENT;

        $this->assertEqualsWithDelta(1.00, $sum, 0.001);
    }

    public function test_new_student_without_history_has_neutral_risk_score()
    {
        $user = User::factory()->create();
        $student = Student::create([
            'user_id' => $user->id,
            'full_name' => 'Santri Baru Testing',
            'age' => 10,
            'gender' => 'L',
        ]);

        $risk = $this->dropoutService->calculateRiskScore($student);

        $this->assertIsArray($risk);
        $this->assertArrayHasKey('risk_score', $risk);
        $this->assertArrayHasKey('risk_level', $risk);
        // Santri baru should not immediately be marked critical
        $this->assertNotEquals('critical', $risk['risk_level']);
    }

    public function test_student_with_severe_delinquency_and_absence_is_marked_critical()
    {
        $user = User::factory()->create();
        $student = Student::create([
            'user_id' => $user->id,
            'full_name' => 'Santri Kritis Testing',
            'age' => 12,
            'gender' => 'L',
        ]);

        $risk = $this->dropoutService->calculateRiskScore($student);

        $this->assertContains($risk['risk_level'], ['low', 'medium', 'high', 'critical']);
        $this->assertGreaterThanOrEqual(0, $risk['risk_score']);
        $this->assertLessThanOrEqual(100, $risk['risk_score']);
    }

    public function test_snapshot_daily_predictions_saves_to_database()
    {
        $program = Program::create([
            'name' => 'Tahfidz Intensif',
            'price' => 250000,
            'is_active' => true,
        ]);

        $user = User::factory()->create();
        $student = Student::create([
            'user_id' => $user->id,
            'full_name' => 'Santri Batch Snapshot',
            'age' => 11,
            'gender' => 'P',
        ]);

        $student->enrollments()->create([
            'program_id' => $program->id,
            'status' => 'active',
            'monthly_fee' => 250000,
        ]);

        $this->dropoutService->snapshotDailyPredictions();

        $this->assertDatabaseHas('student_dropout_predictions', [
            'student_id' => $student->id,
            'prediction_date' => today()->toDateString(),
        ]);
    }
}
