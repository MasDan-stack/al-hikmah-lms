<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Iqra & Dasar Al-Qur\'an',
                'description' => 'Memulai perjalanan mengenal huruf hijaiyah dan membaca Al-Qur\'an secara bertahap.',
                'duration_weeks' => 8,
                'price' => 400000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Tahsin Dasar',
                'description' => 'Membantu memperbaiki bacaan agar lebih baik dan sesuai dengan kaidah tajwid.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Adab & Doa Harian',
                'description' => 'Mengenalkan nilai-nilai adab Islami dan doa yang dapat diamalkan dalam kehidupan sehari-hari.',
                'duration_weeks' => 8,
                'price' => 350000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Tahfidz Al-Qur\'an',
                'description' => 'Mendampingi anak dalam menghafal Al-Qur\'an secara bertahap dengan murajaah dan pembiasaan.',
                'duration_weeks' => 16,
                'price' => 500000,
                'level' => 'Anak (10-15 th)',
            ],
            [
                'name' => 'Belajar dari Nol (Dewasa)',
                'description' => 'Tidak pernah terlambat untuk memulai. Program untuk siapa saja yang ingin belajar dari dasar.',
                'duration_weeks' => 12,
                'price' => 400000,
                'level' => 'Dewasa',
            ],
            [
                'name' => 'Tahsin Dewasa',
                'description' => 'Pendampingan untuk memperbaiki makhraj, tajwid, dan kualitas bacaan.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Dewasa',
            ],
            [
                'name' => 'Kelas Muslimah',
                'description' => 'Ruang belajar yang nyaman bagi muslimah bersama pendamping wanita.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Muslimah',
            ],
            [
                'name' => 'Tahfidz Dewasa',
                'description' => 'Mendampingi perjalanan menghafal dengan target yang disesuaikan kemampuan.',
                'duration_weeks' => 16,
                'price' => 550000,
                'level' => 'Dewasa',
            ],
            [
                'name' => 'Bahasa Arab Dasar',
                'description' => 'Mengenal kosakata dan percakapan dasar untuk membangun fondasi bahasa Arab.',
                'duration_weeks' => 12,
                'price' => 500000,
                'level' => 'Bahasa Arab',
            ],
            [
                'name' => 'Nahwu & Sharaf',
                'description' => 'Mempelajari dasar-dasar tata bahasa Arab sebagai bekal memahami teks keislaman.',
                'duration_weeks' => 16,
                'price' => 600000,
                'level' => 'Bahasa Arab',
            ],
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(
                ['name' => $program['name']],
                $program
            );
        }
    }
}
