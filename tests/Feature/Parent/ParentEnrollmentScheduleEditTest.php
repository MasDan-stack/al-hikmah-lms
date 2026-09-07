<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Parent']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Student']);

    $this->parentUser = User::factory()->create([
        'name' => 'Bpk Wali Santri',
        'role_id' => $this->parentRole->id,
    ]);
    $this->parentProfile = ParentProfile::create([
        'user_id' => $this->parentUser->id,
        'address' => 'Jl. Merdeka No 1',
        'emergency_phone' => '08123456789',
    ]);

    $this->childUser = User::factory()->create(['role_id' => $this->studentRole->id]);
    $this->student = Student::create([
        'user_id' => $this->childUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Rayhan Santri',
        'age' => 9,
        'gender' => 'L',
    ]);

    $this->program = Program::create([
        'name' => 'Tahsin Privat',
        'category' => 'Tahsin',
        'price' => 350000,
        'level' => 'Pemula',
        'duration_weeks' => 12,
        'is_active' => true,
    ]);
});

test('parent can view edit schedule page before payment is completed', function () {
    $enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'program_price' => 350000,
        'learning_method' => 'offline',
        'requested_days' => ['monday', 'wednesday'],
        'requested_time' => '16:00',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $response = $this->actingAs($this->parentUser)->get(route('parent.enrollments.edit', $enrollment->id));

    $response->assertOk();
    $response->assertSee('Ubah Preferensi Hari & Jam');
    $response->assertSee('hanya dapat dilakukan sebelum pembayaran diselesaikan');
});

test('parent can update schedule days and time before payment', function () {
    $enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'program_price' => 350000,
        'learning_method' => 'offline',
        'requested_days' => ['monday', 'wednesday'],
        'requested_time' => '16:00',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $response = $this->actingAs($this->parentUser)->put(route('parent.enrollments.update', $enrollment->id), [
        'learning_method' => 'online',
        'requested_days' => ['tuesday', 'thursday'],
        'requested_time' => '18:30',
        'parent_notes' => 'Tolong ustadz yang sabar',
    ]);

    $response->assertRedirect(route('parent.enrollments.show', $enrollment->id));
    $response->assertSessionHas('success');

    $enrollment->refresh();
    expect($enrollment->learning_method)->toBe('online');
    expect($enrollment->requested_days)->toBe(['tuesday', 'thursday']);
    expect(substr((string) $enrollment->requested_time, 0, 5))->toBe('18:30');
    expect($enrollment->parent_notes)->toBe('Tolong ustadz yang sabar');
});

test('parent cannot edit schedule after payment is paid or class is active', function () {
    $enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'program_price' => 350000,
        'learning_method' => 'offline',
        'requested_days' => ['monday'],
        'requested_time' => '16:00',
        'status' => EnrollmentStatus::ACTIVE,
    ]);

    Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'enrollment_id' => $enrollment->id,
        'amount' => 350000,
        'status' => 'paid',
        'payment_purpose' => 'registration',
        'invoice_number' => 'INV-TEST-001',
    ]);

    // Akses GET edit ditolak
    $responseGet = $this->actingAs($this->parentUser)->get(route('parent.enrollments.edit', $enrollment->id));
    $responseGet->assertRedirect(route('parent.enrollments.show', $enrollment->id));
    $responseGet->assertSessionHas('error');

    // Akses PUT update ditolak
    $responsePut = $this->actingAs($this->parentUser)->put(route('parent.enrollments.update', $enrollment->id), [
        'learning_method' => 'online',
        'requested_days' => ['friday'],
        'requested_time' => '10:00',
    ]);
    $responsePut->assertRedirect(route('parent.enrollments.show', $enrollment->id));
    $responsePut->assertSessionHas('error');

    $enrollment->refresh();
    expect($enrollment->requested_days)->toBe(['monday']); // Tetap monday, tidak berubah
});
