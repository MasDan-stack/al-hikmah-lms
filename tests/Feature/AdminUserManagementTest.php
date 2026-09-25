<?php

namespace Tests\Feature;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Role $adminRole;

    private Role $mentorRole;

    private Role $parentRole;

    private Role $studentRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $this->mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $this->parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => RoleEnum::PARENT->label()]);
        $this->studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);

        $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);
    }

    public function test_admin_can_view_users_list_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee($this->admin->name);
        $response->assertSee('Manajemen Pengguna');
    }

    public function test_admin_can_create_user_and_auto_creates_mentor_profile(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Ustadz Baru',
            'email' => 'ustadz.baru@alhikmah.com',
            'phone' => '081234567890',
            'role_id' => $this->mentorRole->id,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'ustadz.baru@alhikmah.com']);
        $this->assertDatabaseHas('mentors', ['full_name' => 'Ustadz Baru']);
    }

    public function test_admin_can_update_user_role_and_sync_profile(): void
    {
        $user = User::factory()->create(['role_id' => $this->parentRole->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user->id), [
            'name' => 'Ustadz Ahmad Updated',
            'email' => $user->email,
            'phone' => $user->phone,
            'role_id' => $this->mentorRole->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertEquals($this->mentorRole->id, $user->fresh()->role_id);
        $this->assertDatabaseHas('mentors', [
            'user_id' => $user->id,
            'full_name' => 'Ustadz Ahmad Updated',
        ]);
    }

    public function test_admin_cannot_demote_own_admin_role(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $this->admin->id), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'role_id' => $this->mentorRole->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals($this->adminRole->id, $this->admin->fresh()->role_id);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        $mentorUser = User::factory()->create(['role_id' => $this->mentorRole->id]);

        $response = $this->actingAs($mentorUser)->get(route('admin.users.index'));
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }
}
