<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\Role as RoleEnum;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_access_sessions_page(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ust. Dandi Hermawan',
            'is_active' => true,
        ]);

        $studentUser = User::factory()->create(['name' => 'Hikmatul Hasanah']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Hikmatul Hasanah',
            'age' => 9,
        ]);

        Session::create([
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'time' => '18:30:00',
            'method' => 'offline',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($mentorUser)->get(route('mentor.sessions.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Sesi Mengajar Santri');
        $response->assertSee('Hikmatul Hasanah');
        $response->assertSee('18:30');
    }

    public function test_mentor_can_access_students_page(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ust. Dandi Hermawan',
            'is_active' => true,
        ]);

        $studentUser = User::factory()->create(['name' => 'Hikmatul Hasanah']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Hikmatul Hasanah',
            'age' => 9,
        ]);

        $program = Program::create([
            'name' => 'Tahfidz Qur\'an',
            'slug' => 'tahfidz-quran',
            'is_active' => true,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'mentor_id' => $mentor->id,
            'status' => EnrollmentStatus::ACTIVE,
            'requested_days' => ['monday'],
            'requested_time' => '18:30:00',
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($mentorUser)->get(route('mentor.students.index'));

        $response->assertStatus(200);
        $response->assertSee('Santri dalam Bimbingan Anda');
        $response->assertSee('Hikmatul Hasanah');
    }

    public function test_mentor_can_access_parents_page(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ust. Dandi Hermawan',
            'is_active' => true,
        ]);

        $parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => RoleEnum::PARENT->label()]);
        $parentUser = User::factory()->create(['role_id' => $parentRole->id, 'name' => 'H. Rahmat', 'phone' => '08123456789']);
        $parent = ParentProfile::create([
            'user_id' => $parentUser->id,
            'address' => 'Pondok Betung',
            'emergency_phone' => '08123456789',
        ]);

        $studentUser = User::factory()->create(['name' => 'Hikmatul Hasanah']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'full_name' => 'Hikmatul Hasanah',
            'age' => 9,
        ]);

        $program = Program::create([
            'name' => 'Tahfidz Qur\'an',
            'slug' => 'tahfidz-quran',
            'is_active' => true,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'mentor_id' => $mentor->id,
            'status' => EnrollmentStatus::ACTIVE,
            'requested_days' => ['monday'],
            'requested_time' => '18:30:00',
            'start_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($mentorUser)->get(route('mentor.students.parents'));

        $response->assertStatus(200);
        $response->assertSee('Buku Kontak Wali Santri');
        $response->assertSee('H. Rahmat');
        $response->assertSee('Hikmatul Hasanah');
        $response->assertSee('Hubungi WA');
    }
}
