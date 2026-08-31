<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\HifzTarget;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\HifzProgressService;
use Database\Seeders\BadgeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BadgeSeeder::class);

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

        $this->studentUser = User::create([
            'name' => 'Dan Student',
            'email' => 'dan.student@alhikmah.com',
            'password' => bcrypt('password123'),
            'role_id' => $studentRole->id,
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'full_name' => 'Dan Student',
            'age' => 12,
            'gender' => 'L',
            'total_points' => 250,
            'current_streak' => 3,
        ]);

        app(HifzProgressService::class)->initializeStudentJuzProgress($this->student);
    }

    public function test_student_can_view_dashboard_with_all_gamification_widgets(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ruang Belajar Santri');
        $response->assertSee('Total Poin Gamifikasi');
        $response->assertSee('Progress Hafalan Al-Qur\'an', false);
    }

    public function test_student_can_view_today_targets_page(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.targets.today'));

        $response->assertStatus(200);
        $response->assertSee('Target Hafalan Hari Ini');
        $response->assertSee('Buat Target Hafalan Mandiri');
    }

    public function test_student_can_create_personal_hifz_target(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->post(route('student.targets.store'), [
                'target_date' => now()->toDateString(),
                'surah_name' => 'QS. Al-Mulk',
                'start_ayat' => 1,
                'end_ayat' => 10,
                'notes' => 'Target murojaah ba\'da maghrib',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hifz_targets', [
            'student_id' => $this->student->id,
            'surah_name' => 'QS. Al-Mulk',
            'start_ayat' => 1,
            'end_ayat' => 10,
            'total_ayat' => 10,
            'status' => 'pending',
        ]);
    }

    public function test_student_can_mark_target_as_completed(): void
    {
        $target = HifzTarget::create([
            'student_id' => $this->student->id,
            'mentor_id' => $this->studentUser->id,
            'target_date' => now()->toDateString(),
            'surah_name' => 'QS. Yasin',
            'start_ayat' => 1,
            'end_ayat' => 12,
            'total_ayat' => 12,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->studentUser)
            ->patch(route('student.targets.complete', $target->id));

        $response->assertRedirect();
        $target->refresh();
        $this->assertEquals('completed', $target->status);
    }

    public function test_student_can_view_progress_30_juz_page(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.progress.juz'));

        $response->assertStatus(200);
        $response->assertSee('Peta Capaian 30 Juz');
        $response->assertSee('Juz 30');
    }

    public function test_student_can_view_badges_and_hall_of_fame(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.badges'));

        $response->assertStatus(200);
        $response->assertSee('Lemari Lencana Prestasi');

        $b01 = Badge::where('code', 'B01')->first();
        $responseHof = $this->actingAs($this->studentUser)
            ->get(route('student.badges.hall-of-fame', $b01->code));

        $responseHof->assertStatus(200);
        $responseHof->assertSee('Hall of Fame Lencana');
    }

    public function test_student_can_view_stats_page(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.stats'));

        $response->assertStatus(200);
        $response->assertSee('Statistik & Riwayat Poin');
    }

    public function test_student_can_toggle_leaderboard_privacy(): void
    {
        $this->assertFalse((bool) $this->student->privacy_leaderboard);

        $response = $this->actingAs($this->studentUser)
            ->post(route('student.privacy.toggle'));

        $response->assertRedirect();
        $this->student->refresh();
        $this->assertTrue((bool) $this->student->privacy_leaderboard);
    }
}
