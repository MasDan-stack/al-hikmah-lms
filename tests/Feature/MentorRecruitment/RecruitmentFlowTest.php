<?php

use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\Role;
use App\Models\User;
use App\Services\MentorRecruitmentService;
use App\Services\MentorTestService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->service = app(MentorRecruitmentService::class);
    $this->testService = app(MentorTestService::class);
});

test('public can view mentor registration and status tracker pages', function () {
    $responseReg = $this->get(route('bergabung'));
    $responseReg->assertStatus(200);
    $responseReg->assertSee('Formulir Pendaftaran Guru Pembimbing');

    $responseStatus = $this->get(route('mentor.recruitment.status'));
    $responseStatus->assertStatus(200);
    $responseStatus->assertSee('Pelacak Status Lamaran Guru');
});

test('public can submit mentor application with password and login to dashboard', function () {
    Storage::fake('local');

    $response = $this->post(route('mentor.recruitment.store'), [
        'full_name' => 'Ustadz Budi Santoso',
        'email' => 'budi.santoso@example.com',
        'password' => 'secret12345',
        'password_confirmation' => 'secret12345',
        'phone' => '081234567890',
        'birth_date' => '1995-01-01',
        'gender' => 'male',
        'address' => 'Jl. Kebenaran No 12',
        'city' => 'Jakarta',
        'education' => 'S1 Pendidikan Agama Islam',
        'institution' => 'UIN Syarif Hidayatullah',
        'experience_years' => 3,
        'experience_description' => 'Mengajar tahsin di TPQ',
        'specialization' => 'Tahsin',
        'hifz_total_juz' => 5,
        'cv' => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf'),
    ]);

    $response->assertRedirect(route('mentor.dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'budi.santoso@example.com',
    ]);

    $this->assertDatabaseHas('mentor_applications', [
        'email' => 'budi.santoso@example.com',
        'status' => 'submitted',
    ]);
});

test('candidate dashboard hides operational menus during recruitment and shows after acceptance', function () {
    $role = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $mentor = Mentor::create([
        'user_id' => $user->id,
        'full_name' => $user->name,
        'status' => 'inactive',
        'is_active' => false,
    ]);

    $application = MentorApplication::factory()->create([
        'user_id' => $user->id,
        'status' => 'submitted',
        'current_stage' => 1,
    ]);

    // 1. As candidate in selection: Operational menus must be hidden
    $responseCandidate = $this->actingAs($user)->get(route('mentor.dashboard'));
    $responseCandidate->assertStatus(200);
    $responseCandidate->assertSee('Dashboard Calon Guru');
    $responseCandidate->assertDontSee('Data Orang Tua');
    $responseCandidate->assertDontSee('Bank Soal & AI Generator');
    $responseCandidate->assertDontSee('Laporan Kinerja');

    // 2. Once accepted to probation: Operational menus appear
    $mentor->update(['status' => 'probation', 'is_active' => true]);
    $application->update(['status' => 'approved', 'current_stage' => 5]);

    $responseApproved = $this->actingAs($user->fresh())->get(route('mentor.dashboard'));
    $responseApproved->assertStatus(200);
    $responseApproved->assertSee('Dashboard Mengajar');
    $responseApproved->assertSee('Data Orang Tua');
    $responseApproved->assertSee('Bank Soal');
    $responseApproved->assertSee('Laporan Kinerja');
});

test('candidate can view and submit recruitment test on mentor portal', function () {
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => json_encode([
                                'questions' => [
                                    [
                                        'question' => 'Jelaskan hukum nun mati?',
                                        'options' => ['Izhar', 'Idgham', 'Iqlab', 'Ikhfa'],
                                        'correct_answer' => 0,
                                        'explanation' => 'Nun mati memiliki 4 hukum.',
                                        'difficulty' => 'Sedang',
                                    ],
                                ],
                            ])],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $role = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $candidateUser = User::factory()->create(['email' => 'candidate@example.com', 'role_id' => $role->id]);

    $application = MentorApplication::factory()->create([
        'user_id' => $candidateUser->id,
        'email' => 'candidate@example.com',
        'status' => 'test_scheduled',
        'current_stage' => 3,
    ]);

    $session = $this->testService->generateTest($application);

    // View test page
    $response = $this->actingAs($candidateUser)
        ->get(route('mentor.recruitment.take-test', $session->id));
    $response->assertStatus(200);
    $response->assertSee('Tes Kompetensi');
    $response->assertSee('Tajwid Test');
    $response->assertSee('Makharijul Huruf');
    $response->assertSee('Tahsin');

    // Submit answers
    $submitResponse = $this->actingAs($candidateUser)
        ->post(route('mentor.recruitment.submit-test', $session->id), [
            'answers' => array_fill(0, 15, 0),
        ]);

    $submitResponse->assertRedirect(route('mentor.dashboard'));
    $submitResponse->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_test_sessions', [
        'id' => $session->id,
        'status' => 'completed',
    ]);

    $this->assertDatabaseHas('mentor_applications', [
        'id' => $application->id,
        'status' => 'test_completed',
    ]);
});

test('admin can view and download application document', function () {
    Storage::fake('local');

    $application = MentorApplication::factory()->create();
    $file = UploadedFile::fake()->create('cv_santri.pdf', 500, 'application/pdf');
    $path = $file->store("private/mentor_applications/{$application->id}");

    $doc = $application->documents()->create([
        'document_type' => 'cv',
        'file_path' => $path,
        'file_name' => 'cv_santri.pdf',
        'file_size' => 500,
        'mime_type' => 'application/pdf',
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.recruitment.applications.document', [$application->id, $doc->id]));

    $response->assertStatus(200);
});

test('admin can approve document review and progress to test stage', function () {
    $application = MentorApplication::factory()->create([
        'status' => 'submitted',
        'current_stage' => 1,
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.recruitment.applications.approveDocument', $application->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_applications', [
        'id' => $application->id,
        'status' => 'document_review',
        'current_stage' => 2,
    ]);
});

test('admin can accept application and it creates mentor account', function () {
    $application = MentorApplication::factory()->create([
        'full_name' => 'Ustadz Ahmad Fauzi',
        'status' => 'interview_scheduled',
        'current_stage' => 4,
        'phone' => '081299887766',
        'specialization' => 'Tahfidz',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.recruitment.applications.accept', $application->id), [
            'notes' => 'Sangat bagus',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_applications', [
        'id' => $application->id,
        'status' => 'approved',
    ]);

    $this->assertDatabaseHas('mentors', [
        'application_id' => $application->id,
        'status' => 'probation',
    ]);
});
