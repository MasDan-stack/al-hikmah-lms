<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'code' => 'B01',
                'name' => 'Penyemai Qur\'an',
                'description' => 'Diberikan saat santri berhasil melakukan setoran ayat pertama.',
                'icon' => 'bi-seedling',
                'category' => 'milestone',
                'points_reward' => 50,
                'criteria_json' => ['type' => 'first_setoran', 'count' => 1],
                'is_active' => true,
            ],
            [
                'code' => 'B02',
                'name' => 'Pembaca Setia',
                'description' => 'Diberikan saat santri telah mencapai 100 ayat terhafal.',
                'icon' => 'bi-book-half',
                'category' => 'milestone',
                'points_reward' => 200,
                'criteria_json' => ['type' => 'total_ayat', 'count' => 100],
                'is_active' => true,
            ],
            [
                'code' => 'B03',
                'name' => 'Bintang Juz',
                'description' => 'Diberikan saat santri menyelesaikan 1 Juz mutqin penuh.',
                'icon' => 'bi-star-fill',
                'category' => 'achievement',
                'points_reward' => 500,
                'criteria_json' => ['type' => 'juz_mutqin', 'count' => 1],
                'is_active' => true,
            ],
            [
                'code' => 'B04',
                'name' => 'Hilal Tahfidz',
                'description' => 'Diberikan saat santri mencapai 5 Juz mutqin.',
                'icon' => 'bi-moon-stars-fill',
                'category' => 'achievement',
                'points_reward' => 1500,
                'criteria_json' => ['type' => 'juz_mutqin', 'count' => 5],
                'is_active' => true,
            ],
            [
                'code' => 'B05',
                'name' => 'Matahari Qur\'an',
                'description' => 'Diberikan saat santri mencapai 10 Juz mutqin.',
                'icon' => 'bi-sun-fill',
                'category' => 'achievement',
                'points_reward' => 3000,
                'criteria_json' => ['type' => 'juz_mutqin', 'count' => 10],
                'is_active' => true,
            ],
            [
                'code' => 'B06',
                'name' => 'Hafidz/ah Qur\'an',
                'description' => 'Pencapaian tertinggi khatam 30 Juz mutqin.',
                'icon' => 'bi-trophy-fill',
                'category' => 'achievement',
                'points_reward' => 10000,
                'criteria_json' => ['type' => 'juz_mutqin', 'count' => 30],
                'is_active' => true,
            ],
            [
                'code' => 'B07',
                'name' => 'Istiqomah 7',
                'description' => 'Konsisten menyetor hafalan selama 7 hari berturut-turut.',
                'icon' => 'bi-fire',
                'category' => 'streak',
                'points_reward' => 150,
                'criteria_json' => ['type' => 'streak_days', 'count' => 7],
                'is_active' => true,
            ],
            [
                'code' => 'B08',
                'name' => 'Istiqomah 30',
                'description' => 'Konsisten menyetor hafalan selama 30 hari berturut-turut.',
                'icon' => 'bi-fire',
                'category' => 'streak',
                'points_reward' => 750,
                'criteria_json' => ['type' => 'streak_days', 'count' => 30],
                'is_active' => true,
            ],
            [
                'code' => 'B09',
                'name' => 'Istiqomah 100',
                'description' => 'Konsisten menyetor hafalan luar biasa selama 100 hari berturut-turut.',
                'icon' => 'bi-fire',
                'category' => 'streak',
                'points_reward' => 2000,
                'criteria_json' => ['type' => 'streak_days', 'count' => 100],
                'is_active' => true,
            ],
            [
                'code' => 'B10',
                'name' => 'Pemanah Tepat',
                'description' => 'Menyelesaikan target harian 100% selama 7 hari berturut-turut.',
                'icon' => 'bi-bullseye',
                'category' => 'milestone',
                'points_reward' => 300,
                'criteria_json' => ['type' => 'target_streak', 'count' => 7],
                'is_active' => true,
            ],
            [
                'code' => 'B11',
                'name' => 'Juara Bulan Ini',
                'description' => 'Meraih peringkat 1 Leaderboard Bulanan.',
                'icon' => 'bi-award-fill',
                'category' => 'leaderboard',
                'points_reward' => 1000,
                'criteria_json' => ['type' => 'leaderboard_rank', 'rank' => 1],
                'is_active' => true,
            ],
            [
                'code' => 'B12',
                'name' => 'Adab Mulia',
                'description' => 'Mendapatkan nilai adab sempurna (100/Mumtaz) sebanyak 10 kali.',
                'icon' => 'bi-heart-fill',
                'category' => 'adab',
                'points_reward' => 250,
                'criteria_json' => ['type' => 'adab_mumtaz', 'count' => 10],
                'is_active' => true,
            ],
            [
                'code' => 'B13',
                'name' => 'Tilawah Pagi',
                'description' => 'Melakukan 30 kali setoran di waktu pagi hari sebelum Dhuha.',
                'icon' => 'bi-brightness-high-fill',
                'category' => 'milestone',
                'points_reward' => 500,
                'criteria_json' => ['type' => 'morning_setoran', 'count' => 30],
                'is_active' => true,
            ],
            [
                'code' => 'B14',
                'name' => 'Kolaborasi Keluarga',
                'description' => 'Wali santri aktif mengonfirmasi 5 sesi pendampingan anak.',
                'icon' => 'bi-people-fill',
                'category' => 'adab',
                'points_reward' => 100,
                'criteria_json' => ['type' => 'parent_confirmation', 'count' => 5],
                'is_active' => true,
            ],
            [
                'code' => 'B15',
                'name' => 'Mutqin Master',
                'description' => 'Lulus ujian mutqin 3 Juz berturut-turut dengan nilai A.',
                'icon' => 'bi-gem',
                'category' => 'achievement',
                'points_reward' => 2500,
                'criteria_json' => ['type' => 'mutqin_exam_streak', 'count' => 3],
                'is_active' => true,
            ],
            [
                'code' => 'M01',
                'name' => 'Mentor Certified',
                'description' => 'Diberikan saat guru lulus masa percobaan 3 bulan.',
                'icon' => 'bi-patch-check-fill',
                'category' => 'achievement',
                'points_reward' => 500,
                'criteria_json' => ['type' => 'probation_passed', 'count' => 1],
                'is_active' => true,
            ],
            [
                'code' => 'M02',
                'name' => 'Sanad Keeper',
                'description' => 'Diberikan kepada guru yang memiliki sanad Al-Qur\'an muttashil.',
                'icon' => 'bi-award-fill',
                'category' => 'achievement',
                'points_reward' => 300,
                'criteria_json' => ['type' => 'sanad_verified', 'count' => 1],
                'is_active' => true,
            ],
            [
                'code' => 'M03',
                'name' => 'Master Trainer',
                'description' => 'Diberikan kepada mentor yang melatih sesama pengajar.',
                'icon' => 'bi-mortarboard-fill',
                'category' => 'achievement',
                'points_reward' => 1000,
                'criteria_json' => ['type' => 'training_conducted', 'count' => 1],
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['code' => $badge['code']], $badge);
        }
    }
}
