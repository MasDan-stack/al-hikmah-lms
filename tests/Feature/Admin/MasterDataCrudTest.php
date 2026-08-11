<?php

use App\Enums\Role as RoleEnum;
use App\Livewire\MentorManager;
use App\Livewire\ProgramManager;
use App\Livewire\StudentManager;
use App\Models\Mentor;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Ensure roles exist
    foreach (RoleEnum::cases() as $role) {
        Role::firstOrCreate(
            ['name' => $role->value],
            ['label' => $role->label()]
        );
    }
});

test('admin can access master data pages', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get(route('admin.programs.index'))
        ->assertStatus(200)
        ->assertSee('Kelola Program Belajar');

    $this->actingAs($admin)
        ->get(route('admin.mentors.index'))
        ->assertStatus(200)
        ->assertSee('Kelola Data Pendamping');

    $this->actingAs($admin)
        ->get(route('admin.students.index'))
        ->assertStatus(200)
        ->assertSee('Kelola Data Murid');
});

test('non-admin users cannot access master data pages', function () {
    $studentRole = Role::where('name', 'student')->first();
    $student = User::factory()->create(['role_id' => $studentRole->id]);

    $this->actingAs($student)
        ->get(route('admin.programs.index'))
        ->assertStatus(403);
});

test('can create, update, and delete program via Livewire', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin);

    Livewire::test(ProgramManager::class)
        ->set('name', 'Tahfidz Juz 30')
        ->set('description', 'Bimbingan khusus hafalan Juz 30')
        ->set('duration_weeks', 12)
        ->set('price', 350000)
        ->set('level', 'Pemula')
        ->call('saveProgram')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('programs', [
        'name' => 'Tahfidz Juz 30',
        'level' => 'Pemula',
    ]);

    $program = Program::where('name', 'Tahfidz Juz 30')->first();

    Livewire::test(ProgramManager::class)
        ->call('openEditModal', $program->id)
        ->set('name', 'Tahfidz Juz 30 Intensif')
        ->call('saveProgram');

    $this->assertDatabaseHas('programs', [
        'id' => $program->id,
        'name' => 'Tahfidz Juz 30 Intensif',
    ]);

    Livewire::test(ProgramManager::class)
        ->call('confirmDelete', $program->id)
        ->call('deleteProgram');

    $this->assertDatabaseMissing('programs', [
        'id' => $program->id,
    ]);
});

test('can create, update, and delete mentor via Livewire', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin);

    Livewire::test(MentorManager::class)
        ->set('full_name', 'Ustadz Ahmad')
        ->set('create_new_user', true)
        ->set('user_email', 'ahmad@alhikmah.id')
        ->set('user_password', 'password123')
        ->set('specialization', 'Tahsin & Tajwid')
        ->set('rating', 4.8)
        ->set('is_active', true)
        ->call('saveMentor')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('mentors', [
        'full_name' => 'Ustadz Ahmad',
        'specialization' => 'Tahsin & Tajwid',
    ]);

    $mentor = Mentor::where('full_name', 'Ustadz Ahmad')->first();

    Livewire::test(MentorManager::class)
        ->call('openEditModal', $mentor->id)
        ->set('specialization', 'Tahfidz & Tajwid')
        ->call('saveMentor');

    $this->assertDatabaseHas('mentors', [
        'id' => $mentor->id,
        'specialization' => 'Tahfidz & Tajwid',
    ]);

    Livewire::test(MentorManager::class)
        ->call('confirmDelete', $mentor->id)
        ->call('deleteMentor');

    $this->assertDatabaseMissing('mentors', [
        'id' => $mentor->id,
    ]);
});

test('can create, update, and delete student via Livewire', function () {
    $adminRole = Role::where('name', 'admin')->first();
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin);

    Livewire::test(StudentManager::class)
        ->set('full_name', 'Muhammad Ali')
        ->set('create_new_user', true)
        ->set('user_email', 'ali@alhikmah.id')
        ->set('user_password', 'password123')
        ->set('age', 12)
        ->set('gender', 'L')
        ->set('location', 'Jakarta')
        ->call('saveStudent')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('students', [
        'full_name' => 'Muhammad Ali',
        'age' => 12,
        'gender' => 'L',
    ]);

    $student = Student::where('full_name', 'Muhammad Ali')->first();

    Livewire::test(StudentManager::class)
        ->call('openEditModal', $student->id)
        ->set('age', 13)
        ->call('saveStudent');

    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'age' => 13,
    ]);

    Livewire::test(StudentManager::class)
        ->call('confirmDelete', $student->id)
        ->call('deleteStudent');

    $this->assertDatabaseMissing('students', [
        'id' => $student->id,
    ]);
});
