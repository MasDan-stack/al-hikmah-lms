<?php

use App\Models\FinancialAuditLog;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\BroadcastService;
use App\Services\WhatsAppService;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);

    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->parentUser = User::factory()->create([
        'name' => 'Bapak Hendra Gunawan',
        'phone' => '628123456789',
        'role_id' => $this->parentRole->id,
    ]);

    $this->parentProfile = ParentProfile::create([
        'user_id' => $this->parentUser->id,
        'emergency_phone' => '628123456789',
    ]);

    $this->program = Program::factory()->create(['name' => 'Tahfidz Anak Intensif']);
    $stUser = User::factory()->create();
    $this->student = Student::create([
        'user_id' => $stUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Muhammad Fatih',
        'age' => 8,
        'gender' => 'L',
    ]);
    $this->student->programs()->attach($this->program->id);
});

test('admin can access whatsapp broadcast console', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.broadcast.index'))
        ->assertStatus(200)
        ->assertSee('WhatsApp Mass Broadcast System')
        ->assertSee('Formulir Broadcast Pesan')
        ->assertSee('Simulasi Tampilan WhatsApp');
});

test('broadcast service resolves dynamic template variables accurately', function () {
    $service = app(BroadcastService::class);
    $template = "Assalamu'alaikum {nama_ortu}, ananda {nama_anak} mengikuti program {program}. Salam dari {lembaga}.";

    $recipientData = [
        'parent_name' => 'Bapak Hendra',
        'children_names' => 'Fatih',
        'program_names' => 'Tahfidz Intensif',
    ];

    $parsed = $service->parseTemplate($template, $recipientData);

    expect($parsed)->toContain('Bapak Hendra');
    expect($parsed)->toContain('Fatih');
    expect($parsed)->toContain('Tahfidz Intensif');
    expect($parsed)->toContain('AL-HIKMAH LMS');
});

test('admin can preview parsed broadcast message via ajax', function () {
    $this->actingAs($this->admin)
        ->postJson(route('admin.broadcast.preview'), [
            'template' => 'Halo {nama_ortu}, bimbingan {program} aktif.',
            'target_type' => 'all',
        ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'total_recipients',
            'sample_recipient',
            'parsed_message',
        ]);
});

test('admin can dispatch mass broadcast and system records audit log', function () {
    $this->withoutExceptionHandling();

    // Mock WhatsAppService so real HTTP isn't called
    $mockWa = Mockery::mock(WhatsAppService::class);
    $mockWa->shouldReceive('sendMessage')->andReturn(true);
    app()->instance(WhatsAppService::class, $mockWa);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.broadcast.send'), [
            'title' => 'Pengumuman Awal Puasa',
            'target_type' => 'all',
            'message_template' => 'Pengumuman resmi untuk Bapak/Ibu {nama_ortu} terkait ananda {nama_anak}.',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $log = FinancialAuditLog::where('action', 'whatsapp_broadcast')->latest()->first();
    expect($log)->not->toBeNull();
    expect($log->new_values['title'])->toBe('Pengumuman Awal Puasa');
});
