<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $programs = Program::all();

        $tahsinProg = $programs->where('name', 'Tahsin Dasar')->first() ?? $programs->first();
        $tahfidzProg = $programs->where('name', 'Tahfidz Al-Qur\'an')->first() ?? $programs->first();
        $iqraProg = $programs->where('name', 'Iqra & Dasar Al-Qur\'an')->first() ?? $programs->first();
        $arabProg = $programs->where('category', 'bahasa_arab')->first() ?? $programs->first();

        $sampleGalleries = [
            [
                'title' => 'Bimbingan Tahsin Tartil Santri Usia 10–15 Tahun',
                'category' => 'kegiatan_belajar_mengajar',
                'program_id' => $tahsinProg?->id,
                'image_url' => 'assets/img/1.jpg',
                'caption' => 'Praktek makharijul huruf dengan metode talaqqi interaktif.',
                'description' => 'Momen bimbingan tatap muka santri AL-HIKMAH dalam memperbaiki pelafalan makhraj huruf hijaiyah dan kaidah tajwid dasar. Santri dibimbing secara sabar satu per satu agar mampu membaca dengan tartil dan percaya diri.',
                'event_date' => now()->subDays(5)->toDateString(),
                'location' => 'Ruang Belajar AL-HIKMAH Jakarta',
                'tags' => ['Anak', 'Tahsin', 'Offline', 'Talaqqi'],
                'is_featured' => true,
                'is_published' => true,
                'uploaded_by' => $admin?->id,
            ],
            [
                'title' => 'Setoran Hafalan Juz 30 & Ujian Kelancaran Santri',
                'category' => 'kegiatan_santri',
                'program_id' => $tahfidzProg?->id,
                'image_url' => 'assets/img/4.jpg',
                'caption' => 'Evaluasi berkala hafalan surat pendek dengan murajaah terstruktur.',
                'description' => 'Santri AL-HIKMAH tengah menyetorkan hafalan surat An-Naba hingga An-Nas dalam evaluasi periodik bersama Ustaz Pembimbing. Kegiatan ini menanamkan kedisiplinan dan kecintaan menjaga ayat-ayat suci Al-Qur\'an.',
                'event_date' => now()->subDays(12)->toDateString(),
                'location' => 'Aula Al-Hikmah',
                'tags' => ['Anak', 'Tahfidz', 'Hafalan', 'Offline'],
                'is_featured' => true,
                'is_published' => true,
                'uploaded_by' => $admin?->id,
            ],
            [
                'title' => 'Sesi Home Visit: Pendampingan Mengaji Privat ke Rumah',
                'category' => 'home_visit_offline',
                'program_id' => $iqraProg?->id,
                'image_url' => 'assets/img/9.jpg',
                'caption' => 'Guru pendamping berkunjung langsung ke kediaman santri binaan.',
                'description' => 'Dokumentasi pelaksanaan program Home Visit AL-HIKMAH di wilayah Jakarta Timur. Suasana belajar privat yang tenang, ramah anak, dan memudahkan orang tua memantau perkembangan ananda secara langsung dari rumah.',
                'event_date' => now()->subDays(18)->toDateString(),
                'location' => 'Kediaman Wali Santri (Jakarta Timur)',
                'tags' => ['Anak', 'Home Visit', 'Offline', 'Iqra'],
                'is_featured' => true,
                'is_published' => true,
                'uploaded_by' => $admin?->id,
            ],
            [
                'title' => 'Kelas Bimbingan Online Interaktif via Zoom Room',
                'category' => 'bimbingan_online',
                'program_id' => $arabProg?->id,
                'image_url' => 'assets/img/8.jpg',
                'caption' => 'Pembelajaran jarak jauh menggunakan media digital interaktif.',
                'description' => 'Peserta dari berbagai kota di Indonesia mengikuti bimbingan online bahasa Arab dan tahsin Al-Qur\'an secara langsung bersama Mentor. Interaksi dua arah, koreksi bacaan live, dan materi slide digital yang mudah dipahami.',
                'event_date' => now()->subDays(22)->toDateString(),
                'location' => 'Online Video Conference',
                'tags' => ['Dewasa', 'Online', 'Bahasa Arab'],
                'is_featured' => false,
                'is_published' => true,
                'uploaded_by' => $admin?->id,
            ],
            [
                'title' => 'Wisuda Santri Tahfidz & Penyerahan Syahadah Kelulusan',
                'category' => 'prestasi_santri',
                'program_id' => $tahfidzProg?->id,
                'image_url' => 'assets/img/2.jpg',
                'caption' => 'Penganugerahan sertifikat hafalan kepada santri berprestasi.',
                'description' => 'Momen haru dan membahagiakan saat santri AL-HIKMAH menerima Syahadah Tahfidz Juz 30 disaksikan oleh orang tua dan jajaran dewan asatidz. Langkah awal menuju generasi Qur\'ani yang berakhlak mulia.',
                'event_date' => now()->subDays(30)->toDateString(),
                'location' => 'Auditorium Lembaga AL-HIKMAH',
                'tags' => ['Anak', 'Wisuda', 'Tahfidz', 'Prestasi'],
                'is_featured' => true,
                'is_published' => true,
                'uploaded_by' => $admin?->id,
            ],
        ];

        foreach ($sampleGalleries as $galleryData) {
            $catId = GalleryCategory::where('slug', $galleryData['category'])->value('id');
            $galleryData['category_id'] = $catId;

            Gallery::updateOrCreate(
                ['title' => $galleryData['title']],
                $galleryData
            );
        }
    }
}
