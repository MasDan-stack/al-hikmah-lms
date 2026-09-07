<?php

namespace Tests\Feature;

use App\Models\Mentor;
use App\Models\MentorApplication;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;

    protected Role $mentorRole;

    protected Role $parentRole;

    protected Role $studentRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        $this->mentorRole = Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);
        $this->parentRole = Role::firstOrCreate(['name' => 'parent'], ['label' => 'Orang Tua']);
        $this->studentRole = Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
    }

    public function test_admin_can_view_and_update_their_profile_with_avatar(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.profile.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Profil Administrator');

        $file = UploadedFile::fake()->create('admin_avatar.jpg', 200, 'image/jpeg');

        $response = $this->actingAs($admin)
            ->post(route('admin.profile.update'), [
                'name' => 'Admin Baru',
                'email' => 'admin_baru@alhikmah.com',
                'phone' => '081234567890',
                'avatar' => $file,
            ]);

        $response->assertRedirect(route('admin.profile.edit'))
            ->assertSessionHas('success');

        $admin->refresh();
        $this->assertSame('Admin Baru', $admin->name);
        $this->assertSame('admin_baru@alhikmah.com', $admin->email);
        $this->assertSame('081234567890', $admin->phone);
        $this->assertNotNull($admin->avatar);

        Storage::disk('public')->assertExists($admin->avatar);
    }

    public function test_mentor_can_update_bio_bank_account_directly_without_approval_and_phone(): void
    {
        $mentorUser = User::create([
            'name' => 'Ustadz Ahmad',
            'email' => 'ahmad@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->mentorRole->id,
            'phone' => '081111111111',
        ]);

        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustadz Ahmad Fauzi',
            'specialization' => 'Tahfidz & Tajwid',
            'bio' => 'Pengajar Al-Qur\'an',
            'address' => 'Jl. Kebon Jeruk No. 5',
            'bank_name' => 'BSI',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Ahmad Fauzi',
        ]);

        $this->actingAs($mentorUser)
            ->get(route('mentor.profile.edit'))
            ->assertOk()
            ->assertSee('Pengaturan Profil & Rekening Guru');

        $response = $this->actingAs($mentorUser)
            ->put(route('mentor.profile.update'), [
                'name' => 'Ustadz Ahmad F.',
                'email' => 'ahmad@alhikmah.com',
                'phone' => '082222222222',
                'specialization' => 'Tahsin, Tahfidz & Fiqih',
                'bio' => 'Lulusan LIPIA dengan sanad tajwid riwayah Hafs.',
                'address' => 'Jl. Pesantren No. 12, Jakarta Selatan',
                'bank_name' => 'Bank Syariah Indonesia (BSI)',
                'bank_account_number' => '7112233445',
                'bank_account_holder' => 'Ustadz Ahmad Fauzi',
            ]);

        $response->assertRedirect(route('mentor.profile.edit'))
            ->assertSessionHas('success');

        $mentorUser->refresh();
        $mentor->refresh();

        $this->assertSame('Ustadz Ahmad F.', $mentorUser->name);
        $this->assertSame('082222222222', $mentorUser->phone);
        $this->assertSame('Lulusan LIPIA dengan sanad tajwid riwayah Hafs.', $mentor->bio);
        $this->assertSame('Jl. Pesantren No. 12, Jakarta Selatan', $mentor->address);
        $this->assertSame('Bank Syariah Indonesia (BSI)', $mentor->bank_name);
        $this->assertSame('7112233445', $mentor->bank_account_number);
        $this->assertSame('Ustadz Ahmad Fauzi', $mentor->bank_account_holder);
    }

    public function test_parent_can_update_address_and_flexible_map_share_link(): void
    {
        $parentUser = User::create([
            'name' => 'Bapak Rahmat',
            'email' => 'rahmat@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->parentRole->id,
            'phone' => '083333333333',
        ]);

        $parent = ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '083333333333',
            'address' => 'Jl. Lama No. 1',
            'maps_link' => null,
        ]);

        $this->actingAs($parentUser)
            ->get(route('parent.profile.edit'))
            ->assertOk()
            ->assertSee('Edit Profil Diri');

        $mapUrl = 'https://maps.app.goo.gl/abc123xyz';

        $response = $this->actingAs($parentUser)
            ->put(route('parent.profile.update'), [
                'name' => 'Bapak Rahmat Hidayat',
                'email' => 'rahmat@alhikmah.com',
                'phone' => '083344445555',
                'address' => 'Komplek Griya Indah Blok B3 No. 7, Jakarta Barat',
                'maps_link' => $mapUrl,
            ]);

        $response->assertRedirect(route('parent.profile.edit'))
            ->assertSessionHas('success');

        $parent->refresh();
        $parentUser->refresh();

        $this->assertSame('Bapak Rahmat Hidayat', $parentUser->name);
        $this->assertSame('083344445555', $parentUser->phone);
        $this->assertSame('Komplek Griya Indah Blok B3 No. 7, Jakarta Barat', $parent->address);
        $this->assertSame($mapUrl, $parent->maps_link);
    }

    public function test_student_profile_inherits_location_from_parent_automatically(): void
    {
        $parentUser = User::create([
            'name' => 'Ibu Siti',
            'email' => 'siti@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->parentRole->id,
        ]);

        $parent = ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '087777777777',
            'address' => 'Jl. Mawar Melati No. 88, Tangerang',
            'maps_link' => 'https://maps.google.com/?q=-6.2088,106.8456',
        ]);

        $studentUser = User::create([
            'name' => 'Fathurrahman',
            'email' => 'fathur@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->studentRole->id,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'full_name' => 'Fathurrahman Al-Ayyubi',
            'nickname' => 'Fathur',
            'gender' => 'L',
            'age' => 12,
        ]);

        $this->assertSame('Jl. Mawar Melati No. 88, Tangerang', $student->effective_address);
        $this->assertSame('https://maps.google.com/?q=-6.2088,106.8456', $student->maps_link);

        // Check student edit view shows inherited location
        $this->actingAs($studentUser)
            ->get(route('student.profile.edit'))
            ->assertOk()
            ->assertSee('Jl. Mawar Melati No. 88, Tangerang')
            ->assertSee('https://maps.google.com/?q=-6.2088,106.8456')
            ->assertSee('Otomatis Sinkron dari Orang Tua');
    }

    public function test_mentor_can_see_student_parent_map_link_on_student_detail(): void
    {
        $mentorUser = User::create([
            'name' => 'Ustadz Ridwan',
            'email' => 'ridwan@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->mentorRole->id,
        ]);

        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustadz Ridwan',
        ]);

        $parentUser = User::create([
            'name' => 'Bapak Anwar',
            'email' => 'anwar@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->parentRole->id,
        ]);

        $parent = ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '088888888888',
            'address' => 'Jl. Cemara Hijau No. 10, Jakarta Selatan',
            'maps_link' => 'https://maps.app.goo.gl/testroute123',
        ]);

        $studentUser = User::create([
            'name' => 'Zaid Anwar',
            'email' => 'zaid@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->studentRole->id,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'mentor_id' => $mentor->id,
            'full_name' => 'Zaid Anwar',
            'gender' => 'L',
            'age' => 10,
        ]);

        $this->actingAs($mentorUser)
            ->get(route('mentor.students.show', $student))
            ->assertOk()
            ->assertSee('Buka Rute Peta Lokasi')
            ->assertSee('https://maps.app.goo.gl/testroute123')
            ->assertSee('Jl. Cemara Hijau No. 10, Jakarta Selatan');
    }

    public function test_student_can_view_profile_and_update_password(): void
    {
        $studentUser = User::create([
            'name' => 'Ali Akbar',
            'email' => 'ali@alhikmah.com',
            'password' => bcrypt('OldPassword123!'),
            'role_id' => $this->studentRole->id,
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Ali Akbar',
            'nickname' => 'Ali',
            'gender' => 'L',
            'age' => 11,
        ]);

        $this->actingAs($studentUser)
            ->get(route('student.profile.edit'))
            ->assertOk()
            ->assertSee('Ali Akbar');

        $response = $this->actingAs($studentUser)
            ->post(route('student.profile.password'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewSecurePass456!',
                'password_confirmation' => 'NewSecurePass456!',
            ]);

        $response->assertRedirect(route('student.profile.edit'))
            ->assertSessionHas('success');

        $studentUser->refresh();
        $this->assertTrue(Hash::check('NewSecurePass456!', $studentUser->password));

        $this->assertDatabaseHas('password_reset_logs', [
            'user_id' => $studentUser->id,
            'reset_method' => 'self',
        ]);
    }

    public function test_profile_update_rejects_avatar_larger_than_2mb(): void
    {
        Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_test@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
        ]);

        // 3MB image
        $largeFile = UploadedFile::fake()->create('huge_avatar.jpg', 3072, 'image/jpeg');

        $response = $this->actingAs($admin)
            ->post(route('admin.profile.update'), [
                'name' => 'Admin Test',
                'email' => 'admin_test@alhikmah.com',
                'avatar' => $largeFile,
            ]);

        $response->assertSessionHasErrors(['avatar']);
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $studentUser = User::create([
            'name' => 'Hasan',
            'email' => 'hasan@alhikmah.com',
            'password' => bcrypt('RealPassword123!'),
            'role_id' => $this->studentRole->id,
        ]);

        Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Hasan',
            'gender' => 'L',
            'age' => 9,
        ]);

        $response = $this->actingAs($studentUser)
            ->post(route('student.profile.password'), [
                'current_password' => 'WrongPassword123!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ]);

        $response->assertSessionHasErrors(['current_password']);
        $studentUser->refresh();
        $this->assertTrue(Hash::check('RealPassword123!', $studentUser->password));
    }

    public function test_student_can_update_profile_and_syncs_full_name_and_nickname(): void
    {
        $studentUser = User::create([
            'name' => 'Ahmad Awal',
            'email' => 'ahmad_awal@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->studentRole->id,
            'phone' => '081234567890',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'full_name' => 'Ahmad Awal',
            'nickname' => 'Ahmad',
            'gender' => 'L',
            'age' => 10,
        ]);

        $response = $this->actingAs($studentUser)
            ->post(route('student.profile.update'), [
                'name' => 'Ahmad Fauzan Al-Banjari',
                'nickname' => 'Ozan',
                'phone' => '089988776655',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $studentUser->refresh();
        $student->refresh();

        $this->assertSame('Ahmad Fauzan Al-Banjari', $studentUser->name);
        $this->assertSame('089988776655', $studentUser->phone);
        $this->assertSame('Ahmad Fauzan Al-Banjari', $student->full_name);
        $this->assertSame('Ozan', $student->nickname);
    }

    public function test_mentor_sessions_index_shows_map_navigation_link_for_offline_session(): void
    {
        $mentorUser = User::create([
            'name' => 'Ustaz Salman',
            'email' => 'salman_sessions@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->mentorRole->id,
        ]);

        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'full_name' => 'Ustaz Salman',
            'is_active' => true,
        ]);

        $parentUser = User::create([
            'name' => 'Bunda Nur',
            'email' => 'nur@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->parentRole->id,
            'phone' => '081299887766',
        ]);

        $parent = ParentProfile::create([
            'user_id' => $parentUser->id,
            'phone' => '081299887766',
            'address' => 'Jl. Anggrek No. 45, Jakarta',
            'maps_link' => 'https://maps.app.goo.gl/sesirumah123',
        ]);

        $studentUser = User::create([
            'name' => 'Ibrahim',
            'email' => 'ibrahim@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->studentRole->id,
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parent->id,
            'mentor_id' => $mentor->id,
            'full_name' => 'Ibrahim Al-Faruq',
            'gender' => 'L',
            'age' => 11,
        ]);

        Session::create([
            'mentor_id' => $mentor->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'time' => '16:00:00',
            'status' => 'scheduled',
            'method' => 'offline',
        ]);

        $this->actingAs($mentorUser)
            ->get(route('mentor.sessions.index'))
            ->assertOk()
            ->assertSee('Buka Peta')
            ->assertSee('https://maps.app.goo.gl/sesirumah123')
            ->assertSee('Offline (Home Visit)');
    }

    public function test_mentor_can_view_all_recruitment_portfolio_fields_on_profile_page(): void
    {
        Storage::fake('local');

        $mentorUser = User::create([
            'name' => 'Ustadz Hidayat, M.Ag',
            'email' => 'hidayat@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->mentorRole->id,
            'phone' => '081234567899',
        ]);

        $application = MentorApplication::create([
            'user_id' => $mentorUser->id,
            'application_code' => 'APP-202609-8888',
            'full_name' => 'Ustadz Hidayat, M.Ag',
            'email' => 'hidayat@alhikmah.com',
            'phone' => '081234567899',
            'birth_date' => '1990-05-15',
            'gender' => 'male',
            'address' => 'Jl. Dakwah No. 99',
            'city' => 'Jakarta Selatan',
            'education' => 'S2 Tafsir Al-Qur\'an',
            'institution' => 'Universitas PTIQ Jakarta',
            'experience_years' => 7,
            'experience_description' => 'Mengajar tahfidz intensif santri mutqin.',
            'specialization' => 'Tahfidz',
            'sanad_chain' => 'Sanad Thariq Asy-Syathibiyyah',
            'hifz_total_juz' => 30,
            'status' => 'approved',
            'current_stage' => 4,
            'submitted_at' => now(),
        ]);

        $cvDoc = $application->documents()->create([
            'document_type' => 'cv',
            'file_path' => 'private/mentor_applications/cv_hidayat.pdf',
            'file_name' => 'cv_hidayat.pdf',
            'file_size' => 150.5,
            'mime_type' => 'application/pdf',
        ]);

        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'application_id' => $application->id,
            'full_name' => 'Ustadz Hidayat, M.Ag',
            'birth_date' => '1990-05-15',
            'gender' => 'L',
            'address' => 'Jl. Dakwah No. 99',
            'city' => 'Jakarta Selatan',
            'education' => 'S2 Tafsir Al-Qur\'an',
            'institution' => 'Universitas PTIQ Jakarta',
            'experience_years' => 7,
            'hifz_total_juz' => 30,
            'specialization' => 'Tahfidz',
            'bio' => 'Mengajar tahfidz intensif santri mutqin.',
            'sanad_chain' => 'Sanad Thariq Asy-Syathibiyyah',
            'is_active' => true,
        ]);

        $this->actingAs($mentorUser)
            ->get(route('mentor.profile.edit'))
            ->assertOk()
            ->assertSee('Ustadz Hidayat, M.Ag')
            ->assertSee('APP-202609-8888')
            ->assertSee('Jakarta Selatan')
            ->assertSee('S2 Tafsir Al-Qur\'an')
            ->assertSee('Universitas PTIQ Jakarta')
            ->assertSee('Sanad Thariq Asy-Syathibiyyah')
            ->assertSee('cv_hidayat.pdf')
            ->assertSee(route('mentor.profile.document.download', $cvDoc->id));
    }

    public function test_mentor_can_update_portfolio_fields_and_upload_cv_and_syncs_with_application(): void
    {
        Storage::fake('local');

        $mentorUser = User::create([
            'name' => 'Ustadzah Maryam',
            'email' => 'maryam@alhikmah.com',
            'password' => bcrypt('password'),
            'role_id' => $this->mentorRole->id,
            'phone' => '087711223344',
        ]);

        $application = MentorApplication::create([
            'user_id' => $mentorUser->id,
            'application_code' => 'APP-202609-7777',
            'full_name' => 'Ustadzah Maryam',
            'email' => 'maryam@alhikmah.com',
            'phone' => '087711223344',
            'birth_date' => '1995-02-10',
            'gender' => 'female',
            'address' => 'Jl. Melati No. 1',
            'city' => 'Bandung',
            'education' => 'S1 Pendidikan Agama Islam',
            'institution' => 'UIN Sunan Gunung Djati',
            'experience_years' => 3,
            'specialization' => 'Tahsin',
            'hifz_total_juz' => 15,
            'status' => 'approved',
            'current_stage' => 4,
            'submitted_at' => now(),
        ]);

        $mentor = Mentor::create([
            'user_id' => $mentorUser->id,
            'application_id' => $application->id,
            'full_name' => 'Ustadzah Maryam',
            'birth_date' => '1995-02-10',
            'gender' => 'P',
            'address' => 'Jl. Melati No. 1',
            'city' => 'Bandung',
            'education' => 'S1 Pendidikan Agama Islam',
            'institution' => 'UIN Sunan Gunung Djati',
            'experience_years' => 3,
            'hifz_total_juz' => 15,
            'specialization' => 'Tahsin',
            'is_active' => true,
        ]);

        $newCv = UploadedFile::fake()->create('cv_maryam_updated.pdf', 300, 'application/pdf');

        $response = $this->actingAs($mentorUser)
            ->put(route('mentor.profile.update'), [
                'name' => 'Ustadzah Maryam Al-Hafizhah',
                'email' => 'maryam@alhikmah.com',
                'phone' => '087799881122',
                'birth_date' => '1995-02-10',
                'gender' => 'P',
                'address' => 'Jl. Dago Asri No. 100',
                'city' => 'Bandung Kota',
                'education' => 'S2 Ulumul Qur\'an',
                'institution' => 'Institut PTIQ',
                'experience_years' => 5,
                'hifz_total_juz' => 30,
                'specialization' => 'Tahfidz',
                'bio' => 'Hafizhah 30 juz bersanad.',
                'sanad_chain' => 'Sanad Riwayat Warsy an Nafi',
                'bank_name' => 'BSI',
                'bank_account_number' => '7119988776',
                'bank_account_name' => 'Maryam',
                'cv' => $newCv,
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $mentor->refresh();
        $application->refresh();

        $this->assertSame('Ustadzah Maryam Al-Hafizhah', $mentor->full_name);
        $this->assertSame('Bandung Kota', $mentor->city);
        $this->assertSame('S2 Ulumul Qur\'an', $mentor->education);
        $this->assertSame('Institut PTIQ', $mentor->institution);
        $this->assertSame(5, $mentor->experience_years);
        $this->assertSame(30, $mentor->hifz_total_juz);
        $this->assertSame('Tahfidz', $mentor->specialization);
        $this->assertSame('Sanad Riwayat Warsy an Nafi', $mentor->sanad_chain);

        // Pastikan juga sinkron ke tabel mentor_applications
        $this->assertSame('Ustadzah Maryam Al-Hafizhah', $application->full_name);
        $this->assertSame('Bandung Kota', $application->city);
        $this->assertSame('S2 Ulumul Qur\'an', $application->education);
        $this->assertSame(30, $application->hifz_total_juz);

        // Pastikan dokumen CV baru tercatat
        $cvDoc = $application->documents()->where('document_type', 'cv')->first();
        $this->assertNotNull($cvDoc);
        $this->assertSame('cv_maryam_updated.pdf', $cvDoc->file_name);
    }
}
