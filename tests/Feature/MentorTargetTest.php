<?php

namespace Tests\Feature;

use App\Models\HifzTarget;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorTargetTest extends TestCase
{
    use RefreshDatabase;

    protected User $mentorUser;

    protected Mentor $mentor;

    protected Student $student1;

    protected Student $student2;

    protected User $parentUser;

    protected function setUp(): void
    {
        parent::setUp();

        $mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
        $parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);

        $this->mentorUser = User::create([
            'name' => 'Ustadz Salman',
            'email' => 'salman@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $mentorRole->id,
        ]);

        $this->mentor = Mentor::create([
            'user_id' => $this->mentorUser->id,
            'full_name' => 'Ustadz Salman',
            'specialization' => 'Tahfidz Qur\'an',
        ]);

        $this->parentUser = User::create([
            'name' => 'Bapak Joko',
            'email' => 'joko@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $parentRole->id,
        ]);

        $parent = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'phone' => '081234567899',
        ]);

        $u1 = User::create(['name' => 'Ananda Ali', 'email' => 'ali@alhikmah.com', 'password' => bcrypt('password'), 'role_id' => $studentRole->id]);
        $this->student1 = Student::create(['user_id' => $u1->id, 'parent_id' => $parent->id, 'full_name' => 'Ananda Ali', 'age' => 9, 'gender' => 'L']);
        $this->student1->mentors()->attach($this->mentor->id);

        $u2 = User::create(['name' => 'Ananda Umar', 'email' => 'umar@alhikmah.com', 'password' => bcrypt('password'), 'role_id' => $studentRole->id]);
        $this->student2 = Student::create(['user_id' => $u2->id, 'parent_id' => $parent->id, 'full_name' => 'Ananda Umar', 'age' => 11, 'gender' => 'L']);
        $this->student2->mentors()->attach($this->mentor->id);
    }

    public function test_mentor_can_view_targets_index(): void
    {
        $response = $this->actingAs($this->mentorUser)
            ->get(route('mentor.targets.index'));

        $response->assertStatus(200);
        $response->assertSee('Target Hafalan Santri');
    }

    public function test_mentor_can_view_targets_create_page(): void
    {
        $response = $this->actingAs($this->mentorUser)
            ->get(route('mentor.targets.create'));

        $response->assertStatus(200);
        $response->assertSee('Penugasan Santri Tunggal');
        $response->assertSee('Penugasan Massal (Bulk Assign)');
    }

    public function test_mentor_can_create_target_for_single_student(): void
    {
        $response = $this->actingAs($this->mentorUser)
            ->post(route('mentor.targets.store'), [
                'student_id' => $this->student1->id,
                'target_date' => now()->toDateString(),
                'surah_name' => 'QS. Al-Mulk',
                'start_ayat' => 1,
                'end_ayat' => 15,
                'notes' => 'Perhatikan makhraj huruf',
            ]);

        $response->assertRedirect(route('mentor.targets.index'));
        $this->assertDatabaseHas('hifz_targets', [
            'student_id' => $this->student1->id,
            'mentor_id' => $this->mentorUser->id,
            'surah_name' => 'QS. Al-Mulk',
            'start_ayat' => 1,
            'end_ayat' => 15,
            'total_ayat' => 15,
            'status' => 'pending',
        ]);
    }

    public function test_mentor_target_creation_sends_notification_to_parent(): void
    {
        $this->actingAs($this->mentorUser)
            ->post(route('mentor.targets.store'), [
                'student_id' => $this->student1->id,
                'target_date' => now()->toDateString(),
                'surah_name' => 'QS. An-Naba',
                'start_ayat' => 1,
                'end_ayat' => 20,
            ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->parentUser->id,
            'type' => 'info',
        ]);
    }

    public function test_mentor_can_bulk_assign_targets_to_multiple_students(): void
    {
        $response = $this->actingAs($this->mentorUser)
            ->post(route('mentor.targets.bulk-assign'), [
                'student_ids' => [$this->student1->id, $this->student2->id],
                'target_date' => now()->toDateString(),
                'surah_name' => 'QS. Al-Insan',
                'start_ayat' => 1,
                'end_ayat' => 10,
                'notes' => 'Tugas pekanan halaqah',
            ]);

        $response->assertRedirect(route('mentor.targets.index'));
        $this->assertDatabaseCount('hifz_targets', 2);
    }

    public function test_mentor_can_evaluate_and_complete_target(): void
    {
        $target = HifzTarget::create([
            'student_id' => $this->student1->id,
            'mentor_id' => $this->mentorUser->id,
            'target_date' => now()->toDateString(),
            'surah_name' => 'QS. Al-Qalam',
            'start_ayat' => 1,
            'end_ayat' => 10,
            'total_ayat' => 10,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->mentorUser)
            ->patch(route('mentor.targets.evaluate', $target->id), [
                'status' => 'completed',
                'notes' => 'Mumtaz, bacaan sangat tartil',
            ]);

        $response->assertRedirect();
        $target->refresh();
        $this->assertEquals('completed', $target->status);
    }
}
