<?php

use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;

test('public landing pages return HTTP 200 status', function () {
    $this->get(route('home'))->assertStatus(200);
    $this->get(route('tentang-kami'))->assertStatus(200);
    $this->get(route('program'))->assertStatus(200);
    $this->get(route('metode'))->assertStatus(200);
    $this->get(route('tahfidz'))->assertStatus(200);
    $this->get(route('biaya'))->assertStatus(403);
});

test('program page renders dynamic database programs when available', function () {
    Program::create([
        'name' => 'Tahfidz Juz 30 Super',
        'description' => 'Program hafalan khusus juz 30',
        'duration_weeks' => 8,
        'price' => 250000,
        'level' => 'Pemula',
    ]);

    $this->get(route('program'))
        ->assertStatus(200)
        ->assertSee('Tahfidz Juz 30 Super');
});

test('biaya page renders dynamic program prices when accessed by parent', function () {
    Program::create([
        'name' => 'Paket Bimbingan Tajwid Extra',
        'description' => 'Pendampingan tajwid intensif',
        'duration_weeks' => 12,
        'price' => 500000,
        'level' => 'Menengah',
    ]);

    $parentUser = User::factory()->parent()->create();
    $parentProfile = ParentProfile::create(['user_id' => $parentUser->id]);
    $studentUser = User::factory()->student()->create();
    Student::create([
        'user_id' => $studentUser->id,
        'parent_id' => $parentProfile->id,
        'full_name' => 'Santri Anak',
        'age' => 10,
        'gender' => 'L',
    ]);

    $this->actingAs($parentUser)->get(route('biaya'))
        ->assertStatus(200)
        ->assertSee('Paket Bimbingan Tajwid Extra')
        ->assertSee('500.000');
});

test('tentang kami page displays statistic counters', function () {
    $this->get(route('tentang-kami'))
        ->assertStatus(200)
        ->assertSee('Santri Terdaftar')
        ->assertSee('Pendamping Aktif')
        ->assertSee('Program Belajar');
});

test('home page renders real-time prayer times widget and modals', function () {
    $this->get(route('home'))
        ->assertStatus(200)
        ->assertSee('Waktu Ibadah Harian')
        ->assertSee('id="jadwal-sholat"', false)
        ->assertSee('id="cityModal"', false)
        ->assertSee('id="qiblaModal"', false)
        ->assertSee('btn-detect-gps');
});
