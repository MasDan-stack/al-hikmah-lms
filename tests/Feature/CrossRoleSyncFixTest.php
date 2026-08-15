<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossRoleSyncFixTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

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

        // 1. Setup Admin
        $this->admin = User::factory()->admin()->create();

        // 2. Setup Parent & Student
        $this->parentUser = User::factory()->parent()->create(['phone' => '08123456789']);
        $this->parentProfile = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'emergency_phone' => '08123456789',
            'address' => 'Jl. Al-Hikmah No. 123',
        ]);

        $this->studentUser = User::factory()->student()->create(['name' => 'Ahmad Santri']);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'parent_id' => $this->parentProfile->id,
            'full_name' => 'Ahmad Santri',
            'age' => 10,
            'gender' => 'L',
            'location' => 'Bandung',
        ]);

        // 3. Setup Mentor
        $this->mentorUser = User::factory()->mentor()->create(['name' => 'Ustadz Abdullah']);
        $this->mentor = Mentor::create([
            'user_id' => $this->mentorUser->id,
            'full_name' => 'Ustadz Abdullah',
            'specialization' => 'Tahsin & Tahfizh',
            'default_max_students_per_day' => 5,
            'is_active' => true,
        ]);

        // 4. Setup Program
        $this->program = Program::create([
            'name' => 'Tahsin Anak Reguler',
            'description' => 'Program Bimbingan Tahsin',
            'price' => 400000,
            'duration' => '1 Bulan',
            'is_active' => true,
        ]);
    }

    public function test_admin_accept_enrollment_sets_status_confirmed_creates_payment_and_syncs_pivot(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'requested_days' => ['monday', 'thursday'],
            'requested_time' => '16:00:00',
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.enrollments.accept', $enrollment->id), [
            'mentor_id' => $this->mentor->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'admin_notes' => 'Jadwal disetujui.',
        ]);

        $response->assertRedirect(route('admin.enrollments.index'));
        $enrollment->refresh();

        $this->assertEquals(EnrollmentStatus::CONFIRMED, $enrollment->status);
        $this->assertEquals($this->mentor->id, $enrollment->mentor_id);
        $this->assertNotNull($enrollment->confirmed_at);

        // Verification: Payment invoice created
        $this->assertDatabaseHas('payments', [
            'enrollment_id' => $enrollment->id,
            'student_id' => $this->student->id,
            'status' => 'pending',
        ]);

        // Verification: mentor_student pivot synced per day
        $this->assertDatabaseHas('mentor_student', [
            'mentor_id' => $this->mentor->id,
            'student_id' => $this->student->id,
            'day_assigned' => 'monday',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('mentor_student', [
            'mentor_id' => $this->mentor->id,
            'student_id' => $this->student->id,
            'day_assigned' => 'thursday',
            'is_active' => true,
        ]);
    }

    public function test_mark_as_paid_and_active_is_idempotent_and_generates_sessions(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'mentor_id' => $this->mentor->id,
            'requested_days' => ['monday'],
            'requested_time' => '16:00:00',
            'status' => EnrollmentStatus::CONFIRMED,
            'start_date' => now()->toDateString(),
        ]);

        $payment = Payment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'enrollment_id' => $enrollment->id,
            'amount' => 550000,
            'status' => 'pending',
            'invoice_number' => 'INV-TEST-001',
        ]);

        // Trigger payment completion
        $enrollment->markAsPaidAndActive();

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::ACTIVE, $enrollment->status);
        $this->assertNotNull($enrollment->paid_at);

        // Verify sessions generated (4 weeks for 1 day = 4 sessions)
        $this->assertEquals(4, Session::where('student_id', $this->student->id)->where('mentor_id', $this->mentor->id)->count());

        // Idempotency check: trigger again
        $enrollment->markAsPaidAndActive();
        $this->assertEquals(4, Session::where('student_id', $this->student->id)->where('mentor_id', $this->mentor->id)->count());
    }

    public function test_export_enrollments_returns_streamed_csv(): void
    {
        Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'mentor_id' => $this->mentor->id,
            'requested_days' => ['monday'],
            'requested_time' => '16:00:00',
            'status' => EnrollmentStatus::ACTIVE,
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.enrollments.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
