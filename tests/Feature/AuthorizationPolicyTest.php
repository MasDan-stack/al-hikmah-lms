<?php

use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

function createTestParentWithStudent(bool $withPaid = true): array
{
    $parentUser = User::factory()->parent()->create();
    $parent = ParentProfile::create([
        'user_id' => $parentUser->id,
        'address' => 'Jl. Kebajikan No. 10',
        'emergency_phone' => '081234567890',
    ]);

    $studentUser = User::factory()->student()->create();
    $student = Student::create([
        'user_id' => $studentUser->id,
        'parent_id' => $parent->id,
        'full_name' => 'Santri '.$studentUser->name,
        'age' => 11,
        'gender' => 'L',
    ]);

    if ($withPaid) {
        $program = Program::first() ?? Program::factory()->create();
        Payment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'amount' => 500000,
            'status' => 'paid',
            'invoice_number' => 'INV-PAID-'.uniqid(),
        ]);
    }

    return [$parentUser, $parent, $student];
}

test('parent can view and export report of their own child', function () {
    [$parentUser, $parent, $student] = createTestParentWithStudent();

    $this->actingAs($parentUser)
        ->get(route('parent.children.show', $student))
        ->assertOk();

    $this->actingAs($parentUser)
        ->get(route('parent.children.report', $student))
        ->assertOk();
});

test('parent is forbidden from viewing another parents child (Anti-IDOR)', function () {
    [$parentUserA, $parentA, $studentA] = createTestParentWithStudent();
    [$parentUserB, $parentB, $studentB] = createTestParentWithStudent();

    // Parent A attempts to access Parent B's child
    $this->actingAs($parentUserA)
        ->get(route('parent.children.show', $studentB))
        ->assertForbidden();

    $this->actingAs($parentUserA)
        ->get(route('parent.children.report', $studentB))
        ->assertForbidden();

    $this->actingAs($parentUserA)
        ->post(route('parent.children.reset-password', $studentB))
        ->assertForbidden();
});

test('parent cannot enroll tahfidz for unowned student ID', function () {
    [$parentUserA, $parentA, $studentA] = createTestParentWithStudent();
    [$parentUserB, $parentB, $studentB] = createTestParentWithStudent();

    $this->actingAs($parentUserA)
        ->post(route('parent.enroll-tahfidz'), [
            'student_id' => (string) $studentB->id,
            'target_tahfidz' => 'Juz 30',
        ])
        ->assertSessionHasErrors(['student_id']);
});

test('mentor can view assigned student but is forbidden from unassigned student (Anti-IDOR)', function () {
    $mentorUserA = User::factory()->mentor()->create();
    $mentorA = Mentor::factory()->create(['user_id' => $mentorUserA->id]);

    [$parentUser, $parent, $assignedStudent] = createTestParentWithStudent();
    [$parentUser2, $parent2, $unassignedStudent] = createTestParentWithStudent();

    // Assign student to Mentor A
    $assignedStudent->mentors()->attach($mentorA->id, ['is_active' => true]);

    // Mentor A views assigned student -> 200 OK
    $this->actingAs($mentorUserA)
        ->get(route('mentor.students.show', $assignedStudent))
        ->assertOk();

    // Mentor A views unassigned student -> 403 Forbidden
    $this->actingAs($mentorUserA)
        ->get(route('mentor.students.show', $unassignedStudent))
        ->assertForbidden();
});

test('mentor can view own salary slip but non-admin cannot access admin salary management', function () {
    $mentorUserA = User::factory()->mentor()->create();
    $mentorA = Mentor::factory()->create(['user_id' => $mentorUserA->id]);

    $mentorUserB = User::factory()->mentor()->create();
    $mentorB = Mentor::factory()->create(['user_id' => $mentorUserB->id]);

    // Mentor A views their own salary slip print view
    $this->actingAs($mentorUserA)
        ->get(route('mentor.salary-slip.print'))
        ->assertOk();

    // Mentor A cannot access admin salary slip print for Mentor B
    $this->actingAs($mentorUserA)
        ->get(route('admin.staff.salary-slip.print', $mentorB))
        ->assertForbidden();

    // Mentor A cannot mark salary as paid
    $this->actingAs($mentorUserA)
        ->post(route('admin.staff.mark-salary-paid', $mentorB), [
            'year' => 2026,
            'month' => 9,
            'status' => 'paid',
        ])
        ->assertForbidden();
});

test('admin can view any student and manage any mentor salary slip', function () {
    $adminUser = User::factory()->admin()->create();
    $mentor = Mentor::factory()->create();
    [$parentUser, $parent, $student] = createTestParentWithStudent();

    // Admin bypasses policy checks on all students
    expect(Gate::forUser($adminUser)->allows('view', $student))->toBeTrue();
    expect(Gate::forUser($adminUser)->allows('update', $student))->toBeTrue();
    expect(Gate::forUser($adminUser)->allows('viewSalarySlip', $mentor))->toBeTrue();

    // Admin prints mentor salary slip
    $this->actingAs($adminUser)
        ->get(route('admin.staff.salary-slip.print', $mentor))
        ->assertOk();

    // Admin marks salary as paid
    $this->actingAs($adminUser)
        ->post(route('admin.staff.mark-salary-paid', $mentor), [
            'year' => 2026,
            'month' => 9,
            'status' => 'paid',
        ])
        ->assertOk()
        ->assertJson(['success' => true]);
});

test('parent can view own invoice and is forbidden from viewing another parents invoice', function () {
    [$parentUserA, $parentA, $studentA] = createTestParentWithStudent();
    [$parentUserB, $parentB, $studentB] = createTestParentWithStudent();

    $program = Program::factory()->create();

    $paymentA = Payment::create([
        'student_id' => $studentA->id,
        'program_id' => $program->id,
        'amount' => 500000,
        'status' => 'pending',
        'invoice_number' => 'INV-TEST-A-001',
    ]);

    $paymentB = Payment::create([
        'student_id' => $studentB->id,
        'program_id' => $program->id,
        'amount' => 500000,
        'status' => 'pending',
        'invoice_number' => 'INV-TEST-B-001',
    ]);

    // Parent A views own payment -> 200 OK
    $this->actingAs($parentUserA)
        ->get(route('parent.payments.show', $paymentA))
        ->assertOk();

    // Parent A attempts to view Parent B's payment -> 403 Forbidden
    $this->actingAs($parentUserA)
        ->get(route('parent.payments.show', $paymentB))
        ->assertForbidden();

    // Parent A attempts to download Parent B's invoice -> 403 Forbidden
    $this->actingAs($parentUserA)
        ->get(route('parent.payments.download', $paymentB))
        ->assertForbidden();
});
