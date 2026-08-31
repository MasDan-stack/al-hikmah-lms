<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('prospective mentor can register via /bergabung and is assigned mentor role', function () {
    Storage::fake('local');

    $response = $this->post(route('mentor.recruitment.store'), [
        'full_name' => 'Ustadz Ahmad Fulan',
        'email' => 'ahmad.mentor@alhikmah.id',
        'phone' => '081234567890',
        'birth_date' => '1995-05-15',
        'gender' => 'male',
        'address' => 'Jl. Dakwah No. 10',
        'city' => 'Jakarta Selatan',
        'education' => 'S1 Pendidikan Agama Islam',
        'institution' => 'LIPIA Jakarta',
        'experience_years' => 3,
        'experience_description' => 'Mengajar tahsin dan tahfidz anak-anak dan dewasa selama 3 tahun.',
        'specialization' => 'Tahsin & Tahfidz Juz 30',
        'sanad_chain' => 'Riwayat Hafs \'an \'Ashim',
        'hifz_total_juz' => 30,
        'cv' => UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf'),
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('mentor.dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'ahmad.mentor@alhikmah.id')->first();
    expect($user)->not->toBeNull();
    expect($user->isMentor())->toBeTrue();
    expect($user->mentor)->not->toBeNull();
    expect($user->mentor->specialization)->toBe('Tahsin & Tahfidz Juz 30');
});

test('parent can register via /register and is assigned parent role', function () {
    $response = $this->post(route('register'), [
        'role' => 'parent',
        'name' => 'Bapak Budi Santoso',
        'email' => 'budi.parent@alhikmah.id',
        'phone' => '089876543210',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('parent.dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'budi.parent@alhikmah.id')->first();
    expect($user)->not->toBeNull();
    expect($user->isParent())->toBeTrue();
    expect($user->parentProfile)->not->toBeNull();
});

test('student can register via /register and is assigned student role', function () {
    $response = $this->post(route('register'), [
        'role' => 'student',
        'name' => 'Santri Ali',
        'email' => 'ali.student@alhikmah.id',
        'phone' => '08111222333',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $this->assertAuthenticated();

    $user = User::where('email', 'ali.student@alhikmah.id')->first();
    expect($user)->not->toBeNull();
    expect($user->isStudent())->toBeTrue();
    expect($user->student)->not->toBeNull();
});
