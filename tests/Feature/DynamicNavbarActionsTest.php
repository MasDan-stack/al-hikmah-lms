<?php

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;

test('guest sees login and start journey buttons on navbar', function () {
    $this->get(route('home'))
        ->assertStatus(200)
        ->assertSee(route('login'))
        ->assertSee('Mulai Perjalanan')
        ->assertDontSee('Keluar (Logout)');
});

test('authenticated admin sees user profile dropdown, role badge, dashboard button, and logout form', function () {
    $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
    $user = User::factory()->create([
        'name' => 'Ahmad Admin',
        'email' => 'admin.test@alhikmah.id',
        'role_id' => $adminRole->id,
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertStatus(200)
        ->assertSee('Ahmad Admin')
        ->assertSee($adminRole->label)
        ->assertSee(route('dashboard'))
        ->assertSee('Keluar (Logout)')
        ->assertDontSee(route('login'));
});

test('authenticated parent sees user profile dropdown, role badge, dashboard button, and logout form', function () {
    $parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => RoleEnum::PARENT->label()]);
    $user = User::factory()->create([
        'name' => 'Ibu Siti Wali',
        'email' => 'siti.parent@alhikmah.id',
        'role_id' => $parentRole->id,
    ]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertStatus(200)
        ->assertSee('Ibu Siti Wali')
        ->assertSee($parentRole->label)
        ->assertSee(route('dashboard'))
        ->assertSee('Keluar (Logout)')
        ->assertDontSee(route('login'));
});

test('all navigation routes in navbar and footer resolve with HTTP 200', function () {
    $this->get(route('home'))->assertStatus(200);
    $this->get(route('tentang-kami'))->assertStatus(200);
    $this->get(route('program'))->assertStatus(200);
    $this->get(route('metode'))->assertStatus(200);
    $this->get(route('tahfidz'))->assertStatus(200);
    $this->get(route('bergabung'))->assertStatus(200);
});
