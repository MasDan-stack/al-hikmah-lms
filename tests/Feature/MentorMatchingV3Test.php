<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\MentorCalendarSync;
use App\Models\MentorLoadBalanceProfile;
use App\Models\MentorPedagogicalProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentLearningStyle;
use App\Models\User;
use App\Services\MentorMatchingService;

beforeEach(function () {
    $this->matchingService = app(MentorMatchingService::class);

    Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Guru / Mentor']);
    Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

    $this->program = Program::create([
        'name' => 'Tahfidz Qur\'an Anak',
        'category' => 'tahfidz',
        'price' => 450000,
        'is_active' => true,
    ]);
});

// 1. Uji Syariat Gender 10 Tahun
test('gender match enforces 10 years sharia rule strictly for male and female students', function () {
    $ustadzUser = User::factory()->create();
    $ustadz = Mentor::create([
        'user_id' => $ustadzUser->id,
        'full_name' => 'Ustadz Ahmad',
        'gender' => 'L',
        'specializations' => ['tahfidz'],
        'is_active' => true,
    ]);

    $ustadzahUser = User::factory()->create();
    $ustadzah = Mentor::create([
        'user_id' => $ustadzahUser->id,
        'full_name' => 'Ustadzah Fatimah',
        'gender' => 'P',
        'specializations' => ['tahfidz'],
        'is_active' => true,
    ]);

    // Santri Putra 11 Tahun -> Wajib Ustadz
    $maleOlder = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Putra 11th',
        'age' => 11,
        'gender' => 'L',
    ]);
    expect($this->matchingService->calculateGenderScore($ustadz, $maleOlder, $this->program))->toBe(100.0);
    expect($this->matchingService->calculateGenderScore($ustadzah, $maleOlder, $this->program))->toBe(0.0);

    // Santri Putra 8 Tahun (< 10) -> Wajib Ustadzah
    $maleYounger = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Putra 8th',
        'age' => 8,
        'gender' => 'L',
    ]);
    expect($this->matchingService->calculateGenderScore($ustadz, $maleYounger, $this->program))->toBe(0.0);
    expect($this->matchingService->calculateGenderScore($ustadzah, $maleYounger, $this->program))->toBe(100.0);

    // Santri Putri -> Wajib Ustadzah
    $femaleStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Putri Aisyah',
        'age' => 12,
        'gender' => 'P',
    ]);
    expect($this->matchingService->calculateGenderScore($ustadz, $femaleStudent, $this->program))->toBe(0.0);
    expect($this->matchingService->calculateGenderScore($ustadzah, $femaleStudent, $this->program))->toBe(100.0);
});

// 2. Uji Deteksi Bentrok Kalender Eksternal
test('external calendar busy conflict drops slot score to zero and disqualifies mentor', function () {
    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Zaid',
        'gender' => 'L',
        'specializations' => ['tahfidz'],
        'is_active' => true,
    ]);

    MentorAvailability::create([
        'mentor_id' => $mentor->id,
        'day' => 'monday',
        'is_available' => true,
        'slot_numbers' => [0, 1, 2, 3, 4],
    ]);

    // Sync busy slot cache at Monday 16:00
    MentorCalendarSync::create([
        'mentor_id' => $mentor->id,
        'provider' => 'google',
        'is_active' => true,
        'busy_slots_cache' => [
            ['day' => 'monday', 'time' => '16:00'],
        ],
    ]);

    $student = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Salman',
        'age' => 12,
        'gender' => 'L',
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'program_id' => $this->program->id,
        'requested_days' => ['monday'],
        'requested_time' => '16:00',
        'learning_method' => 'online',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $breakdown = $this->matchingService->calculateBreakdown($mentor, $student, $this->program, 'monday', 'online', 10.0, $enrollment);

    expect($breakdown['slot'])->toBe(0.0);
    expect($breakdown['disqualified_reason'])->toContain('Google Calendar');
});

// 3. Uji Perlindungan Beban Kerja (Load Throttling)
test('mentor with overload or throttled status receives disqualification from recommendations', function () {
    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Populer',
        'gender' => 'L',
        'specializations' => ['tahfidz'],
        'is_active' => true,
    ]);

    MentorLoadBalanceProfile::create([
        'mentor_id' => $mentor->id,
        'is_throttled' => true,
        'burnout_level' => 'critical',
        'burnout_risk_score' => 85.00,
        'cooldown_reason' => 'Perlindungan kelelahan mengajar',
    ]);

    $student = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Umar',
        'age' => 12,
        'gender' => 'L',
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'program_id' => $this->program->id,
        'requested_days' => ['monday'],
        'learning_method' => 'online',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $breakdown = $this->matchingService->calculateBreakdown($mentor, $student, $this->program, 'monday', 'online', 10.0, $enrollment);

    expect($breakdown['disqualified_reason'])->toContain('pemulihan beban mengajar');
});

// 4. Uji Keselarasan Gaya Belajar (Pedagogical Fit / Cosine Similarity)
test('pedagogical match calculates cosine similarity between student and mentor profiles', function () {
    $mentorAuditoriUser = User::factory()->create();
    $mentorAuditori = Mentor::create([
        'user_id' => $mentorAuditoriUser->id,
        'full_name' => 'Ustadz Talaqqi',
        'gender' => 'L',
        'specializations' => ['tahfidz'],
        'is_active' => true,
    ]);

    MentorPedagogicalProfile::create([
        'mentor_id' => $mentorAuditori->id,
        'visual_capability' => 3,
        'auditory_capability' => 10,
        'kinesthetic_capability' => 2,
        'patience_rating' => 9,
        'historical_retention_rate' => 95.00,
    ]);

    $mentorVisualUser = User::factory()->create();
    $mentorVisual = Mentor::create([
        'user_id' => $mentorVisualUser->id,
        'full_name' => 'Ustadz Visual',
        'gender' => 'L',
        'specializations' => ['tahfidz'],
        'is_active' => true,
    ]);

    MentorPedagogicalProfile::create([
        'mentor_id' => $mentorVisual->id,
        'visual_capability' => 10,
        'auditory_capability' => 3,
        'kinesthetic_capability' => 3,
        'patience_rating' => 5,
        'historical_retention_rate' => 80.00,
    ]);

    $studentAuditori = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Bilal',
        'age' => 11,
        'gender' => 'L',
    ]);

    StudentLearningStyle::create([
        'student_id' => $studentAuditori->id,
        'visual_score' => 2,
        'auditory_score' => 10,
        'kinesthetic_score' => 2,
        'patience_need' => 9,
    ]);

    $scoreAuditori = $this->matchingService->calculatePedagogyScore($mentorAuditori, $studentAuditori);
    $scoreVisual = $this->matchingService->calculatePedagogyScore($mentorVisual, $studentAuditori);

    expect($scoreAuditori)->toBeGreaterThan($scoreVisual);
    expect($scoreAuditori)->toBeGreaterThanOrEqual(90.0);
});
