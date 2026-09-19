<?php

namespace Database\Seeders;

use App\Models\Mentor;
use Illuminate\Database\Seeder;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a Specific Top Mentor for Testing
        Mentor::factory()->create([
            'full_name' => 'Ustadz Ahmad Al-Hafidz',
            'gender' => 'L',
            'specialization' => 'Tahfidz 30 Juz & Sanad',
            'bio' => 'Berpengalaman mengajar Tahfidz lebih dari 10 tahun dan memiliki sanad Qira\'at Ashim riwayat Hafs.',
            'experience_years' => 10,
            'hifz_total_juz' => 30,
            'city' => 'Jakarta Selatan',
            'rating' => 5.0,
            'status' => 'active',
            'is_active' => true,
        ]);

        Mentor::factory()->create([
            'full_name' => 'Ustadzah Fatimah Az-Zahra',
            'gender' => 'P',
            'specialization' => 'Tahsin & Makharijul Huruf',
            'bio' => 'Fokus pada perbaikan bacaan (Tahsin) dari dasar hingga mahir dengan metode interaktif untuk anak-anak dan dewasa.',
            'experience_years' => 5,
            'hifz_total_juz' => 5,
            'city' => 'Depok',
            'rating' => 4.8,
            'status' => 'active',
            'is_active' => true,
        ]);

        Mentor::factory()->create([
            'full_name' => 'Ustadz Budi Santoso',
            'gender' => 'L',
            'specialization' => 'Iqra & Dasar Al-Qur\'an',
            'bio' => 'Sabar dalam mengajarkan anak-anak Iqra dari nol.',
            'experience_years' => 3,
            'hifz_total_juz' => 2,
            'city' => 'Bogor',
            'rating' => 4.5,
            'status' => 'probation',
            'probation_end_date' => now()->addDays(30),
            'is_active' => true,
        ]);

        // 2. Generate 20 random Mentors
        Mentor::factory()->count(20)->create();
    }
}
