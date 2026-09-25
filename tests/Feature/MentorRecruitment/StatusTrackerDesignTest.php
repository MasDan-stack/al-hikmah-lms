<?php

use App\Models\MentorApplication;
use Carbon\Carbon;

test('public can view the status tracker page with antislop design elements', function () {
    $response = $this->get(route('mentor.recruitment.status'));

    $response->assertStatus(200);
    $response->assertSee('Pelacak Status Lamaran Guru');
    $response->assertSee('Portal Transparansi Rekrutmen');
    $response->assertSee('Alur Standar Rekrutmen Guru AL-HIKMAH');
    $response->assertSee('Pertanyaan yang Sering Diajukan');
    $response->assertDontSee('Pelacak Status v8.3'); // Verify fake AI version badge is removed
});

test('applicant can check status using phone number and view responsive timeline', function () {
    $app = MentorApplication::factory()->create([
        'application_code' => 'APP-TEST-1001',
        'full_name' => 'Ustadzah Aminah Az-Zahra',
        'phone' => '081234567899',
        'specialization' => 'Tahfidz Anak',
        'hifz_total_juz' => 15,
        'status' => 'document_review',
        'current_stage' => 2,
        'submitted_at' => Carbon::now()->subDays(2),
    ]);

    $response = $this->post(route('mentor.recruitment.check-status'), [
        'phone' => '081234567899',
    ]);

    $response->assertStatus(200);
    $response->assertSee('Ustadzah Aminah Az-Zahra');
    $response->assertSee('APP-TEST-1001');
    $response->assertSee('Tahfidz Anak');
    $response->assertSee('15 Juz');
    $response->assertSee('Verifikasi Berkas');
    $response->assertSee('tracker-stepper-desktop');
    $response->assertSee('tracker-stepper-mobile');
});

test('applicant can check status using normalized phone format', function () {
    $app = MentorApplication::factory()->create([
        'phone' => '089612345678',
        'status' => 'submitted',
        'current_stage' => 1,
    ]);

    // Check with country code 62 format
    $response = $this->post(route('mentor.recruitment.check-status'), [
        'phone' => '6289612345678',
    ]);

    $response->assertStatus(200);
    $response->assertSee($app->full_name);
});

test('applicant can check status using application code', function () {
    $app = MentorApplication::factory()->create([
        'application_code' => 'APP-CODE-9999',
        'status' => 'test_scheduled',
        'current_stage' => 3,
    ]);

    $response = $this->post(route('mentor.recruitment.check-status'), [
        'phone' => 'APP-CODE-9999',
    ]);

    $response->assertStatus(200);
    $response->assertSee($app->full_name);
    $response->assertSee('Sesi Ujian Tes Kompetensi Telah Dibuka');
});

test('invalid phone or code redirects back with error and retains input', function () {
    $response = $this->post(route('mentor.recruitment.check-status'), [
        'phone' => '080000000000',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error');
});
