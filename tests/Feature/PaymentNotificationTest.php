<?php

use App\Livewire\Parent\ParentNotifications;
use App\Models\Notification;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Admin']);
    $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Parent']);
    $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Student']);

    // Admin user
    $this->adminUser = User::factory()->create(['role_id' => $this->adminRole->id]);

    // Parent user
    $this->parentUser = User::factory()->create(['role_id' => $this->parentRole->id]);
    $this->parentUser->update(['name' => 'Ibu Fitri']);
    $this->parentProfile = ParentProfile::create(['user_id' => $this->parentUser->id]);

    // Student
    $this->studentUser = User::factory()->create(['role_id' => $this->studentRole->id]);
    $this->student = Student::create([
        'user_id' => $this->studentUser->id,
        'parent_id' => $this->parentProfile->id,
        'full_name' => 'Ahmad Fathir',
        'age' => 8,
        'gender' => 'L',
    ]);
});

test('admin dapat menginput angka nominal tagihan spp baru dan mengirimkan notifikasi', function () {
    $response = $this->actingAs($this->adminUser)->post(route('admin.payments.store'), [
        'student_id' => $this->student->id,
        'amount' => 350000,
        'due_date' => now()->addDays(7)->format('Y-m-d'),
        'invoice_number' => 'INV-SPP-350K',
        'status' => 'pending',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('payments', [
        'student_id' => $this->student->id,
        'amount' => 350000,
        'invoice_number' => 'INV-SPP-350K',
    ]);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $this->parentUser->id,
        'type' => 'warning',
    ]);
});

test('admin dapat merubah nominal dan status tagihan spp', function () {
    $payment = Payment::create([
        'student_id' => $this->student->id,
        'amount' => 250000,
        'invoice_number' => 'INV-2026-08-EDIT',
        'status' => 'pending',
        'due_date' => now()->addDays(5),
    ]);

    $response = $this->actingAs($this->adminUser)->put(route('admin.payments.update', $payment->id), [
        'amount' => 400000,
        'due_date' => now()->addDays(10)->format('Y-m-d'),
        'status' => 'paid',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'amount' => 400000,
        'status' => 'paid',
    ]);
});

test('komponen livewire notifications dapat dirender dan menandai notifikasi dibaca', function () {
    $notif = Notification::create([
        'user_id' => $this->parentUser->id,
        'type' => 'payment_reminder',
        'title' => 'Pengingat Pembayaran SPP',
        'message' => 'Tagihan SPP Ahmad jatuh tempo 15/08',
        'is_read' => false,
    ]);

    Livewire::actingAs($this->parentUser)
        ->test(ParentNotifications::class)
        ->assertSee('Pengingat Pembayaran SPP')
        ->assertSee('Tagihan SPP Ahmad jatuh tempo 15/08')
        ->call('markAsRead', $notif->id)
        ->assertSet('unreadCount', 0);

    $this->assertDatabaseHas('notifications', [
        'id' => $notif->id,
        'is_read' => true,
    ]);
});

test('admin dapat mengirimkan pengingat tagihan spp massal ke orang tua santri', function () {
    Payment::create([
        'student_id' => $this->student->id,
        'amount' => 250000,
        'invoice_number' => 'INV-2026-08-001',
        'status' => 'pending',
        'due_date' => now()->addDays(2),
    ]);

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.payments.send-reminder'));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('notifications', [
        'user_id' => $this->parentUser->id,
        'type' => 'warning',
        'title' => 'Pengingat Pembayaran SPP',
    ]);
});

test('halaman manajemen pembayaran admin dan portal orang tua dapat diakses dengan data tagihan valid', function () {
    Payment::create([
        'student_id' => $this->student->id,
        'amount' => 250000,
        'invoice_number' => 'INV-2026-08-002',
        'status' => 'pending',
        'due_date' => now()->addDays(3),
    ]);

    $responseAdmin = $this->actingAs($this->adminUser)->get(route('admin.payments.index'));
    $responseAdmin->assertStatus(200);
    $responseAdmin->assertSee('INV-2026-08-002');

    $responseParent = $this->actingAs($this->parentUser)->get(route('parent.payments.index'));
    $responseParent->assertStatus(200);
    $responseParent->assertSee('INV-2026-08-002');
});
