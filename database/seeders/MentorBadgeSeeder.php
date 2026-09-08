<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class MentorBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'code' => 'M01',
                'name' => 'Pendamping Teladan',
                'description' => 'Dedikasi bimbingan prima & evaluasi adab sangat baik selama masa bimbingan.',
                'icon' => 'fas fa-award',
                'category' => 'adab',
                'points_reward' => 500,
                'criteria_json' => ['type' => 'adab_score', 'threshold' => 90],
                'is_active' => true,
            ],
            [
                'code' => 'M02',
                'name' => 'Muallim Inspiratif',
                'description' => 'Inovasi metode ajar interaktif dan disenangi para santri.',
                'icon' => 'fas fa-lightbulb',
                'category' => 'achievement',
                'points_reward' => 500,
                'criteria_json' => ['type' => 'method_innovation', 'threshold' => 1],
                'is_active' => true,
            ],
            [
                'code' => 'M03',
                'name' => 'Guru Mumtaz',
                'description' => 'Rata-rata nilai tajwid seluruh santri binaan mencapai predikat Mumtaz (>= 90.0).',
                'icon' => 'fas fa-quran',
                'category' => 'achievement',
                'points_reward' => 500,
                'criteria_json' => ['type' => 'tajwid_score', 'threshold' => 90],
                'is_active' => true,
            ],
            [
                'code' => 'M04',
                'name' => 'Retention Champion',
                'description' => 'Tingkat retensi santri konsisten >= 95.0% selama 3 bulan berturut-turut.',
                'icon' => 'fas fa-shield-alt',
                'category' => 'achievement',
                'points_reward' => 750,
                'criteria_json' => ['type' => 'retention_rate', 'threshold' => 95, 'months' => 3],
                'is_active' => true,
            ],
            [
                'code' => 'M05',
                'name' => 'Tajwid Master',
                'description' => 'Nilai tajwid seluruh santri konsisten >= 90.0 dalam 1 semester bimbingan.',
                'icon' => 'fas fa-book-reader',
                'category' => 'achievement',
                'points_reward' => 750,
                'criteria_json' => ['type' => 'tajwid_mastery', 'threshold' => 90, 'months' => 6],
                'is_active' => true,
            ],
            [
                'code' => 'M06',
                'name' => 'Parent Favorite',
                'description' => 'Rata-rata rating ulasan kepuasan wali santri >= 4.90 dengan minimal 20 ulasan.',
                'icon' => 'fas fa-star',
                'category' => 'achievement',
                'points_reward' => 750,
                'criteria_json' => ['type' => 'parent_rating', 'threshold' => 4.9, 'min_reviews' => 20],
                'is_active' => true,
            ],
            [
                'code' => 'M07',
                'name' => 'Consistency King',
                'description' => 'Meraih Skor Komposit Kinerja >= 85.0 selama 6 bulan berturut-turut.',
                'icon' => 'fas fa-crown',
                'category' => 'milestone',
                'points_reward' => 1000,
                'criteria_json' => ['type' => 'composite_consistency', 'threshold' => 85, 'months' => 6],
                'is_active' => true,
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::updateOrCreate(
                ['code' => $badgeData['code']],
                $badgeData
            );
        }
    }
}
