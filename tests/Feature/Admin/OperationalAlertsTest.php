<?php

use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AlertService;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);

    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->parentUser = User::factory()->create(['role_id' => $this->parentRole->id]);
});

test('admin can access operational alerts center', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.alerts.index'))
        ->assertStatus(200)
        ->assertSee('Pusat Peringatan Operasional')
        ->assertSee('Kritis')
        ->assertSee('Perhatian')
        ->assertSee('Info');
});

test('alert service generates critical alert for overdue invoices > 30 days', function () {
    $program = Program::factory()->create();
    $stUser = User::factory()->create();
    $student = Student::create([
        'user_id' => $stUser->id,
        'full_name' => 'Santri Overdue',
        'age' => 12,
        'gender' => 'L',
    ]);

    // Create overdue invoice > 30 days
    Payment::create([
        'invoice_number' => 'INV-ALERT-001',
        'student_id' => $student->id,
        'program_id' => $program->id,
        'amount' => 750000,
        'status' => 'pending',
        'due_date' => now()->subDays(35),
    ]);

    $service = app(AlertService::class);
    $alerts = $service->getAllAlerts();

    expect($alerts['total_count'])->toBeGreaterThanOrEqual(1);
    expect($alerts['critical_count'])->toBeGreaterThanOrEqual(1);

    $overdueAlert = collect($alerts['critical'])->firstWhere('id', 'crit_overdue_30');
    expect($overdueAlert)->not->toBeNull();
    expect($overdueAlert['level'])->toBe('critical');
});
