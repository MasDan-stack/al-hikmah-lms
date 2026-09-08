<?php

use App\Models\Mentor;
use App\Models\MentorFeedback;
use App\Models\Role;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

    $this->parentUser = User::factory()->create([
        'name' => 'Bunda Annisa',
        'role_id' => $this->parentRole->id,
    ]);

    $this->mentorUser = User::factory()->create([
        'name' => 'Ust. Zulkifli',
        'role_id' => $this->mentorRole->id,
    ]);

    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ust. Zulkifli',
        'is_active' => true,
    ]);

    $this->studentUser = User::factory()->create([
        'name' => 'Santri Rayyan',
        'role_id' => $this->studentRole->id,
    ]);
    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'full_name' => 'Santri Rayyan',
        'age' => 10,
        'gender' => 'L',
    ]);

    $this->session = Session::create([
        'mentor_id' => $this->mentor->id,
        'student_id' => $this->student->id,
        'title' => 'Bimbingan Tahsin Pekan 1',
        'date' => now()->toDateString(),
        'time' => '08:00:00',
        'method' => 'online',
        'status' => 'completed',
    ]);
});

test('parent can submit post session rating with quick chips and anonymous option', function () {
    $response = $this->actingAs($this->parentUser)->post(route('parent.feedbacks.store'), [
        'session_id' => $this->session->id,
        'mentor_id' => $this->mentor->id,
        'overall_rating' => 5,
        'categories' => [
            'teaching_quality' => 5,
            'patience' => 5,
            'punctuality' => 5,
        ],
        'quick_tags' => ['#SangatSabar', '#TepatWaktu', '#PenyampaianJelas'],
        'comment' => 'Ustaz sangat sabar meluruskan makhraj huruf santri.',
        'is_anonymous' => 1,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_feedback', [
        'mentor_id' => $this->mentor->id,
        'parent_id' => $this->parentUser->id,
        'session_id' => $this->session->id,
        'overall_rating' => 5,
        'is_anonymous' => 1,
    ]);

    $feedback = MentorFeedback::where('session_id', $this->session->id)->first();
    $this->assertNotNull($feedback);
    $this->assertContains('#SangatSabar', $feedback->quick_tags);
    $this->assertDatabaseHas('mentor_feedback_ratings', [
        'feedback_id' => $feedback->id,
        'category' => 'teaching_quality',
        'rating' => 5,
    ]);
});
