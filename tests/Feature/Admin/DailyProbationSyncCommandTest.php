<?php

use App\Models\Mentor;
use App\Models\MentorProbationTracking;
use App\Models\Role;
use App\Models\User;
use App\Services\WhatsAppService;
use Carbon\Carbon;

beforeEach(function () {
    $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Admin']);

    // Admin user with phone
    $this->adminUser = User::factory()->create([
        'name' => 'Admin Lembaga',
        'role_id' => $this->adminRole->id,
        'phone' => '081211112222',
    ]);

    // Mentor on probation
    $this->mentorUser = User::factory()->create([
        'name' => 'Ust. Zulkifli',
        'role_id' => $this->mentorRole->id,
        'phone' => '081233334444',
    ]);

    $this->mentor = Mentor::create([
        'user_id' => $this->mentorUser->id,
        'full_name' => 'Ust. Zulkifli',
        'status' => 'probation',
        'is_active' => true,
        'rating' => 4.20,
    ]);

    // Probation tracking: expires in 10 days (H-10), with rating 4.20 (below 4.50 KPI)
    $this->probation = MentorProbationTracking::create([
        'mentor_id' => $this->mentor->id,
        'start_date' => Carbon::today()->subDays(80),
        'end_date' => Carbon::today()->addDays(10),
        'duration_months' => 3,
        'status' => 'active',
        'average_rating' => 4.20,
        'attendance_rate' => 85.00,
        'training_modules_required' => 4,
        'training_modules_completed' => 2,
    ]);
});

test('daily probation sync command successfully syncs tracking stats and detects at-risk probation', function () {
    $this->artisan('probation:daily-sync')
        ->expectsOutputToContain('Memulai sinkronisasi harian masa percobaan')
        ->expectsOutputToContain('Ust. Zulkifli')
        ->expectsOutputToContain('Berisiko')
        ->assertSuccessful();

    $this->probation->refresh();
    expect($this->probation->last_synced_at)->not->toBeNull();
});

test('daily probation sync command sends whatsapp notification to admin when notify option is enabled', function () {
    $mockWa = Mockery::mock(WhatsAppService::class);
    $mockWa->shouldReceive('sendMessage')
        ->once()
        ->withArgs(function ($phone, $msg) {
            return str_contains($msg, 'PERINGATAN EVALUASI MASA PERCOBAAN GURU (H-14)')
                && str_contains($msg, 'Ust. Zulkifli');
        })
        ->andReturn(true);

    $this->app->instance(WhatsAppService::class, $mockWa);

    $this->artisan('probation:daily-sync --notify')
        ->expectsOutputToContain('Notifikasi peringatan H-14 berhasil dikirim ke Admin')
        ->assertSuccessful();
});

test('probation with safe KPI and more than 14 days does not trigger at-risk warning', function () {
    // Update probation to safe KPI and 60 days left
    $this->probation->update([
        'end_date' => Carbon::today()->addDays(60),
        'average_rating' => 4.80,
        'attendance_rate' => 95.00,
        'training_modules_completed' => 4,
    ]);

    $mockWa = Mockery::mock(WhatsAppService::class);
    $mockWa->shouldNotReceive('sendMessage');
    $this->app->instance(WhatsAppService::class, $mockWa);

    $this->artisan('probation:daily-sync --notify')
        ->expectsOutputToContain('Guru Berisiko (H-14): 0')
        ->assertSuccessful();
});
