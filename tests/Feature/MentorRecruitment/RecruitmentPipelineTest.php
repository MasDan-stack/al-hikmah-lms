<?php

use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\MentorProbationTracking;
use App\Models\MentorTestSession;
use App\Models\Role;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
    ]);
});

test('candidate can register through recruitment portal and create pending application', function () {
    Storage::fake('local');

    $response = $this->post(route('mentor.recruitment.store'), [
        'full_name' => 'Ustadz Ahmad Farhan',
        'email' => 'ahmad.farhan@example.com',
        'password' => 'Bismillah123!',
        'password_confirmation' => 'Bismillah123!',
        'phone' => '081234567890',
        'birth_date' => '1992-05-15',
        'gender' => 'male',
        'address' => 'Komplek Masjid Al-Hikmah Blok B2',
        'city' => 'Bandung',
        'education' => 'S1 Ilmu Al-Quran dan Tafsir',
        'institution' => 'Universitas Al-Azhar Kairo / UIN SGD',
        'experience_years' => 4,
        'experience_description' => 'Guru tahfidz intensif juz 1-10',
        'specialization' => 'Tahfidz & Tahsin',
        'hifz_total_juz' => 30,
        'cv' => UploadedFile::fake()->create('cv_ahmad_farhan.pdf', 500, 'application/pdf'),
    ]);

    $response->assertRedirect(route('mentor.dashboard'));

    $user = User::where('email', 'ahmad.farhan@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role_id)->toBe($this->mentorRole->id);

    $this->assertDatabaseHas('mentor_applications', [
        'email' => 'ahmad.farhan@example.com',
        'status' => 'submitted',
        'current_stage' => 1,
    ]);

    $this->assertDatabaseHas('mentors', [
        'user_id' => $user->id,
        'status' => 'inactive',
        'is_active' => false,
    ]);
});

test('admin can approve document with 1-click generating 15-question test and notifying candidate', function () {
    $waMock = mock(WhatsAppService::class);
    $waMock->shouldReceive('sendMessage')->atLeast()->once()->andReturn(true);
    app()->instance(WhatsAppService::class, $waMock);

    $user = User::factory()->create(['role_id' => $this->mentorRole->id]);
    $application = MentorApplication::factory()->create([
        'user_id' => $user->id,
        'status' => 'submitted',
        'current_stage' => 1,
        'phone' => '081234567890',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.recruitment.applications.approveDocument', $application->id), [
            'notes' => 'Portofolio tahfidz 30 juz sangat baik dan terverifikasi.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $application->refresh();
    expect($application->status)->toBe('test_scheduled');
    expect($application->current_stage)->toBe(3);
    expect($application->admin_notes)->toBe('Portofolio tahfidz 30 juz sangat baik dan terverifikasi.');

    $testSession = MentorTestSession::where('application_id', $application->id)->first();
    expect($testSession)->not->toBeNull();
    expect($testSession->ai_question_payload['total_questions'])->toBe(15);
    expect($testSession->status)->toBe('in_progress');
});

test('admin can schedule interview and dispatch whatsapp confirmation', function () {
    $waMock = mock(WhatsAppService::class);
    $waMock->shouldReceive('sendMessage')->atLeast()->once()->andReturn(true);
    app()->instance(WhatsAppService::class, $waMock);

    $user = User::factory()->create(['role_id' => $this->mentorRole->id]);
    $application = MentorApplication::factory()->create([
        'user_id' => $user->id,
        'status' => 'test_completed',
        'current_stage' => 3,
        'phone' => '081234567890',
    ]);

    $interviewDate = now()->addDays(2)->setHour(10)->setMinute(0)->format('Y-m-d H:i');

    $response = $this->actingAs($this->admin)
        ->post(route('admin.recruitment.applications.scheduleInterview', $application->id), [
            'interview_scheduled_at' => $interviewDate,
            'interview_type' => 'online',
            'interview_meeting_link' => 'https://meet.google.com/al-hikmah-recruitment',
            'interview_notes' => 'Wawancara pedagogi dan microteaching 15 menit.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $application->refresh();
    expect($application->status)->toBe('interview_scheduled');
    expect($application->current_stage)->toBe(4);
    expect($application->interview_meeting_link)->toBe('https://meet.google.com/al-hikmah-recruitment');
    expect($application->interview_type)->toBe('online');
});

test('admin can accept candidate creating active probation tracking and mentor activation', function () {
    $waMock = mock(WhatsAppService::class);
    $waMock->shouldReceive('sendMessage')->atLeast()->once()->andReturn(true);
    app()->instance(WhatsAppService::class, $waMock);

    $user = User::factory()->create([
        'name' => 'Ustadz Salman Al-Farisi',
        'email' => 'salman@example.com',
        'role_id' => $this->mentorRole->id,
    ]);

    $mentor = Mentor::create([
        'user_id' => $user->id,
        'full_name' => 'Ustadz Salman Al-Farisi',
        'status' => 'inactive',
        'is_active' => false,
    ]);

    $application = MentorApplication::factory()->create([
        'user_id' => $user->id,
        'email' => 'salman@example.com',
        'status' => 'interview_completed',
        'current_stage' => 4,
        'phone' => '081234567890',
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.recruitment.applications.accept', $application->id), [
            'notes' => 'Lolos seleksi komprehensif, siap masuk tahap masa percobaan (probation).',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $application->refresh();
    expect($application->status)->toBe('approved');
    expect($application->current_stage)->toBe(5);

    $mentor->refresh();
    expect($mentor->status)->toBe('probation');
    expect($mentor->is_active)->toBeTrue();
    expect($mentor->probation_end_date)->not->toBeNull();

    $probation = MentorProbationTracking::where('mentor_id', $mentor->id)->first();
    expect($probation)->not->toBeNull();
    expect($probation->status)->toBe('active');
});
