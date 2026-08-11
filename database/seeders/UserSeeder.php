<?php

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Mentor;
use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', RoleEnum::ADMIN->value)->first();
        $mentorRole = Role::where('name', RoleEnum::MENTOR->value)->first();
        $parentRole = Role::where('name', RoleEnum::PARENT->value)->first();
        $studentRole = Role::where('name', RoleEnum::STUDENT->value)->first();

        // 1. Admin Users
        User::firstOrCreate(
            ['email' => 'admin@alhikmah.com'],
            [
                'name' => 'Admin Utama Al-Hikmah',
                'password' => Hash::make('password'),
                'role_id' => $adminRole?->id,
                'phone' => '081234567890',
            ]
        );

        // 2. Mentors (Pengajar)
        $mentorsData = [
            [
                'email' => 'ustadz.ahmad@alhikmah.com',
                'name' => 'Ustadz Ahmad Al-Hafiz',
                'phone' => '081234567891',
                'full_name' => 'Ustadz Ahmad Al-Hafiz, S.Ag',
                'specialization' => 'Tahsin & Tahfidz Al-Qur\'an',
                'bio' => 'Pengajar Al-Qur\'an berpengalaman 10 tahun dengan sanad bacaan Riwayat Hafs \'an \'Asyim.',
                'rating' => 4.95,
            ],
            [
                'email' => 'ustadzah.fatimah@alhikmah.com',
                'name' => 'Ustazah Fatimah Az-Zahra',
                'phone' => '081234567894',
                'full_name' => 'Ustazah Fatimah Az-Zahra, M.Pd',
                'specialization' => 'Bimbingan Iqra & Tajwid Anak',
                'bio' => 'Spesialis metode pengajaran Al-Qur\'an anak usia 8-15 tahun secara menyenangkan.',
                'rating' => 4.88,
            ],
            [
                'email' => 'ustadz.hasan@alhikmah.com',
                'name' => 'Ustadz Hasan Basri',
                'phone' => '081234567895',
                'full_name' => 'Ustadz Hasan Basri, S.Hum',
                'specialization' => 'Muroja\'ah & Tafsir Ringkas',
                'bio' => 'Pembimbing hafalan dan pengajar tadabbur Al-Qur\'an.',
                'rating' => 4.90,
            ],
        ];

        foreach ($mentorsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $mentorRole?->id,
                    'phone' => $data['phone'],
                ]
            );

            Mentor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $data['full_name'],
                    'specialization' => $data['specialization'],
                    'bio' => $data['bio'],
                    'rating' => $data['rating'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Parents & Students
        $parentsData = [
            [
                'email' => 'orangtua@alhikmah.com',
                'name' => 'Bapak Budi Santoso',
                'phone' => '081234567892',
                'address' => 'Jl. Kebon Jeruk No. 45, Jakarta Selatan',
                'emergency_phone' => '081234567892',
                'students' => [
                    [
                        'email' => 'santri.fathan@alhikmah.com',
                        'name' => 'Muhammad Fathan',
                        'full_name' => 'Muhammad Fathan Santoso',
                        'age' => 12,
                        'gender' => 'L',
                        'location' => 'Jakarta Selatan',
                        'notes' => 'Fokus perbaikan tajwid surah Al-Baqarah dan hafalan Juz 30.',
                    ],
                ],
            ],
            [
                'email' => 'orangtua.rahmat@alhikmah.com',
                'name' => 'Ibu Rahmatia',
                'phone' => '081234567896',
                'address' => 'Jl. Margonda Raya No. 12, Depok',
                'emergency_phone' => '081234567896',
                'students' => [
                    [
                        'email' => 'santri.aisyah@alhikmah.com',
                        'name' => 'Aisyah Humaira',
                        'full_name' => 'Aisyah Humaira Rahmat',
                        'age' => 10,
                        'gender' => 'P',
                        'location' => 'Depok',
                        'notes' => 'Program Bimbingan Iqra Jilid 5 & latihan makhraj.',
                    ],
                    [
                        'email' => 'santri.rayhan@alhikmah.com',
                        'name' => 'Rayhan Pratama',
                        'full_name' => 'Rayhan Pratama Rahmat',
                        'age' => 14,
                        'gender' => 'L',
                        'location' => 'Depok',
                        'notes' => 'Program Tahfidz Juz 29 dan Muroja\'ah Juz 30.',
                    ],
                ],
            ],
        ];

        foreach ($parentsData as $parentInfo) {
            $parentUser = User::firstOrCreate(
                ['email' => $parentInfo['email']],
                [
                    'name' => $parentInfo['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $parentRole?->id,
                    'phone' => $parentInfo['phone'],
                ]
            );

            $parentProfile = ParentProfile::firstOrCreate(
                ['user_id' => $parentUser->id],
                [
                    'address' => $parentInfo['address'],
                    'emergency_phone' => $parentInfo['emergency_phone'],
                ]
            );

            foreach ($parentInfo['students'] as $studentInfo) {
                $studentUser = User::firstOrCreate(
                    ['email' => $studentInfo['email']],
                    [
                        'name' => $studentInfo['name'],
                        'password' => Hash::make('password'),
                        'role_id' => $studentRole?->id,
                        'phone' => $parentInfo['phone'],
                    ]
                );

                $studentModel = Student::firstOrCreate(
                    ['user_id' => $studentUser->id],
                    [
                        'parent_id' => $parentProfile->id,
                        'full_name' => $studentInfo['full_name'],
                        'age' => $studentInfo['age'],
                        'gender' => $studentInfo['gender'],
                        'location' => $studentInfo['location'],
                        'notes' => $studentInfo['notes'],
                    ]
                );

                // Attach to mentors
                $allMentors = Mentor::all();
                if ($allMentors->isNotEmpty()) {
                    $studentModel->mentors()->syncWithoutDetaching([$allMentors->random()->id]);
                }
            }
        }
    }
}
