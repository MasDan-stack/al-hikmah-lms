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
                'name' => 'Tahsin & Makhraj Al-Qur\'an',
                'description' => 'Program perbaikan pengucapan huruf (makhraj) dan hukum tajwid dasar secara tartil.',
                'duration_weeks' => 12,
                'price' => 350000,
                'level' => 'Pemula',
            ],
            [
                'name' => 'Tahfidz Juz 30 & Juz Amma',
                'description' => 'Program bimbingan hafalan Juz 30 lengkap dengan setoran rutin dan tes kelancaran.',
                'duration_weeks' => 16,
                'price' => 450000,
                'level' => 'Menengah',
            ],
            [
                'name' => 'Bimbingan Iqra Anak (Jilid 1-6)',
                'description' => 'Program dasar membaca Al-Qur\'an bagi anak usia 10-15 tahun dari tingkat paling dasar.',
                'duration_weeks' => 8,
                'price' => 250000,
                'level' => 'Dasar',
            ],
            [
                'name' => 'Muroja\'ah & Adab Al-Qur\'an',
                'description' => 'Pendampingan menjaga hafalan yang sudah dimiliki serta penanaman adab penuntut ilmu Al-Qur\'an.',
                'duration_weeks' => 12,
                'price' => 400000,
                'level' => 'Lanjutan',
            ],
            [
                'name' => 'Tahfidz Tematik & Tajwid Lengkap',
                'description' => 'Bimbingan hafalan ayat-ayat pilihan dan pendalaman hukum tajwid mutaqaddimin.',
                'duration_weeks' => 20,
                'price' => 600000,
                'level' => 'Lanjutan',
            ],
        ];

        foreach ($programs as $program) {
            Program::firstOrCreate(
                ['name' => $program['name']],
                $program
            );
        }
    }
}
