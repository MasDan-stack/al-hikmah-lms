<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $adminRole = Role::where('name', RoleEnum::ADMIN->value)->first();
        $mentorRole = Role::where('name', RoleEnum::MENTOR->value)->first();
        $parentRole = Role::where('name', RoleEnum::PARENT->value)->first();
        $studentRole = Role::where('name', RoleEnum::STUDENT->value)->first();

        // 1. Admin Account
        User::firstOrCreate(
            ['email' => 'admin@alhikmah.com'],
            [
                'name' => 'Admin Al-Hikmah',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'phone' => '081234567890',
            ]
        );

        // 2. Mentor Account & Profile
        $mentorUser = User::firstOrCreate(
            ['email' => 'ustadz.ahmad@alhikmah.com'],
            [
                'name' => 'Ustadz Ahmad Al-Hafiz',
                'password' => Hash::make('password'),
                'role_id' => $mentorRole?->id,
                'phone' => '081234567891',
            ]
        );

        Mentor::firstOrCreate(
            ['user_id' => $mentorUser->id],
            [
                'full_name' => 'Ustadz Ahmad Al-Hafiz, S.Ag',
                'specialization' => 'Tahsin & Tahfidz Al-Qur\'an',
                'bio' => 'Pengajar Al-Qur\'an berpengalaman 10 tahun dengan sanad bacaan Riwayat Hafs \'an \'Asyim.',
                'rating' => 4.95,
                'is_active' => true,
            ]
        );

        // 3. Parent Account & Profile
        $parentUser = User::firstOrCreate(
            ['email' => 'orangtua@alhikmah.com'],
            [
                'name' => 'Bapak Budi Santoso',
                'password' => Hash::make('password'),
                'role_id' => $parentRole?->id,
                'phone' => '081234567892',
            ]
        );

        $parentProfile = ParentProfile::firstOrCreate(
            ['user_id' => $parentUser->id],
            [
                'address' => 'Jl. Kebon Jeruk No. 45, Jakarta Selatan',
                'emergency_phone' => '081234567892',
            ]
        );

        // 4. Student Account & Profile
        $studentUser = User::firstOrCreate(
            ['email' => 'santri.fathan@alhikmah.com'],
            [
                'name' => 'Muhammad Fathan',
                'password' => Hash::make('password'),
                'role_id' => $studentRole?->id,
                'phone' => '081234567893',
            ]
        );

        Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'parent_id' => $parentProfile->id,
                'full_name' => 'Muhammad Fathan Santoso',
                'age' => 12,
                'gender' => 'L',
                'location' => 'Jakarta Selatan',
                'notes' => 'Fokus perbaikan tajwid surah Al-Baqarah dan hafalan Juz 30.',
            ]
        );
    }
}
