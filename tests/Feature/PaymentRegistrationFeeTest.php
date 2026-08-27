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

class PaymentRegistrationFeeTest extends TestCase
{
    use RefreshDatabase;

    private User $parentUser;

    private ParentProfile $parentProfile;

    private Student $student;

    private Program $program;

    private Mentor $mentor;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Pendamping']);

        // Users
        $this->adminUser = User::factory()->create(['role_id' => $adminRole->id]);

        $this->parentUser = User::factory()->create([
            'role_id' => $parentRole->id,
            'phone' => '08123456789',
        ]);
        $this->parentProfile = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'address' => 'Jl. Kebon Sirih No. 45, Jakarta',
            'emergency_phone' => '08123456789',
        ]);

        $studentUser = User::factory()->create(['name' => 'Ahmad Santri']);
        $this->student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $this->parentProfile->id,
            'full_name' => 'Ahmad Santri',
            'age' => 10,
            'gender' => 'L',
            'location' => 'Jakarta Selatan',
        ]);

        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $this->mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustadz Hafiz',
            'bio' => 'Ustadz Hafiz 30 Juz',
            'is_active' => true,
        ]);

        $this->program = Program::create([
            'name' => 'Tahsin Dasar',
            'slug' => 'tahsin-dasar',
            'description' => 'Program membaca Al-Qur\'an',
            'price' => 450000,
            'is_active' => true,
        ]);
    }

    public function test_first_enrollment_invoice_includes_one_time_registration_fee(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_price' => $this->program->price,
            'requested_days' => ['monday', 'wednesday'],
            'requested_time' => '16:00',
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        // Admin menyetujui
        $response = $this->actingAs($this->adminUser)->post(route('admin.enrollments.accept', $enrollment->id), [
            'mentor_id' => $this->mentor->id,
            'start_date' => now()->addDays(2)->toDateString(),
        ]);

        $response->assertRedirect(route('admin.enrollments.index'));

        $payment = Payment::where('enrollment_id', $enrollment->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(450000, $payment->program_fee);
        $this->assertEquals(150000, $payment->registration_fee);
        $this->assertEquals(600000, $payment->amount);
    }

    public function test_subsequent_enrollment_does_not_charge_registration_fee_again(): void
    {
        // Pendaftaran 1 (sudah bayar dan lunas)
        $payment1 = Payment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_fee' => 450000,
            'registration_fee' => 150000,
            'amount' => 600000,
            'status' => 'paid',
            'invoice_number' => 'INV-TEST-PAID-001',
        ]);

        $this->assertTrue($this->student->hasPaidRegistrationFee());

        // Pendaftaran 2 (program baru)
        $program2 = Program::create([
            'name' => 'Tahfidz Juz 30',
            'slug' => 'tahfidz-juz-30',
            'price' => 500000,
            'is_active' => true,
        ]);

        $enrollment2 = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $program2->id,
            'program_price' => $program2->price,
            'requested_days' => ['tuesday', 'thursday'],
            'requested_time' => '16:00',
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        $this->actingAs($this->adminUser)->post(route('admin.enrollments.accept', $enrollment2->id), [
            'mentor_id' => $this->mentor->id,
            'start_date' => now()->addDays(2)->toDateString(),
        ]);

        $payment2 = Payment::where('enrollment_id', $enrollment2->id)->first();
        $this->assertNotNull($payment2);
        $this->assertEquals(500000, $payment2->program_fee);
        $this->assertEquals(0, $payment2->registration_fee);
        $this->assertEquals(500000, $payment2->amount);
    }

    public function test_paying_invoice_triggers_auto_activation_and_pivot_relationships(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_price' => $this->program->price,
            'mentor_id' => $this->mentor->id,
            'requested_days' => ['monday'],
            'requested_time' => '16:00',
            'status' => EnrollmentStatus::CONFIRMED,
        ]);

        $payment = Payment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'enrollment_id' => $enrollment->id,
            'program_fee' => 450000,
            'registration_fee' => 150000,
            'amount' => 600000,
            'status' => 'pending',
            'invoice_number' => 'INV-TEST-001',
        ]);

        // Parent inisialisasi bayar online via Pakasir
        $response = $this->actingAs($this->parentUser)->post(route('parent.payments.pay', $payment->id), [
            'payment_method' => 'qris',
        ]);

        $response->assertRedirect(route('parent.payments.show', $payment->id));

        // Simulasi Webhook Pakasir callback
        $webhookResponse = $this->postJson(route('api.webhook.pakasir'), [
            'order_id' => $payment->invoice_number,
            'amount' => 604200,
            'status' => 'completed',
            'payment_method' => 'qris',
        ]);
        $webhookResponse->assertStatus(200);

        $enrollment->refresh();
        $this->assertEquals(EnrollmentStatus::ACTIVE, $enrollment->status);
        $this->assertNotNull($enrollment->paid_at);

        // Verifikasi pivot student_program & mentor_student
        $this->assertTrue($this->student->programs()->where('program_id', $this->program->id)->exists());
        $this->assertTrue($this->mentor->students()->where('student_id', $this->student->id)->exists());
    }

    public function test_admin_active_enrollments_view(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_price' => $this->program->price,
            'mentor_id' => $this->mentor->id,
            'requested_days' => ['monday'],
            'requested_time' => '16:00',
            'status' => EnrollmentStatus::ACTIVE,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.active-enrollments.index'));
        $response->assertOk();
        $response->assertSee('Ahmad Santri');
        $response->assertSee('Tahsin Dasar');
    }
}
