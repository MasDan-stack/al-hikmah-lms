<?php

namespace Tests\Feature;

use App\Actions\Mentors\AssignStudentAction;
use App\Enums\Role as RoleEnum;
use App\Events\StudentAssignedToMentor;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MentorAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_mentor_availability_calendar(): void
    {
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'max_students' => 5,
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.mentors.availability'));

        $response->assertStatus(200);
        $response->assertSee($mentorUser->name);
    }

    public function test_admin_can_assign_student_to_mentor_on_available_day(): void
    {
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Santri Testing',
            'age' => 10,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'max_students' => 5,
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.mentors.assign-student'), [
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'day' => 'monday',
        ]);

        $response->assertRedirect(route('admin.mentors.availability'));
        $this->assertDatabaseHas('mentor_student', [
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'day_assigned' => 'monday',
            'is_active' => true,
        ]);
    }

    public function test_mentor_can_view_his_student_parents(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $parentRole = Role::firstOrCreate(['name' => RoleEnum::PARENT->value], ['label' => RoleEnum::PARENT->label()]);
        $parentUser = User::factory()->create(['role_id' => $parentRole->id, 'name' => 'Bpk Wali Santri']);
        $parent = ParentProfile::create([
            'user_id' => $parentUser->id,
            'emergency_phone' => '08123456789',
        ]);

        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'full_name' => 'Santri Binaan',
            'age' => 12,
        ]);

        $mentor->students()->attach($student->id, ['day_assigned' => 'monday', 'is_active' => true]);

        $response = $this->actingAs($mentorUser)->get(route('mentor.students.parents'));

        $response->assertStatus(200);
        $response->assertSee('Santri Binaan');
        $response->assertSee('Bpk Wali Santri');
    }

    public function test_api_returns_available_mentors_for_day(): void
    {
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ustadz Zulfikar']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'tuesday',
            'max_students' => 5,
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.mentors.available-api', ['day' => 'tuesday']));

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Ustadz Zulfikar']);
    }

    public function test_mentor_can_update_availability_to_unavailable(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $response = $this->actingAs($mentorUser)->post(route('mentor.availability.update-bulk'), [
            'days' => [
                [
                    'day' => 'monday',
                    'is_available' => '0',
                    'start_time' => '08:00',
                    'end_time' => '16:00',
                    'max_students' => 5,
                ],
                [
                    'day' => 'tuesday',
                    'is_available' => '1',
                    'start_time' => '08:00',
                    'end_time' => '16:00',
                    'max_students' => 5,
                ],
            ],
        ]);

        $response->assertRedirect(route('mentor.availability.index'));

        $this->assertDatabaseHas('mentor_availabilities', [
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'is_available' => false,
        ]);

        $this->assertDatabaseHas('mentor_availabilities', [
            'mentor_id' => $mentor->id,
            'day' => 'tuesday',
            'is_available' => true,
        ]);
    }

    public function test_assign_student_action_dispatches_event_and_locks_quota(): void
    {
        Event::fake([StudentAssignedToMentor::class]);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ust. Concurrency Test',
            'is_active' => true,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'max_students' => 1,
            'is_available' => true,
        ]);

        $u1 = User::factory()->create();
        $student1 = Student::create(['user_id' => $u1->id, 'full_name' => 'Santri 1', 'age' => 10]);

        $u2 = User::factory()->create();
        $student2 = Student::create(['user_id' => $u2->id, 'full_name' => 'Santri 2', 'age' => 11]);

        $action = app(AssignStudentAction::class);

        $action->execute($mentor->id, $student1->id, 'monday');

        $this->assertDatabaseHas('mentor_student', [
            'mentor_id' => $mentor->id,
            'student_id' => $student1->id,
            'day_assigned' => 'monday',
            'is_active' => true,
        ]);

        Event::assertDispatched(StudentAssignedToMentor::class);

        $this->expectException(ValidationException::class);
        $action->execute($mentor->id, $student2->id, 'monday');
    }
}
