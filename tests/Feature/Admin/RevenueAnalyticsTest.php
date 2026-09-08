<?php

use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\RevenueAnalyticsService;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);

    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->parentUser = User::factory()->create(['role_id' => $this->parentRole->id]);
});

test('admin can access revenue analytics dashboard', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.revenue.index'))
        ->assertStatus(200)
        ->assertSee('Analitik Pendapatan', false)
        ->assertSee('Total Pendapatan', false)
        ->assertSee('Bulan Ini', false);
});

test('non-admin is forbidden from accessing revenue dashboard', function () {
    $this->actingAs($this->parentUser)
        ->get(route('admin.revenue.index'))
        ->assertStatus(403);
});

test('revenue analytics service calculates metrics and MoM growth accurately', function () {
    $program = Program::factory()->create(['price' => 500000]);
    $stUser = User::factory()->create();
    $student = Student::create([
        'user_id' => $stUser->id,
        'full_name' => 'Santri Test',
        'age' => 10,
        'gender' => 'L',
    ]);

    // Last month payment
    Payment::create([
        'invoice_number' => 'INV-TEST-001',
        'student_id' => $student->id,
        'program_id' => $program->id,
        'amount' => 1000000,
        'status' => 'paid',
        'payment_date' => now()->subMonth()->startOfMonth()->addDays(5),
        'due_date' => now()->subMonth()->startOfMonth()->addDays(10),
    ]);

    // This month payment
    Payment::create([
        'invoice_number' => 'INV-TEST-002',
        'student_id' => $student->id,
        'program_id' => $program->id,
        'amount' => 1500000,
        'status' => 'paid',
        'payment_date' => now()->startOfMonth()->addDays(2),
        'due_date' => now()->startOfMonth()->addDays(10),
    ]);

    // Overdue pending payment
    Payment::create([
        'invoice_number' => 'INV-TEST-003',
        'student_id' => $student->id,
        'program_id' => $program->id,
        'amount' => 500000,
        'status' => 'pending',
        'due_date' => now()->subDays(5),
    ]);

    $service = app(RevenueAnalyticsService::class);
    $metrics = $service->getSummaryMetrics();

    expect($metrics['total_revenue'])->toBe(2500000.0);
    expect($metrics['this_month_revenue'])->toBe(1500000.0);
    expect($metrics['last_month_revenue'])->toBe(1000000.0);
    expect($metrics['mom_growth_percent'])->toBe(50.0);
    expect($metrics['overdue_invoices_count'])->toBeGreaterThanOrEqual(1);
    expect($metrics['overdue_invoices_amount'])->toBeGreaterThanOrEqual(500000.0);
});

test('analytics api endpoints return valid json for apexcharts', function () {
    $this->actingAs($this->admin)
        ->getJson(route('api.analytics.revenue-trend', ['period' => '12months']))
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                'categories',
                'series' => [
                    '*' => ['name', 'data'],
                ],
                'invoices_count_series',
            ],
        ]);

    $this->actingAs($this->admin)
        ->getJson(route('api.analytics.program-breakdown'))
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                'labels',
                'series',
                'details',
            ],
        ]);

    $this->actingAs($this->admin)
        ->getJson(route('api.analytics.payment-status'))
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                'paid' => ['count', 'amount', 'percent'],
                'pending' => ['count', 'amount', 'percent'],
                'overdue' => ['count', 'amount', 'percent'],
                'cancelled' => ['count', 'amount', 'percent'],
                'total_count',
            ],
        ]);
});
