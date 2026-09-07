<?php

use App\Enums\EnrollmentStatus;
use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\MentorTraining;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentMutationLog;
use App\Models\User;
use App\Services\MentorMatchingService;

beforeEach(function () {
    $this->matchingService = app(MentorMatchingService::class);

    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Guru / Mentor']);
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
});

test('matching algorithm returns top 3 recommended mentors with multi-level tie breaker', function () {
    $program = Program::create([
        'name' => 'Tahfidz Al-Qur\'an Juz 30',
        'category' => 'tahfidz',
        'price' => 500000,
        'is_active' => true,
    ]);

    $studentUser = User::factory()->create();
    $student = Student::create([
        'user_id' => $studentUser->id,
        'full_name' => 'Santri Fathan',
        'age' => 10,
        'gender' => 'L',
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'program_id' => $program->id,
        'day_preference' => 'Senin',
        'learning_method' => 'online',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    // Create 5 Mentors with availabilities on monday
    for ($i = 1; $i <= 5; $i++) {
        $mUser = User::factory()->create(['name' => 'Ustadz '.$i]);
        $mentor = Mentor::create([
            'user_id' => $mUser->id,
            'full_name' => 'Ustadz '.$i,
            'gender' => 'L',
            'specialization' => 'Tahfidz',
            'specializations' => ['tahfidz'],
            'rating' => 4.5 + ($i * 0.1),
            'is_active' => true,
            'max_students_per_day' => 10,
            'students_count' => $i * 2,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'start_time' => '16:00:00',
            'end_time' => '18:00:00',
            'max_students' => 10,
            'is_available' => true,
            'is_holiday' => false,
        ]);
    }

    $recommendations = $this->matchingService->getTopRecommendations($enrollment, 3);

    expect($recommendations)->toHaveCount(3);
    expect($recommendations[0]['score'])->toBeGreaterThanOrEqual($recommendations[1]['score']);
    expect($recommendations[1]['score'])->toBeGreaterThanOrEqual($recommendations[2]['score']);
    expect($recommendations[0]['breakdown'])->toHaveKeys(['gender', 'location', 'slot', 'specialization', 'load']);
});

test('gender match follows sharia age and gender guidelines', function () {
    $femaleStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santriwati Aisyah',
        'age' => 12,
        'gender' => 'P',
    ]);

    $youngBoyStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Cilik Hasan',
        'age' => 7,
        'gender' => 'L',
    ]);

    $olderBoyStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Remaja Ali',
        'age' => 11,
        'gender' => 'L',
    ]);

    $femaleMentor = Mentor::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Ustadzah Maryam',
        'gender' => 'P',
        'is_active' => true,
    ]);

    $maleMentor = Mentor::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Ustadz Ahmad',
        'gender' => 'L',
        'is_active' => true,
    ]);

    $program = (object) ['name' => 'Tahfidz Qur\'an', 'category' => 'tahfidz'];

    // 1. Santri Perempuan -> Wajib Ustazah (100%), Ustadz Diskualifikasi (0%)
    expect($this->matchingService->calculateGenderScore($femaleMentor, $femaleStudent, $program))->toBe(100.0);
    expect($this->matchingService->calculateGenderScore($maleMentor, $femaleStudent, $program))->toBe(0.0);

    // 2. Santri Laki-laki < 10 Tahun -> Wajib Ustazah (100%), Ustadz Diskualifikasi (0%)
    expect($this->matchingService->calculateGenderScore($femaleMentor, $youngBoyStudent, $program))->toBe(100.0);
    expect($this->matchingService->calculateGenderScore($maleMentor, $youngBoyStudent, $program))->toBe(0.0);

    // 3. Santri Laki-laki >= 10 Tahun -> Wajib Ustadz (100%), Ustazah Diskualifikasi (0%)
    expect($this->matchingService->calculateGenderScore($maleMentor, $olderBoyStudent, $program))->toBe(100.0);
    expect($this->matchingService->calculateGenderScore($femaleMentor, $olderBoyStudent, $program))->toBe(0.0);
});

