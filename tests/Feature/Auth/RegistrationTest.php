<?php

use App\Models\Payment;
use App\Models\Program;
use App\Models\Student;

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

test('completing registration with pre_registration session without program redirects to dashboard', function () {
    $this->withSession([
        'pre_registration' => [
            'nama' => 'Orang Tua Modal',
            'nama_anak' => 'Ahmad Junior',
            'whatsapp' => '089988776655',
            'usia' => '12 tahun',
            'gender' => 'L',
            'lokasi' => 'Bandung',
            'program' => null,
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

test('completing registration with program redirects directly to step 2 enrollments create with prefilled student and method', function () {
    $program = Program::firstOrCreate(
        ['name' => 'Tahsin Dasar'],
        ['price' => 450000, 'description' => 'Belajar Tahsin']
    );

    $this->withSession([
        'pre_registration' => [
            'nama' => 'Bapak Hendra',
            'nama_anak' => 'Rizky Pratama',
            'whatsapp' => '081234445555',
            'usia' => '10-15 tahun (Anak)',
            'gender' => 'L',
            'lokasi' => 'Jakarta Selatan',
            'program_id' => $program->id,
            'metode' => 'offline',
            'learning_method' => 'offline',
        ],
    ]);

    $response = $this->post('/register', [
        'name' => 'Bapak Hendra',
        'email' => 'hendra@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'parent',
    ]);

    $this->assertAuthenticated();

    $student = Student::where('full_name', 'Rizky Pratama')->first();
    expect($student)->not->toBeNull();

    $response->assertRedirect(route('parent.enrollments.create', [
        'program_id' => $program->id,
        'student_id' => $student->id,
        'method' => 'offline',
    ]));

    // Memastikan tidak ada status aktif prematur di tabel pivot student_program
    expect($student->programs()->where('student_program.status', 'active')->exists())->toBeFalse();

    // Memastikan tidak ada tagihan / pembayaran palsu yang langsung aktif
    expect(Payment::where('student_id', $student->id)->exists())->toBeFalse();
});

test('completing registration with under 10 years old sets student age correctly below 10 for ustazah matching', function () {
    $this->withSession([
        'pre_registration' => [
            'nama' => 'Bunda Fatimah',
            'nama_anak' => 'Hasan Cilik',
            'whatsapp' => '081122334455',
            'usia' => 'Di bawah 10 tahun (4-9 tahun)',
            'gender' => 'L',
            'lokasi' => 'Semarang',
            'program' => null,
            'metode' => 'Online',
        ],
    ]);

    $response = $this->post('/register', [
        'name' => 'Bunda Fatimah',
        'email' => 'bundafatimah@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'parent',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('parent.dashboard'));

    $student = Student::where('full_name', 'Hasan Cilik')->first();
    expect($student)->not->toBeNull();
    expect($student->age)->toBeLessThan(10);
    expect($student->gender)->toBe('L');
});
