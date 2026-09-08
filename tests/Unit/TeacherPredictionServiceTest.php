<?php

namespace Tests\Unit;

use App\Models\Mentor;
use App\Models\MentorPerformanceSnapshot;
use App\Models\Role;
use App\Models\User;
use App\Services\PredictiveAnalytics\TeacherPredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TeacherPredictionService $teacherService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacherService = app(TeacherPredictionService::class);
        Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    }

    public function test_mentor_with_declining_trend_triggers_coaching()
    {
        $user = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'full_name' => 'Ustadz Declining Trend',
            'status' => 'active',
            'rating' => 3.8,
        ]);

        // Simulasikan tren skor komposit menurun selama 4 bulan
        $starts = ['2026-05-01', '2026-06-01', '2026-07-01', '2026-08-01'];
        $ends = ['2026-05-31', '2026-06-30', '2026-07-31', '2026-08-31'];
        $scores = [90.0, 82.0, 74.0, 65.0];

        for ($i = 0; $i < 4; $i++) {
            MentorPerformanceSnapshot::create([
                'mentor_id' => $mentor->id,
                'period_type' => 'monthly',
                'period_start' => $starts[$i],
                'period_end' => $ends[$i],
                'composite_score' => $scores[$i],
                'total_students' => 15,
                'active_students' => 15,
                'total_sessions' => 20,
                'completed_sessions' => 20,
                'avg_rating_raw' => 3.8,
                'avg_rating_bayesian' => 3.9,
                'target_achievement_rate' => 65.0,
            ]);
        }

        $prediction = $this->teacherService->predictMentorPerformance($mentor);

        $this->assertTrue($prediction['coaching_needed']);
        $this->assertLessThan(0, $prediction['slope']);
        $this->assertContains($prediction['coaching_urgency'], ['high', 'critical']);
    }

    public function test_mentor_with_stable_high_rating_is_safe()
    {
        $user = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'full_name' => 'Ustadzah Mumtazah',
            'status' => 'active',
            'rating' => 4.9,
        ]);

        $prediction = $this->teacherService->predictMentorPerformance($mentor);

        $this->assertFalse($prediction['coaching_needed']);
        $this->assertEquals('low', $prediction['coaching_urgency']);
    }

    public function test_snapshot_daily_predictions_updates_mentors()
    {
        $user = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'full_name' => 'Ustadz Testing Snapshot',
            'status' => 'active',
            'rating' => 3.5,
        ]);

        $this->teacherService->snapshotDailyPredictions();

        $mentor->refresh();
        $this->assertNotNull($mentor->last_coaching_alert_at);
        $this->assertTrue($mentor->coaching_needed);
    }
}
