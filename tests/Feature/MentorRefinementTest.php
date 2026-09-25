<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\Message;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorRefinementTest extends TestCase
{
    use RefreshDatabase;

    protected User $mentorUser;

    protected Mentor $mentor;

    protected User $parentUser;

    protected ParentProfile $parentProfile;

    protected User $studentUser;

    protected Student $student;

    protected Program $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mentorUser = User::factory()->mentor()->create(['name' => 'Ustaz Salman']);
        $this->mentor = Mentor::create([
            'user_id' => $this->mentorUser->id,
            'full_name' => 'Ustaz Salman',
            'is_active' => true,
            'specialization' => 'Tahsin & Tahfidz',
        ]);

        $this->parentUser = User::factory()->parent()->create(['name' => 'Bunda Anisa', 'phone' => '08123456789']);
        $this->parentProfile = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'emergency_phone' => '08123456789',
            'address' => 'Jl. Cisitu No. 5 Bandung',
        ]);

        $this->studentUser = User::factory()->student()->create(['name' => 'Fadhil Santri']);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'parent_id' => $this->parentProfile->id,
            'full_name' => 'Fadhil Santri',
            'age' => 11,
            'gender' => 'L',
            'location' => 'Bandung',
        ]);

        $this->program = Program::create([
            'name' => 'Tahsin Anak',
            'slug' => 'tahsin-anak',
            'price' => 250000,
            'is_active' => true,
        ]);
    }

    public function test_roadmap_shows_pilih_program_for_authenticated_parent(): void
    {
        $response = $this->actingAs($this->parentUser)->get(route('roadmap'));

        $response->assertStatus(200);
        $response->assertSee('Pilih Program');
        $response->assertSee(route('biaya'));
    }

    public function test_roadmap_and_biaya_show_reviewing_status_when_enrollment_pending(): void
    {
        Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_price' => 250000,
            'status' => EnrollmentStatus::WAITING_ADMIN,
            'requested_days' => ['monday'],
            'requested_time' => '16:00:00',
        ]);

        // Roadmap page check
        $roadmapRes = $this->actingAs($this->parentUser)->get(route('roadmap'));
        $roadmapRes->assertStatus(200);
        $roadmapRes->assertSee('Sedang Direview');
        $roadmapRes->assertSee('Tahsin Anak');

        // Biaya page check
        $biayaRes = $this->actingAs($this->parentUser)->get(route('biaya'));
        $biayaRes->assertStatus(200);
        $biayaRes->assertSee('Status: Sedang Direview');
        $biayaRes->assertSee('Fadhil Santri');
        $biayaRes->assertSee('Pantau Status Jadwal');
    }

    public function test_roadmap_and_biaya_show_active_program_when_enrollment_paid(): void
    {
        Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'mentor_id' => $this->mentor->id,
            'program_price' => 250000,
            'status' => EnrollmentStatus::ACTIVE,
            'start_date' => now(),
            'paid_at' => now(),
        ]);

        // Roadmap page check
        $roadmapRes = $this->actingAs($this->parentUser)->get(route('roadmap'));
        $roadmapRes->assertStatus(200);
        $roadmapRes->assertSee('Program Aktif: Tahsin Anak');

        // Biaya page check
        $biayaRes = $this->actingAs($this->parentUser)->get(route('biaya'));
        $biayaRes->assertStatus(200);
        $biayaRes->assertSee('Status: Bimbingan Aktif Berjalan');
        $biayaRes->assertSee('Lihat Sesi Bimbingan');
    }

    public function test_roadmap_shows_booking_jadwal_modal_for_guest(): void
    {
        $response = $this->get(route('roadmap'));

        $response->assertStatus(200);
        $response->assertSee('Booking Jadwal');
        $response->assertSee('data-bs-target="#daftarModal"', false);
    }

    public function test_mentor_progress_create_has_deduplicated_students(): void
    {
        // Simulasikan multi-jadwal (2 entri di pivot table untuk santri yang sama)
        $this->mentor->students()->attach($this->student->id, ['day_assigned' => 'monday', 'time_assigned' => '16:00:00']);
        $this->mentor->students()->attach($this->student->id, ['day_assigned' => 'thursday', 'time_assigned' => '16:00:00']);

        $response = $this->actingAs($this->mentorUser)->get(route('mentor.progress.create'));

        $response->assertStatus(200);
        $students = $response->viewData('students');

        // Harus hanya 1 santri unik
        $this->assertCount(1, $students);
        $this->assertEquals('Fadhil Santri', $students->first()->full_name);
    }

    public function test_mentor_dashboard_has_deduplicated_students_count(): void
    {
        // Multi entri pivot table
        $this->mentor->students()->attach($this->student->id, ['day_assigned' => 'monday', 'time_assigned' => '16:00:00']);
        $this->mentor->students()->attach($this->student->id, ['day_assigned' => 'thursday', 'time_assigned' => '16:00:00']);

        $response = $this->actingAs($this->mentorUser)->get(route('mentor.dashboard'));

        $response->assertStatus(200);
        $students = $response->viewData('students');
        $activeStudentsCount = $response->viewData('activeStudentsCount');

        $this->assertCount(1, $students);
        $this->assertEquals(1, $activeStudentsCount);
    }

    public function test_mentor_can_view_messages_inbox(): void
    {
        Message::create([
            'sender_id' => $this->parentUser->id,
            'receiver_id' => $this->mentorUser->id,
            'student_id' => $this->student->id,
            'message' => 'Assalamualaikum Ustaz, ananda besok izin terlambat 10 menit.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->mentorUser)->get(route('mentor.messages.index'));

        $response->assertStatus(200);
        $response->assertSee('Bunda Anisa');
        $response->assertSee('Assalamualaikum Ustaz, ananda besok izin terlambat');
    }

    public function test_mentor_can_chat_and_reply_to_parent(): void
    {
        $msg = Message::create([
            'sender_id' => $this->parentUser->id,
            'receiver_id' => $this->mentorUser->id,
            'student_id' => $this->student->id,
            'message' => 'Tanya perkembangan tajwid Fadhil Ustaz.',
            'is_read' => false,
        ]);

        // Buka chat room -> pesan harus otomatis ditandai dibaca
        $response = $this->actingAs($this->mentorUser)->get(route('mentor.messages.chat', $this->parentUser->id));
        $response->assertStatus(200);
        $response->assertSee('Tanya perkembangan tajwid Fadhil');
        $this->assertTrue($msg->fresh()->is_read);

        // Balas chat
        $replyResponse = $this->actingAs($this->mentorUser)->post(route('mentor.messages.store'), [
            'receiver_id' => $this->parentUser->id,
            'student_id' => $this->student->id,
            'message' => 'Waalaikumsalam Bunda, Alhamdulillah makhraj huruf Fadhil sudah sangat meningkat.',
        ]);

        $replyResponse->assertRedirect(route('mentor.messages.chat', $this->parentUser->id));
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->mentorUser->id,
            'receiver_id' => $this->parentUser->id,
            'message' => 'Waalaikumsalam Bunda, Alhamdulillah makhraj huruf Fadhil sudah sangat meningkat.',
        ]);
    }

    public function test_role_aware_403_page_displays_mentor_dashboard_link_when_logged_in(): void
    {
        // Mentor mencoba paksa akses parent dashboard
        $response = $this->actingAs($this->mentorUser)->get(route('parent.dashboard'));

        $response->assertStatus(403);
        $response->assertSee('Guru / Pendamping');
        $response->assertSee(route('mentor.dashboard'));
        $response->assertSee('Kembali ke Dashboard Guru/Pendamping');
    }
}
