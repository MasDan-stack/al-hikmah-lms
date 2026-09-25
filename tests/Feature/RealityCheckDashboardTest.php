<?php

use App\Models\Role;
use App\Models\User;

test('admin can view reality check dashboard', function () {
    $role = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $admin = User::factory()->create(['role_id' => $role->id]);

    $response = $this->actingAs($admin)->get(route('admin.reality-check.index'));
    $response->assertStatus(200);
});

test('admin can export reality check csv', function () {
    $role = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $admin = User::factory()->create(['role_id' => $role->id]);

    $response = $this->actingAs($admin)->get(route('admin.reality-check.export'));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});