test('family blacklist completely disqualifies mentor to zero score', function () {
    $parentUser = User::factory()->create();
    $parent = ParentProfile::create([
        'user_id' => $parentUser->id,
        'address' => 'Jakarta',
    ]);

    $studentUser = User::factory()->create();
    $student = Student::create([
        'user_id' => $studentUser->id,
        'parent_id' => $parent->id,
        'full_name' => 'Santri Umar',
        'age' => 9,
        'gender' => 'L',
    ]);

    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Zaid',
        'gender' => 'L',
        'is_active' => true,
    ]);

    MentorAvailability::create([
        'mentor_id' => $mentor->id,
        'day' => 'monday',
        'start_time' => '16:00:00',
        'end_time' => '18:00:00',
        'max_students' => 10,
        'is_available' => true,
    ]);

    // Insert mutation log as dissatisfaction
    StudentMutationLog::create([
        'parent_id' => $parent->id,
        'student_id' => $student->id,
        'previous_mentor_id' => $mentor->id,
        'reason_category' => 'dissatisfaction',
        'notes' => 'Tidak cocok dengan metode ajar',
    ]);

    $program = Program::create([
        'name' => 'Tahsin Dasar',
        'category' => 'tahsin',
        'price' => 350000,
        'is_active' => true,
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'program_id' => $program->id,
        'day_preference' => 'Senin',
        'learning_method' => 'online',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $recommendations = $this->matchingService->getTopRecommendations($enrollment, 3);
    $blacklistedRec = $recommendations->firstWhere('mentor.id', $mentor->id);

    expect($blacklistedRec)->toBeNull();
    expect($this->matchingService->isFamilyBlacklisted($student, $mentor))->toBeTrue();
});

test('location score handles online 100 percent and offline distance brackets', function () {
    $studentUser = User::factory()->create();
    $student = Student::create([
        'user_id' => $studentUser->id,
        'full_name' => 'Santri Bilal',
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'age' => 10,
        'gender' => 'L',
    ]);

    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Salman',
        'gender' => 'L',
        'latitude' => -6.2100, // Very close (~300m)
        'longitude' => 106.8460,
        'is_active' => true,
    ]);

    // Online method -> Always 100%
    $onlineScore = $this->matchingService->calculateLocationScore($mentor, $student, 'online');
    expect($onlineScore)->toBe(100.0);

    // Offline method close <= 5km -> 100%
    $offlineScore = $this->matchingService->calculateLocationScore($mentor, $student, 'offline');
    expect($offlineScore)->toBe(100.0);

    // Coordinate empty fallback -> 80%
    $emptyStudent = Student::create(['user_id' => User::factory()->create()->id, 'full_name' => 'Santri No GPS', 'age' => 10, 'gender' => 'L']);
    $fallbackScore = $this->matchingService->calculateLocationScore($mentor, $emptyStudent, 'offline');
    expect($fallbackScore)->toBe(80.0);
});

test('gamification badge boost and high rating boost grant additional 5 percent', function () {
    $badge = Badge::firstOrCreate(
        ['code' => 'M01'],
        [
            'name' => 'Pendamping Teladan',
            'description' => 'Lencana penghargaan mentor berdedikasi tinggi',
            'icon' => 'bi-award-fill',
            'category' => 'achievement',
            'is_active' => true,
        ]
    );

    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Teladan',
        'gender' => 'L',
        'is_active' => true,
        'rating' => 4.95,
    ]);

    MentorTraining::create([
        'mentor_id' => $mentor->id,
        'title' => 'Sertifikasi Muallim Qur\'ani',
        'category' => 'pedagogy',
        'training_date' => now(),
        'duration_hours' => 10,
        'badge_id' => $badge->id,
    ]);

    $student = Student::create(['user_id' => User::factory()->create()->id, 'full_name' => 'Santri A', 'age' => 10, 'gender' => 'L']);
    $program = (object) ['name' => 'Tahfidz', 'category' => 'tahfidz'];

    $breakdown = $this->matchingService->calculateBreakdown($mentor, $student, $program, 'monday', 'online');

    expect($breakdown['gamification_boost'])->toBe(5.0);
});

test('auto assign allocates mentor when score is 95 percent or higher', function () {
    $mentorUser = User::factory()->create(['name' => 'Ustadz Top Match']);
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Top Match',
        'gender' => 'L',
        'specialization' => 'Tahfidz',
        'specializations' => ['tahfidz'],
        'rating' => 5.0,
        'is_active' => true,
        'max_students_per_day' => 10,
        'students_count' => 1,
    ]);

    MentorAvailability::create([
        'mentor_id' => $mentor->id,
        'day' => 'monday',
        'start_time' => '16:00:00',
        'end_time' => '18:00:00',
        'max_students' => 10,
        'is_available' => true,
    ]);

    $program = Program::create([
        'name' => 'Tahfidz Al-Qur\'an',
        'category' => 'tahfidz',
        'price' => 450000,
        'is_active' => true,
    ]);

    $student = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Sempurna',
        'age' => 10,
        'gender' => 'L',
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'program_id' => $program->id,
        'day_preference' => 'Senin',
        'learning_method' => 'online',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $autoAssigned = $this->matchingService->autoAssignIfEligible($enrollment);

    expect($autoAssigned)->toBeTrue();
    $enrollment->refresh();
    expect($enrollment->mentor_id)->toBe($mentor->id);
    expect($enrollment->status)->toBe(EnrollmentStatus::CONFIRMED);
    expect($enrollment->matching_score)->toBeGreaterThanOrEqual(95.0);
});

