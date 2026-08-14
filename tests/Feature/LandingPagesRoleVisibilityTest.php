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
}
