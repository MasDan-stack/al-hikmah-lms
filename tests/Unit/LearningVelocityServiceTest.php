<?php

namespace Tests\Unit;

use App\Models\Mentor;
use App\Models\Progress;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\PredictiveAnalytics\LearningVelocityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningVelocityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LearningVelocityService $velocityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->velocityService = app(LearningVelocityService::class);
        Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
        Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    }

    public function test_velocity_calculation_with_active_progress()
    {
        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Santri Rajin Velocity',
            'age' => 10,
            'gender' => 'L',
        ]);

        $mentorUser = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustadz Pembimbing',
        ]);

        // Buat setoran 10 ayat per hari pada 2 hari berbeda
        $p1 = new Progress([
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'kategori' => 'Tahfidz',
            'surah_start' => 'An-Naba',
            'surah_end' => 'An-Naba',
            'ayat_start' => 1,
            'ayat_end' => 10,
        ]);
        $p1->created_at = now()->subDays(2);
        $p1->save();

        $p2 = new Progress([
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'kategori' => 'Tahfidz',
            'surah_start' => 'An-Naba',
            'surah_end' => 'An-Naba',
            'ayat_start' => 11,
            'ayat_end' => 20,
        ]);
        $p2->created_at = now()->subDays(1);
        $p2->save();

        $velocity = $this->velocityService->calculateVelocity($student);

        $this->assertIsArray($velocity);
        $this->assertEquals(2, $velocity['days_active']);
        $this->assertEquals(20, $velocity['total_ayat_30d']);
        $this->assertEquals(10.0, $velocity['velocity_ayat_per_day']);
        $this->assertEquals('excellent', $velocity['velocity_status']);
        $this->assertNotNull($velocity['projected_completion_date']);
    }

    public function test_velocity_status_returns_no_data_for_inactive_student()
    {
        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Santri Pasif',
            'age' => 9,
            'gender' => 'P',
        ]);

        $velocity = $this->velocityService->calculateVelocity($student);

        $this->assertEquals(0, $velocity['days_active']);
        $this->assertEquals('no_data', $velocity['velocity_status']);
        $this->assertEquals(0.0, $velocity['velocity_ayat_per_day']);
    }

    public function test_projected_completion_date_calculation()
    {
        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Santri ETA Target',
            'age' => 12,
            'gender' => 'L',
        ]);

        $mentorUser = User::factory()->create();
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustadz Pembimbing ETA',
        ]);

        $p = new Progress([
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'kategori' => 'Tahfidz',
            'surah_start' => 'Al-Mulk',
            'surah_end' => 'Al-Mulk',
            'ayat_start' => 1,
            'ayat_end' => 30,
        ]);
        $p->created_at = now();
        $p->save();

        $velocity = $this->velocityService->calculateVelocity($student);

        $this->assertGreaterThan(0, $velocity['estimated_days_remaining']);
        $this->assertNotNull($velocity['projected_completion_date']);
    }
}
