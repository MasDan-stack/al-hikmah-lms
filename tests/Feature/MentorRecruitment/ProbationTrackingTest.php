<?php

use App\Models\Mentor;
use App\Models\MentorProbationTracking;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();

    $this->mentorUser = User::factory()->mentor()->create();
    $this->mentor = Mentor::factory()->create([
        'user_id' => $this->mentorUser->id,
        'status' => 'probation',
        'probation_end_date' => now()->addDays(90),
    ]);

    $this->probation = MentorProbationTracking::create([
        'mentor_id' => $this->mentor->id,
        'start_date' => now(),
        'end_date' => now()->addDays(90),
        'status' => 'active',
    ]);
});

test('admin can update probation checklist and scores', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.mentors.probation.updateScores', $this->probation->id), [
            'attendance_rate' => 95.5,
            'average_rating' => 4.85,
            'orientation_completed' => 1,
            'system_training_completed' => 1,
            'first_session_conducted' => 1,
            'training_modules_completed' => 3,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_probation_trackings', [
        'id' => $this->probation->id,
        'attendance_rate' => 95.5,
        'average_rating' => 4.85,
        'orientation_completed' => true,
        'system_training_completed' => true,
        'first_session_conducted' => true,
        'training_modules_completed' => 3,
    ]);
});

test('admin can complete probation as passed and mentor becomes active', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.mentors.probation.complete', $this->probation->id), [
            'decision' => 'passed',
            'notes' => 'Mentor sangat kompeten dan disiplin',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_probation_trackings', [
        'id' => $this->probation->id,
        'status' => 'passed',
        'final_decision' => 'passed',
    ]);

    $this->assertDatabaseHas('mentors', [
        'id' => $this->mentor->id,
        'status' => 'active',
        'is_active' => true,
    ]);
});

test('admin can terminate probation and mentor becomes inactive', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.mentors.probation.complete', $this->probation->id), [
            'decision' => 'terminated',
            'notes' => 'Tidak memenuhi standar kehadiran',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_probation_trackings', [
        'id' => $this->probation->id,
        'status' => 'terminated',
        'final_decision' => 'terminated',
    ]);

    $this->assertDatabaseHas('mentors', [
        'id' => $this->mentor->id,
        'status' => 'inactive',
        'is_active' => false,
    ]);
});
