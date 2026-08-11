<?php

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;

test('guest cannot see biaya link but can see bergabung link on navbar', function () {
    $this->get(route('home'))
        ->assertStatus(200)
        ->assertDontSee(route('biaya'))
        ->assertSee(route('bergabung'));
});

test('admin user can see biaya link but cannot see bergabung link on navbar', function () {
    $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => 'Admin']);
    $user = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertStatus(200)
        ->assertSee(route('biaya'))
        ->assertDontSee(route('bergabung'));
});

test('parent user can see biaya link but cannot see bergabung link on navbar', function () {
    $parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => 'Orang Tua / Wali']);
    $user = User::factory()->create(['role_id' => $parentRole->id]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertStatus(200)
        ->assertSee(route('biaya'))
        ->assertDontSee(route('bergabung'));
});

test('mentor user cannot see biaya link and cannot see bergabung link on navbar', function () {
    $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => 'Pendamping / Guru']);
    $user = User::factory()->create(['role_id' => $mentorRole->id]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertStatus(200)
        ->assertDontSee(route('biaya'))
        ->assertDontSee(route('bergabung'));
});

test('student user cannot see biaya link and cannot see bergabung link on navbar', function () {
    $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => 'Murid / Santri']);
    $user = User::factory()->create(['role_id' => $studentRole->id]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertStatus(200)
        ->assertDontSee(route('biaya'))
        ->assertDontSee(route('bergabung'));
});
