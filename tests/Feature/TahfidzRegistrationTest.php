<?php

namespace Tests\Feature;

use App\Enums\Role as RoleEnum;
use App\Models\ParentProfile;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahfidzRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_tahfidz_pre_registration(): void
    {
        $response = $this->post(route('tahfidz.pre-register'), [
            'nama' => 'Bpk Abdullah',
            'nama_anak' => 'Ahmad Tahfidz',
            'whatsapp' => '081299998888',
            'usia' => '11',
            'gender' => 'L',
            'lokasi' => 'Jakarta Selatan',
            'target_tahfidz' => 'Juz 30 (Juz Amma)',
            'level_tahfidz' => 'Pemula (Belum ada hafalan)',
            'metode' => 'Online',
        ]);

        $response->assertRedirect(route('register'));
        $this->assertEquals('Ahmad Tahfidz', session('pre_registration.nama_anak'));
        $this->assertEquals('Juz 30 (Juz Amma)', session('pre_registration.target_tahfidz'));
    }

    public function test_parent_user_creation_from_tahfidz_pre_registration(): void
    {
        Program::create([
            'name' => 'Program Tahfidz Al-Qur\'an',
            'description' => 'Bimbingan Menghafal Al-Qur\'an',
            'price' => 350000,
        ]);

        $this->withSession(['pre_registration' => [
            'nama' => 'Ibu Halimah',
            'nama_anak' => 'Fatimah Az-Zahra',
            'whatsapp' => '081277776666',
            'usia' => '9',
            'gender' => 'P',
            'lokasi' => 'Bandung',
            'target_tahfidz' => 'Juz 29',
            'level_tahfidz' => 'Juz 30 Sebagian',
            'metode' => 'Offline (Home Visit)',
            'program' => 'Tahfidz Al-Qur\'an',
            'is_tahfidz' => true,
        ]]);

        $response = $this->withoutExceptionHandling()->post(route('register.store'), [
            'name' => 'Ibu Halimah',
            'email' => 'halimah@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081277776666',
            'role' => 'parent',
        ]);

        $response->assertRedirect(route('parent.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'halimah@gmail.com',
            'name' => 'Ibu Halimah',
        ]);

        $this->assertDatabaseHas('students', [
            'full_name' => 'Fatimah Az-Zahra',
            'gender' => 'P',
        ]);
    }

    public function test_logged_in_parent_can_enroll_child_to_tahfidz(): void
    {
        Program::create([
            'name' => 'Program Tahfidz Al-Qur\'an',
            'description' => 'Bimbingan Menghafal Al-Qur\'an',
            'price' => 350000,
        ]);

        $parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => RoleEnum::PARENT->label()]);
        $parentUser = User::factory()->create(['role_id' => $parentRole->id]);
        $parentProfile = ParentProfile::create(['user_id' => $parentUser->id]);

        $studentUser = User::factory()->student()->create();
        $initialStudent = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parentProfile->id,
            'full_name' => 'Kakak Bilal',
            'age' => 14,
            'gender' => 'L',
        ]);

        Payment::create([
            'student_id' => $initialStudent->id,
            'status' => 'paid',
            'amount' => 350000,
            'invoice_number' => 'INV-TEST-TAHFIDZ',
        ]);

        $response = $this->withoutExceptionHandling()->actingAs($parentUser)->post(route('parent.enroll-tahfidz'), [
            'student_id' => 'new',
            'new_nama_anak' => 'Muhammad Bilal',
            'new_usia' => 12,
            'new_gender' => 'L',
            'target_tahfidz' => 'Target 30 Juz',
            'level_tahfidz' => 'Pemula',
            'metode' => 'Online',
        ]);

        $response->assertRedirect(route('parent.children.index'));

        $this->assertDatabaseHas('students', [
            'parent_id' => $parentProfile->id,
            'full_name' => 'Muhammad Bilal',
        ]);
    }
}
