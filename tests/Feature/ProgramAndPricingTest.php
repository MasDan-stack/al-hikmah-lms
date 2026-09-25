<?php

namespace Tests\Feature;

use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use App\Services\RevenueAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramAndPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_page_displays_active_categories_and_descriptions_only(): void
    {
        $programAnak = Program::create([
            'name' => 'Iqra & Dasar Al-Qur\'an',
            'category' => 'anak',
            'icon' => 'bi-book-half',
            'description' => 'Memulai perjalanan mengenal huruf hijaiyah dan membaca Al-Qur\'an secara bertahap.',
            'duration_weeks' => 8,
            'price' => 400000,
            'level' => 'Anak (10-15 th)',
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('program'));

        $response->assertStatus(200);
        $response->assertSee('Iqra &amp; Dasar Al-Qur&#039;an', false);
        $response->assertSee('Memulai perjalanan mengenal huruf hijaiyah');
        $response->assertDontSee('Rp 400.000');
    }

    public function test_guest_cannot_access_biaya_page_and_receives_403(): void
    {
        $response = $this->get(route('biaya'));

        $response->assertStatus(403);
        $response->assertSee('Akses Terbatas');
    }

    public function test_guest_does_not_see_package_prices_or_simulations_on_home_page(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee('Rp 600.000');
        $response->assertDontSee('Rp 1.200.000');
        $response->assertDontSee('Flat Rp 150.000');
        $response->assertDontSee('homeSimulasiButtons');
        $response->assertDontSee(route('biaya'));
    }

    public function test_mentor_cannot_access_biaya_page_and_receives_403(): void
    {
        $mentorUser = User::factory()->mentor()->create();

        $response = $this->actingAs($mentorUser)->get(route('biaya'));

        $response->assertStatus(403);
        $response->assertSee('Akses Terbatas');
    }

    public function test_student_cannot_access_biaya_page_and_receives_403(): void
    {
        $studentUser = User::factory()->student()->create();

        $response = $this->actingAs($studentUser)->get(route('biaya'));

        $response->assertStatus(403);
        $response->assertSee('Akses Terbatas');
    }

    public function test_admin_can_access_biaya_page(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this->actingAs($adminUser)->get(route('biaya'));

        $response->assertStatus(200);
    }

    public function test_authenticated_parent_can_access_biaya_page_with_prices(): void
    {
        $program = Program::create([
            'name' => 'Tahsin Dasar',
            'category' => 'anak',
            'icon' => 'bi-mic',
            'description' => 'Membantu memperbaiki bacaan.',
            'duration_weeks' => 12,
            'price' => 450000,
            'level' => 'Anak (10-15 th)',
            'is_popular' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $parentUser = User::factory()->parent()->create();
        $parentProfile = ParentProfile::create(['user_id' => $parentUser->id]);
        $studentUser = User::factory()->student()->create();
        Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parentProfile->id,
            'full_name' => 'Santri Anak',
            'age' => 10,
            'gender' => 'L',
        ]);

        $response = $this->actingAs($parentUser)->get(route('biaya'));

        $response->assertStatus(200);
        $response->assertSee('Tahsin Dasar');
        $response->assertSee('Rp 450.000');
        $response->assertSee('150.000');
        $response->assertSee('Pilih Paket &amp; Tentukan Jadwal', false);
        $response->assertDontSee('Flat Rp 150.000');
        $response->assertDontSee('Untuk Mentor');
        $response->assertDontSee('Untuk Owner');
        $response->assertDontSee('10% dari alokasi');
    }

    public function test_pre_register_program_stores_session_and_redirects(): void
    {
        $program = Program::create([
            'name' => 'Tahsin Dasar',
            'category' => 'anak',
            'icon' => 'bi-mic',
            'duration_weeks' => 12,
            'price' => 450000,
            'level' => 'Anak (10-15 th)',
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->post(route('program.pre-register'), [
            'program_id' => $program->id,
            'nama' => 'Bunda Fatimah',
            'nama_anak' => 'Ahmad Santri',
            'whatsapp' => '081234567890',
            'usia' => '10-15 tahun (Anak)',
            'gender' => 'L',
            'lokasi' => 'Semarang Barat',
            'metode' => 'Online',
        ]);

        $response->assertRedirect(route('register'));
        $this->assertEquals($program->id, session('pre_registration')['program_id']);
        $this->assertEquals('Ahmad Santri', session('pre_registration')['nama_anak']);
    }

    public function test_registering_from_pre_registration_links_student_to_program(): void
    {
        $program = Program::create([
            'name' => 'Tahsin Dasar',
            'category' => 'anak',
            'icon' => 'bi-mic',
            'duration_weeks' => 12,
            'price' => 450000,
            'level' => 'Anak (10-15 th)',
            'is_popular' => false,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->withSession(['pre_registration' => [
            'program_id' => $program->id,
            'nama' => 'Bunda Fatimah',
            'nama_anak' => 'Ahmad Santri',
            'whatsapp' => '081234567890',
            'usia' => '10-15 tahun (Anak)',
            'gender' => 'L',
            'lokasi' => 'Semarang Barat',
            'metode' => 'Online',
            'program' => 'Tahsin Dasar',
        ]]);

        $response = $this->post(route('register.store'), [
            'name' => 'Bunda Fatimah',
            'email' => 'bunda.fatimah@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'role' => 'parent',
        ]);

        $createdStudent = Student::where('full_name', 'Ahmad Santri')->first();
        $this->assertNotNull($createdStudent);

        $program = Program::where('name', 'Tahsin Dasar')->first();

        $response->assertRedirect(route('parent.enrollments.create', [
            'program_id' => $program->id,
            'student_id' => $createdStudent->id,
            'method' => 'online',
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'bunda.fatimah@example.com',
            'name' => 'Bunda Fatimah',
        ]);

        $this->assertDatabaseHas('students', [
            'full_name' => 'Ahmad Santri',
        ]);

        $this->assertNull(session('pre_registration'));
    }

    public function test_guest_does_not_see_biaya_cta_on_program_page(): void
    {
        $response = $this->get(route('program'));

        $response->assertStatus(200);
        $response->assertDontSee('Ingin Mengetahui Rincian Investasi');
    }

    public function test_authenticated_parent_sees_biaya_cta_on_program_page(): void
    {
        $parentUser = User::factory()->parent()->create();

        $response = $this->actingAs($parentUser)->get(route('program'));

        $response->assertStatus(200);
        $response->assertSee('Ingin Mengetahui Rincian Investasi');
        $response->assertSee('Lihat Informasi Biaya');
    }

    public function test_revenue_analytics_service_calculates_sharing_formula_correctly(): void
    {
        $service = app(RevenueAnalyticsService::class);
        $summary = $service->getRevenueSharingSummary();

        $this->assertEquals(150000, RevenueAnalyticsService::RATE_PER_SESSION);
        $this->assertEquals(100000, RevenueAnalyticsService::MENTOR_FEE_PER_SESSION);
        $this->assertEquals(50000, RevenueAnalyticsService::OWNER_GROSS_PER_SESSION);
        $this->assertEquals(5000, RevenueAnalyticsService::INFAQ_PER_SESSION);
        $this->assertEquals(45000, RevenueAnalyticsService::OWNER_NET_PER_SESSION);
        $this->assertArrayHasKey('completed_sessions_count', $summary);
        $this->assertArrayHasKey('total_retail_revenue', $summary);
        $this->assertArrayHasKey('total_mentor_honor', $summary);
        $this->assertArrayHasKey('total_infaq_dakwah', $summary);
        $this->assertArrayHasKey('total_owner_net', $summary);
    }
}
