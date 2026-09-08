<?php

use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\MentorProbationTracking;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

    // 1. Parent User & Profile
    $this->parentUser = User::factory()->create([
        'name' => 'Bunda Fatimah',
        'role_id' => $this->parentRole->id,
    ]);
    $this->parentProfile = ParentProfile::create([
        'user_id' => $this->parentUser->id,
        'father_name' => 'Ayah Fatimah',
        'mother_name' => 'Bunda Fatimah',
        'phone' => '081299998888',
    ]);

    // 2. Mentor User & Profile (on probation)
    $this->mentorUser = User::factory()->create([
        'name' => 'Ust. Luqman Hakim',
        'role_id' => $this->mentorRole->id,
    ]);
    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ust. Luqman Hakim',
        'status' => 'probation',
        'is_active' => true,
        'rating' => 5.00,
    ]);
    $this->probation = MentorProbationTracking::create([
        'mentor_id' => $this->mentor->id,
        'start_date' => today(),
        'end_date' => today()->addMonths(3),
        'duration_months' => 3,
        'status' => 'active',
        'average_rating' => 5.00,
    ]);

    // 3. Student
    $this->studentUser = User::factory()->create([
        'name' => 'Ananda Faris',
        'role_id' => $this->studentRole->id,
    ]);
    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Ananda Faris',
        'age' => 9,
        'gender' => 'L',
    ]);

    // Program & Active Enrollment so $hasPaidProgram = true
    $this->program = Program::firstOrCreate(
        ['name' => 'Tahsin Anak'],
        ['slug' => 'tahsin-anak', 'price' => 250000, 'duration_months' => 3, 'is_active' => true]
    );

    Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'mentor_id' => $this->mentor->id,
        'status' => 'active',
        'payment_status' => 'paid',
    ]);

    // 4. Completed Session without Feedback
    $this->session = Session::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'title' => 'Sesi Tajwid Nun Mati & Tanwin',
        'date' => today()->subDay()->toDateString(),
        'time' => '16:00:00',
        'method' => 'online',
        'status' => 'completed',
    ]);
});

test('parent dashboard displays pending feedback card for unreviewed completed session', function () {
    $response = $this->actingAs($this->parentUser)->get(route('parent.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Sesi Bimbingan Menunggu Ulasan');
    $response->assertSee('Ust. Luqman Hakim');
    $response->assertSee('Beri Nilai');
});

test('parent can submit multi-category feedback synchronizing mentor rating and probation average', function () {
    $response = $this->actingAs($this->parentUser)
        ->post(route('parent.feedbacks.store'), [
            'session_id' => $this->session->id,
            'mentor_id' => $this->mentor->id,
            'student_id' => $this->student->id,
            'overall_rating' => 5,
            'rating_communication' => 5,
            'rating_punctuality' => 4,
            'rating_teaching_method' => 5,
            'rating_child_progress' => 5,
            'quick_tags' => ['#SangatSabar', '#MakhrajJelas', '#TepatWaktu'],
            'comment' => 'MasyaAllah penyampaian materi sangat mudah dipahami anak kami.',
            'is_anonymous' => 1,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify feedback record
    $feedback = MentorFeedback::where('session_id', $this->session->id)->first();
    expect($feedback)->not->toBeNull();
    expect($feedback->overall_rating)->toBe(5);
    expect($feedback->is_anonymous)->toBeTrue();
    expect($feedback->quick_tags)->toContain('#SangatSabar');

    // Verify ratings relation
    $this->assertDatabaseHas('mentor_feedback_ratings', [
        'feedback_id' => $feedback->id,
        'category' => 'communication',
        'rating' => 5,
    ]);

    // Verify mentor rating updated
    $this->mentor->refresh();
    expect((float) $this->mentor->rating)->toBe(5.0);

    // Verify probation tracking average_rating updated
    $this->probation->refresh();
    expect((float) $this->probation->average_rating)->toBe(5.0);
});

test('reviewed session is removed from pending feedback list on parent dashboard', function () {
    // Create feedback for session
    MentorFeedback::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'parent_id' => $this->parentUser->id,
        'session_id' => $this->session->id,
        'overall_rating' => 5,
        'comment' => 'Sudah dinilai',
    ]);

    $response = $this->actingAs($this->parentUser)->get(route('parent.dashboard'));

    $response->assertStatus(200);
    // Banner should not be rendered since no pending feedback sessions exist
    $response->assertDontSee('Sesi Bimbingan Menunggu Ulasan');
});

test('low rating feedback updates mentor score and triggers low feedback record', function () {
    // Submit low rating feedback (2 stars)
    $response = $this->actingAs($this->parentUser)
        ->post(route('parent.feedbacks.store'), [
            'session_id' => $this->session->id,
            'mentor_id' => $this->mentor->id,
            'student_id' => $this->student->id,
            'overall_rating' => 2,
            'comment' => 'Guru terlambat 20 menit dan kurang interaktif.',
        ]);

    $response->assertRedirect();

    $this->mentor->refresh();
    expect((float) $this->mentor->rating)->toBe(2.0);

    $this->probation->refresh();
    expect((float) $this->probation->average_rating)->toBe(2.0);

    // Verify feedback exists with rating 2
    $this->assertDatabaseHas('mentor_feedback', [
        'mentor_id' => $this->mentor->id,
        'overall_rating' => 2,
    ]);
});

test('parent schedule list displays review button for unreviewed session and rating badge when feedback exists', function () {
    // 1. Visit schedule list before review
    $response = $this->actingAs($this->parentUser)->get(route('parent.schedules.list'));
    $response->assertOk();
    $response->assertSee('Beri Nilai');

    // 2. Add feedback
    MentorFeedback::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'parent_id' => $this->parentUser->id,
        'session_id' => $this->session->id,
        'overall_rating' => 5,
        'comment' => 'Bagus sekali pengajarannya.',
    ]);

    // 3. Visit schedule list after review
    $responseAfter = $this->actingAs($this->parentUser)->get(route('parent.schedules.list'));
    $responseAfter->assertOk();
    $responseAfter->assertSee('5.0');
    $responseAfter->assertDontSee('Beri Nilai');
});
