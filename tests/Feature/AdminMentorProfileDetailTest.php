<?php

use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
        'email' => 'admin.test@alhikmah.com',
    ]);

    $this->mentorUser = User::factory()->create([
        'role_id' => $this->mentorRole->id,
        'name' => 'Ustaz Salman Al-Farisi',
        'email' => 'salman@alhikmah.com',
        'phone' => '081298765432',
    ]);

    $this->application = MentorApplication::factory()->create([
        'user_id' => $this->mentorUser->id,
        'application_code' => 'APP-202609-001',
        'full_name' => 'Ustaz Salman Al-Farisi',
        'email' => 'salman@alhikmah.com',
        'phone' => '081298765432',
        'gender' => 'male',
        'specialization' => 'Tahfidz 30 Juz & Qiraat Asyrah',
        'hifz_total_juz' => 30,
        'education' => 'S1 Ilmu Al-Qur\'an dan Tafsir',
        'institution' => 'Universitas Al-Azhar Kairo',
        'experience_years' => 5,
        'status' => 'approved',
    ]);

    $this->mentor = Mentor::factory()->create([
        'user_id' => $this->mentorUser->id,
        'application_id' => $this->application->id,
        'full_name' => 'Ustaz Salman Al-Farisi',
        'specialization' => 'Tahfidz 30 Juz & Qiraat Asyrah',
        'hifz_total_juz' => 30,
        'sanad_chain' => 'Syaikh Abdul Fattah Al-Mishri (Riwayat Hafs \'an \'Ashim)',
        'bank_name' => 'Bank Syariah Indonesia (BSI)',
        'bank_account_number' => '7123456789',
        'bank_account_name' => 'Salman Al-Farisi',
        'emergency_contact' => '081298765432',
        'status' => 'active',
        'is_active' => true,
    ]);
});

test('admin can view mentor detailed profile page with all sections and masked bank account', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.show', $this->mentor->id));

    $response->assertOk();
    $response->assertViewIs('admin.staff.show');
    $response->assertSee('Ustaz Salman Al-Farisi');
    $response->assertSee('Tahfidz 30 Juz & Qiraat Asyrah');
    $response->assertSee('Universitas Al-Azhar Kairo');
    $response->assertSee('Bank Syariah Indonesia (BSI)');
    // Bank account should be masked by default
    $response->assertSee('7123****89');
    $response->assertSee('Verifikasi Rekening');
    $response->assertSee('Informasi Pribadi');
    $response->assertSee('Informasi Profesional');
});

test('non-admin user cannot access admin mentor profile detail', function () {
    $parentUser = User::factory()->create([
        'role_id' => $this->parentRole->id,
    ]);

    $response = $this->actingAs($parentUser)
        ->get(route('admin.staff.show', $this->mentor->id));

    // Admin routes protected by admin middleware usually return 403 or redirect
    expect(in_array($response->status(), [403, 302]))->toBeTrue();
});

test('unauthenticated guest is redirected when viewing mentor profile', function () {
    $response = $this->get(route('admin.staff.show', $this->mentor->id));

    $response->assertRedirect(route('login'));
});

test('admin can verify mentor bank account successfully via ajax', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.staff.verify-bank', $this->mentor->id), [
            'status' => 'verified',
            'notes' => 'Buku tabungan valid sesuai nama di KTP.',
        ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'status' => 'verified',
    ]);

    $this->assertDatabaseHas('mentor_activity_logs', [
        'mentor_id' => $this->mentor->id,
        'action' => 'bank_account_verification',
    ]);

    $this->assertDatabaseHas('financial_audit_logs', [
        'user_id' => $this->admin->id,
        'action' => 'mentor_bank_verification',
        'entity_type' => 'mentor',
        'entity_id' => $this->mentor->id,
    ]);
});

test('admin can flag mentor bank account as needs clarification via ajax', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.staff.verify-bank', $this->mentor->id), [
            'status' => 'needs_clarification',
            'notes' => 'Nama pada rekening tidak sesuai dengan identitas KTP.',
        ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'status' => 'needs_clarification',
    ]);

    $this->assertDatabaseHas('mentor_activity_logs', [
        'mentor_id' => $this->mentor->id,
        'action' => 'bank_account_verification',
    ]);
});

test('viewing non-existent mentor profile returns 404', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.staff.show', 999999));

    $response->assertNotFound();
});
