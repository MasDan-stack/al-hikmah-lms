<?php

namespace Tests\Unit;

use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\MentorFeedbackRating;
use App\Models\MentorPerformanceSnapshot;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\MentorPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorPerformanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MentorPerformanceService $performanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->performanceService = app(MentorPerformanceService::class);
        Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
        Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
        Role::firstOrCreate(['name' => 'parent'], ['label' => 'Wali']);
    }

    public function test_bayesian_rating_smoothing_formula()
    {
        $user = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $user->id,
            'full_name' => 'Ustadz Testing Bayesian',
            'is_active' => true,
        ]);

        // When mentor has 0 feedback: should return prior mean 4.5
        $smoothZero = $this->performanceService->computeBayesianRating($mentor->id);
        $this->assertEquals(4.50, $smoothZero);

        // When mentor has 1 feedback with 5 stars: (1*5.0 + 5*4.5) / (1 + 5) = 27.5 / 6 = 4.58
        $parentUser = User::factory()->create();
        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Santri Test',
            'age' => 10,
            'gender' => 'L',
        ]);

        $feedback = MentorFeedback::create([
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'parent_id' => $parentUser->id,
            'overall_rating' => 5,
            'comment' => 'Bagus sekali',
        ]);
        MentorFeedbackRating::create([
            'feedback_id' => $feedback->id,
            'category' => 'teaching_quality',
            'rating' => 5,
        ]);

        $smoothOne = $this->performanceService->computeBayesianRating($mentor->id);
        $this->assertEquals(4.58, $smoothOne);
    }

    public function test_program_dynamic_weighting_shifts_weights_appropriately()
    {
        $weightsTahfidz = $this->performanceService->getDynamicWeights('tahfidz');
        $this->assertArrayHasKey('tajwid', $weightsTahfidz);
        $this->assertGreaterThan(0, $weightsTahfidz['tajwid']);

        // Bahasa Arab / Terjemah: tajwid is zeroed out and weight is redistributed
        $weightsArabic = $this->performanceService->getDynamicWeights('bahasa_arab');
        $this->assertEquals(0.0, $weightsArabic['tajwid'] ?? 0.0);
        $this->assertGreaterThan($weightsTahfidz['curriculum_milestones'], $weightsArabic['curriculum_milestones']);
    }

    public function test_student_handicap_sabar_multiplier_awards_bonus()
    {
        $mUser = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $mUser->id,
            'full_name' => 'Ustadz Sabar',
            'is_active' => true,
        ]);

        $stUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $stUser->id,
            'full_name' => 'Santri Khusus',
            'age' => 5, // < 6 years old gives difficulty bonus
        ]);

        $mentor->students()->attach($student->id, ['day_assigned' => 'monday']);

        $bonus = $this->performanceService->calculateHandicapBonus($mentor->id);
        $this->assertGreaterThan(0.0, $bonus);
    }

    public function test_composite_score_calculation_and_snapshot_persistence()
    {
        $mUser = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $mUser->id,
            'full_name' => 'Ustadz Snapshot',
            'is_active' => true,
        ]);

        $period = now()->format('Y-m');
        $snapshot = $this->performanceService->calculateAndSaveSnapshot($mentor->id, $period);

        $this->assertInstanceOf(MentorPerformanceSnapshot::class, $snapshot);
        $this->assertEquals($mentor->id, $snapshot->mentor_id);
        $this->assertGreaterThanOrEqual(0, $snapshot->composite_score);
        $this->assertLessThanOrEqual(100, $snapshot->composite_score);
    }
}
