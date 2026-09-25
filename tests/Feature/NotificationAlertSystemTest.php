<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationType;
use App\Livewire\NotificationBell;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\Notification;
use App\Models\ParentProfile;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationAlertSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $parentUser;

    protected ParentProfile $parentProfile;

    protected User $studentUser;

    protected Student $student;

    protected User $mentorUser;

    protected Mentor $mentor;

    protected Program $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->admin()->create(['name' => 'Admin Utama']);

        $this->parentUser = User::factory()->parent()->create(['name' => 'Bunda Fatimah', 'phone' => '081299998888']);
        $this->parentProfile = ParentProfile::create([
            'user_id' => $this->parentUser->id,
            'emergency_phone' => '081299998888',
            'address' => 'Bandung',
        ]);

        $this->studentUser = User::factory()->student()->create(['name' => 'Ahmad Santri']);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'parent_id' => $this->parentProfile->id,
            'full_name' => 'Ahmad Santri',
            'age' => 12,
            'gender' => 'L',
            'location' => 'Bandung',
        ]);

        $this->mentorUser = User::factory()->mentor()->create(['name' => 'Ustaz Abdullah']);
        $this->mentor = Mentor::create([
            'user_id' => $this->mentorUser->id,
            'full_name' => 'Ustaz Abdullah',
            'is_active' => true,
        ]);

        $this->program = Program::create([
            'name' => 'Tahsin Intensif',
            'slug' => 'tahsin-intensif',
            'price' => 350000,
            'is_active' => true,
        ]);
    }

    public function test_notification_service_creates_record_and_type_enum(): void
    {
        $notif = NotificationService::send(
            $this->parentUser,
            'Tagihan SPP Baru',
            'Tagihan SPP bulan ini sebesar Rp 350.000 telah terbit.',
            NotificationType::WARNING,
            '/parent/payments',
            'payment'
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $notif->id,
            'user_id' => $this->parentUser->id,
            'title' => 'Tagihan SPP Baru',
            'type' => 'warning',
            'category' => 'payment',
            'is_read' => false,
        ]);

        $this->assertEquals(NotificationType::WARNING, $notif->getTypeEnum());
        $this->assertEquals('bg-warning text-dark', $notif->getTypeEnum()->badgeClass());
    }

    public function test_notification_service_broadcasts_to_all_admins(): void
    {
        $secondAdmin = User::factory()->admin()->create(['name' => 'Admin Dua']);

        NotificationService::notifyAdmins(
            'Permohonan Pendaftaran Baru',
            'Ahmad Santri mendaftar program Tahsin Intensif',
            NotificationType::INFO,
            '/admin/enrollments'
        );

        $this->assertDatabaseHas('notifications', ['user_id' => $this->adminUser->id, 'title' => 'Permohonan Pendaftaran Baru']);
        $this->assertDatabaseHas('notifications', ['user_id' => $secondAdmin->id, 'title' => 'Permohonan Pendaftaran Baru']);
    }

    public function test_livewire_notification_bell_renders_unread_badge_and_marks_read(): void
    {
        $notif = NotificationService::send(
            $this->parentUser,
            'Pemberitahuan Sistem',
            'Selamat datang di platform AL-HIKMAH.',
            NotificationType::SUCCESS
        );

        Livewire::actingAs($this->parentUser)
            ->test(NotificationBell::class)
            ->assertSee('Pemberitahuan Sistem')
            ->assertSee('1 baru')
            ->call('markAsRead', $notif->id);

        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_livewire_notification_bell_marks_all_as_read(): void
    {
        NotificationService::send($this->parentUser, 'Notif 1', 'Pesan 1');
        NotificationService::send($this->parentUser, 'Notif 2', 'Pesan 2');

        Livewire::actingAs($this->parentUser)
            ->test(NotificationBell::class)
            ->assertSee('2 baru')
            ->call('markAllAsRead')
            ->assertDontSee('2 baru');

        $this->assertEquals(0, Notification::where('user_id', $this->parentUser->id)->unread()->count());
    }

    public function test_parent_enrollment_store_dispatches_notification_to_admins(): void
    {
        $response = $this->actingAs($this->parentUser)->post(route('parent.enrollments.store'), [
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'requested_days' => ['monday', 'thursday'],
            'requested_time' => '16:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->adminUser->id,
            'title' => 'Permohonan Jadwal Belajar Baru',
            'category' => 'enrollment',
        ]);
    }

    public function test_admin_accept_enrollment_dispatches_notification_to_parent(): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'program_price' => 350000,
            'requested_days' => ['monday'],
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('admin.enrollments.accept', $enrollment->id), [
            'mentor_id' => $this->mentor->id,
            'start_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->parentUser->id,
            'title' => 'Jadwal Pendaftaran Disetujui!',
            'type' => 'success',
        ]);
    }

    public function test_mentor_progress_store_dispatches_notification_to_parent(): void
    {
        $response = $this->actingAs($this->mentorUser)->post(route('mentor.progress.store'), [
            'student_id' => $this->student->id,
            'kategori' => 'Tahfidz Juz 30',
            'surah_start' => 'Al-Fatiha',
            'ayat_start' => '1',
            'ayat_end' => '7',
            'catatan_evaluasi' => 'Bagus dan lancar',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->parentUser->id,
            'title' => 'Laporan Progres Belajar Santri',
            'category' => 'progress',
        ]);
    }

    public function test_contact_form_submission_dispatches_notification_to_admins(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => 'Bunda Sari',
            'email' => 'sari@example.com',
            'phone' => '081234567890',
            'address' => 'Bandung Kota',
            'message' => 'Saya ingin bertanya tentang biaya program tahsin anak.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->adminUser->id,
            'title' => 'Pesan Kontak Baru Received',
            'category' => 'contact',
        ]);
    }
}
