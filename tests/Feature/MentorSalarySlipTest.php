<?php

use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Session;
use App\Models\SessionConfirmation;
use App\Models\Student;
use App\Models\User;
use App\Services\RevenueAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Setup helpers ────────────────────────────────────────────────────────────

function makeMentorWithUser(): Mentor
{
    return Mentor::factory()->create();
}

/**
 * @return array{0: Student, 1: ParentProfile}
 */
function createStudentAndParent(): array
{
    $parentUser = User::factory()->parent()->create();
    $parent = ParentProfile::create(['user_id' => $parentUser->id]);

    $studentUser = User::factory()->student()->create();
    $student = Student::create([
        'user_id' => $studentUser->id,
        'parent_id' => $parent->id,
        'full_name' => $studentUser->name,
        'age' => 10,
        'gender' => 'L',
    ]);

    return [$student, $parent];
}

function createSessionWithConfirmation(Mentor $mentor, Student $student, ParentProfile $parent, string $status, ?string $date = null): Session
{
    $session = Session::create([
        'mentor_id' => $mentor->id,
        'student_id' => $student->id,
        'date' => $date ?? now()->format('Y-m-d'),
        'time' => '16:00:00',
        'method' => 'online',
        'status' => 'completed',
    ]);

    SessionConfirmation::create([
        'session_id' => $session->id,
        'parent_id' => $parent->id,
        'status' => $status,
    ]);

    return $session;
}

// ─── Tests ────────────────────────────────────────────────────────────────────

test('salary slip counts hadir and terlambat as valid attendance', function () {
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    createSessionWithConfirmation($mentor, $student, $parent, 'hadir');
    createSessionWithConfirmation($mentor, $student, $parent, 'terlambat');
    createSessionWithConfirmation($mentor, $student, $parent, 'izin');
    createSessionWithConfirmation($mentor, $student, $parent, 'sakit');

    $service = app(RevenueAnalyticsService::class);
    $slip = $service->getMentorSalarySlipData($mentor->id, now()->month, now()->year);

    expect($slip['total_valid_attendance'])->toBe(2);
    expect($slip['total_honor'])->toBe(2 * RevenueAnalyticsService::MENTOR_FEE_PER_SESSION);
    expect($slip['total_sessions'])->toBe(4);
});

test('salary slip excludes izin and sakit from valid attendance', function () {
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    createSessionWithConfirmation($mentor, $student, $parent, 'izin');
    createSessionWithConfirmation($mentor, $student, $parent, 'sakit');

    $service = app(RevenueAnalyticsService::class);
    $slip = $service->getMentorSalarySlipData($mentor->id, now()->month, now()->year);

    expect($slip['total_valid_attendance'])->toBe(0);
    expect($slip['total_honor'])->toBe(0);
});

test('salary slip groups students correctly in section B', function () {
    $mentor = makeMentorWithUser();
    [$studentA, $parentA] = createStudentAndParent();
    [$studentB, $parentB] = createStudentAndParent();

    createSessionWithConfirmation($mentor, $studentA, $parentA, 'hadir');
    createSessionWithConfirmation($mentor, $studentA, $parentA, 'terlambat');
    createSessionWithConfirmation($mentor, $studentB, $parentB, 'hadir');
    createSessionWithConfirmation($mentor, $studentB, $parentB, 'sakit');

    $service = app(RevenueAnalyticsService::class);
    $slip = $service->getMentorSalarySlipData($mentor->id, now()->month, now()->year);

    $byStudent = collect($slip['students_b'])->keyBy('student_id');

    expect($byStudent[$studentA->id]['valid_attendance'])->toBe(2);
    expect($byStudent[$studentA->id]['subtotal'])->toBe(2 * RevenueAnalyticsService::MENTOR_FEE_PER_SESSION);

    expect($byStudent[$studentB->id]['valid_attendance'])->toBe(1);
    expect($byStudent[$studentB->id]['subtotal'])->toBe(RevenueAnalyticsService::MENTOR_FEE_PER_SESSION);
});

test('salary slip default status is pending', function () {
    $mentor = makeMentorWithUser();

    $service = app(RevenueAnalyticsService::class);
    $slip = $service->getMentorSalarySlipData($mentor->id, now()->month, now()->year);

    expect($slip['salary_status'])->toBe('pending');
});

test('admin can mark mentor salary as paid', function () {
    $admin = User::factory()->admin()->create();
    $mentor = makeMentorWithUser();

    $this->actingAs($admin)
        ->post(route('admin.staff.mark-salary-paid', $mentor->id), [
            'year' => now()->year,
            'month' => now()->month,
            'status' => 'paid',
        ])
        ->assertOk()
        ->assertJson(['success' => true, 'status' => 'paid']);

    $service = app(RevenueAnalyticsService::class);
    $slip = $service->getMentorSalarySlipData($mentor->id, now()->month, now()->year);
    expect($slip['salary_status'])->toBe('paid');
});

test('mentor can view salary slip print page with required note', function () {
    $mentor = makeMentorWithUser();
    $user = $mentor->user;

    $response = $this->actingAs($user)
        ->get(route('mentor.salary-slip.print', ['slip_month' => now()->month, 'slip_year' => now()->year]))
        ->assertOk()
        ->assertViewIs('mentor.salary-slip-print');

    $response->assertSee('honor dihitung dari Daftar hadir: tiap anak yang hadir atau terlambat pada satu pertemuan dihitung satu kehadiran, slip ini belum menandakan pembayaran. Status berubah setelah admin menandai lunas', false);
});

test('admin can view mentor salary slip print page', function () {
    $admin = User::factory()->admin()->create();
    $mentor = makeMentorWithUser();

    $this->actingAs($admin)
        ->get(route('admin.staff.salary-slip.print', ['id' => $mentor->id, 'slip_month' => now()->month, 'slip_year' => now()->year]))
        ->assertOk()
        ->assertViewIs('mentor.salary-slip-print');
});

test('admin staff show view contains salary slip data', function () {
    $admin = User::factory()->admin()->create();
    $mentor = makeMentorWithUser();

    $response = $this->actingAs($admin)
        ->get(route('admin.staff.show', $mentor->id))
        ->assertOk();

    $response->assertSee('Slip Gaji &amp; Pelunasan Honor', false);
    $response->assertSee('Total Honor Mengajar', false);
});
