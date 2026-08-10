<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $galleries = [
            [
                'title' => 'Kegiatan Bimbingan Tahsin Santri',
                'image_url' => 'assets/img/hero/hero-illustration.svg',
                'description' => 'Dokumentasi suasana bimbingan membaca Al-Qur\'an secara tartil dan interaktif.',
                'uploaded_by' => $admin?->id,
            ],
            [
                'title' => 'Ujian Kelancaran Hafalan Juz 30',
                'image_url' => 'assets/img/logo/logo.png',
                'description' => 'Santri AL-HIKMAH saat mengikuti serangkaian evaluasi hafalan juz 30.',
                'uploaded_by' => $admin?->id,
            ],
        ];

        foreach ($galleries as $gallery) {
            Gallery::firstOrCreate(
                ['title' => $gallery['title']],
                $gallery
            );
        }
    }
}
