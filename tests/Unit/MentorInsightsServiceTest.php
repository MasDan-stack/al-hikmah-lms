<?php

namespace Tests\Unit;

use App\Models\Mentor;
use App\Models\MentorInsight;
use App\Models\Role;
use App\Models\User;
use App\Services\MentorInsightsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorInsightsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MentorInsightsService $insightsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->insightsService = app(MentorInsightsService::class);
        Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    }

    public function test_heuristic_fallback_produces_structured_insights_and_action_plan()
    {
        $mUser = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $mUser->id,
            'full_name' => 'Ustadz AI Fallback',
            'is_active' => true,
        ]);

        $metrics = [
            'composite_score' => 88.5,
            'retention_rate' => 96.0,
            'academic_quality_score' => 85.0,
            'attendance_rate' => 98.0,
            'avg_rating_bayesian' => 4.8,
            'target_achievement_rate' => 90.0,
            'active_students' => 12,
        ];

        $insight = $this->insightsService->generateInsights($mentor->id, '2026-09', $metrics);

        $this->assertInstanceOf(MentorInsight::class, $insight);
        $this->assertEquals($mentor->id, $insight->mentor_id);
        $this->assertNotEmpty($insight->ai_summary);
        $this->assertIsArray($insight->coaching_recommendations);
        $this->assertGreaterThanOrEqual(1, count($insight->coaching_recommendations));
        $this->assertContains($insight->risk_level, ['low', 'medium', 'high']);
    }
}
