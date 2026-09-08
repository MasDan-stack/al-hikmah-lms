<?php

use App\Models\Mentor;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);

    $this->admin = User::factory()->create([
        'role_id' => $this->adminRole->id,
    ]);

    $this->mentorUser = User::factory()->create([
        'name' => 'Ustadz Ahmad Fauzi',
        'role_id' => $this->mentorRole->id,
        'phone' => '081234567890',
    ]);

    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ustadz Ahmad Fauzi',
        'is_active' => true,
        'phone' => '081234567890',
        'specialization' => 'Tahfidz Al-Qur\'an',
    ]);
});

test('admin can access mentor performance dashboard and view kpis', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.performance.mentors.index'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard Performa Mentor & AI Coaching');
    $response->assertSee('Top 10 Guru Pembimbing Berprestasi');
});

test('admin can view mentor 360 scorecard and ai coaching recommendations', function () {
    $this->withoutExceptionHandling();
    $response = $this->actingAs($this->admin)->get(route('admin.performance.mentors.show', ['id' => $this->mentor->id]));

    $response->assertStatus(200);
    $response->assertSee('Scorecard 360°');
    $response->assertSee('Ustadz Ahmad Fauzi');
    $response->assertSee('Radar Kinerja 5 Dimensi');
});

test('admin can trigger snapshot recalculation with financial audit log', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.performance.mentors.recalculate', ['id' => $this->mentor->id]), [
        'month' => now()->format('Y-m'),
        'reason' => 'Koreksi penyesuaian presensi santri pekan kedua oleh koordinator kurikulum.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('financial_audit_logs', [
        'user_id' => $this->admin->id,
        'action' => 'recalculate_mentor_performance',
    ]);
});

test('admin can send whatsapp performance digest to mentor', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.performance.mentors.send-wa', [
        'id' => $this->mentor->id,
        'month' => now()->format('Y-m'),
    ]));

    $response->assertRedirect();
    $response->assertSessionHas('success');
});
