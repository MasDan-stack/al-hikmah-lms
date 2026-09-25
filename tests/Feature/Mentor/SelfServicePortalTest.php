<?php

use App\Models\Mentor;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);

    $this->mentorUser = User::factory()->create([
        'name' => 'Ustadzah Fatimah',
        'role_id' => $this->mentorRole->id,
    ]);

    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ustadzah Fatimah',
        'is_active' => true,
        'status' => 'active',
        'specialization' => 'Tahsin Tilawah',
    ]);
});

test('mentor can view personal performance scorecard and percentile', function () {
    $response = $this->actingAs($this->mentorUser)->get(route('mentor.performance.index'));

    $response->assertStatus(200);
    $response->assertSee('Portal Kinerja Saya & Goals');
    $response->assertSee('Skor Komposit Bulan Ini');
    $response->assertSee('Diagram Keseimbangan 5 Pilar');
});

test('mentor can adopt 1-click ai recommendation into mentor goals', function () {
    $response = $this->actingAs($this->mentorUser)->post(route('mentor.performance.goals.store'), [
        'title' => 'Pertahankan kedisiplinan jadwal dan mulai sesi tepat waktu',
        'goal_type' => 'attendance',
        'target_value' => 100,
        'period' => now()->format('Y-m'),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_goals', [
        'mentor_id' => $this->mentor->id,
        'title' => 'Pertahankan kedisiplinan jadwal dan mulai sesi tepat waktu',
        'status' => 'in_progress',
    ]);
});

test('mentor can submit monthly self assessment reflection', function () {
    $response = $this->actingAs($this->mentorUser)->post(route('mentor.performance.self-assessment.store'), [
        'period' => now()->format('Y-m'),
        'self_score' => 90,
        'strengths' => 'Santri menunjukkan perkembangan signifikan dalam kelancaran membaca.',
        'challenges' => 'Koneksi internet kadang kurang stabil saat sesi online.',
        'action_plan' => 'Menyiapkan materi rekaman audio offline sebagai pendukung.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('mentor_self_assessments', [
        'mentor_id' => $this->mentor->id,
        'period' => now()->format('Y-m'),
    ]);
});
