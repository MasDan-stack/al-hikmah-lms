<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Database\Seeder;

class GalleryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // 1. Kategori Utama (Core Activities)
            [
                'name' => 'Kegiatan Belajar Mengajar',
                'slug' => 'kegiatan_belajar_mengajar',
                'group' => 'Kategori Utama',
                'icon' => 'bi-book',
                'badge_class' => 'bg-success',
                'description' => 'Dokumentasi pelaksanaan kegiatan bimbingan mengaji dan belajar Al-Qur\'an.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Kegiatan Santri',
                'slug' => 'kegiatan_santri',
                'group' => 'Kategori Utama',
                'icon' => 'bi-mortarboard',
                'badge_class' => 'bg-primary',
                'description' => 'Aktivitas santri dalam setoran hafalan, murajaah, dan pembiasaan adab harian.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Kegiatan Mentor / Pengajar',
                'slug' => 'kegiatan_mentor',
                'group' => 'Kategori Utama',
                'icon' => 'bi-person-video3',
                'badge_class' => 'bg-info text-dark',
                'description' => 'Aktivitas pendamping dan pengajar Al-Qur\'an dalam membimbing peserta didik.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Home Visit / Bimbingan Offline',
                'slug' => 'home_visit_offline',
                'group' => 'Kategori Utama',
                'icon' => 'bi-house-door',
                'badge_class' => 'bg-warning text-dark',
                'description' => 'Sesi tatap muka langsung di kediaman santri binaan secara privat dan intensif.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Bimbingan Online',
                'slug' => 'bimbingan_online',
                'group' => 'Kategori Utama',
                'icon' => 'bi-laptop',
                'badge_class' => 'bg-secondary',
                'description' => 'Sesi pembelajaran jarak jauh interaktif via ruang video conference.',
                'sort_order' => 5,
                'is_active' => true,
            ],

            // 2. Kategori Acara Khusus (Events)
            [
                'name' => 'Acara Spesial',
                'slug' => 'acara_spesial',
                'group' => 'Acara Khusus',
                'icon' => 'bi-balloon',
                'badge_class' => 'bg-danger',
                'description' => 'Peringatan hari besar Islam, pesantren kilat, dan kegiatan khusus lainnya.',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Prestasi Santri',
                'slug' => 'prestasi_santri',
                'group' => 'Prestasi & Kolaborasi',
                'icon' => 'bi-trophy',
                'badge_class' => 'bg-success-subtle text-success border border-success',
                'description' => 'Pencapaian wisuda tahfidz, sertifikat kelulusan jilid, dan apresiasi santri berprestasi.',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Kunjungan & Kolaborasi',
                'slug' => 'kunjungan_kolaborasi',
                'group' => 'Prestasi & Kolaborasi',
                'icon' => 'bi-people',
                'badge_class' => 'bg-primary-subtle text-primary border border-primary',
                'description' => 'Kunjungan silaturahmi, kemitraan lembaga, dan kolaborasi dakwah Qur\'ani.',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $catData) {
            $category = GalleryCategory::updateOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );

            // Hubungkan foto galeri yang sudah ada dengan slug ini ke category_id
            Gallery::where('category', $catData['slug'])->update(['category_id' => $category->id]);
        }
    }
}
