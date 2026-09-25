<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            // ========================================================
            // 4 PAKET RESMI UTAMA (OPSI A) — FLAT RP 150.000 / SESI 90 MENIT
            // ========================================================
            [
                'name' => 'Paket Tunas Istiqomah',
                'category' => 'anak',
                'icon' => 'bi-flower1',
                'description' => 'Model privat intensif (1 Guru 1 Santri) dengan 4 sesi per bulan (1x per pekan) durasi 90 menit. Dilengkapi modul materi, mutabaah hafalan, dan evaluasi tajwid berkala.',
                'duration_weeks' => 4,
                'price' => 600000,
                'level' => 'Dasar (4 Sesi/Bulan)',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Paket Bimbingan Mumtaz',
                'category' => 'anak',
                'icon' => 'bi-stars',
                'description' => 'Model privat intensif (1 Guru 1 Santri) dengan 8 sesi per bulan (2x per pekan) durasi 90 menit. Paling diminati untuk pembiasaan mengaji teratur dan akselerasi makharijul huruf.',
                'duration_weeks' => 8,
                'price' => 1200000,
                'level' => 'Paling Diminati (8 Sesi/Bulan)',
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Paket Akselerasi Itqan',
                'category' => 'anak',
                'icon' => 'bi-lightning-charge',
                'description' => 'Model privat intensif (1 Guru 1 Santri) dengan 12 sesi per bulan (3x per pekan) durasi 90 menit. Pendampingan mendalam untuk santri yang menargetkan kelancaran tartil dan pemahaman kaidah.',
                'duration_weeks' => 12,
                'price' => 1800000,
                'level' => 'Akselerasi (12 Sesi/Bulan)',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Program Unggulan: Mahir Tahfidz Al-Qur\'an',
                'category' => 'anak',
                'icon' => 'bi-bookmark-star-fill',
                'description' => 'Program unggulan khusus tahfidz privat intensif 18 sesi per bulan durasi 90 menit. Pendampingan setoran hafalan baru, murajaah harian terstruktur, dan bimbingan mutqin.',
                'duration_weeks' => 18,
                'price' => 2700000,
                'level' => 'Program Unggulan (18 Sesi/Bulan)',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],

            // ========================================================
            // PROGRAM MATERI TEMATIK & KELAS KHUSUS DEWASA / ARAB
            // ========================================================
            [
                'name' => 'Tahsin Dasar',
                'category' => 'anak',
                'icon' => 'bi-mic',
                'description' => 'Membantu memperbaiki bacaan agar lebih baik dan sesuai dengan kaidah tajwid.',
                'duration_weeks' => 12,
                'price' => 600000,
                'level' => 'Anak & Remaja',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Iqra & Dasar Al-Qur\'an',
                'category' => 'anak',
                'icon' => 'bi-book-half',
                'description' => 'Memulai perjalanan mengenal huruf hijaiyah dan membaca Al-Qur\'an secara bertahap.',
                'duration_weeks' => 8,
                'price' => 600000,
                'level' => 'Anak (10-15 th)',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Tahsin Dewasa',
                'category' => 'dewasa',
                'icon' => 'bi-mic',
                'description' => 'Pendampingan privat untuk memperbaiki makhraj, kaidah tajwid, dan kualitas bacaan dewasa.',
                'duration_weeks' => 12,
                'price' => 600000,
                'level' => 'Dewasa',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Bahasa Arab Dasar',
                'category' => 'bahasa_arab',
                'icon' => 'bi-chat-dots',
                'description' => 'Mengenal kosakata dan percakapan dasar untuk membangun fondasi bahasa Arab Al-Qur\'an.',
                'duration_weeks' => 12,
                'price' => 600000,
                'level' => 'Bahasa Arab',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Nahwu & Sharaf',
                'category' => 'bahasa_arab',
                'icon' => 'bi-book',
                'description' => 'Mempelajari dasar-dasar tata bahasa Arab sebagai bekal memahami teks keislaman dan kaidah.',
                'duration_weeks' => 16,
                'price' => 600000,
                'level' => 'Bahasa Arab',
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 9,
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
