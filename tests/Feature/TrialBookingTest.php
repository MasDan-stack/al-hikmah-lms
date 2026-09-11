<?php

namespace Tests\Feature;

use App\Models\Program;
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
}
