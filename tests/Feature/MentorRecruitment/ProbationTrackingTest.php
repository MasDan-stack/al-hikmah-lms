<?php

use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorProbationTracking;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Session;
use App\Models\Student;
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

test('admin can terminate probation with student handover to substitute mentor', function () {
    $substituteUser = User::factory()->mentor()->create();
    $substitute = Mentor::factory()->create([
        'user_id' => $substituteUser->id,
        'is_active' => true,
        'status' => 'active',
    ]);

    $studentUser = User::factory()->create();
    $parentUser = User::factory()->create();
    $parent = ParentProfile::create([
        'user_id' => $parentUser->id,
        'phone' => '08123456789',
    ]);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'parent_id' => $parent->id,
        'full_name' => 'Santri Handover',
        'age' => 10,
        'gender' => 'L',
    ]);

    $this->mentor->students()->attach($student->id, [
        'day_assigned' => 'monday',
        'time_assigned' => '08:00',
        'is_active' => true,
    ]);

    $program = Program::factory()->create();

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'program_id' => $program->id,
        'mentor_id' => $this->mentor->id,
        'status' => 'active',
        'program_price' => 250000,
    ]);

    $session = Session::create([
        'student_id' => $student->id,
        'mentor_id' => $this->mentor->id,
        'date' => now()->addDays(2),
        'time' => '08:00:00',
        'method' => 'online',
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.mentors.probation.complete', $this->probation->id), [
            'decision' => 'terminated',
            'notes' => 'Tidak memenuhi standar kehadiran',
            'substitute_mentor_id' => $substitute->id,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('student_mutation_logs', [
        'student_id' => $student->id,
        'previous_mentor_id' => $this->mentor->id,
        'new_mentor_id' => $substitute->id,
        'reason_category' => 'probation_terminated',
    ]);

    $this->assertDatabaseHas('mentor_student', [
        'mentor_id' => $substitute->id,
        'student_id' => $student->id,
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('mentor_student', [
        'mentor_id' => $this->mentor->id,
        'student_id' => $student->id,
        'is_active' => false,
    ]);

    expect($enrollment->fresh()->mentor_id)->toBe($substitute->id);
    expect($session->fresh()->mentor_id)->toBe($substitute->id);
});

test('admin can view probation index table with 4 orientation modules progress', function () {
    $this->probation->update([
        'orientation_modules_status' => [
            'mod1' => true,
            'mod2' => true,
            'mod3' => true,
            'mod4' => true,
        ],
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.mentors.probation.index'));

    $response->assertStatus(200);
    $response->assertSee('4 Modul Orientasi');
    $response->assertSee('4/4 Tuntas');
    $response->assertSee($this->mentor->getDisplayName());
});
