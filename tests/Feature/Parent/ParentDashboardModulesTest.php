<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Progress;
use App\Models\Role;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Parent']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Student']);

    // Setup Parent 1
    $this->parentUser = User::factory()->create([
        'name' => 'Bpk Wali Santri',
        'role_id' => $this->parentRole->id,
    ]);
    $this->parentProfile = ParentProfile::create([
        'user_id' => $this->parentUser->id,
        'address' => 'Jl. Kebon Jeruk No 12',
        'emergency_phone' => '08123456789',
    ]);

    // Setup Child for Parent 1
    $this->childUser = User::factory()->create([
        'role_id' => $this->studentRole->id,
    ]);
    $this->childUser->update(['name' => 'Ahmad Rayhan']);

    $this->student = Student::create([
        'user_id' => $this->childUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Ahmad Rayhan',
        'age' => 9,
        'gender' => 'L',
        'location' => 'Jakarta',
    ]);

    // Setup Mentor
    $this->mentorUser = User::factory()->create([
        'name' => 'Ustaz Abdullah',
        'role_id' => $this->mentorRole->id,
    ]);
    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ustaz Abdullah',
        'specialization' => 'Tahfidz Al-Qur\'an',
    ]);

    // Setup Active Program & Paid Payment for State 3 access
    $this->program = Program::create([
        'name' => 'Tahsin Reguler',
        'price' => 500000,
        'description' => 'Test Program',
        'is_active' => true,
    ]);

    $this->enrollment = Enrollment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'mentor_id' => $this->mentor->id,
        'status' => EnrollmentStatus::ACTIVE->value,
        'requested_days' => ['monday', 'thursday'],
        'learning_method' => 'online',
    ]);

    Payment::create([
        'student_id' => $this->student->id,
        'program_id' => $this->program->id,
        'enrollment_id' => $this->enrollment->id,
        'amount' => 500000,
        'status' => 'paid',
        'invoice_number' => 'INV-TEST-MODULES',
    ]);
});

test('tamu atau non-parent tidak dapat mengakses parent dashboard', function () {
    $response = $this->get(route('parent.dashboard'));
    $response->assertRedirect();
});

test('orang tua dapat mengakses dashboard utama dengan data statistik valid', function () {
    $response = $this->actingAs($this->parentUser)->get(route('parent.dashboard'));
    $response->assertStatus(200);
    $response->assertViewHas(['totalChildrenCount', 'monthSessionsCount', 'avgTajwidScore', 'pendingPaymentsCount']);
});

test('modul anak menampilkan daftar anak dan detail progres anak milik orang tua terkait', function () {
    Progress::create([
        'student_id' => $this->student->id,
        'mentor_id' => $this->mentor->id,
        'kategori' => 'Hafalan Baru',
        'surah_start' => 'Al-Fatihah',
        'juz' => 1,
        'nilai_tajwid' => 90,
        'nilai_fluent' => 85,
        'nilai_adab' => 95,
        'catatan_evaluasi' => 'MasyaAllah bacaan sangat fasih',
    ]);

    $responseIndex = $this->actingAs($this->parentUser)->get(route('parent.children.index'));
    $responseIndex->assertStatus(200);

    $responseShow = $this->actingAs($this->parentUser)->get(route('parent.children.show', $this->student->id));
    $responseShow->assertStatus(200);
    $responseShow->assertSee('Ahmad Rayhan');

    $responseReport = $this->actingAs($this->parentUser)->get(route('parent.children.report', $this->student->id));
    $responseReport->assertStatus(200);
});

test('orang tua tidak dapat melihat detail anak milik orang tua lain (data security isolation)', function () {
    $otherParentUser = User::factory()->create(['role_id' => $this->parentRole->id]);
    $otherParent = ParentProfile::create(['user_id' => $otherParentUser->id]);
    $otherStudentUser = User::factory()->create(['role_id' => $this->studentRole->id]);
    $otherStudent = Student::create([
        'user_id' => $otherStudentUser->id,
        'parent_id' => $otherParent->id,
        'full_name' => 'Fatimah Az-Zahra',
        'age' => 10,
        'gender' => 'P',
    ]);

    $response = $this->actingAs($this->parentUser)->get(route('parent.children.show', $otherStudent->id));
    $response->assertStatus(403);
});

