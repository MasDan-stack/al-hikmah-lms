<?php

namespace Tests\Unit;

use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\Role;
use App\Models\User;
use App\Services\MentorFeedbackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorFeedbackServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MentorFeedbackService $feedbackService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackService = app(MentorFeedbackService::class);
        Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
        Role::firstOrCreate(['name' => 'parent'], ['label' => 'Wali']);
    }

    public function test_parent_feedback_submission_with_quick_chips_and_categories()
    {
        $mUser = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $mUser->id,
            'full_name' => 'Ustadz Feedback Target',
            'is_active' => true,
        ]);

        $parent = User::factory()->create();

        $feedback = $this->feedbackService->submitFeedback([
            'mentor_id' => $mentor->id,
            'parent_id' => $parent->id,
            'overall_rating' => 5,
            'categories' => [
                'teaching_quality' => 5,
                'patience' => 5,
                'punctuality' => 4,
            ],
            'quick_tags' => ['#SangatSabar', '#TepatWaktu', '#PenyampaianJelas'],
            'comment' => 'Ustaz sangat telaten membimbing tajwid.',
            'is_anonymous' => true,
        ]);

        $this->assertInstanceOf(MentorFeedback::class, $feedback);
        $this->assertEquals(5, $feedback->overall_rating);
        $this->assertTrue($feedback->is_anonymous);
        $this->assertCount(3, $feedback->quick_tags);
        $this->assertContains('#SangatSabar', $feedback->quick_tags);
        $this->assertCount(3, $feedback->ratings);
    }
}
