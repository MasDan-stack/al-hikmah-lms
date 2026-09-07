<?php

use App\Models\AhpEvaluationSnapshot;
use App\Models\Mentor;
use App\Models\User;
use App\Services\DecisionSupport\AhpRankingService;

beforeEach(function () {
    $this->adminUser = User::factory()->admin()->create(['name' => 'Admin Lembaga']);
    $this->mentorUser = User::factory()->mentor()->create(['name' => 'Ustadz Salman Al-Farisi']);
    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ustadz Salman Al-Farisi',
        'is_active' => true,
        'status' => 'active',
        'rating' => 4.90,
        'gender' => 'L',
    ]);
});

test('admin can view ahp ranking dashboard', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.mentors.ahp-ranking.index'));

    $response->assertOk();
    $response->assertSee('SPK Guru Teladan (AHP)');
    $response->assertSee('Podium Ustadz/Ustazah Teladan');
    $response->assertSee('radarChartAhp');
    $response->assertSee('ahpLeaderboardTable');
});

test('non-admin user cannot access ahp ranking dashboard', function () {
    $response = $this->actingAs($this->mentorUser)
        ->get(route('admin.mentors.ahp-ranking.index'));

    $response->assertForbidden();
});

test('admin can simulate ahp weights live via ajax', function () {
    $service = new AhpRankingService;
    $matrix = $service->getDefaultPairwiseMatrix();
    $month = now()->format('Y-m');

    $response = $this->actingAs($this->adminUser)
        ->postJson(route('admin.mentors.ahp-ranking.simulate'), [
            'matrix' => $matrix,
            'month' => $month,
        ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'is_consistent' => true,
    ]);
    $response->assertJsonStructure([
        'success',
        'is_consistent',
        'cr',
        'cr_percent',
        'simulated_weights',
        'rank_diffs',
    ]);
});

test('admin can update ahp pairwise matrix successfully when consistent', function () {
    $service = new AhpRankingService;
    $matrix = $service->getDefaultPairwiseMatrix();

    $response = $this->actingAs($this->adminUser)
        ->postJson(route('admin.mentors.ahp-ranking.update-matrix'), [
            'matrix' => $matrix,
        ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'is_consistent' => true,
    ]);

    $this->assertDatabaseHas('ahp_criteria_configs', [
        'criteria_key' => 'pedagogy',
        'is_active' => true,
    ]);
});

test('admin updating inconsistent matrix is rejected with 422', function () {
    $inconsistentMatrix = [
        'discipline' => [
            'discipline' => 1.0,
            'pedagogy' => 9.0,
            'morals_communication' => 1 / 9,
            'parent_satisfaction' => 7.0,
            'institutional_involvement' => 1 / 8,
        ],
        'pedagogy' => [
            'discipline' => 1 / 9,
            'pedagogy' => 1.0,
            'morals_communication' => 8.0,
            'parent_satisfaction' => 1 / 7,
            'institutional_involvement' => 9.0,
        ],
        'morals_communication' => [
            'discipline' => 9.0,
            'pedagogy' => 1 / 8,
            'morals_communication' => 1.0,
            'parent_satisfaction' => 9.0,
            'institutional_involvement' => 1 / 9,
        ],
        'parent_satisfaction' => [
            'discipline' => 1 / 7,
            'pedagogy' => 7.0,
            'morals_communication' => 1 / 9,
            'parent_satisfaction' => 1.0,
            'institutional_involvement' => 8.0,
        ],
        'institutional_involvement' => [
            'discipline' => 8.0,
            'pedagogy' => 1 / 9,
            'morals_communication' => 9.0,
            'parent_satisfaction' => 1 / 8,
            'institutional_involvement' => 1.0,
        ],
    ];

    $response = $this->actingAs($this->adminUser)
        ->postJson(route('admin.mentors.ahp-ranking.update-matrix'), [
            'matrix' => $inconsistentMatrix,
        ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'is_consistent' => false,
    ]);
});

test('admin can save special notes for mentor on ahp evaluation', function () {
    $periodMonth = now()->format('Y-m');
    $snapshot = AhpEvaluationSnapshot::create([
        'period_month' => $periodMonth,
        'mentor_id' => $this->mentor->id,
        'c1_discipline_score' => 95.0,
        'c2_pedagogy_score' => 90.0,
        'c3_morals_score' => 92.0,
        'c4_satisfaction_score' => 96.0,
        'c5_involvement_score' => 85.0,
        'final_ahp_score' => 92.5,
        'rank_position' => 1,
        'reward_amount' => 1000000.0,
        'calculated_at' => now(),
    ]);

    $response = $this->actingAs($this->adminUser)
        ->postJson(route('admin.mentors.ahp-ranking.update-notes', ['id' => $snapshot->id]), [
            'admin_notes' => 'Mengkhatamkan 5 santri tajwid Mumtaz dalam 1 bulan.',
        ]);

    $response->assertOk();
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('ahp_evaluation_snapshots', [
        'id' => $snapshot->id,
        'admin_notes' => 'Mengkhatamkan 5 santri tajwid Mumtaz dalam 1 bulan.',
    ]);
});

test('admin can announce results and trigger whatsapp notification', function () {
    $month = now()->format('Y-m');
    AhpEvaluationSnapshot::create([
        'period_month' => $month,
        'mentor_id' => $this->mentor->id,
        'c1_discipline_score' => 90.0,
        'c2_pedagogy_score' => 90.0,
        'c3_morals_score' => 90.0,
        'c4_satisfaction_score' => 90.0,
        'c5_involvement_score' => 90.0,
        'final_ahp_score' => 90.0,
        'rank_position' => 1,
        'reward_amount' => 1000000.0,
        'calculated_at' => now(),
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.mentors.ahp-ranking.announce', ['month' => $month]));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('ahp_evaluation_snapshots', [
        'period_month' => $month,
        'mentor_id' => $this->mentor->id,
        'is_announced' => true,
    ]);
});

test('admin can view and print official decree letter sk in a4 format', function () {
    $month = now()->format('Y-m');

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.mentors.ahp-ranking.print-sk', ['month' => $month]));

    $response->assertOk();
    $response->assertSee('SURAT KEPUTUSAN PIMPINAN LEMBAGA');
    $response->assertSee('PENETAPAN PENGHARGAAN USTADZ/USTAZAH TELADAN');
    $response->assertSee('Ustadz Salman Al-Farisi');
});

test('mentor dashboard displays ahp personal performance score widget', function () {
    AhpEvaluationSnapshot::create([
        'period_month' => now()->format('Y-m'),
        'mentor_id' => $this->mentor->id,
        'c1_discipline_score' => 95.0,
        'c2_pedagogy_score' => 90.0,
        'c3_morals_score' => 92.0,
        'c4_satisfaction_score' => 96.0,
        'c5_involvement_score' => 85.0,
        'final_ahp_score' => 92.5,
        'rank_position' => 1,
        'reward_amount' => 1000000.0,
        'calculated_at' => now(),
    ]);

    $response = $this->actingAs($this->mentorUser)
        ->get(route('mentor.dashboard'));

    $response->assertOk();
    $response->assertSee('Skor Performa Saya', false);
    $response->assertSee('Pencapaian 5 Pilar Pedagogis Anda:');
});