test('modul jadwal bimbingan dan konfirmasi kehadiran berfungsi dengan baik', function () {
    $session = Session::create([
        'student_id' => $this->student->id,
        'mentor_id' => $this->mentor->id,
        'date' => now()->addDays(2),
        'time' => '16:00',
        'method' => 'online',
        'status' => 'scheduled',
    ]);

    $responseIndex = $this->actingAs($this->parentUser)->get(route('parent.schedules.index'));
    $responseIndex->assertStatus(200);

    $responseList = $this->actingAs($this->parentUser)->get(route('parent.schedules.list'));
    $responseList->assertStatus(200);

    $responseShow = $this->actingAs($this->parentUser)->get(route('parent.schedules.show', $session->id));
    $responseShow->assertStatus(200);

    $responseConfirm = $this->actingAs($this->parentUser)->post(route('parent.schedules.confirm', $session->id), [
        'status' => 'hadir',
        'notes' => 'Siap bimbingan TEPAT WAKTU',
    ]);
    $responseConfirm->assertRedirect();

    $this->assertDatabaseHas('session_confirmations', [
        'session_id' => $session->id,
        'parent_id' => $this->parentProfile->id,
        'status' => 'hadir',
    ]);
});

test('modul tagihan dan pembayaran online Midtrans berjalan aman', function () {
    $payment = Payment::create([
        'student_id' => $this->student->id,
        'amount' => 250000,
        'invoice_number' => 'INV-TEST-001',
        'status' => 'pending',
    ]);

    $responseIndex = $this->actingAs($this->parentUser)->get(route('parent.payments.index'));
    $responseIndex->assertStatus(200);

    $responseShow = $this->actingAs($this->parentUser)->get(route('parent.payments.show', $payment->id));
    $responseShow->assertStatus(200);

    $responsePay = $this->actingAs($this->parentUser)->post(route('parent.payments.pay', $payment->id), [
        'payment_method' => 'Midtrans QRIS',
    ]);
    $responsePay->assertRedirect(route('parent.payments.history'));

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'status' => 'paid',
    ]);

    $responseDownload = $this->actingAs($this->parentUser)->get(route('parent.payments.download', $payment->id));
    $responseDownload->assertStatus(200);
});

test('modul komunikasi dan pesan ke mentor berjalan sukses', function () {
    $responseCreate = $this->actingAs($this->parentUser)->get(route('parent.messages.create'));
    $responseCreate->assertStatus(200);

    $responseStore = $this->actingAs($this->parentUser)->post(route('parent.messages.store'), [
        'receiver_id' => $this->mentorUser->id,
        'student_id' => $this->student->id,
        'message' => 'Assalamu\'alaikum Ustadz, bagaimana hafalan Rayhan?',
    ]);
    $responseStore->assertRedirect();

    $this->assertDatabaseHas('messages', [
        'sender_id' => $this->parentUser->id,
        'receiver_id' => $this->mentorUser->id,
        'message' => 'Assalamu\'alaikum Ustadz, bagaimana hafalan Rayhan?',
    ]);

    $responseIndex = $this->actingAs($this->parentUser)->get(route('parent.messages.index'));
    $responseIndex->assertStatus(200);

    $responseChat = $this->actingAs($this->parentUser)->get(route('parent.messages.chat', $this->mentor->id));
    $responseChat->assertStatus(200);
});

test('modul profil dan kelola data anak dapat diperbarui oleh orang tua', function () {
    $responseEdit = $this->actingAs($this->parentUser)->get(route('parent.profile.edit'));
    $responseEdit->assertStatus(200);

    $responseUpdate = $this->actingAs($this->parentUser)->post(route('parent.profile.update'), [
        'name' => 'Wali Santri Update',
        'email' => $this->parentUser->email,
        'emergency_phone' => '0899999999',
        'address' => 'Jl. Merdeka No. 45',
    ]);
    $responseUpdate->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id' => $this->parentUser->id,
        'name' => 'Wali Santri Update',
    ]);

    $responseAddChild = $this->actingAs($this->parentUser)->post(route('parent.profile.store-child'), [
        'full_name' => 'Siti Khadijah',
        'age' => 7,
        'gender' => 'P',
        'location' => 'Jakarta',
    ]);
    $responseAddChild->assertRedirect();

    $this->assertDatabaseHas('students', [
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Siti Khadijah',
    ]);
});
