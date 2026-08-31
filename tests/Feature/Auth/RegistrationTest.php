<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('pre registration stores session and redirects to register', function () {
    $response = $this->post(route('register.pre'), [
        'nama' => 'Orang Tua Test',
        'nama_anak' => 'Fathir Ahmad',
        'whatsapp' => '08123456789',
        'usia' => '10-15 tahun (Anak)',
        'gender' => 'L',
        'lokasi' => 'Jakarta South',
        'program' => 'Tahsin',
        'metode' => 'Online',
    ]);

    $response->assertRedirect(route('register'));
    $response->assertSessionHas('pre_registration');
});

test('completing registration with pre_registration session creates parent profile and unique student user', function () {
    $this->withSession([
        'pre_registration' => [
            'nama' => 'Orang Tua Modal',
            'nama_anak' => 'Ahmad Junior',
            'whatsapp' => '089988776655',
            'usia' => '12 tahun',
            'gender' => 'L',
            'lokasi' => 'Bandung',
            'program' => 'Tahfidz',
            'metode' => 'Offline (Home Visit)',
        ],
    ]);

    $response = $this->post('/register', [
        'name' => 'Orang Tua Modal',
        'email' => 'parentmodal@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'parent',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('parent.dashboard'));

    $this->assertDatabaseHas('users', [
        'email' => 'parentmodal@example.com',
        'name' => 'Orang Tua Modal',
    ]);

    $this->assertDatabaseHas('parents', [
        'address' => 'Bandung',
        'emergency_phone' => '089988776655',
    ]);

    $this->assertDatabaseHas('students', [
        'full_name' => 'Ahmad Junior',
        'location' => 'Bandung',
    ]);
});
