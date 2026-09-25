<?php

use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->parent()->create();

    $this->parentProfile = ParentProfile::create([
        'user_id' => $this->user->id,
        'emergency_phone' => '08123456789',
        'address' => 'Jl. Test No. 123',
    ]);

    $this->program = Program::create([
        'name' => 'Tahsin Dasar',
        'price' => 450000,
        'description' => 'Program belajar membaca Al-Qur\'an dari dasar.',
        'is_active' => true,
    ]);
});

it('redirects direct registered parent to children registration when accessing biaya without children', function () {
    $this->actingAs($this->user);

    expect($this->user->hasChildren())->toBeFalse();

    $response = $this->get('/biaya');

    $response->assertRedirect(route('parent.profile.children'));
    $response->assertSessionHas('warning', 'Sebelum memilih program belajar, silakan daftarkan data lengkap anak binaan Anda terlebih dahulu.');
});

it('displays onboarding state 1A (Isi Data Anak) on dashboard when parent has 0 children', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('parent.dashboard'));

    $response->assertOk();
    $response->assertSee('Langkah 1: Daftarkan Data Anak');
    $response->assertSee('Data Anak Belum Terdaftar');
    $response->assertSee(route('parent.profile.children'));
});

it('successfully registers child from parent profile children page', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('parent.profile.store-child'), [
        'full_name' => 'Ahmad Rayhan Al-Fatih',
        'age' => 9,
        'gender' => 'L',
        'location' => 'Jakarta Selatan',
    ]);

    $response->assertRedirect(route('parent.profile.children'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('students', [
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Ahmad Rayhan Al-Fatih',
        'age' => 9,
        'gender' => 'L',
    ]);

    // Refresh model cache
    $freshUser = $this->user->fresh();
    expect($freshUser->hasChildren())->toBeTrue();
});

it('allows parent to access biaya and displays state 1B on dashboard once child is registered', function () {
    $studentUser = User::factory()->student()->create();
    Student::create([
        'user_id' => $studentUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Ahmad Rayhan',
        'age' => 9,
        'gender' => 'L',
    ]);

    $this->actingAs($this->user);
    expect($this->user->hasChildren())->toBeTrue();

    // 1. Dashboard State 1B
    $responseDashboard = $this->get(route('parent.dashboard'));
    $responseDashboard->assertOk();
    $responseDashboard->assertSee('Langkah 2: Pilih Program Belajar');
    $responseDashboard->assertSee(url('/biaya'));

    // 2. Akses halaman Biaya diizinkan (200 OK)
    $responseBiaya = $this->get('/biaya');
    $responseBiaya->assertOk();
    $responseBiaya->assertSee('Tahsin Dasar');
});

it('redirects parent without children when accessing enrollment create page', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('parent.enrollments.create', ['program_id' => $this->program->id]));

    $response->assertRedirect(route('parent.profile.children'));
    $response->assertSessionHas('warning');
});
