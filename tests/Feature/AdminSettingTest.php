<?php

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;

test('admin can access website settings page', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $this->actingAs($admin)
        ->get(route('admin.settings.index'))
        ->assertStatus(200)
        ->assertSee('Pengaturan Website')
        ->assertSee('Nomor WhatsApp CS');
});

test('non admin cannot access website settings page', function () {
    $parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $user = User::factory()->create(['role_id' => $parentRole->id]);

    $this->actingAs($user)
        ->get(route('admin.settings.index'))
        ->assertStatus(403);
});

test('admin can update website settings', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $admin = User::factory()->create(['role_id' => $adminRole->id]);

    $response = $this->actingAs($admin)
        ->post(route('admin.settings.update'), [
            'settings' => [
                'whatsapp_number' => '6281299998888',
                'instagram_handle' => 'alhikmah_official',
            ],
        ]);

    $response->assertRedirect(route('admin.settings.index'));

    expect(Setting::get('whatsapp_number'))->toBe('6281299998888');
    expect(Setting::get('instagram_handle'))->toBe('alhikmah_official');
    expect(wa_url('Test'))->toBe('https://wa.me/6281299998888?text=Test');
});
