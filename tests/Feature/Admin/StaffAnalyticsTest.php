<?php

use App\Models\Mentor;
use App\Models\MentorLeave;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\StaffAnalyticsService;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);

    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->parentUser = User::factory()->create(['role_id' => $this->parentRole->id]);
});

test('admin can access staff analytics page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.staff.index'))
        ->assertStatus(200)
        ->assertSee('Manajemen SDM & Beban Kerja Guru', false)
        ->assertSee('Rasio Guru : Santri', false)
        ->assertSee('Top Performing Mentors', false);
});

test('staff analytics correctly classifies workload capacity and leave statuses', function () {
    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Salman Al-Farisi',
        'specialization' => 'Tahfidz',
        'rating' => 4.9,
        'is_active' => true,
    ]);

    // Create 35 active students to trigger 'busy' status
    $studentIds = [];
    for ($i = 0; $i < 35; $i++) {
        $stUser = User::factory()->create();
        $st = Student::create([
            'user_id' => $stUser->id,
            'full_name' => 'Santri '.$i,
            'age' => 10,
            'gender' => 'L',
        ]);
        $studentIds[] = $st->id;
    }
    $mentor->students()->attach($studentIds, ['is_active' => true]);

    $service = app(StaffAnalyticsService::class);
    $workloadList = $service->getMentorWorkloadList();

    $targetMentor = collect($workloadList)->firstWhere('id', $mentor->id);
    expect($targetMentor)->not->toBeNull();
    expect($targetMentor['active_students_count'])->toBe(35);
    expect($targetMentor['capacity_status'])->toBe('busy');

    // Test leave detection with leave_date
    MentorLeave::create([
        'mentor_id' => $mentor->id,
        'leave_date' => now()->toDateString(),
        'reason' => 'Umroh & Ziarah',
        'status' => 'approved',
    ]);

    $summary = $service->getStaffSummary();
    expect($summary['mentors_on_leave_today'])->toBeGreaterThanOrEqual(1);
});
