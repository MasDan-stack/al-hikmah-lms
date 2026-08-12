<?php

use App\Models\MentorActivityLog;
use App\Models\Progress;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('mentor dashboard renders correctly with chart and low progress alert', function () {
    $mentorUser = User::where('email', 'ustadz.ahmad@alhikmah.com')->first();
    $mentor = $mentorUser->mentor;

    // Create a student with low tajwid score (< 70)
    $student = Student::first();
    if ($student && $mentor) {
        $mentor->students()->syncWithoutDetaching([$student->id]);
        Progress::create([
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'kategori' => 'Tajwid & Bacaan',
            'nilai_tajwid' => 65,
            'nilai_fluent' => 60,
        ]);
    }

    $response = $this->actingAs($mentorUser)->get('/mentor/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Dashboard Utama');
    $response->assertSee('Grafik Perkembangan Bimbingan');
    $response->assertSee('Santri Perlu Perhatian Khusus');
});

test('mentor can access and submit bulk progress entry', function () {
    $mentorUser = User::where('email', 'ustadz.ahmad@alhikmah.com')->first();
    $mentor = $mentorUser->mentor;
    $students = Student::take(2)->get();

    $response = $this->actingAs($mentorUser)->get('/mentor/progress/bulk');
    $response->assertStatus(200);
    $response->assertSee('Catat Progres Massal');

    $payload = [
        'entries' => [
            [
                'student_id' => $students[0]->id,
                'kategori' => 'Hafalan Baru',
                'surah_start' => 'Al-Fatihah',
                'ayat_start' => '1',
                'ayat_end' => '7',
                'juz' => 1,
                'nilai_fluent' => 90,
                'nilai_tajwid' => 88,
                'nilai_adab' => 90,
            ],
            [
                'student_id' => $students[1]->id,
                'kategori' => "Muraja'ah",
                'surah_start' => 'An-Nas',
                'juz' => 30,
                'nilai_fluent' => 85,
                'nilai_tajwid' => 82,
                'nilai_adab' => 80,
            ],
        ],
    ];

    $postResponse = $this->actingAs($mentorUser)->post('/mentor/progress/bulk', $payload);
    $postResponse->assertRedirect(route('mentor.dashboard'));

    expect(Progress::where('surah_start', 'Al-Fatihah')->exists())->toBeTrue();
    expect(Progress::where('surah_start', 'An-Nas')->exists())->toBeTrue();
    expect(MentorActivityLog::where('action', 'bulk_progres')->exists())->toBeTrue();
});

test('mentor can update session status to in_progress', function () {
    $mentorUser = User::where('email', 'ustadz.ahmad@alhikmah.com')->first();
    $mentor = $mentorUser->mentor;

    $session = Session::where('mentor_id', $mentor->id)->first();

    if ($session) {
        $response = $this->actingAs($mentorUser)->post("/mentor/sessions/{$session->id}/status", [
            'status' => 'in_progress',
        ]);

        $response->assertRedirect();
        expect($session->fresh()->status)->toBe('in_progress');
        expect(MentorActivityLog::where('action', 'update_status_sesi')->exists())->toBeTrue();
    }
});

test('mentor can export performance report', function () {
    $mentorUser = User::where('email', 'ustadz.ahmad@alhikmah.com')->first();

    $response = $this->actingAs($mentorUser)->get('/mentor/reports/export');

    $response->assertStatus(200);
    $response->assertSee('Laporan Kinerja Mentor');
    $response->assertSee('TOTAL SESI');
});