test('explain mentor exclusion returns reasons when mentor is inactive or unsuitable', function () {
    $inactiveMentor = Mentor::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Ustadz Cuti',
        'gender' => 'L',
        'is_active' => false,
    ]);

    $student = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Test',
        'age' => 10,
        'gender' => 'L',
    ]);

    $program = Program::create([
        'name' => 'Tahsin Qur\'an',
        'category' => 'tahsin',
        'price' => 300000,
        'is_active' => true,
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $student->id,
        'program_id' => $program->id,
        'day_preference' => 'Senin',
        'learning_method' => 'offline',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $reasons = $this->matchingService->explainMentorExclusion($enrollment, $inactiveMentor->id);

    expect($reasons)->toBeArray();
    expect(implode(' ', $reasons))->toContain('tidak aktif');
});

test('mentor on holiday or off duty receives zero slot score and is excluded', function () {
    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustadz Hari Bebas',
        'gender' => 'L',
        'is_active' => true,
    ]);

    // Availability set as is_holiday = true
    MentorAvailability::create([
        'mentor_id' => $mentor->id,
        'day' => 'monday',
        'start_time' => '16:00:00',
        'end_time' => '18:00:00',
        'max_students' => 10,
        'is_available' => true,
        'is_holiday' => true,
        'notes' => 'Hari Bebas Kuliah',
    ]);

    $student = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Budi',
        'age' => 12,
        'gender' => 'L',
    ]);

    $slotScore = $this->matchingService->calculateSlotScore($mentor, 'monday', '16:00:00');
    expect($slotScore)->toBe(0.0);
});

test('mentor with booked 1-on-1 slot on any requested day is disqualified with zero score', function () {
    $mentorUser = User::factory()->create();
    $mentor = Mentor::create([
        'user_id' => $mentorUser->id,
        'full_name' => 'Ustazah Fatimah',
        'gender' => 'P',
        'is_active' => true,
        'specializations' => ['tahsin'],
    ]);

    // Availabilities on Monday and Tuesday
    MentorAvailability::create([
        'mentor_id' => $mentor->id,
        'day' => 'monday',
        'slot_numbers' => [4],
        'is_available' => true,
        'is_holiday' => false,
    ]);

    MentorAvailability::create([
        'mentor_id' => $mentor->id,
        'day' => 'tuesday',
        'slot_numbers' => [4],
        'is_available' => true,
        'is_holiday' => false,
    ]);

    // Existing active student on Monday slot 4
    $existingStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Lain',
        'age' => 8,
        'gender' => 'P',
    ]);

    $mentor->students()->attach($existingStudent->id, [
        'day_assigned' => 'monday',
        'slot_number' => 4,
        'time_assigned' => '16:30:00',
        'is_active' => true,
    ]);

    $newStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Baru',
        'age' => 7,
        'gender' => 'P',
    ]);

    $program = Program::create([
        'name' => 'Iqra & Dasar',
        'category' => 'tahsin',
        'price' => 300000,
        'is_active' => true,
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $newStudent->id,
        'program_id' => $program->id,
        'requested_days' => ['monday', 'tuesday'],
        'requested_time' => '16:00:00',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    $slotScore = $this->matchingService->calculateSlotScore($mentor, 'monday', $enrollment);
    expect($slotScore)->toBe(0.0);

    $recs = $this->matchingService->getTopRecommendations($enrollment, 3);
    expect($recs->contains(fn ($item) => $item['mentor']->id === $mentor->id))->toBeFalse();
    expect($mentor->hasScheduleConflict(['monday', 'tuesday'], '16:00:00', $newStudent->id))->toBeTrue();
    expect($mentor->isAvailableForSchedule(['monday', 'tuesday'], '16:00:00', $newStudent->id))->toBeFalse();
});
