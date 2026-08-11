<?php

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;

test('authenticated admin can access admin dashboard and quick action routes', function () {
    $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertSee('Dashboard AL-HIKMAH')
        ->assertSee(route('admin.students.index'))
        ->assertSee(route('report.download'));

    $this->actingAs($admin)
        ->get(route('admin.students.index'))
        ->assertStatus(200);

    $this->actingAs($admin)
        ->get(route('admin.mentors.index'))
        ->assertStatus(200);

    $this->actingAs($admin)
        ->get(route('admin.programs.index'))
        ->assertStatus(200);
});

test('authenticated users can access report download route', function () {
    $parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => RoleEnum::PARENT->label()]);
    $user = User::factory()->create(['role_id' => $parentRole->id]);

    $this->actingAs($user)
        ->get(route('report.download'))
        ->assertStatus(200)
        ->assertSee('AL-HIKMAH LEARNING MANAGEMENT SYSTEM')
        ->assertSee('Cetak / Simpan PDF');
});

test('generic dashboard redirects appropriately based on user role', function () {
    $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertRedirect(route('admin.dashboard'));

    $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);
    $student = User::factory()->create(['role_id' => $studentRole->id]);

    $this->actingAs($student)
        ->get(route('dashboard'))
        ->assertRedirect(route('student.dashboard'));
});
