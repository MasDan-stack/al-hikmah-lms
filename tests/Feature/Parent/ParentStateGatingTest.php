<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Setup Parent User
    $this->user = User::factory()->parent()->create();

    $this->parentProfile = ParentProfile::create([
        'user_id' => $this->user->id,
        'phone_number' => '08123456789',
        'address' => 'Jl. Test',
    ]);

    $studentUser = User::factory()->student()->create();

    $this->student = Student::create([
        'user_id' => $studentUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Santri Test',
        'gender' => 'L',
        'birth_place' => 'Jakarta',
        'birth_date' => '2010-01-01',
        'age' => 10,
    ]);

    $this->program = Program::create([
        'name' => 'Program Test',
        'price' => 500000,
        'description' => 'Test',
    ]);
});

it('redirects to dashboard when state 1 (onboarding) parent accesses protected routes', function () {
    // State 1: No enrollment at all

    $this->actingAs($this->user);

    // Boleh akses dashboard
    $response = $this->get('/parent/dashboard');
    $response->assertOk();

    // Tidak boleh akses route yang diprotect (misalnya /parent/children) - Diasumsikan redirect ke /parent/dashboard
    $response = $this->get('/parent/children');
    $response->assertRedirect('/parent/dashboard');
});

it('redirects to dashboard when state 2 (transisi) parent accesses protected routes', function () {
    // State 2: WAITING_ADMIN enrollment
    Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'status' => EnrollmentStatus::WAITING_ADMIN,
        'requested_days' => ['monday'],
    ]);

    $this->actingAs($this->user);

    // Boleh akses dashboard
    $response = $this->get('/parent/dashboard');
    $response->assertOk();

    // Boleh akses enrollments dan payments
    $response = $this->get('/parent/enrollments');
    $response->assertOk();

    $response = $this->get('/parent/payments');
    $response->assertOk();

    // Tidak boleh akses route yang diprotect
    $response = $this->get('/parent/children');
    $response->assertRedirect('/parent/dashboard');
});

it('allows access to protected routes when state 3 (active) parent accesses them', function () {
    // State 3: ACTIVE enrollment and PAID payment
    $enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'status' => EnrollmentStatus::ACTIVE,
        'requested_days' => ['monday'],
    ]);

    Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'enrollment_id' => $enrollment->id,
        'status' => 'paid',
        'amount' => 500000,
        'invoice_number' => 'INV-TEST-1',
    ]);

    $this->actingAs($this->user);

    // Boleh akses dashboard
    $response = $this->get('/parent/dashboard');
    $response->assertOk();

    // Boleh akses route yang diprotect
    $response = $this->get('/parent/children');
    $response->assertOk();
});

it('handles memoization correctly so repeated calls do not query database multiple times', function () {
    $enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'status' => EnrollmentStatus::ACTIVE,
        'requested_days' => ['monday'],
    ]);

    Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'enrollment_id' => $enrollment->id,
        'status' => 'paid',
        'amount' => 500000,
        'invoice_number' => 'INV-TEST-2',
    ]);

    // Refresh user to clear relations
    $user = $this->user->fresh();

    // Cek jumlah query
    DB::enableQueryLog();

    $result1 = $user->hasActivePaidProgram();
    $queries1 = count(DB::getQueryLog());

    DB::flushQueryLog();

    $result2 = $user->hasActivePaidProgram();
    $queries2 = count(DB::getQueryLog());

    expect($result1)->toBeTrue();
    expect($result2)->toBeTrue();
    expect($queries2)->toBe(0); // Memastikan memoization bekerja
});
