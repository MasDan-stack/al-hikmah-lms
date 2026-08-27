<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Metode & Tips Belajar',
                'slug' => 'metode-tips-belajar',
                'description' => 'Panduan praktis dan tips efektif mendampingi anak belajar Al-Qur\'an di rumah.',
                'icon' => 'bi-lightbulb-fill',
                'color' => '#0d7a3e',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Tahsin & Tajwid',
                'slug' => 'tahsin-tajwid',
                'description' => 'Kaidah tajwid, makharijul huruf, dan panduan membaca Al-Qur\'an dengan fasih dan tartil.',
                'icon' => 'bi-mic-fill',
                'color' => '#12a852',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Tahfidz Al-Qur\'an',
                'slug' => 'tahfidz-alquran',
                'description' => 'Metode menghafal Al-Qur\'an, strategi murajaah terstruktur, dan menjaga hafalan tetap mutqin.',
                'icon' => 'bi-bookmark-star-fill',
                'color' => '#ffc107',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Adab & Parenting Islami',
                'slug' => 'adab-parenting-islami',
                'description' => 'Mendidik akhlak anak, adab terhadap Al-Qur\'an dan guru, serta membangun keluarga Qur\'ani.',
                'icon' => 'bi-heart-pulse-fill',
                'color' => '#0284c7',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Wawasan Keislaman',
                'slug' => 'wawasan-keislaman',
                'description' => 'Kisah inspiratif para penghafal Al-Qur\'an, hikmah ayat, dan penguatan motivasi beribadah.',
                'icon' => 'bi-journal-bookmark-fill',
                'color' => '#8b5cf6',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            BlogCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
