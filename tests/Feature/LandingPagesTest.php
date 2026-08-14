<?php

use App\Models\Program;
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

    $this->actingAs($parentUser)->get(route('biaya'))
        ->assertStatus(200)
        ->assertSee('Paket Bimbingan Tajwid Extra')
        ->assertSee('500.000');
});

test('tentang kami page displays statistic counters', function () {
    $this->get(route('tentang-kami'))
        ->assertStatus(200)
        ->assertSee('Santri Terdaftar')
        ->assertSee('Pendamping Aktif');
});
