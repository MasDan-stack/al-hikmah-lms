<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Session;
use App\Models\SessionConfirmation;
use App\Models\Student;
use App\Models\User;
use App\Services\RevenueAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

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
        ->get(route('admin.staff.salary-slip.print', ['mentor' => $mentor->id, 'slip_month' => now()->month, 'slip_year' => now()->year]))
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

test('honorarium summary synchronizes correctly when attendance is marked', function () {
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    $program = Program::create([
        'name' => 'Paket Bimbingan Mumtaz Test',
        'category' => 'anak',
        'duration_weeks' => 8,
        'price' => 1200000,
        'is_active' => true,
    ]);

    DB::table('mentor_student')->insert([
        'mentor_id' => $mentor->id,
        'student_id' => $student->id,
        'program_id' => $program->id,
        'day_assigned' => 'monday',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $service = app(RevenueAnalyticsService::class);

    $initialSummary = $service->getMentorHonorariumSummary($mentor->id);
    expect($initialSummary['this_month_sessions'])->toBe(0);
    expect($initialSummary['total_completed_sessions'])->toBe(0);
    expect($initialSummary['upcoming_sessions'])->toBe(8);

    createSessionWithConfirmation($mentor, $student, $parent, 'hadir');

    $afterHadirSummary = $service->getMentorHonorariumSummary($mentor->id);
    expect($afterHadirSummary['this_month_sessions'])->toBe(1);
    expect($afterHadirSummary['total_completed_sessions'])->toBe(1);
    expect($afterHadirSummary['upcoming_sessions'])->toBe(7);
    expect($afterHadirSummary['this_month_honor'])->toBe(100000);

    $slip = $service->getMentorSalarySlipData($mentor->id, now()->month, now()->year);
    expect($slip['total_valid_attendance'])->toBe(1);
    expect($slip['total_honor'])->toBe(100000);
    expect($slip['students_b'][0]['program_name'])->toBe('Paket Bimbingan Mumtaz Test');
});

test('parent attendance confirmation updates session status to completed and syncs with mentor summary', function () {
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    // Satisfy parent.paid middleware
    Payment::create([
        'student_id' => $student->id,
        'amount' => 150000,
        'status' => 'paid',
        'invoice_number' => 'INV-TEST-001',
        'payment_date' => now(),
    ]);

    $session = Session::create([
        'mentor_id' => $mentor->id,
        'student_id' => $student->id,
        'date' => now()->format('Y-m-d'),
        'time' => '16:00:00',
        'method' => 'online',
        'status' => 'pending',
    ]);

    $this->actingAs($parent->user)
        ->post(route('parent.schedules.confirm', $session->id), [
            'status' => 'hadir',
            'notes' => 'Ananda hadir tepat waktu',
        ])
        ->assertRedirect();

    $session->refresh();
    expect($session->status)->toBe('completed');

    $service = app(RevenueAnalyticsService::class);
    $summary = $service->getMentorHonorariumSummary($mentor->id);
    expect($summary['this_month_sessions'])->toBe(1);
    expect($summary['total_completed_sessions'])->toBe(1);
});

test('parent dashboard loads upcoming sessions with relations without undefined relationship program error', function () {
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    $program = Program::create([
        'name' => 'Tahsin Dasar Test',
        'category' => 'anak',
        'duration_weeks' => 12,
        'price' => 600000,
        'is_active' => true,
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'mentor_id' => $mentor->id,
        'program_id' => $program->id,
        'status' => EnrollmentStatus::ACTIVE,
        'learning_method' => 'online',
        'program_price' => 600000,
    ]);

    Payment::create([
        'student_id' => $student->id,
        'program_id' => $program->id,
        'enrollment_id' => $enrollment->id,
        'amount' => 600000,
        'status' => 'paid',
        'invoice_number' => 'INV-TEST-SYNC',
    ]);

    Session::create([
        'student_id' => $student->id,
        'mentor_id' => $mentor->id,
        'date' => today()->addDays(3)->format('Y-m-d'),
        'time' => '16:00:00',
        'method' => 'online',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($parent->user)->get(route('parent.dashboard'));
    $response->assertOk();
    $response->assertSee($student->user->name);
});

test('parent attendance confirmation appears in admin staff show Section B with proof warning', function () {
    $admin = User::factory()->admin()->create();
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    // Session confirmed as hadir by parent without mentor photo proof yet
    $session = Session::create([
        'mentor_id' => $mentor->id,
        'student_id' => $student->id,
        'date' => now()->format('Y-m-d'),
        'time' => '16:00:00',
        'method' => 'offline',
        'status' => 'completed',
    ]);

    SessionConfirmation::create([
        'session_id' => $session->id,
        'parent_id' => $parent->id,
        'status' => 'hadir',
        'confirmed_by' => 'parent',
        'notes' => 'Hadir tepat waktu',
        'proof_image' => null,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.staff.show', $mentor->id));
    $response->assertOk();
    $response->assertSee('B. Rincian Kehadiran &amp; Honor Persantri', false);
    $response->assertSee($student->full_name);
    $response->assertSee('Belum Ada Bukti Foto');
    $response->assertSee('Rp 100.000');
});

test('mentor dashboard displays red alert banner when sessions lack proof', function () {
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    Session::create([
        'mentor_id' => $mentor->id,
        'student_id' => $student->id,
        'date' => now()->format('Y-m-d'),
        'time' => '16:00:00',
        'method' => 'offline',
        'status' => 'completed',
    ]);

    $response = $this->actingAs($mentor->user)->get(route('mentor.dashboard'));
    $response->assertOk();
    $response->assertSee('Peringatan: 1 Sesi Belum Dilengkapi Foto Bukti Pengajaran!');
    $response->assertSee('Upload Bukti Sekarang');
});

test('mentor can store progress successfully via mentor progress store route', function () {
    $mentor = makeMentorWithUser();
    [$student, $parent] = createStudentAndParent();

    $response = $this->actingAs($mentor->user)->post(route('mentor.progress.store'), [
        'student_id' => $student->id,
        'kategori' => 'Tahfidz',
        'surah_start' => 'Al-Mulk',
        'ayat_start' => '1',
        'ayat_end' => '10',
        'nilai_tajwid' => '85',
        'nilai_kelancaran' => '90',
        'nilai_makhraj' => '88',
        'catatan' => 'Bacaan sangat lancar dan tartil',
        'is_mutqin' => '1',
    ]);

    $response->assertRedirect(route('mentor.progress.create', ['student_id' => $student->id]));

    $this->assertDatabaseHas('progress', [
        'student_id' => $student->id,
        'mentor_id' => $mentor->id,
        'kategori' => 'Tahfidz',
        'surah_start' => 'Al-Mulk',
        'ayat_start' => 1,
        'ayat_end' => 10,
        'nilai_tajwid' => 85,
        'nilai_fluent' => 90,
        'is_mutqin_test' => 1,
    ]);
});
