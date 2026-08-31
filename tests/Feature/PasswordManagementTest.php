<?php

namespace Tests\Feature;

use App\Models\PasswordResetLog;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

        $this->studentUser = User::create([
            'name' => 'Santri Dan',
            'email' => 'dan.santri@alhikmah.com',
            'password' => Hash::make('CurrentPass123!'),
            'role_id' => $studentRole->id,
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'full_name' => 'Santri Dan',
            'age' => 12,
            'gender' => 'L',
        ]);
    }

    public function test_student_can_view_password_reset_page(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('student.password.index'));

        $response->assertStatus(200);
        $response->assertSee('Ganti Password Akun');
    }

    public function test_student_can_reset_password_with_valid_current_password_and_strong_new_password(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->post(route('student.password.reset'), [
                'current_password' => 'CurrentPass123!',
                'new_password' => 'BrandNewStrongPass99@',
                'new_password_confirmation' => 'BrandNewStrongPass99@',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('BrandNewStrongPass99@', $this->studentUser->password));
    }

    public function test_student_cannot_reset_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->post(route('student.password.reset'), [
                'current_password' => 'WrongPassword123!',
                'new_password' => 'BrandNewStrongPass99@',
                'new_password_confirmation' => 'BrandNewStrongPass99@',
            ]);

        $response->assertSessionHasErrors('current_password');

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('CurrentPass123!', $this->studentUser->password));
    }

    public function test_student_cannot_reset_password_with_weak_password(): void
    {
        // Weak: no special character
        $response = $this->actingAs($this->studentUser)
            ->post(route('student.password.reset'), [
                'current_password' => 'CurrentPass123!',
                'new_password' => 'WeakPassword1234',
                'new_password_confirmation' => 'WeakPassword1234',
            ]);

        $response->assertSessionHasErrors('new_password');

        // Weak: too short
        $responseShort = $this->actingAs($this->studentUser)
            ->post(route('student.password.reset'), [
                'current_password' => 'CurrentPass123!',
                'new_password' => 'Aa1!',
                'new_password_confirmation' => 'Aa1!',
            ]);

        $responseShort->assertSessionHasErrors('new_password');
    }

    public function test_student_password_reset_creates_audit_log_without_password_hash(): void
    {
        $this->actingAs($this->studentUser)
            ->post(route('student.password.reset'), [
                'current_password' => 'CurrentPass123!',
                'new_password' => 'BrandNewStrongPass99@',
                'new_password_confirmation' => 'BrandNewStrongPass99@',
            ]);

        $this->assertDatabaseHas('password_reset_logs', [
            'user_id' => $this->studentUser->id,
            'changed_by' => null,
            'reset_method' => 'self',
            'notification_channel' => 'whatsapp',
            'notification_status' => 'sent',
        ]);

        $log = PasswordResetLog::where('user_id', $this->studentUser->id)->first();
        $this->assertNotNull($log);
        $this->assertArrayNotHasKey('old_password_hash', $log->getAttributes());
        $this->assertArrayNotHasKey('new_password_hash', $log->getAttributes());
    }

    public function test_student_cannot_reset_password_with_unconfirmed_password(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->post(route('student.password.reset'), [
                'current_password' => 'CurrentPass123!',
                'new_password' => 'BrandNewStrongPass99@',
                'new_password_confirmation' => 'MismatchPass99@',
            ]);

        $response->assertSessionHasErrors('new_password');
    }

    public function test_guest_cannot_access_student_password_page(): void
    {
        $response = $this->get(route('student.password.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_parent_cannot_access_student_password_reset_endpoint(): void
    {
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
        $parentUser = User::create([
            'name' => 'Bapak Parent',
            'email' => 'parent@alhikmah.com',
            'password' => Hash::make('ParentPass123!'),
            'role_id' => $parentRole->id,
        ]);

        $response = $this->actingAs($parentUser)
            ->get(route('student.password.index'));

        $response->assertStatus(403);
    }
}
