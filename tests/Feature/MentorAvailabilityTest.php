<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Enums\Role as RoleEnum;
use App\Events\StudentAssignedToMentor;
use App\Models\Enrollment;
use App\Models\Mentor;
use App\Models\MentorAvailability;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\MentorAvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MentorAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_save_availability_slots_0_to_6(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ust. Abdullah']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $response = $this->actingAs($mentorUser)->post(route('mentor.availability.store'), [
            'max_students' => 5,
            'days' => [
                'monday' => ['slots' => [1, 2, 3, 4]],
                'tuesday' => ['slots' => [5]],
                'wednesday' => ['slots' => [1, 2, 3, 4]],
                'thursday' => ['slots' => [1, 2, 3, 4, 5, 6]],
                'friday' => ['slots' => [2, 3, 5, 6]],
                'saturday' => ['slots' => [3, 4, 5]],
                'sunday' => ['slots' => [1, 2, 3, 4, 5, 6]],
            ],
        ]);

        $response->assertRedirect(route('mentor.availability.index'));

        $this->assertDatabaseHas('mentor_availabilities', [
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'is_available' => true,
        ]);

        $monAvail = MentorAvailability::where('mentor_id', $mentor->id)->where('day', 'monday')->first();
        $this->assertEquals([1, 2, 3, 4], $monAvail->slot_numbers);

        $tueAvail = MentorAvailability::where('mentor_id', $mentor->id)->where('day', 'tuesday')->first();
        $this->assertEquals([5], $tueAvail->slot_numbers);
    }

    public function test_whatsapp_format_parser_converts_text_to_slots(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ust. Abdullah']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $waText = "Nama : Ust. Abdullah\n\nsenin : 1 2 3 4\nselasa : 5\nRabu : 1 2 3 4\nKamis : 1 2 3 4 5 6\nJumat : 2 3 5 6\nSabtu : 3 4 5\nAhad : 1 2 3 4 5 6";

        $response = $this->actingAs($mentorUser)->post(route('mentor.availability.import-wa'), [
            'raw_text' => $waText,
            'max_students' => 5,
        ]);

        $response->assertRedirect(route('mentor.availability.index'));

        $mon = MentorAvailability::where('mentor_id', $mentor->id)->where('day', 'monday')->first();
        $this->assertEquals([1, 2, 3, 4], $mon->slot_numbers);

        $tue = MentorAvailability::where('mentor_id', $mentor->id)->where('day', 'tuesday')->first();
        $this->assertEquals([5], $tue->slot_numbers);
    }

    public function test_admin_can_view_mentor_availability_matrix_and_unfilled_notification(): void
    {
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $m1User = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ust. Abdullah bin Abdul Aziz']);
        $m1 = Mentor::create([
            'user_id' => $m1User->id,
            'full_name' => $m1User->name,
            'is_active' => true,
        ]);

        MentorAvailability::create([
            'mentor_id' => $m1->id,
            'day' => 'monday',
            'slot_numbers' => [1, 2, 3, 4],
            'max_students' => 5,
            'is_available' => true,
        ]);

        // Mentor 2 yang belum mengisi jadwal sama sekali
        $m2User = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ust. Belum Isi']);
        $m2 = Mentor::create([
            'user_id' => $m2User->id,
            'full_name' => $m2User->name,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.mentors.availability'));

        $response->assertStatus(200);
        $response->assertSee('Ust. Abdullah bin Abdul Aziz');
        $response->assertSee('08:00');
        $response->assertSee('10:00');

        // Memastikan notifikasi tertulis "Jadwal Kosong" dan "Belum Diisi" tampil di dashboard admin
        $response->assertSee('Belum Mengisi Jadwal (Jadwal Kosong)');
        $response->assertSee('Ust. Belum Isi');
        $response->assertSee('Semua Jam Kosong');
        $response->assertSee('Sisa Jam Kosong');
    }

    public function test_admin_can_assign_student_to_specific_slot(): void
    {
        Event::fake([StudentAssignedToMentor::class]);

        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ust. Pengajar Slot',
            'is_active' => true,
        ]);

        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Ahmad Fauzi',
            'age' => 10,
        ]);

        $program = Program::create([
            'name' => 'Tahfidz Qur\'an',
            'slug' => 'tahfidz-quran',
            'is_active' => true,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'slot_numbers' => [1, 2, 3, 4],
            'max_students' => 5,
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.mentors.assign-student'), [
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'slot_number' => 1,
            'program_id' => $program->id,
            'notes' => 'Mulai pekan ini',
        ]);

        $response->assertRedirect(route('admin.mentors.availability'));

        $this->assertDatabaseHas('mentor_student', [
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'day_assigned' => 'monday',
            'slot_number' => 1,
            'time_label' => '08:00',
            'is_active' => true,
        ]);

        Event::assertDispatched(StudentAssignedToMentor::class);
    }

    public function test_api_returns_available_mentors_for_slot(): void
    {
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ust. Hasan Basri']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'tuesday',
            'slot_numbers' => [5],
            'max_students' => 5,
            'is_available' => true,
        ]);

        // Cari slot 5 di hari Selasa -> Ditemukan
        $res = $this->actingAs($admin)->get(route('admin.mentors.available-api', ['day' => 'tuesday', 'slot' => 5]));
        $res->assertStatus(200);
        $res->assertJsonFragment(['name' => 'Ust. Hasan Basri']);

        // Cari slot 1 di hari Selasa -> Tidak Ditemukan
        $resEmpty = $this->actingAs($admin)->get(route('admin.mentors.available-api', ['day' => 'tuesday', 'slot' => 1]));
        $resEmpty->assertStatus(200);
        $resEmpty->assertJsonMissing(['name' => 'Ust. Hasan Basri']);
    }

    public function test_per_slot_capacity_and_concurrency_lock(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id]);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ust. Test Kuota',
            'is_active' => true,
        ]);

        MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'day' => 'monday',
            'slot_numbers' => [1, 5],
            'max_students' => 1,
            'is_available' => true,
        ]);

        $u1 = User::factory()->create();
        $st1 = Student::create(['user_id' => $u1->id, 'full_name' => 'Santri 1', 'age' => 10]);

        $u2 = User::factory()->create();
        $st2 = Student::create(['user_id' => $u2->id, 'full_name' => 'Santri 2', 'age' => 11]);

        $service = app(MentorAvailabilityService::class);

        // Alokasi Santri 1 ke Slot 1 -> Berhasil
        $service->assignStudent([
            'mentor_id' => $mentor->id,
            'student_id' => $st1->id,
            'day' => 'monday',
            'slot_number' => 1,
        ]);

        // Alokasi Santri 2 ke Slot 1 lagi -> Gagal (kuota max 1 sudah penuh)
        $this->expectException(ValidationException::class);
        $service->assignStudent([
            'mentor_id' => $mentor->id,
            'student_id' => $st2->id,
            'day' => 'monday',
            'slot_number' => 1,
        ]);
    }

    public function test_enrollment_activation_automatically_syncs_mentor_availability_and_slots(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ust. Dandi Hermawan']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $studentUser = User::factory()->create();
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Hikmatul Hasanah',
            'age' => 9,
        ]);

        $program = Program::create([
            'name' => 'Iqra & Dasar Al-Qur\'an',
            'slug' => 'iqra-dasar',
            'is_active' => true,
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'mentor_id' => $mentor->id,
            'status' => EnrollmentStatus::CONFIRMED,
            'requested_days' => ['monday', 'tuesday', 'wednesday', 'sunday'],
            'requested_time' => '18:30:00', // Slot 5
            'start_date' => now()->toDateString(),
        ]);

        // Simulasi pembayaran lunas dan aktivasi enrollment
        $enrollment->markAsPaidAndActive();

        // 1. Verifikasi pivot mentor_student memiliki slot 5 dan time_label 18:30
        $this->assertDatabaseHas('mentor_student', [
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'day_assigned' => 'monday',
            'slot_number' => 5,
            'time_label' => '18:30',
            'is_active' => true,
        ]);

        // 2. Verifikasi mentor_availabilities otomatis memiliki slot 5 pada hari-hari tersebut
        $monAvail = MentorAvailability::where('mentor_id', $mentor->id)->where('day', 'monday')->first();
        $this->assertNotNull($monAvail);
        $this->assertContains(5, $monAvail->slot_numbers);

        $tueAvail = MentorAvailability::where('mentor_id', $mentor->id)->where('day', 'tuesday')->first();
        $this->assertNotNull($tueAvail);
        $this->assertContains(5, $tueAvail->slot_numbers);

        // 3. Verifikasi halaman ketersediaan mentor langsung menampilkan slot dan nama santri
        $response = $this->actingAs($mentorUser)->get(route('mentor.availability.index'));
        $response->assertStatus(200);
        $response->assertSee('Hikmatul Hasanah');
        $response->assertSee('18:30');
    }

    public function test_mentor_saving_holiday_notifies_admin(): void
    {
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMIN->value], ['label' => RoleEnum::ADMIN->label()]);
        $adminUser = User::factory()->create(['role_id' => $adminRole->id, 'name' => 'Admin Al-Hikmah']);

        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ustazah Aisyah']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $response = $this->actingAs($mentorUser)->post(route('mentor.availability.store'), [
            'max_students' => 5,
            'days' => [
                'monday' => ['slots' => [1, 2]],
                'tuesday' => ['is_holiday' => true, 'notes' => 'Keperluan keluarga'],
                'wednesday' => ['slots' => [1, 2]],
                'thursday' => ['slots' => [1, 2]],
                'friday' => ['is_holiday' => true, 'notes' => 'Hari Libur'],
                'saturday' => ['slots' => [1, 2]],
                'sunday' => ['slots' => [1, 2]],
            ],
        ]);

        $response->assertRedirect(route('mentor.availability.index'));

        // Pastikan notifikasi tersimpan untuk Admin
        $this->assertDatabaseHas('notifications', [
            'user_id' => $adminUser->id,
            'category' => 'mentor_availability',
        ]);
    }

    public function test_enrollment_calculates_first_session_matching_requested_days(): void
    {
        $studentRole = Role::firstOrCreate(['name' => RoleEnum::STUDENT->value], ['label' => RoleEnum::STUDENT->label()]);
        $studentUser = User::factory()->create(['role_id' => $studentRole->id, 'name' => 'Ahmad Zaki']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Ahmad Zaki',
            'gender' => 'L',
            'age' => 10,
        ]);
        $program = Program::create(['name' => 'Tahfidz Anak', 'slug' => 'tahfidz-anak', 'is_active' => true]);

        // Siswa minta hari Senin, Rabu, Jumat, Minggu
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'requested_days' => ['monday', 'wednesday', 'friday', 'sunday'],
            'requested_time' => '10:00:00',
            'status' => EnrollmentStatus::WAITING_ADMIN,
        ]);

        // Base date: Sabtu (2026-09-05) + 3 hari = Selasa (2026-09-08)
        // Tetapi hari belajar yang diminta adalah Senin, Rabu, Jumat, Minggu.
        // Maka sesi pertama tidak boleh Selasa (2026-09-08), melainkan Rabu (2026-09-09)!
        $baseDate = Carbon::parse('2026-09-08'); // Tuesday
        $firstSession = $enrollment->calculateFirstSessionDate($baseDate);

        $this->assertEquals('wednesday', strtolower($firstSession->format('l')));
        $this->assertEquals('2026-09-09', $firstSession->format('Y-m-d'));
    }

    public function test_mentor_saving_availability_does_not_cascade_empty_days_into_holidays(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ustazah Fatimah']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        // Guru hanya memilih libur hari Minggu (Ahad) dan membuka hari Senin
        $response = $this->actingAs($mentorUser)->post(route('mentor.availability.store'), [
            'days' => [
                'monday' => ['slots' => [4]],
                'tuesday' => ['slots' => []], // Kosong, tapi BUKAN libur
                'wednesday' => ['slots' => []], // Kosong, tapi BUKAN libur
                'thursday' => ['slots' => []], // Kosong, tapi BUKAN libur
                'friday' => ['slots' => []], // Kosong, tapi BUKAN libur
                'saturday' => ['slots' => []], // Kosong, tapi BUKAN libur
                'sunday' => ['is_holiday' => true, 'notes' => 'Ahad saya libur'],
            ],
        ]);

        $response->assertRedirect(route('mentor.availability.index'));

        // Pastikan hanya hari Minggu yang is_holiday = true
        $sundayAvail = MentorAvailability::where('mentor_id', $mentor->id)->where('day', 'sunday')->first();
        $this->assertTrue((bool) $sundayAvail->is_holiday);
        $this->assertEquals('Ahad saya libur', $sundayAvail->notes);

        // Pastikan hari kerja lain TIDAK menjadi libur
        $nonHolidayDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        foreach ($nonHolidayDays as $day) {
            $avail = MentorAvailability::where('mentor_id', $mentor->id)->where('day', $day)->first();
            $this->assertFalse((bool) $avail->is_holiday, "Hari {$day} seharusnya tidak berstatus libur.");
            $this->assertNull($avail->notes);
        }
    }

    public function test_1_on_1_private_conflict_prevents_double_booking_same_mentor_same_slot(): void
    {
        $mentorRole = Role::firstOrCreate(['name' => RoleEnum::MENTOR->value], ['label' => RoleEnum::MENTOR->label()]);
        $mentorUser = User::factory()->create(['role_id' => $mentorRole->id, 'name' => 'Ustazah Fatimah']);
        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => $mentorUser->name,
            'is_active' => true,
        ]);

        $st1User = User::factory()->create(['name' => 'Santri 1']);
        $st1 = Student::create(['user_id' => $st1User->id, 'full_name' => 'Santri 1', 'age' => 8, 'gender' => 'P']);

        $st2User = User::factory()->create(['name' => 'Santri 2']);
        $st2 = Student::create(['user_id' => $st2User->id, 'full_name' => 'Santri 2', 'age' => 9, 'gender' => 'P']);

        $service = app(MentorAvailabilityService::class);
        $service->saveAvailability($mentor->id, [
            'availability' => ['monday' => [4]],
            'max_students' => 1,
        ]);

        // Alokasi Santri 1 ke Slot 4 hari Senin -> Berhasil
        $service->assignStudent([
            'mentor_id' => $mentor->id,
            'student_id' => $st1->id,
            'day' => 'monday',
            'slot_number' => 4,
        ]);

        // Alokasi Santri 2 ke Slot 4 hari Senin pada Guru yang sama -> Harus Ditolak (1-on-1 conflict)
        $this->expectException(ValidationException::class);
        $service->assignStudent([
            'mentor_id' => $mentor->id,
            'student_id' => $st2->id,
            'day' => 'monday',
            'slot_number' => 4,
        ]);
    }
}
