<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\LeaderboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LeaderboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeaderboardService $leaderboardService;

    protected Student $student1;

    protected Student $student2;

    protected Student $student3;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaderboardService = app(LeaderboardService::class);

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

        $u1 = User::create(['name' => 'Ahmad Santri', 'email' => 'ahmad@alhikmah.com', 'password' => bcrypt('password'), 'role_id' => $studentRole->id]);
        $this->student1 = Student::create(['user_id' => $u1->id, 'full_name' => 'Ahmad Santri', 'age' => 10, 'gender' => 'L', 'total_points' => 500, 'current_streak' => 10]);

        $u2 = User::create(['name' => 'Budi Santri', 'email' => 'budi@alhikmah.com', 'password' => bcrypt('password'), 'role_id' => $studentRole->id]);
        $this->student2 = Student::create(['user_id' => $u2->id, 'full_name' => 'Budi Santri', 'age' => 16, 'gender' => 'L', 'total_points' => 800, 'current_streak' => 5]);

        $u3 = User::create(['name' => 'Citra Santri', 'email' => 'citra@alhikmah.com', 'password' => bcrypt('password'), 'role_id' => $studentRole->id]);
        $this->student3 = Student::create(['user_id' => $u3->id, 'full_name' => 'Citra Santri', 'age' => 9, 'gender' => 'P', 'total_points' => 300, 'current_streak' => 15, 'privacy_leaderboard' => true]);
    }

    public function test_leaderboard_service_returns_ranked_students(): void
    {
        $leaderboard = $this->leaderboardService->getLeaderboard('overall', 10);

        $this->assertCount(3, $leaderboard);
        $this->assertEquals(1, $leaderboard[0]->rank);
        $this->assertEquals('Budi Santri', $leaderboard[0]->student_name);
        $this->assertEquals(800, $leaderboard[0]->total_points);

        $this->assertEquals(2, $leaderboard[1]->rank);
        $this->assertEquals('Ahmad Santri', $leaderboard[1]->student_name);
    }

    public function test_leaderboard_service_filters_by_category_anak(): void
    {
        $leaderboard = $this->leaderboardService->getLeaderboard('anak', 10);

        $this->assertCount(2, $leaderboard); // age 10 and 9 (<=12)
        $this->assertEquals('Ahmad Santri', $leaderboard[0]->student_name);
    }

    public function test_leaderboard_service_filters_by_category_dewasa(): void
    {
        $leaderboard = $this->leaderboardService->getLeaderboard('dewasa', 10);

        $this->assertCount(1, $leaderboard); // age 16 (>12)
        $this->assertEquals('Budi Santri', $leaderboard[0]->student_name);
    }

    public function test_leaderboard_service_filters_by_category_streak(): void
    {
        $leaderboard = $this->leaderboardService->getLeaderboard('streak', 10);

        $this->assertEquals(1, $leaderboard[0]->rank);
        // Citra has highest streak (15), but privacy enabled -> masked name
        $this->assertEquals('Santri #'.$this->student3->id, $leaderboard[0]->student_name);
        $this->assertEquals(15, $leaderboard[0]->current_streak);
    }

    public function test_leaderboard_masks_name_when_privacy_is_enabled(): void
    {
        $leaderboard = $this->leaderboardService->getLeaderboard('overall', 10);

        $citraEntry = collect($leaderboard)->firstWhere('student_id', $this->student3->id);
        $this->assertNotNull($citraEntry);
        $this->assertEquals('Santri #'.$this->student3->id, $citraEntry->student_name);
    }

    public function test_leaderboard_caches_results_and_invalidates(): void
    {
        $leaderboard1 = $this->leaderboardService->getLeaderboard('overall', 10);
        $this->assertTrue(Cache::has('leaderboard_overall_10'));

        $this->leaderboardService->invalidateCache();
        $this->assertFalse(Cache::has('leaderboard_overall_10'));
    }
}
