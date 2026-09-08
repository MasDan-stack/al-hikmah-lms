<?php

use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\MentorFeedback;
use App\Models\MentorProbationTracking;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AlertService;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
    ]);
});

test('admin dashboard renders recruitment and probation kpi counts', function () {
    // 1. Create applications in various stages
    MentorApplication::factory()->create(['status' => 'submitted', 'current_stage' => 1]);
    MentorApplication::factory()->create(['status' => 'document_review', 'current_stage' => 2]);
    MentorApplication::factory()->create(['status' => 'approved', 'current_stage' => 5]);

    // 2. Create probation trackings
    $mentorUser = User::factory()->create(['name' => 'Ust. Hamzah', 'role_id' => $this->mentorRole->id]);
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ust. Hamzah',
        'status' => 'probation',
        'is_active' => true,
    ]);

    MentorProbationTracking::create([
        'mentor_id' => $mentor->id,
        'start_date' => today()->subDays(80),
        'end_date' => today()->addDays(10), // Expiring within 14 days
        'duration_months' => 3,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Pusat Rekrutmen Guru');
    $response->assertViewHas('pendingApplicationsCount', fn ($val) => $val >= 2);
    $response->assertViewHas('totalApplicationsCount', fn ($val) => $val >= 3);
    $response->assertViewHas('activeProbationsCount', fn ($val) => $val >= 1);
    $response->assertViewHas('expiringProbationsCount', fn ($val) => $val >= 1);
});

test('admin dashboard renders parent feedback tab with recent feedback entries', function () {
    $mentorUser = User::factory()->create([
        'name' => 'Ustazah Aisyah Putri',
        'role_id' => $this->mentorRole->id,
    ]);
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustazah Aisyah Putri',
        'is_active' => true,
    ]);

    $studentUser = User::factory()->create(['name' => 'Ahmad Rabbani']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'full_name' => 'Ahmad Rabbani',
        'age' => 8,
        'gender' => 'L',
    ]);

    $parentUser = User::factory()->create([
        'name' => 'Bunda Sarah',
        'role_id' => $this->parentRole->id,
    ]);

    MentorFeedback::create([
        'mentor_id' => $mentor->id,
        'student_id' => $student->id,
        'parent_id' => $parentUser->id,
        'overall_rating' => 5,
        'comment' => 'Bimbingan tahsin sangat berkualitas dan sabar.',
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('recentFeedbacks');
    $response->assertSee('Ustazah Aisyah Putri');
    $response->assertSee('Bimbingan tahsin sangat berkualitas dan sabar.');
});

test('low mentor rating feedback within 7 days triggers warning alert in alert service and admin dashboard', function () {
    $mentorUser = User::factory()->create([
        'name' => 'Ust. Zaid Abdullah',
        'role_id' => $this->mentorRole->id,
    ]);
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ust. Zaid Abdullah',
        'is_active' => true,
    ]);

    $studentUser = User::factory()->create();
    $student = Student::create([
        'user_id' => $studentUser->id,
        'full_name' => 'Fathan Mubina',
        'age' => 10,
        'gender' => 'L',
    ]);

    MentorFeedback::create([
        'mentor_id' => $mentor->id,
        'student_id' => $student->id,
        'overall_rating' => 2,
        'comment' => 'Guru sering datang terlambat tanpa pemberitahuan.',
        'created_at' => now()->subDays(2),
    ]);

    // 1. Direct Service check
    $alertService = app(AlertService::class);
    $alerts = $alertService->getAllAlerts();

    $warningAlertIds = array_column($alerts['warning'], 'id');
    expect($warningAlertIds)->toContain('warn_low_mentor_feedback');

    // 2. Admin Dashboard view data check
    $responseDashboard = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $responseDashboard->assertStatus(200);
    $responseDashboard->assertViewHas('allAlerts', function ($allAlerts) {
        $warningIds = array_column($allAlerts['warning'], 'id');

        return in_array('warn_low_mentor_feedback', $warningIds);
    });

    // 3. Admin Alerts Page visual check
    $responseAlerts = $this->actingAs($this->admin)->get(route('admin.alerts.index'));
    $responseAlerts->assertStatus(200);
    $responseAlerts->assertSee('Feedback Mentor Rendah');
});

test('admin dashboard renders probation tab with orientation module progress and countdown', function () {
    $mentorUser = User::factory()->create(['name' => 'Ust. Salman Al-Farisi', 'role_id' => $this->mentorRole->id]);
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ust. Salman Al-Farisi',
        'status' => 'probation',
        'is_active' => true,
    ]);

    MentorProbationTracking::create([
        'mentor_id' => $mentor->id,
        'start_date' => today()->subDays(30),
        'end_date' => today()->addDays(60),
        'duration_months' => 3,
        'status' => 'active',
        'orientation_modules_status' => [
            'mod1' => true,
            'mod2' => true,
            'mod3' => false,
            'mod4' => false,
        ],
        'attendance_rate' => 96.5,
        'average_rating' => 4.90,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('activeProbationList');
    $response->assertSee('Ust. Salman Al-Farisi');
    $response->assertSee('Guru Masa Percobaan');
    $response->assertSee('2/4 Selesai');
    $response->assertSee('Tinjau Evaluasi');
});
