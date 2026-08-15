<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Session;
use App\Models\SessionConfirmation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorSessionAndAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $parentUser;

    protected ParentProfile $parentProfile;

    protected User $studentUser;

    protected Student $student;

    protected User $mentorUser;

    protected Mentor $mentor;

    protected Program $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parentUser = User::factory()->parent()->create(['name' => 'Bunda Fatimah', 'phone' => '081299998888']);
        $this->parentProfile = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'emergency_phone' => '081299998888',
            'address' => 'Bandung',
        ]);

        $this->studentUser = User::factory()->student()->create(['name' => 'Ahmad Santri']);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'parent_id' => $this->parentProfile->id,
            'full_name' => 'Ahmad Santri',
            'age' => 12,
            'gender' => 'L',
            'location' => 'Bandung',
        ]);

        $this->mentorUser = User::factory()->mentor()->create(['name' => 'Ustaz Abdullah']);
        $this->mentor = Mentor::create([
            'user_id' => $this->mentorUser->id,
            'full_name' => 'Ustaz Abdullah',
            'is_active' => true,
        ]);

        $this->program = Program::create([
            'name' => 'Tahsin Intensif',
            'slug' => 'tahsin-intensif',
            'price' => 350000,
            'is_active' => true,
        ]);
    }

    public function test_mentor_students_list_only_shows_paid_active_students(): void
    {
        // Enrollment status CONFIRMED (belum bayar)
        $unpaidEnrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'mentor_id' => $this->mentor->id,
            'status' => EnrollmentStatus::CONFIRMED,
        ]);

        $response = $this->actingAs($this->mentorUser)->get(route('mentor.students.index'));
        $response->assertStatus(200);
        $response->assertDontSee('Ahmad Santri');

        // Setelah lunas & ACTIVE
        $unpaidEnrollment->markAsPaidAndActive();

        $responseActive = $this->actingAs($this->mentorUser)->get(route('mentor.students.index'));
        $responseActive->assertStatus(200);
        $responseActive->assertSee('Ahmad Santri');
    }

    public function test_initial_session_generator_uses_parent_selected_learning_method(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'mentor_id' => $this->mentor->id,
            'learning_method' => 'offline',
            'requested_days' => ['monday'],
            'requested_time' => '16:00',
            'status' => EnrollmentStatus::CONFIRMED,
        ]);

        $enrollment->markAsPaidAndActive();

        $this->assertDatabaseHas('learning_sessions', [
            'student_id' => $this->student->id,
            'mentor_id' => $this->mentor->id,
            'method' => 'offline',
        ]);
    }

    public function test_parent_session_confirmation_dispatches_notification_to_mentor(): void
    {
        $session = Session::create([
            'student_id' => $this->student->id,
            'mentor_id' => $this->mentor->id,
            'date' => now()->toDateString(),
            'time' => '16:00:00',
            'method' => 'offline',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->parentUser)->post(route('parent.schedules.confirm', $session->id), [
            'status' => 'izin',
            'notes' => 'Ada acara keluarga',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('session_confirmations', [
            'session_id' => $session->id,
            'parent_id' => $this->parentProfile->id,
            'status' => 'izin',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->mentorUser->id,
            'category' => 'attendance',
            'type' => 'warning',
        ]);
    }

    public function test_mentor_dashboard_displays_student_attendance_confirmation_badge(): void
    {
        $session = Session::create([
            'student_id' => $this->student->id,
            'mentor_id' => $this->mentor->id,
            'date' => now()->toDateString(),
            'time' => '16:00:00',
            'method' => 'offline',
            'status' => 'scheduled',
        ]);

        SessionConfirmation::create([
            'session_id' => $session->id,
            'parent_id' => $this->parentProfile->id,
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->mentorUser)->get(route('mentor.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Hadir');
        $response->assertSee('Offline');
    }
}
