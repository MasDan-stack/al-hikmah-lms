<?php

use App\Models\Mentor;
use App\Models\User;
use App\Services\DecisionSupport\AhpRankingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('ahp service calculates weights and proves mathematical consistency cr <= 0.10', function () {
    $service = new AhpRankingService;
    $matrix = $service->getDefaultPairwiseMatrix();

    $result = $service->calculateWeights($matrix);

    expect($result['is_consistent'])->toBeTrue();
    expect($result['cr'])->toBeLessThanOrEqual(0.10);
    expect($result['weights'])->toHaveKeys([
        'discipline',
        'pedagogy',
        'morals_communication',
        'parent_satisfaction',
        'institutional_involvement',
    ]);

    // Total weights must sum to approximately 1.0 (100%)
    $totalWeight = array_sum($result['weights']);
    expect(abs($totalWeight - 1.0))->toBeLessThan(0.001);

    // Pedagogy should be the highest weighted criterion
    expect($result['weights']['pedagogy'])->toBeGreaterThan($result['weights']['discipline']);
});

test('ahp service detects inconsistent pairwise matrix when cr > 0.10', function () {
    $service = new AhpRankingService;

    // Matriks yang sengaja dibuat kontradiktif / inkonsisten ekstrem
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

    $result = $service->calculateWeights($inconsistentMatrix);

    expect($result['is_consistent'])->toBeFalse();
    expect($result['cr'])->toBeGreaterThan(0.10);
});

test('ahp service evaluates mentors and assigns proper podium ranks and rewards', function () {
    $service = new AhpRankingService;

    // Create 2 active mentors
    $user1 = User::factory()->mentor()->create(['name' => 'Ustadz Ahmad Al-Hafidz']);
    $mentor1 = Mentor::create([
        'user_id' => $user1->id,
        'full_name' => 'Ustadz Ahmad Al-Hafidz',
        'is_active' => true,
        'rating' => 4.95,
        'gender' => 'L',
    ]);

    $user2 = User::factory()->mentor()->create(['name' => 'Ustazah Fatimah Az-Zahra']);
    $mentor2 = Mentor::create([
        'user_id' => $user2->id,
        'full_name' => 'Ustazah Fatimah Az-Zahra',
        'is_active' => true,
        'rating' => 4.80,
        'gender' => 'P',
    ]);

    $periodMonth = now()->format('Y-m');
    $evaluation = $service->evaluateAndRank($periodMonth);

    expect($evaluation['total_mentors'])->toBeGreaterThanOrEqual(2);
    expect($evaluation['podium'])->toHaveCount(min(3, $evaluation['total_mentors']));

    $firstWinner = $evaluation['podium']->first();
    expect($firstWinner['rank_position'])->toBe(1);
    expect($firstWinner['reward_amount'])->toBe(1000000.0);
    expect($firstWinner['tier_badge'])->toContain('Juara 1');
});

test('ahp service simulates weight changes and reports rank diffs', function () {
    $service = new AhpRankingService;

    $user1 = User::factory()->mentor()->create(['name' => 'Ustadz Zaid']);
    Mentor::create([
        'user_id' => $user1->id,
        'full_name' => 'Ustadz Zaid',
        'is_active' => true,
        'rating' => 4.90,
    ]);

    $periodMonth = now()->format('Y-m');
    $defaultMatrix = $service->getDefaultPairwiseMatrix();

    $simResult = $service->simulateWeights($defaultMatrix, $periodMonth);

    expect($simResult)->toHaveKeys(['simulated_weights', 'is_consistent', 'cr', 'rank_diffs']);
    expect($simResult['is_consistent'])->toBeTrue();
});
