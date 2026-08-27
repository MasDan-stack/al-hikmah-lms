<?php

namespace Tests\Unit;

use App\Models\Badge;
use App\Models\HifzTarget;
use App\Models\JuzProgress;
use App\Models\Mentor;
use App\Models\Progress;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\BadgeEvaluatorService;
use App\Services\GamificationService;
use App\Services\HifzProgressService;
use App\Services\StreakTrackerService;
use Database\Seeders\BadgeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GamificationService $gamificationService;

    protected BadgeEvaluatorService $badgeEvaluator;

    protected StreakTrackerService $streakTracker;

    protected HifzProgressService $hifzProgressService;

    protected Student $student;

    protected Mentor $mentor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(BadgeSeeder::class);

        $this->gamificationService = app(GamificationService::class);
        $this->badgeEvaluator = app(BadgeEvaluatorService::class);
        $this->streakTracker = app(StreakTrackerService::class);
        $this->hifzProgressService = app(HifzProgressService::class);

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);

        $studentUser = User::create([
            'name' => 'Santri Gamify',
            'email' => 'santri.gamify@alhikmah.com',
            'password' => bcrypt('password123'),
            'role_id' => $studentRole->id,
        ]);

        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Santri Gamify',
            'age' => 11,
            'gender' => 'L',
            'total_points' => 0,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);

        $mentorUser = User::create([
            'name' => 'Ustadz Ahmad',
            'email' => 'ustadz.ahmad@alhikmah.com',
            'password' => bcrypt('password123'),
            'role_id' => $mentorRole->id,
        ]);

        $this->mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustadz Ahmad',
            'specialization' => 'Tahfidz',
        ]);

        $this->hifzProgressService->initializeStudentJuzProgress($this->student);
    }

    public function test_award_points_updates_student_total_and_logs_ledger(): void
    {
        $pointRecord = $this->gamificationService->awardPoints(
            $this->student,
            100,
            'setoran',
            'Setoran lancar QS. Al-Mulk'
        );

        $this->student->refresh();
        $this->assertEquals(100, $this->student->total_points);
        $this->assertDatabaseHas('gamification_points', [
            'student_id' => $this->student->id,
            'points' => 100,
            'source_type' => 'setoran',
        ]);
    }

    public function test_streak_tracker_increments_streak_on_consecutive_days(): void
    {
        $this->student->update([
            'current_streak' => 2,
            'longest_streak' => 2,
            'last_setoran_date' => now()->subDay()->toDateString(),
        ]);

        $streak = $this->streakTracker->recordActivity($this->student);

        $this->student->refresh();
        $this->assertEquals(3, $streak);
        $this->assertEquals(3, $this->student->current_streak);
        $this->assertEquals(3, $this->student->longest_streak);
    }

    public function test_streak_tracker_maintains_streak_on_same_day(): void
    {
        $this->student->update([
            'current_streak' => 5,
            'longest_streak' => 5,
            'last_setoran_date' => now()->toDateString(),
        ]);

        $streak = $this->streakTracker->recordActivity($this->student);

        $this->assertEquals(5, $streak);
    }

    public function test_streak_tracker_resets_streak_after_gap_days(): void
    {
        $this->student->update([
            'current_streak' => 10,
            'longest_streak' => 10,
            'last_setoran_date' => now()->subDays(3)->toDateString(),
        ]);

        $streak = $this->streakTracker->recordActivity($this->student);

        $this->student->refresh();
        $this->assertEquals(1, $streak);
        $this->assertEquals(10, $this->student->longest_streak);
    }

    public function test_badge_evaluator_awards_b01_first_setoran(): void
    {
        $progress = Progress::create([
            'student_id' => $this->student->id,
            'mentor_id' => $this->mentor->id,
            'surah_start' => 'Al-Fatihah',
            'ayat_start' => 1,
            'ayat_end' => 7,
            'nilai_tajwid' => 90,
            'nilai_fluent' => 90,
            'keterangan' => 'Mumtaz',
        ]);

        $this->assertDatabaseHas('student_badges', [
            'student_id' => $this->student->id,
            'badge_id' => Badge::where('code', 'B01')->value('id'),
        ]);
    }

    public function test_badge_evaluator_awards_b07_streak_7_days(): void
    {
        $this->student->update(['current_streak' => 7]);

        $awarded = $this->badgeEvaluator->evaluateOnStreak($this->student, 7);

        $this->assertNotEmpty($awarded);
        $this->assertDatabaseHas('student_badges', [
            'student_id' => $this->student->id,
            'badge_id' => Badge::where('code', 'B07')->value('id'),
        ]);
    }

    public function test_badge_evaluator_awards_b03_juz_30_mutqin(): void
    {
        $juz30 = JuzProgress::where('student_id', $this->student->id)->where('juz_number', 30)->first();
        $juz30->update([
            'status' => 'mutqin',
            'is_mutqin' => true,
            'ayat_hafal' => 564,
        ]);

        $awarded = $this->badgeEvaluator->evaluateOnJuzCompletion($this->student, 30, true);

        $this->assertNotEmpty($awarded);
        $this->assertDatabaseHas('student_badges', [
            'student_id' => $this->student->id,
            'badge_id' => Badge::where('code', 'B03')->value('id'),
        ]);
    }

    public function test_gamification_orchestrator_handles_progress_setoran(): void
    {
        $progress = Progress::create([
            'student_id' => $this->student->id,
            'mentor_id' => $this->mentor->id,
            'surah_start' => 'An-Naba',
            'ayat_start' => 1,
            'ayat_end' => 40,
            'nilai_tajwid' => 95,
            'nilai_fluent' => 95,
            'keterangan' => 'Lancar',
        ]);

        $this->student->refresh();
        $this->assertGreaterThan(0, $this->student->total_points);
        $this->assertEquals(1, $this->student->current_streak);
    }

    public function test_complete_target_awards_bonus_points_and_updates_status(): void
    {
        $target = HifzTarget::create([
            'student_id' => $this->student->id,
            'mentor_id' => $this->mentor->user_id,
            'target_date' => now()->toDateString(),
            'surah_name' => 'Al-Mulk',
            'start_ayat' => 1,
            'end_ayat' => 10,
            'total_ayat' => 10,
            'status' => 'pending',
        ]);

        $this->gamificationService->completeTarget($target);

        $target->refresh();
        $this->student->refresh();

        $this->assertEquals('completed', $target->status);
        $this->assertNotNull($target->completed_at);
        $this->assertGreaterThanOrEqual(50, $this->student->total_points);
    }

    public function test_hifz_progress_service_calculates_summary(): void
    {
        $summary = $this->hifzProgressService->getProgressSummary($this->student);

        $this->assertArrayHasKey('total_mutqin', $summary);
        $this->assertArrayHasKey('total_completed', $summary);
        $this->assertArrayHasKey('total_active', $summary);
        $this->assertArrayHasKey('total_ayat_hafal', $summary);
        $this->assertEquals(30, $summary['juz_list']->count());
    }
}
