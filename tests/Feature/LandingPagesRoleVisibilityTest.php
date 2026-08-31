<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPagesRoleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_does_not_see_biaya_buttons_on_any_landing_page(): void
    {
        $metodeResponse = $this->get(route('metode'));
        $metodeResponse->assertStatus(200);
        $metodeResponse->assertDontSee('Informasi Pendampingan');
        $metodeResponse->assertDontSee('Kamu Administrator');

        $tahfidzResponse = $this->get(route('tahfidz'));
        $tahfidzResponse->assertStatus(200);
        $tahfidzResponse->assertDontSee('Informasi Pendampingan');
        $tahfidzResponse->assertDontSee('Kamu Administrator');

        $programResponse = $this->get(route('program'));
        $programResponse->assertStatus(200);
        $programResponse->assertDontSee('Ingin Mengetahui Rincian Investasi');
        $programResponse->assertDontSee('Lihat Informasi Biaya');
        $programResponse->assertDontSee('Kamu Administrator');
    }

    public function test_mentor_does_not_see_biaya_buttons_on_landing_pages(): void
    {
        $mentor = User::factory()->mentor()->create();

        $metodeResponse = $this->actingAs($mentor)->get(route('metode'));
        $metodeResponse->assertStatus(200);
        $metodeResponse->assertDontSee('Informasi Pendampingan');

        $tahfidzResponse = $this->actingAs($mentor)->get(route('tahfidz'));
        $tahfidzResponse->assertStatus(200);
        $tahfidzResponse->assertDontSee('Informasi Pendampingan');

        $programResponse = $this->actingAs($mentor)->get(route('program'));
        $programResponse->assertStatus(200);
        $programResponse->assertDontSee('Ingin Mengetahui Rincian Investasi');
        $programResponse->assertDontSee('Lihat Informasi Biaya');
    }

    public function test_student_does_not_see_biaya_buttons_on_landing_pages(): void
    {
        $student = User::factory()->student()->create();

        $metodeResponse = $this->actingAs($student)->get(route('metode'));
        $metodeResponse->assertStatus(200);
        $metodeResponse->assertDontSee('Informasi Pendampingan');

        $tahfidzResponse = $this->actingAs($student)->get(route('tahfidz'));
        $tahfidzResponse->assertStatus(200);
        $tahfidzResponse->assertDontSee('Informasi Pendampingan');

        $programResponse = $this->actingAs($student)->get(route('program'));
        $programResponse->assertStatus(200);
        $programResponse->assertDontSee('Ingin Mengetahui Rincian Investasi');
        $programResponse->assertDontSee('Lihat Informasi Biaya');
    }

    public function test_parent_sees_standard_biaya_buttons_without_admin_label(): void
    {
        $parent = User::factory()->parent()->create();

        $metodeResponse = $this->actingAs($parent)->get(route('metode'));
        $metodeResponse->assertStatus(200);
        $metodeResponse->assertSee('Informasi Pendampingan');
        $metodeResponse->assertDontSee('Kamu Administrator');

        $tahfidzResponse = $this->actingAs($parent)->get(route('tahfidz'));
        $tahfidzResponse->assertStatus(200);
        $tahfidzResponse->assertSee('Informasi Pendampingan');
        $tahfidzResponse->assertDontSee('Kamu Administrator');

        $programResponse = $this->actingAs($parent)->get(route('program'));
        $programResponse->assertStatus(200);
        $programResponse->assertSee('Ingin Mengetahui Rincian Investasi');
        $programResponse->assertSee('Lihat Informasi Biaya');
        $programResponse->assertDontSee('Kamu Administrator');
    }

    public function test_admin_sees_biaya_buttons_with_administrator_context_label(): void
    {
        $admin = User::factory()->admin()->create();

        $metodeResponse = $this->actingAs($admin)->get(route('metode'));
        $metodeResponse->assertStatus(200);
        $metodeResponse->assertSee('Informasi Pendampingan (Kamu Administrator)');

        $tahfidzResponse = $this->actingAs($admin)->get(route('tahfidz'));
        $tahfidzResponse->assertStatus(200);
        $tahfidzResponse->assertSee('Informasi Pendampingan (Kamu Administrator)');

        $programResponse = $this->actingAs($admin)->get(route('program'));
        $programResponse->assertStatus(200);
        $programResponse->assertSee('Ingin Mengetahui Rincian Investasi');
        $programResponse->assertSee('Lihat Informasi Biaya');
        $programResponse->assertSee('Kamu Administrator');
    }

    public function test_guest_sees_registration_modal_cta_on_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Mulai Belajar');
        $response->assertSee('Mulai Perjalanan Belajar');
        $response->assertSee('data-bs-target="#daftarModal"', false);
    }

    public function test_parent_sees_dashboard_and_enrollment_cta_on_home_page(): void
    {
        $parent = User::factory()->parent()->create(['name' => 'Bunda Siti']);

        $response = $this->actingAs($parent)->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('Mulai Perjalanan Belajar');
        $response->assertSee('Daftarkan Program Baru Anak');
        $response->assertSee('Dashboard Orang Tua');
        $response->assertSee('Selamat Datang Kembali');
        $response->assertSee('Bunda Siti');
    }

    public function test_student_sees_ruang_santri_cta_on_home_page(): void
    {
        $student = User::factory()->student()->create(['name' => 'Ahmad Santri']);

        $response = $this->actingAs($student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('Mulai Perjalanan Belajar');
        $response->assertSee('Masuk Ruang Santri');
        $response->assertSee('Target Hafalan Hari Ini');
        $response->assertSee('Ahmad Santri');
    }

    public function test_mentor_sees_mengajar_cta_on_home_page(): void
    {
        $mentor = User::factory()->mentor()->create(['name' => 'Ustadz Ali']);

        $response = $this->actingAs($mentor)->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('Mulai Perjalanan Belajar');
        $response->assertSee('Dashboard Mengajar');
        $response->assertSee('Jadwal Mengajar');
        $response->assertSee('Ustadz Ali');
    }

    public function test_admin_sees_admin_dashboard_cta_on_home_page(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);

        $response = $this->actingAs($admin)->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('Mulai Perjalanan Belajar');
        $response->assertSee('Dashboard Admin');
        $response->assertSee('Kelola Pendaftaran');
        $response->assertSee('Admin Utama');
    }
}
