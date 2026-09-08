<?php

namespace Tests\Unit;

use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\StudentAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StudentAccountService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StudentAccountService::class);
    }

    public function test_email_generation_standard_two_words(): void
    {
        $email = $this->service->generateStudentEmail('Dan Hermawan');
        $this->assertEquals('danhermawan@alhikmah.com', $email);
    }

    public function test_email_generation_single_word(): void
    {
        $email = $this->service->generateStudentEmail('Fatimah');
        $this->assertEquals('fatimah@alhikmah.com', $email);
    }

    public function test_email_generation_three_words(): void
    {
        $email = $this->service->generateStudentEmail('Muhammad Dan Hermawan');
        $this->assertEquals('muhammaddanhermawan@alhikmah.com', $email);
    }

    public function test_email_generation_with_special_characters(): void
    {
        $email = $this->service->generateStudentEmail('Hikmatul Hasanah, S.Pd');
        $this->assertEquals('hikmatulhasanahspd@alhikmah.com', $email);
    }

    public function test_email_generation_handles_duplicates(): void
    {
        Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

        User::create([
            'name' => 'Dan Hermawan',
            'email' => 'danhermawan@alhikmah.com',
            'password' => bcrypt('password123'),
        ]);

        $secondEmail = $this->service->generateStudentEmail('Dan Hermawan');
        $this->assertEquals('danhermawan2@alhikmah.com', $secondEmail);

        User::create([
            'name' => 'Dan Hermawan 2',
            'email' => 'danhermawan2@alhikmah.com',
            'password' => bcrypt('password123'),
        ]);

        $thirdEmail = $this->service->generateStudentEmail('Dan Hermawan');
        $this->assertEquals('danhermawan3@alhikmah.com', $thirdEmail);
    }

    public function test_password_generation_length(): void
    {
        $password = $this->service->generatePassword(8);
        $this->assertEquals(8, strlen($password));

        $password12 = $this->service->generatePassword(12);
        $this->assertEquals(12, strlen($password12));
    }

    public function test_password_generation_complexity(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $password = $this->service->generatePassword(10);
            $this->assertMatchesRegularExpression('/[A-Z]/', $password, 'Password must contain uppercase');
            $this->assertMatchesRegularExpression('/[a-z]/', $password, 'Password must contain lowercase');
            $this->assertMatchesRegularExpression('/[0-9]/', $password, 'Password must contain number');
            $this->assertMatchesRegularExpression('/[!@#$%^&*]/', $password, 'Password must contain special char');
        }
    }

    public function test_create_student_account_creates_all_records_and_initializes_30_juz(): void
    {
        Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
        Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);

        $parentUser = User::create([
            'name' => 'Bapak Abdullah',
            'email' => 'abdullah@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $parent = ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '081234567890',
            'address' => 'Jakarta Selatan',
        ]);

        $result = $this->service->createStudentAccount(
            $parent,
            'Ahmad Fauzan',
            10,
            'L',
            'Jakarta Selatan',
            'Program Tahfidz'
        );

        $this->assertNotNull($result['user']);
        $this->assertNotNull($result['student']);
        $this->assertEquals('santri123', $result['plain_password']);
        $this->assertEquals('ahmadfauzan@alhikmah.com', $result['user']->email);
        $this->assertEquals(10, $result['student']->age);

        // Verify 30 juz initialized
        $this->assertDatabaseCount('juz_progress', 30);
        $this->assertDatabaseHas('juz_progress', [
            'student_id' => $result['student']->id,
            'juz_number' => 30,
            'status' => 'not_started',
        ]);
    }

    public function test_domain_setting_respected(): void
    {
        Setting::create([
            'key' => 'institution_domain',
            'value' => 'pesantrenalhikmah.sch.id',
        ]);

        $email = $this->service->generateStudentEmail('Zaid Abdullah');
        $this->assertEquals('zaidabdullah@pesantrenalhikmah.sch.id', $email);
    }
}
