<?php

namespace Tests\Feature;

use App\Models\ParentProfile;
use App\Models\PasswordResetLog;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentChildPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected User $parentUser;

    protected ParentProfile $parent;

    protected Student $child;

    protected User $childUser;

    protected function setUp(): void
    {
        parent::setUp();

        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);

        $this->parentUser = User::create([
            'name' => 'Bapak Hendra',
            'email' => 'hendra@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $parentRole->id,
        ]);

        $this->parent = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'phone' => '081234567800',
        ]);

        $this->childUser = User::create([
            'name' => 'Ananda Dan',
            'email' => 'dan.hendra@alhikmah.com',
            'password' => bcrypt('OldSecret123!'),
            'role_id' => $studentRole->id,
        ]);

        $this->child = Student::create([
            'user_id' => $this->childUser->id,
            'parent_id' => $this->parent->id,
            'full_name' => 'Ananda Dan',
            'age' => 10,
            'gender' => 'L',
            'total_points' => 150,
            'current_streak' => 4,
        ]);

        // Satisfy parent.paid middleware
        Payment::create([
            'student_id' => $this->child->id,
            'amount' => 150000,
            'status' => 'paid',
            'invoice_number' => 'INV-TEST-001',
            'payment_date' => now(),
        ]);
    }

    public function test_parent_can_view_children_list_with_gamification_badges(): void
    {
        $response = $this->actingAs($this->parentUser)
            ->get(route('parent.children.index'));

        $response->assertStatus(200);
        $response->assertSee('Ananda Dan');
        $response->assertSee('Reset & Kirim Password', false);
        $response->assertSee('150'); // Points
        $response->assertSee('4 Hari'); // Streak
    }

    public function test_parent_can_reset_and_send_child_password(): void
    {
        $oldHash = $this->childUser->password;

        $response = $this->actingAs($this->parentUser)
            ->post(route('parent.children.reset-password', $this->child->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->childUser->refresh();
        $this->assertNotEquals($oldHash, $this->childUser->password);
    }

    public function test_parent_password_reset_creates_audit_log_without_hash(): void
    {
        $this->actingAs($this->parentUser)
            ->post(route('parent.children.reset-password', $this->child->id));

        $this->assertDatabaseHas('password_reset_logs', [
            'user_id' => $this->childUser->id,
            'changed_by' => $this->parentUser->id,
            'reset_method' => 'parent',
        ]);

        $log = PasswordResetLog::where('user_id', $this->childUser->id)->first();
        $this->assertNotNull($log);
        $this->assertArrayNotHasKey('old_password_hash', $log->getAttributes());
        $this->assertArrayNotHasKey('new_password_hash', $log->getAttributes());
    }

    public function test_parent_cannot_reset_password_of_another_parents_child(): void
    {
        $otherParentUser = User::create([
            'name' => 'Bapak Lain',
            'email' => 'lain@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->parentUser->role_id,
        ]);

        $otherParent = ParentProfile::create([
            'user_id' => $otherParentUser->id,
        ]);

        $otherChild = Student::create([
            'user_id' => $this->childUser->id,
            'parent_id' => $otherParent->id,
            'full_name' => 'Ananda Lain',
            'age' => 8,
            'gender' => 'L',
        ]);

        Payment::create([
            'student_id' => $otherChild->id,
            'amount' => 150000,
            'status' => 'paid',
            'invoice_number' => 'INV-TEST-002',
            'payment_date' => now(),
        ]);

        $response = $this->actingAs($otherParentUser)
            ->post(route('parent.children.reset-password', $this->child->id));

        $response->assertStatus(403);
    }
}
