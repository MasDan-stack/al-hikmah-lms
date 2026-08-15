<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentNegotiationTest extends TestCase
{
    use RefreshDatabase;

    private User $parentUser;

    private ParentProfile $parentProfile;

    private Student $student;

    private Program $program;

    private User $adminUser;

    private Mentor $mentor;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Pendamping']);

        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'admin@alhikmah.com',
        ]);

        $this->parentUser = User::factory()->create([
            'role_id' => $parentRole->id,
            'name' => 'Bunda Anisa',
        ]);

        $this->parentProfile = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'emergency_phone' => '08123456789',
        ]);

        $studentUser = User::factory()->create([
            'name' => 'Fathan Ahmad',
        ]);

        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $this->parentProfile->id,
            'full_name' => 'Fathan Ahmad',
            'age' => 12,
            'gender' => 'L',
        ]);

        $this->program = Program::create([
            'name' => 'Tahsin & Tahfidz Intensif',
            'category' => 'tahfidz',
            'price' => 500000,
            'is_active' => true,
        ]);

        $mentorUser = User::factory()->create([
            'role_id' => $mentorRole->id,
            'name' => 'Ustadz Abdullah',
        ]);

        $this->mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustadz Abdullah',
            'is_active' => true,
            'default_max_students_per_day' => 5,
        ]);
    }

    public function test_parent_can_view_enrollment_form_and_submit_request(): void
    {
        $response = $this->actingAs($this->parentUser)->get(route('parent.enrollments.create', ['program_id' => $this->program->id]));
        $response->assertStatus(200);
        $response->assertSee('Tahsin & Tahfidz Intensif');

        $submitData = [
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'requested_days' => ['monday', 'wednesday'],
            'requested_time' => '15:30',
            'parent_notes' => 'Preferensi mentor Ustadz Abdullah',
        ];

        $postResponse = $this->actingAs($this->parentUser)->post(route('parent.enrollments.store'), $submitData);

        $enrollment = Enrollment::first();
        $this->assertNotNull($enrollment);
        $this->assertEquals($this->student->id, $enrollment->student_id);
        $this->assertEquals(500000, $enrollment->program_price);
        $this->assertEquals(EnrollmentStatus::WAITING_ADMIN, $enrollment->status);

        $postResponse->assertRedirect(route('parent.enrollments.show', $enrollment->id));
    }

    public function test_admin_can_view_enrollments_index_and_accept_schedule(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_price' => 500000,
            'requested_days' => ['monday', 'wednesday'],
            'requested_time' => '15:30:00',
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        $indexResponse = $this->actingAs($this->adminUser)->get(route('admin.enrollments.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Fathan Ahmad');

        $acceptData = [
            'mentor_id' => $this->mentor->id,
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'admin_notes' => 'Jadwal disetujui, mentor siap.',
        ];

        $acceptResponse = $this->actingAs($this->adminUser)->post(route('admin.enrollments.accept', $enrollment->id), $acceptData);
        $acceptResponse->assertRedirect(route('admin.enrollments.index'));

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::CONFIRMED, $enrollment->status);
        $this->assertEquals($this->mentor->id, $enrollment->mentor_id);

        $payment = Payment::where('enrollment_id', $enrollment->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(500000, $payment->program_fee);
        $this->assertEquals(150000, $payment->registration_fee);
        $this->assertEquals(650000, $payment->amount);
        $this->assertEquals('registration', $payment->payment_purpose);

    }

    public function test_admin_can_offer_alternative_and_parent_can_accept_or_reject(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_price' => 500000,
            'requested_days' => ['monday'],
            'requested_time' => '15:00:00',
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        // Admin offer alternative
        $offerData = [
            'mentor_id' => $this->mentor->id,
            'offered_days' => ['tuesday', 'thursday'],
            'offered_time' => '16:00',
            'admin_notes' => 'Hari Senin penuh, kami tawarkan Selasa & Kamis.',
        ];

        $offerResponse = $this->actingAs($this->adminUser)->post(route('admin.enrollments.offer', $enrollment->id), $offerData);
        $offerResponse->assertRedirect(route('admin.enrollments.index'));

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::WAITING_PARENT, $enrollment->status);

        // Parent accepts offer
        $acceptOfferResponse = $this->actingAs($this->parentUser)->post(route('parent.enrollments.accept-offer', $enrollment->id));
        $acceptOfferResponse->assertRedirect(route('parent.enrollments.show', $enrollment->id));

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::CONFIRMED, $enrollment->status);

        $payment = Payment::where('enrollment_id', $enrollment->id)->first();
        $this->assertNotNull($payment);
    }
}
