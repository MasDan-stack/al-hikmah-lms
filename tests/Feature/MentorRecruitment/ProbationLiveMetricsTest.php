<?php

use App\Models\Badge;
use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\MentorProbationTracking;
use App\Models\Role;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use App\Services\MentorProbationService;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
    ]);

    $this->mentorUser = User::factory()->create([
        'name' => 'Ust. Abdullah Faqih',
        'role_id' => $this->mentorRole->id,
    ]);
    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ust. Abdullah Faqih',
        'status' => 'probation',
        'is_active' => true,
        'rating' => 5.0,
        'probation_end_date' => today()->addMonths(3),
    ]);

    $this->probation = MentorProbationTracking::create([
        'mentor_id' => $this->mentor->id,
        'start_date' => today(),
        'end_date' => today()->addMonths(3),
        'duration_months' => 3,
        'status' => 'active',
        'attendance_rate' => 0.0,
        'average_rating' => 0.0,
        'first_session_conducted' => false,
    ]);

    $this->studentUser = User::factory()->create();
    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'full_name' => 'Santri Ilyas',
        'age' => 10,
        'gender' => 'L',
    ]);
});

test('syncTrackingStats accurately calculates live lms metrics from completed sessions and feedback', function () {
    // 1. Create sessions: 2 completed out of 2 past scheduled sessions (100% attendance)
    Session::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'title' => 'Sesi Tahsin 1',
        'date' => today()->subDays(3)->toDateString(),
        'time' => '08:00:00',
        'method' => 'online',
        'status' => 'completed',
    ]);

    Session::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'title' => 'Sesi Tahsin 2',
        'date' => today()->subDays(1)->toDateString(),
        'time' => '08:00:00',
        'method' => 'online',
        'status' => 'completed',
    ]);

    // 1 future scheduled session (should not penalize attendance)
    Session::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'title' => 'Sesi Tahsin 3',
        'date' => today()->addDays(2)->toDateString(),
        'time' => '08:00:00',
        'method' => 'online',
        'status' => 'scheduled',
    ]);

    // 2. Create feedback with rating 4.8
    MentorFeedback::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'overall_rating' => 5,
        'comment' => 'Sangat bagus',
    ]);
    MentorFeedback::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'overall_rating' => 4,
        'comment' => 'Bagus',
    ]);

    $probationService = app(MentorProbationService::class);
    $result = $probationService->syncTrackingStats($this->probation);

    expect($result)->toBeTrue();

    $this->probation->refresh();
    expect((float) $this->probation->attendance_rate)->toBe(100.0);
    expect((float) $this->probation->average_rating)->toBe(4.5);
    expect($this->probation->first_session_conducted)->toBeTrue();
    expect($this->probation->total_sessions_conducted)->toBe(2);
    expect($this->probation->last_synced_at)->not->toBeNull();
});

test('admin can trigger live metrics synchronization from probation show page', function () {
    Session::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'title' => 'Sesi Perdana',
        'date' => today()->subDay()->toDateString(),
        'time' => '09:00:00',
        'method' => 'online',
        'status' => 'completed',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.mentors.probation.sync', $this->probation->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->probation->refresh();
    expect($this->probation->first_session_conducted)->toBeTrue();
    expect($this->probation->last_synced_at)->not->toBeNull();
});

test('evaluating probation as passed promotes mentor to active and awards M01 certification badge', function () {
    Badge::firstOrCreate(
        ['code' => 'M01'],
        [
            'name' => 'Mentor Certified',
            'description' => 'Diberikan saat guru lulus masa percobaan 3 bulan.',
            'icon' => 'bi-patch-check-fill',
            'category' => 'achievement',
            'points_reward' => 500,
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($this->admin)
        ->post(route('admin.mentors.probation.complete', $this->probation->id), [
            'decision' => 'passed',
            'notes' => 'Performa sangat istimewa, telah menyelesaikan orientasi dan memiliki rating tinggi.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->probation->refresh();
    expect($this->probation->status)->toBe('passed');
    expect($this->probation->final_decision)->toBe('passed');

    $this->mentor->refresh();
    expect($this->mentor->status)->toBe('active');
    expect($this->mentor->is_active)->toBeTrue();

    // Verify Badge M01 awarded in mentor_trainings
    $badge = Badge::where('code', 'M01')->first();
    $this->assertDatabaseHas('mentor_trainings', [
        'mentor_id' => $this->mentor->id,
        'badge_id' => $badge->id,
    ]);
});

test('evaluating probation as extended extends mentor probation end date', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.mentors.probation.complete', $this->probation->id), [
            'decision' => 'extended',
            'notes' => 'Diberikan tambahan waktu 1 bulan untuk pemenuhan modul orientasi.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->probation->refresh();
    expect($this->probation->status)->toBe('extended');
    expect($this->probation->final_decision)->toBe('extended');

    $this->mentor->refresh();
    expect($this->mentor->status)->toBe('probation');
    expect($this->mentor->probation_end_date->toDateString())->toBe(today()->addMonths(1)->toDateString());
});
