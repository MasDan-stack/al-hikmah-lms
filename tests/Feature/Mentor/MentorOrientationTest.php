<?php

use App\Models\Mentor;
use App\Models\MentorActivityLog;
use App\Models\MentorProbationTracking;
use App\Models\User;

beforeEach(function () {
    $this->mentorUser = User::factory()->mentor()->create();
    $this->mentor = Mentor::factory()->create([
        'user_id' => $this->mentorUser->id,
        'status' => 'probation',
        'is_active' => true,
        'probation_end_date' => now()->addDays(90),
    ]);

    $this->probation = MentorProbationTracking::create([
        'mentor_id' => $this->mentor->id,
        'start_date' => now(),
        'end_date' => now()->addDays(90),
        'status' => 'active',
        'training_modules_completed' => 0,
        'training_modules_required' => 4,
        'orientation_completed' => false,
        'system_training_completed' => false,
        'first_session_conducted' => false,
    ]);
});

test('unauthenticated guest cannot access mentor orientation', function () {
    $this->get(route('mentor.orientation.index'))
        ->assertRedirect(route('login'));
});

test('mentor on probation can view orientation hub with 4 modules', function () {
    $response = $this->actingAs($this->mentorUser)
        ->get(route('mentor.orientation.index'));

    $response->assertStatus(200);
    $response->assertSee('Pusat Pembekalan & Standar Mutu Guru', false);
    $response->assertSee('MOD-01');
    $response->assertSee('MOD-02');
    $response->assertSee('MOD-03');
    $response->assertSee('MOD-04');
    $response->assertSee('0/4 Modul');
});

test('mentor dashboard links "Buka Panduan & Materi Orientasi" to mentor orientation route', function () {
    $response = $this->actingAs($this->mentorUser)
        ->get(route('mentor.dashboard'));

    $response->assertStatus(200);
    $response->assertSee(route('mentor.orientation.index'));
    // Ensure it does not link the orientation button to mentor.profile
    $response->assertDontSee('href="'.route('mentor.profile').'" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"', false);
});

test('mentor can mark module as completed and it updates probation tracking and audit log', function () {
    $response = $this->actingAs($this->mentorUser)
        ->post(route('mentor.orientation.complete', 'mod1'));

    $response->assertSessionHas('success');
    $response->assertRedirect();

    $this->probation->refresh();
    expect($this->probation->orientation_completed)->toBeTrue();
    expect($this->probation->isModuleCompleted('mod1'))->toBeTrue();
    expect($this->probation->getCompletedModulesCount())->toBe(1);
    expect($this->probation->training_modules_completed)->toBe(1);

    // Verify activity log recorded
    $log = MentorActivityLog::where('mentor_id', $this->mentor->id)
        ->where('action', 'orientation_module_completed')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->description)->toContain('Modul 1');
});

test('mentor can complete all 4 modules and dashboard reflects 4/4 selesai', function () {
    // Complete all 4
    $this->actingAs($this->mentorUser)->post(route('mentor.orientation.complete', 'mod1'));
    $this->actingAs($this->mentorUser)->post(route('mentor.orientation.complete', 'mod2'));
    $this->actingAs($this->mentorUser)->post(route('mentor.orientation.complete', 'mod3'));
    $this->actingAs($this->mentorUser)->post(route('mentor.orientation.complete', 'mod4'));

    $this->probation->refresh();
    expect($this->probation->orientation_completed)->toBeTrue();
    expect($this->probation->system_training_completed)->toBeTrue();
    expect($this->probation->first_session_conducted)->toBeTrue();
    expect($this->probation->getCompletedModulesCount())->toBe(4);
    expect($this->probation->training_modules_completed)->toBe(4);

    // Check dashboard reflection
    $response = $this->actingAs($this->mentorUser)->get(route('mentor.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('4/4 Selesai');
});

test('invalid module key returns validation error', function () {
    $response = $this->actingAs($this->mentorUser)
        ->post(route('mentor.orientation.complete', 'invalid_mod'));

    $response->assertSessionHas('error');
    $response->assertRedirect();
});
