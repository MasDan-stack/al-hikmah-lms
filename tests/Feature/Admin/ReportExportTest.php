<?php

use App\Models\FinancialAuditLog;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);

    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
    $this->parentUser = User::factory()->create(['role_id' => $this->parentRole->id]);
});

test('admin can access report generator and preview page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.reports.index'))
        ->assertStatus(200)
        ->assertSee('Rekapitulasi Laporan Keuangan')
        ->assertSee('Ekspor Excel / CSV', false)
        ->assertSee('Cetak / Unduh PDF Resmi', false);
});

test('admin can export revenue report to excel csv and generates audit log', function () {
    $program = Program::factory()->create();
    $stUser1 = User::factory()->create();
    $student = Student::create([
        'user_id' => $stUser1->id,
        'full_name' => 'Santri Export 1',
        'age' => 9,
        'gender' => 'P',
    ]);

    Payment::create([
        'invoice_number' => 'INV-EXP-001',
        'student_id' => $student->id,
        'program_id' => $program->id,
        'amount' => 500000,
        'status' => 'paid',
        'payment_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export-excel', [
            'type' => 'revenue',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

    // Assert audit log was recorded
    $log = FinancialAuditLog::where('action', 'export_report_excel')->latest()->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->admin->id);
});

test('admin can export revenue report to pdf format and generates audit log', function () {
    $program = Program::factory()->create();
    $stUser2 = User::factory()->create();
    $student = Student::create([
        'user_id' => $stUser2->id,
        'full_name' => 'Santri Export 2',
        'age' => 11,
        'gender' => 'L',
    ]);

    Payment::create([
        'invoice_number' => 'INV-EXP-002',
        'student_id' => $student->id,
        'program_id' => $program->id,
        'amount' => 600000,
        'status' => 'paid',
        'payment_date' => now(),
    ]);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.reports.export-pdf', [
            'type' => 'revenue',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));

    $response->assertStatus(200);
    $response->assertSee('LEMBAGA PENDIDIKAN', false);
    $response->assertSee('REKAPITULASI LAPORAN KEUANGAN', false);

    // Assert audit log was recorded
    $log = FinancialAuditLog::where('action', 'export_report_pdf')->latest()->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->admin->id);
});
