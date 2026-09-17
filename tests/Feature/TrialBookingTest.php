<?php

namespace Tests\Feature;

use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Role;
use App\Models\TrialBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrialBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_trial_booking_via_standard_form(): void
    {
        $program = Program::create([
            'name' => 'Bimbingan Iqra & Al-Qur\'an',
            'slug' => 'iqra-dasar',
            'description' => 'Program pengenalan hijaiyah',
            'monthly_fee' => 350000,
            'is_active' => true,
        ]);

        $response = $this->post(route('trial.store'), [
            'parent_name' => 'Bunda Fatimah',
            'child_name' => 'Fatih Al-Ayyubi',
            'whatsapp' => '081234567890',
            'child_age' => '7 Tahun',
            'gender' => 'L',
            'program_id' => $program->id,
            'trial_focus' => 'iqra_placement',
            'preferred_date' => now()->addDays(2)->format('Y-m-d'),
            'preferred_time_slot' => 'sore',
            'learning_method' => 'online',
            'city' => 'Depok',
            'notes' => 'Ingin evaluasi kelancaran makhraj huruf',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('trial_booked', true);

        $this->assertDatabaseHas('trial_bookings', [
            'parent_name' => 'Bunda Fatimah',
            'child_name' => 'Fatih Al-Ayyubi',
            'whatsapp' => '6281234567890',
            'trial_focus' => 'iqra_placement',
            'preferred_time_slot' => 'sore',
            'learning_method' => 'online',
            'status' => 'pending',
        ]);
    }

    public function test_guest_can_submit_trial_booking_via_ajax(): void
    {
        $response = $this->postJson(route('trial.store'), [
            'parent_name' => 'Ayah Salman',
            'child_name' => 'Zahra Humaira',
            'whatsapp' => '6287711223344',
            'child_age' => '9 Tahun',
            'gender' => 'P',
            'trial_focus' => 'tahsin_tajwid',
            'preferred_time_slot' => 'pagi',
            'learning_method' => 'online',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'booking_id',
            'wa_url',
        ]);

        $this->assertDatabaseHas('trial_bookings', [
            'parent_name' => 'Ayah Salman',
            'child_name' => 'Zahra Humaira',
            'whatsapp' => '6287711223344',
            'trial_focus' => 'tahsin_tajwid',
        ]);
    }

    public function test_trial_booking_validation_fails_for_missing_required_fields(): void
    {
        $response = $this->post(route('trial.store'), []);

        $response->assertSessionHasErrors([
            'parent_name',
            'child_name',
            'whatsapp',
            'child_age',
            'gender',
            'trial_focus',
            'preferred_time_slot',
            'learning_method',
        ]);
    }

    public function test_authenticated_parent_submitting_trial_associates_user_id_and_displays_on_parent_dashboard(): void
    {
        $role = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'phone' => '081234567890',
        ]);
        $parent = ParentProfile::create([
            'user_id' => $user->id,
            'address' => 'Jl. Al-Hikmah No. 10',
            'emergency_phone' => '081234567890',
        ]);

        $this->actingAs($user)->post(route('trial.store'), [
            'parent_name' => $user->name,
            'child_name' => 'Aisyah Humaira',
            'whatsapp' => '081234567890',
            'child_age' => '8 Tahun',
            'gender' => 'P',
            'trial_focus' => 'tahfidz_hafalan',
            'preferred_time_slot' => 'pagi',
            'learning_method' => 'online',
        ]);

        $this->assertDatabaseHas('trial_bookings', [
            'user_id' => $user->id,
            'child_name' => 'Aisyah Humaira',
            'trial_focus' => 'tahfidz_hafalan',
        ]);

        $response = $this->actingAs($user)->get(route('parent.dashboard'));
        $response->assertOk();
        $response->assertSee('Aisyah Humaira');
        $response->assertSee('Status Sesi Uji Coba Gratis 15 Menit');
    }

    public function test_admin_dashboard_displays_recent_trial_bookings(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        TrialBooking::create([
            'parent_name' => 'Ummu Maryam',
            'child_name' => 'Maryam Salihah',
            'whatsapp' => '6289912345678',
            'child_age' => '6 Tahun',
            'gender' => 'P',
            'trial_focus' => 'iqra_placement',
            'preferred_time_slot' => 'sore',
            'learning_method' => 'online',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Permintaan Sesi Uji Coba Gratis 15 Menit');
        $response->assertSee('Maryam Salihah');
        $response->assertSee('Ummu Maryam');
    }

    public function test_mentor_dashboard_displays_assigned_trial_bookings(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Guru / Pembimbing']);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
            'status' => 'active',
            'specialization' => 'Tahsin & Tahfidz',
            'bio' => 'Ustadz pembimbing Al-Qur\'an',
        ]);

        TrialBooking::create([
            'parent_name' => 'Abu Bakar',
            'child_name' => 'Umar Faruq',
            'whatsapp' => '6281122334455',
            'child_age' => '10 Tahun',
            'gender' => 'L',
            'trial_focus' => 'tahfidz_hafalan',
            'preferred_time_slot' => 'malam',
            'learning_method' => 'online',
            'status' => 'scheduled',
            'assigned_mentor_id' => $mentor->id,
        ]);

        $response = $this->actingAs($mentorUser)->get(route('mentor.dashboard'));
        $response->assertOk();
        $response->assertSee('Tugas Sesi Uji Coba &amp; Placement Test (15 Menit)', false);
        $response->assertSee('Umar Faruq');
    }

    public function test_tahfidz_page_renders_tiered_ctas_and_hides_exclusive_price_card(): void
    {
        $response = $this->get(route('tahfidz'));
        $response->assertOk();

        // 2 CTA utama untuk tamu
        $response->assertSee('Coba Sesi Uji Coba Gratis 15 Menit');
        $response->assertSee('Konsultasi via WhatsApp');

        // Kartu harga Rp 2.700.000 tidak boleh tampil
        $response->assertDontSee('Rp 2.700.000');
        $response->assertDontSee('Program Unggulan Eksklusif');
    }
}
