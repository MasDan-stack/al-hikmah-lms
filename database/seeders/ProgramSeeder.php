<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            // 1. Program Anak (10–15 tahun) — Utama
            [
                'name' => 'Iqra & Dasar Al-Qur\'an',
                'category' => 'anak',
                'icon' => 'bi-book-half',
                'description' => 'Memulai perjalanan mengenal huruf hijaiyah dan membaca Al-Qur\'an secara bertahap.',
                'duration_weeks' => 8,
                'price' => 400000,
                'level' => 'Anak (10-15 th)',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Tahsin Dasar',
                'category' => 'anak',
                'icon' => 'bi-mic',
                'description' => 'Membantu memperbaiki bacaan agar lebih baik dan sesuai dengan kaidah tajwid.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Anak (10-15 th)',
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Adab & Doa Harian',
                'category' => 'anak',
                'icon' => 'bi-emoji-laughing',
                'description' => 'Mengenalkan nilai-nilai adab Islami dan doa yang dapat diamalkan dalam kehidupan sehari-hari.',
                'duration_weeks' => 8,
                'price' => 350000,
                'level' => 'Anak (10-15 th)',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Tahfidz Al-Qur\'an',
                'category' => 'anak',
                'icon' => 'bi-clipboard2-pulse',
                'description' => 'Mendampingi anak dalam menghafal Al-Qur\'an secara bertahap dengan murajaah dan pembiasaan.',
                'duration_weeks' => 16,
                'price' => 500000,
                'level' => 'Anak (10-15 th)',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],

            // 2. Program Tambahan (Dewasa & Muslimah)
            [
                'name' => 'Belajar dari Nol (Dewasa)',
                'category' => 'dewasa',
                'icon' => 'bi-book',
                'description' => 'Tidak pernah terlambat untuk memulai. Program untuk siapa saja yang ingin belajar dari dasar.',
                'duration_weeks' => 12,
                'price' => 400000,
                'level' => 'Dewasa',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Tahsin Dewasa',
                'category' => 'dewasa',
                'icon' => 'bi-mic',
                'description' => 'Pendampingan untuk memperbaiki makhraj, tajwid, dan kualitas bacaan.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Dewasa',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Kelas Muslimah',
                'category' => 'dewasa',
                'icon' => 'bi-people',
                'description' => 'Ruang belajar yang nyaman bagi muslimah bersama pendamping wanita.',
                'duration_weeks' => 12,
                'price' => 450000,
                'level' => 'Muslimah',
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Tahfidz Dewasa',
                'category' => 'dewasa',
                'icon' => 'bi-clipboard2-pulse',
                'description' => 'Mendampingi perjalanan menghafal dengan target yang disesuaikan kemampuan.',
                'duration_weeks' => 16,
                'price' => 550000,
                'level' => 'Dewasa',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],

            // 3. Program Bahasa Arab
            [
                'name' => 'Bahasa Arab Dasar',
                'category' => 'bahasa_arab',
                'icon' => 'bi-chat-dots',
                'description' => 'Mengenal kosakata dan percakapan dasar untuk membangun fondasi bahasa Arab.',
                'duration_weeks' => 12,
                'price' => 500000,
                'level' => 'Bahasa Arab',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Nahwu & Sharaf',
                'category' => 'bahasa_arab',
                'icon' => 'bi-book',
                'description' => 'Mempelajari dasar-dasar tata bahasa Arab sebagai bekal memahami teks keislaman.',
                'duration_weeks' => 16,
                'price' => 600000,
                'level' => 'Bahasa Arab',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 10,
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
