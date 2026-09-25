<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\PakasirService;

beforeEach(function () {
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->parentUser = User::factory()->create(['role_id' => $this->parentRole->id]);
    $this->parentProfile = ParentProfile::create(['user_id' => $this->parentUser->id]);

    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
    $this->studentUser = User::factory()->create(['role_id' => $this->studentRole->id]);
    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Rayhan Al-Ghifari',
        'age' => 10,
        'gender' => 'L',
        'nis' => 'NIS-PAKASIR-001',
    ]);

    $this->program = Program::create([
        'name' => 'Tahsin & Tajwid Eksekutif',
        'slug' => 'tahsin-tajwid-eksekutif',
        'price' => 300000,
        'is_active' => true,
    ]);
});

test('perhitungan biaya admin gateway pakasir service berjalan akurat', function () {
    $pakasirService = app(PakasirService::class);

    // QRIS Fee (0.7% dari Rp 300.000 = Rp 2.100)
    $qrisFee = $pakasirService->calculateFee(300000, 'qris');
    expect($qrisFee)->toBe(2100);

    // VA BCA Flat Fee (Rp 3.500)
    $vaFee = $pakasirService->calculateFee(300000, 'va_bca');
    expect($vaFee)->toBe(3500);
});

test('wali santri dapat melihat invoice detail dengan metode pembayaran pakasir', function () {
    $payment = Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'amount' => 300000,
        'invoice_number' => 'INV-PAKASIR-101',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->parentUser)->get(route('parent.payments.show', $payment->id));
    $response->assertStatus(200);
    $response->assertSee('INV-PAKASIR-101');
    $response->assertSee('QRIS Instant');
    $response->assertSee('BCA Virtual Account');
});

test('wali santri dapat menginisiasi pembayaran qris dan mendapatkan instruksi pembayaran', function () {
    $payment = Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'amount' => 300000,
        'invoice_number' => 'INV-PAKASIR-102',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->parentUser)->post(route('parent.payments.pay', $payment->id), [
        'payment_method' => 'qris',
    ]);

    $response->assertRedirect(route('parent.payments.show', $payment->id));

    $payment->refresh();
    expect($payment->pakasir_order_id)->toBe('INV-PAKASIR-102');
    expect((int) $payment->admin_fee)->toBe(2100);
    expect((int) $payment->total_amount)->toBe(302100);
    expect($payment->qr_content)->not->toBeNull();
});

test('endpoint polling status pembayaran realtime berfungsi dengan benar', function () {
    $payment = Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'amount' => 300000,
        'invoice_number' => 'INV-PAKASIR-103',
        'status' => 'pending',
        'pakasir_order_id' => 'INV-PAKASIR-103',
    ]);

    // Saat masih pending
    $responsePending = $this->actingAs($this->parentUser)->getJson(route('parent.payments.status', $payment->id));
    $responsePending->assertStatus(200);
    $responsePending->assertJson([
        'is_paid' => false,
        'status' => 'pending',
    ]);

    // Saat sudah lunas
    $payment->update(['status' => 'paid', 'payment_date' => now()]);
    $responsePaid = $this->actingAs($this->parentUser)->getJson(route('parent.payments.status', $payment->id));
    $responsePaid->assertStatus(200);
    $responsePaid->assertJson([
        'is_paid' => true,
        'status' => 'paid',
    ]);
});

test('wali santri dapat membatalkan transaksi aktif untuk mengganti metode pembayaran', function () {
    $payment = Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'amount' => 300000,
        'invoice_number' => 'INV-PAKASIR-104',
        'status' => 'pending',
        'pakasir_order_id' => 'INV-PAKASIR-104',
        'admin_fee' => 3500,
        'total_amount' => 303500,
    ]);

    $response = $this->actingAs($this->parentUser)->post(route('parent.payments.cancel', $payment->id));
    $response->assertRedirect(route('parent.payments.show', $payment->id));

    $payment->refresh();
    expect($payment->pakasir_order_id)->toBeNull();
    expect((int) $payment->admin_fee)->toBe(0);
});

test('webhook pakasir memproses pembayaran lunas dan mengaktifkan kelas santri', function () {
    $enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'status' => EnrollmentStatus::CONFIRMED->value,
    ]);

    $payment = Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'enrollment_id' => $enrollment->id,
        'amount' => 300000,
        'invoice_number' => 'INV-PAKASIR-105',
        'status' => 'pending',
        'pakasir_order_id' => 'INV-PAKASIR-105',
    ]);

    $webhookPayload = [
        'order_id' => 'INV-PAKASIR-105',
        'amount' => 302100,
        'status' => 'completed',
        'payment_method' => 'qris',
    ];

    $response = $this->postJson(route('api.webhook.pakasir'), $webhookPayload);
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'status' => 'success',
    ]);

    $payment->refresh();
    expect($payment->status)->toBe('paid');
    expect($payment->payment_date)->not->toBeNull();

    $enrollment->refresh();
    expect($enrollment->status)->toBe(EnrollmentStatus::ACTIVE);
    expect($enrollment->paid_at)->not->toBeNull();
});

test('webhook pakasir memiliki proteksi idempotency ketika dipanggil berulang', function () {
    $payment = Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'amount' => 300000,
        'invoice_number' => 'INV-PAKASIR-106',
        'status' => 'paid',
        'payment_date' => now(),
    ]);

    $response = $this->postJson(route('api.webhook.pakasir'), [
        'order_id' => 'INV-PAKASIR-106',
        'amount' => 300000,
        'status' => 'completed',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Payment already processed and marked as PAID',
    ]);
});
