<?php

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
});

test('admin enrollment edit page excludes mentors with schedule conflict from OPSI A dropdown', function () {
    $program = Program::create([
        'name' => 'Iqra & Dasar Al-Qur\'an',
        'category' => 'tahsin',
        'price' => 350000,
        'is_active' => true,
    ]);

    // Mentor Fatimah
    $fatimahUser = User::factory()->create(['name' => 'Ustazah Fatimah']);
    $fatimah = Mentor::create([
        'user_id' => $fatimahUser->id,
        'full_name' => 'Ustazah Fatimah Az-Zahra',
        'gender' => 'P',
        'is_active' => true,
        'specializations' => ['tahsin'],
    ]);

    MentorAvailability::create([
        'mentor_id' => $fatimah->id,
        'day' => 'monday',
        'slot_numbers' => [4],
        'is_available' => true,
        'is_holiday' => false,
    ]);

    MentorAvailability::create([
        'mentor_id' => $fatimah->id,
        'day' => 'tuesday',
        'slot_numbers' => [4],
        'is_available' => true,
        'is_holiday' => false,
    ]);

    // Assign existing student to Fatimah on Monday at 16:30 (slot 4)
    $existingStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Santri Eksisting',
        'age' => 8,
        'gender' => 'P',
    ]);

    $fatimah->students()->attach($existingStudent->id, [
        'day_assigned' => 'monday',
        'slot_number' => 4,
        'time_assigned' => '16:30:00',
        'is_active' => true,
    ]);

    // New student enrolling for Monday & Tuesday at 16:00
    $newStudent = Student::create([
        'user_id' => User::factory()->create()->id,
        'full_name' => 'Hikmatul Hasanah',
        'age' => 7,
        'gender' => 'P',
    ]);

    $enrollment = Enrollment::create([
        'student_id' => $newStudent->id,
        'program_id' => $program->id,
        'requested_days' => ['monday', 'tuesday'],
        'requested_time' => '16:00:00',
        'status' => EnrollmentStatus::WAITING_ADMIN,
    ]);

    // Access edit page
    $response = $this->actingAs($this->admin)->get(route('admin.enrollments.edit', $enrollment->id));
    $response->assertOk();

    // Fatimah must NOT be in OPSI A available mentors
    $availableMentorsForOptionA = $response->viewData('availableMentorsForOptionA');
    expect($availableMentorsForOptionA->contains('id', $fatimah->id))->toBeFalse();

    // The page should show the conflict warning for OPSI A if no other mentors available
    $response->assertSee('Seluruh mentor tidak tersedia / bentrok jadwal');
    $response->assertSee('Jadwal Bentrok (Gunakan OPSI B)');

    // Attempting to post accept for conflicting mentor must be rejected
    $postResponse = $this->actingAs($this->admin)->post(route('admin.enrollments.accept', $enrollment->id), [
        'mentor_id' => $fatimah->id,
        'start_date' => now()->addDays(3)->format('Y-m-d'),
    ]);

    $postResponse->assertSessionHas('error');
    expect($enrollment->fresh()->status)->toBe(EnrollmentStatus::WAITING_ADMIN);
});
