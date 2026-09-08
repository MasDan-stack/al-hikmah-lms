<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('role', function ($q) {
            $q->where('name', 'admin');
        })->first() ?? User::first();

        if (! $admin) {
            return;
        }

        $categories = BlogCategory::all()->keyBy('slug');
        $tags = BlogTag::all()->keyBy('slug');

        $articlesData = [
            [
                'title' => '5 Tips Efektif Mendampingi Anak Belajar Al-Qur\'an di Rumah dengan Penuh Cinta',
                'slug' => '5-tips-efektif-mendampingi-anak-belajar-al-quran-di-rumah',
                'category_slug' => 'metode-tips-belajar',
                'cover_image' => 'assets/img/1.jpg',
                'cover_caption' => 'Suasana pendampingan mengaji anak di rumah yang nyaman dan menyenangkan.',
                'excerpt' => 'Mendampingi anak belajar mengaji tidak harus penuh paksaan. Simak 5 langkah sederhana membangun kebiasaan mencintai Al-Qur\'an sejak usia dini.',
                'content' => '<p>Belajar Al-Qur\'an pada usia anak-anak bukan sekadar menuntaskan target lembaran buku atau juz, melainkan menanamkan benih kecintaan yang abadi terhadap kalam Ilahi. Banyak orang tua merasa kewalahan ketika anak mulai bosan atau mogok saat waktu mengaji tiba.</p>

<h3>1. Ciptakan Suasana Belajar yang Tenang dan Nyaman</h3>
<p>Sebelum memulai sesi mengaji, pastikan anak dalam kondisi segar dan tidak sedang lelah setelah aktivitas fisik berat. Ruangan yang bersih, wangi, dan bebas dari distraksi gawai (smartphone/TV) akan sangat membantu fokus anak.</p>

<h3>2. Gunakan Metode Bertahap dan Konsisten (Istiqamah)</h3>
<p>Lebih baik belajar 15–20 menit setiap hari secara rutin daripada 2 jam sekali seminggu yang membuat anak merasa terbebani. Kunci utama keberhasilan belajar membaca Al-Qur\'an adalah repetisi yang konsisten dan penuh kesabaran.</p>

<blockquote>"Sebaik-baik amalan adalah yang konsisten (istiqamah) meskipun sedikit." (HR. Bukhari & Muslim)</blockquote>

<h3>3. Berikan Apresiasi dan Penguatan Positif</h3>
<p>Pujilah usaha anak ketika ia berhasil membedakan huruf hijaiyah yang sulit atau melafalkan makhraj dengan tepat. Reward sederhana berupa pelukan hangat, stiker prestasi, atau doa tulus dari orang tua memberikan suntikan motivasi yang luar biasa.</p>

<h3>4. Jadilah Teladan di Hadapan Anak</h3>
<p>Anak adalah peniru yang ulung. Ketika anak sering melihat ayah dan bundanya membaca Al-Qur\'an dengan khusyuk di rumah, rasa penasaran dan keinginan untuk mencontoh akan tumbuh secara alami tanpa perlu dipaksa.</p>

<h3>5. Hadirkan Guru Privat yang Telaten dan Berakhlak Baik</h3>
<p>Jika orang tua memiliki keterbatasan waktu atau merasa kurang percaya diri dalam mengoreksi tajwid, menghadirkan guru privat datang ke rumah (Home Visit) dari lembaga terpercaya seperti AL-HIKMAH adalah ikhtiar terbaik untuk mendampingi tumbuh kembang Qur\'ani ananda.</p>',
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 1240,
                'shares_count' => 88,
                'reading_time' => 4,
                'published_at' => now()->subDays(2),
                'tag_slugs' => ['parenting', 'tips-mengaji', 'keluarga-qurani', 'home-visit'],
            ],
            [
                'title' => 'Mengenal Perbedaan Makharijul Huruf: Panduan Dasar Tajwid bagi Pemula',
                'slug' => 'mengenal-perbedaan-makharijul-huruf-panduan-dasar-tajwid',
                'category_slug' => 'tahsin-tajwid',
                'cover_image' => 'assets/img/2.jpg',
                'cover_caption' => 'Pelafalan makharijul huruf yang tepat merupakan fondasi bacaan tartil.',
                'excerpt' => 'Mengapa ketepatan makhraj sangat krusial dalam membaca Al-Qur\'an? Pelajari 5 kelompok tempat keluarnya huruf hijaiyah beserta contohnya.',
                'content' => '<p>Makharijul Huruf (tempat-tempat keluarnya huruf) adalah salah satu pilar paling fundamental dalam ilmu tajwid. Kesalahan dalam melafalkan makhraj huruf hijaiyah tidak hanya mengubah bunyi bacaan, tetapi juga dapat merubah makna ayat yang sangat sakral.</p>

<h3>5 Kelompok Utama Tempat Keluarnya Huruf</h3>
<ul>
    <li><strong>Al-Jauf (Rongga Mulut & Tenggorokan):</strong> Tempat keluarnya huruf-huruf mad (Alif, Wawu mad, dan Ya mad).</li>
    <li><strong>Al-Halq (Tenggorokan):</strong> Terdiri dari pangkal tenggorokan (ء, هـ), tengah tenggorokan (ع, ح), dan ujung tenggorokan (غ, خ).</li>
    <li><strong>Al-Lisan (Lidah):</strong> Tempat keluarnya sebagian besar huruf hijaiyah seperti ق, ك, ج, ش, ي, ض, ل, ن, ر, ط, د, ت, ص, ز, س, ظ, ذ, ث.</li>
    <li><strong>Asy-Syafatain (Dua Bibir):</strong> Huruf yang keluar dari bibir yaitu ف, و, ب, م.</li>
    <li><strong>Al-Khaisyum (Pangkal Hidung):</strong> Tempat keluarnya suara dengung (ghunnah).</li>
</ul>

<h3>Kiat Melatih Makhraj Anak Sejak Dini</h3>
<p>Anak-anak memiliki kelenturan organ bicara yang sangat baik. Melatih pelafalan huruf dengan cara talaqqi (tatap muka langsung mendengarkan dan menirukan bibir guru) adalah metode paling efektif agar koreksi makhraj dapat dilakukan dengan presisi tinggi.</p>',
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 950,
                'shares_count' => 64,
                'reading_time' => 5,
                'published_at' => now()->subDays(4),
                'tag_slugs' => ['tahsin', 'tajwid', 'makhraj', 'bimbingan-privat'],
            ],
            [
                'title' => 'Metode Murajaah Terstruktur: Rahasia Hafalan Al-Qur\'an Tetap Melekat Kuat (Mutqin)',
                'slug' => 'metode-murajaah-terstruktur-rahasia-hafalan-mutqin',
                'category_slug' => 'tahfidz-alquran',
                'cover_image' => 'assets/img/62.jpg',
                'cover_caption' => 'Santri AL-HIKMAH sedang menyetorkan murajaah hafalan kepada ustadz pembimbing.',
                'excerpt' => 'Menghafal ayat baru itu mudah, namun menjaga hafalan lama membutuhkan strategi yang disiplin. Pelajari formula murajaah harian yang terbukti efektif.',
                'content' => '<p>Banyak penghafal Al-Qur\'an mengeluhkan bahwa hafalan yang telah disetorkan kerap kali terlupakan saat beralih ke juz berikutnya. Rasulullah SAW mengibaratkan hafalan Al-Qur\'an bagaikan unta yang diikat; jika tidak dijaga dan diikat kuat dengan murajaah, ia akan mudah terlepas.</p>

<h3>Formula 3 Tingkat Murajaah Harian</h3>
<p>Di AL-HIKMAH LMS, kami menerapkan metode murajaah 3 pilar yang adaptif untuk santri:</p>
<ol>
    <li><strong>Sabqi (Murajaah Dekat):</strong> Mengulang 1–2 lembar sebelum hafalan baru yang disetorkan hari ini.</li>
    <li><strong>Manzil (Murajaah Sedang):</strong> Mengulang hafalan dalam 1 juz yang sedang berjalan secara bergilir setiap hari.</li>
    <li><strong>Ammah (Murajaah Jauh):</strong> Mengulang juz-juz lama yang sudah selesai secara tuntas minimal satu putaran per pekan.</li>
</ol>

<blockquote>"Jagalah Al-Qur\'an ini, demi Dzat yang jiwaku berada di tangan-Nya, sungguh Al-Qur\'an itu lebih cepat lepasnya daripada unta dari ikatannya." (HR. Bukhari)</blockquote>

<h3>Peran Pemantauan Orang Tua</h3>
<p>Dengan sistem pencatatan perkembangan online di AL-HIKMAH, wali santri dapat melihat grafik kelancaran hafalan dan catatan perbaikan tajwid langsung setelah sesi bimbingan berakhir.</p>',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 1560,
                'shares_count' => 112,
                'reading_time' => 4,
                'published_at' => now()->subDays(6),
                'tag_slugs' => ['tahfidz', 'murajaah', 'tips-mengaji', 'keluarga-qurani'],
            ],
            [
                'title' => 'Menumbuhkan Adab Sebelum Ilmu: Pondasi Penting Pendidikan Karakter Anak',
                'slug' => 'menumbuhkan-adab-sebelum-ilmu-pendidikan-karakter-anak',
                'category_slug' => 'adab-parenting-islami',
                'cover_image' => 'assets/img/3.jpg',
                'cover_caption' => 'Penanaman adab dan akhlak mulia dalam setiap sesi pembelajaran.',
                'excerpt' => 'Para ulama salaf terdahulu mempelajari adab selama puluhan tahun sebelum mempelajari ilmu. Mengapa adab menjadi kunci keberkahan ilmu anak?',
                'content' => '<p>Imam Malik rahimahullah pernah berpesan kepada seorang pemuda Quraisy: <em>"Pelajarilah adab sebelum engkau mempelajari ilmu."</em> Nasihat ini menjadi pengingat berharga bagi kita semua di tengah era modernisasi yang serba instan.</p>

<h3>Adab Terhadap Al-Qur\'an</h3>
<p>Sebelum membuka mushaf, santri dibiasakan untuk berwudhu, duduk dengan posisi sopan menghadap kiblat, dan memulai dengan ta\'awwudz serta basmalah. Hal-hal kecil ini mendidik rasa takzim dan pengagungan terhadap firman Allah Ta\'ala.</p>

<h3>Adab Terhadap Orang Tua dan Guru</h3>
<p>Guru dan orang tua adalah wasilah datangnya ilmu dan ridha Allah. Anak-anak diajarkan untuk menyimak dengan seksama saat guru menerangkan, tidak memotong pembicaraan, dan selalu mengucapkan terima kasih serta mendoakan kebaikan.</p>',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 820,
                'shares_count' => 45,
                'reading_time' => 3,
                'published_at' => now()->subDays(8),
                'tag_slugs' => ['adab', 'parenting', 'anak-sholeh', 'keluarga-qurani'],
            ],
            [
                'title' => 'Memilih Antara Kelas Mengaji Online vs Offline (Home Visit): Mana yang Lebih Tepat?',
                'slug' => 'memilih-antara-kelas-mengaji-online-vs-offline-home-visit',
                'category_slug' => 'metode-tips-belajar',
                'cover_image' => 'assets/img/4.jpg',
                'cover_caption' => 'Pilihan metode belajar fleksibel sesuai kenyamanan dan kebutuhan keluarga.',
                'excerpt' => 'Bingung menentukan metode bimbingan mengaji terbaik untuk buah hati Anda? Mari bedah kelebihan masing-masing metode Online dan Offline.',
                'content' => '<p>Setiap keluarga memiliki ritme aktivitas, lokasi, dan karakter anak yang berbeda-beda. Memilih metode belajar yang tepat adalah langkah awal memastikan anak betah dan bersemangat dalam menuntut ilmu.</p>

<h3>Kelebihan Metode Offline (Home Visit)</h3>
<ul>
    <li><strong>Koreksi Makhraj Maksimal:</strong> Guru dapat melihat langsung posisi bibir dan lidah anak secara jelas.</li>
    <li><strong>Pendekatan Emosional Erat:</strong> Kedekatan guru dan santri lebih cepat terbangun dalam sesi tatap muka langsung.</li>
    <li><strong>Kenyamanan Rumah Sendiri:</strong> Orang tua tidak perlu repot mengantar-jemput di tengah kemacetan jalanan.</li>
</ul>

<h3>Kelebihan Metode Online Interactive</h3>
<ul>
    <li><strong>Fleksibilitas Tanpa Batas:</strong> Santri dapat belajar dari mana saja di seluruh Indonesia maupun luar negeri.</li>
    <li><strong>Jadwal Lebih Leluasa:</strong> Pilihan waktu belajar lebih beragam sesuai ketersediaan waktu keluarga.</li>
</ul>

<p>Di AL-HIKMAH, Anda bahkan dapat memilih sistem <strong>Hybrid</strong> yang mengombinasikan sesi tatap muka dan daring secara fleksibel.</p>',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 670,
                'shares_count' => 30,
                'reading_time' => 4,
                'published_at' => now()->subDays(10),
                'tag_slugs' => ['home-visit', 'online-learning', 'bimbingan-privat', 'tips-mengaji'],
            ],
            [
                'title' => 'Kemuliaan Orang Tua yang Memiliki Anak Penghafal Al-Qur\'an di Hari Kiamat',
                'slug' => 'kemuliaan-orang-tua-anak-penghafal-alquran-hari-kiamat',
                'category_slug' => 'wawasan-keislaman',
                'cover_image' => 'assets/img/5.jpg',
                'cover_caption' => 'Mahkota kemuliaan cahaya di akhirat bagi orang tua yang mendidik anak mencintai Al-Qur\'an.',
                'excerpt' => 'Investasi terbaik orang tua bukanlah warisan harta, melainkan doa dan hafalan Al-Qur\'an dari anak-anak yang sholeh. Simak keutamaan agungnya.',
                'content' => '<p>Tidak ada kebahagiaan yang melebihi saat orang tua dipakaikan mahkota kemuliaan dari cahaya yang lebih terang daripada sinar matahari di hari kiamat kelak. Inilah balasan istimewa bagi ayah dan ibu yang bersabar mendidik anaknya bersama Al-Qur\'an.</p>

<blockquote>"Barangsiapa membaca Al-Qur\'an, mempelajarinya, dan mengamalkannya, maka dipakaikan kepada kedua orang tuanya pada hari kiamat sebuah mahkota dari cahaya yang sinarnya seperti sinar matahari..." (HR. Al-Hakim)</blockquote>

<h3>Langkah Nyata Memulai dari Sekarang</h3>
<p>Perjalanan mulia ini tidak harus langsung menuntut anak menghafal 30 juz seketika. Mulailah dari surah-surah pendek Juz Amma, perbaiki bacaannya, dan jadikan proses belajar mengaji sebagai momen yang dirindukan oleh anak setiap hari.</p>',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 1890,
                'shares_count' => 140,
                'reading_time' => 3,
                'published_at' => now()->subDays(12),
                'tag_slugs' => ['tahfidz', 'anak-sholeh', 'keluarga-qurani', 'parenting'],
            ],
        ];

        foreach ($articlesData as $item) {
            $category = $categories->get($item['category_slug']);
            $tagSlugs = $item['tag_slugs'] ?? [];
            unset($item['category_slug'], $item['tag_slugs']);

            $item['user_id'] = $admin->id;
            $item['category_id'] = $category?->id;

            $article = Article::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );

            // Sync tags
            $tagIds = [];
            foreach ($tagSlugs as $tSlug) {
                if ($tag = $tags->get($tSlug)) {
                    $tagIds[] = $tag->id;
                }
            }
            if (! empty($tagIds)) {
                $article->tags()->sync($tagIds);
            }
        }
    }
}
