<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=(self)');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
    }

    public function test_hsts_header_is_present_on_secure_requests(): void
    {
        $response = $this->get('https://localhost/');

        $response->assertStatus(200);
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_contact_form_has_rate_limiting(): void
    {

        // Send 5 requests (allowed)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/kontak', [
                'name' => 'Fulan',
                'email' => "fulan{$i}@example.com",
                'subject' => 'Pertanyaan',
                'message' => 'Assalamualaikum, ini pesan pengujian keamanan.',
            ]);
            // Either redirect (success/validation) or 302
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th request should hit 429 Too Many Requests
        $response = $this->post('/kontak', [
            'name' => 'Spammer',
            'email' => 'spammer@example.com',
            'subject' => 'Spam Test',
            'message' => 'Spam message',
        ]);

        $response->assertStatus(429);
    }

    public function test_trial_booking_has_rate_limiting(): void
    {
        RateLimiter::clear('trial_booking');

        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/uji-coba-gratis', [
                'parent_name' => 'Bunda Aisyah',
                'parent_phone' => '081234567890',
                'child_name' => 'Abdullah',
                'child_age' => 8,
                'program_interest' => 'tahsin',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th request should be throttled
        $response = $this->post('/uji-coba-gratis', [
            'parent_name' => 'Bunda Aisyah',
            'parent_phone' => '081234567890',
            'child_name' => 'Abdullah',
            'child_age' => 8,
            'program_interest' => 'tahsin',
        ]);

        $response->assertStatus(429);
    }

    public function test_status_tracker_has_rate_limiting(): void
    {
        RateLimiter::clear('status_tracker');

        for ($i = 0; $i < 10; $i++) {
            $response = $this->post('/cek-status-lamaran', [
                'phone' => '081234567890',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 11th request should be throttled
        $response = $this->post('/cek-status-lamaran', [
            'phone' => '081234567890',
        ]);

        $response->assertStatus(429);
    }

    public function test_session_cookie_configuration_is_secure(): void
    {
        $this->assertTrue(config('session.http_only'));
        $this->assertEquals('lax', config('session.same_site'));
    }

    public function test_file_upload_rejects_dangerous_file_extensions(): void
    {
        $dangerousFile = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $response = $this->post('/bergabung', [
            'full_name' => 'Attacker',
            'email' => 'attacker@example.com',
            'phone' => '081299998888',
            'gender' => 'male',
            'address' => 'Jl. Uji Keamanan',
            'city' => 'Jakarta',
            'education' => 'S1',
            'institution' => 'Universitas',
            'experience_years' => 2,
            'experience_description' => 'Pengajar',
            'specialization' => 'tahsin',
            'hifz_total_juz' => 5,
            'cv' => $dangerousFile,
        ]);

        $response->assertSessionHasErrors('cv');
    }

    public function test_login_route_has_rate_limiting(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'wrong@alhikmah.com',
                'password' => 'wrong-password',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th attempt should be throttled
        $response = $this->post('/login', [
            'email' => 'wrong@alhikmah.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }
}
