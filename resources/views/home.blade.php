@extends('layouts.landing')

@section('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')

@section('content')
    <!-- ============================================ -->
    <!-- 1. ETRAIN HERO BANNER SECTION -->
    <!-- ============================================ -->
    <section id="beranda" class="banner_part" aria-label="Hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-xl-6">
                    <div class="banner_text">
                        <div class="banner_text_iner">
                            <h5><i class="bi bi-star-fill text-warning me-1"></i> Bismillāhirraḥmānirraḥīm</h5>
                            <h1>Menumbuhkan Cinta kepada <span class="text-gradient">Al-Qur'an</span>, Satu Langkah Setiap
                                Hari.</h1>
                            <p>Kami percaya bahwa belajar Al-Qur'an adalah perjalanan untuk mengenal Allah, membentuk adab,
                                dan menumbuhkan akhlak yang mulia. AL-HIKMAH hadir untuk mendampingi buah hati dan keluarga
                                dalam setiap prosesnya.</p>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="#" class="btn_1" data-bs-toggle="modal" data-bs-target="#daftarModal">
                                    <i class="bi bi-pencil-square"></i> Mulai Belajar
                                </a>
                                <a href="{{ route('tentang-kami') }}" class="btn_2">
                                    <i class="bi bi-info-circle"></i> Kenali Kami
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-6 text-center mt-5 mt-lg-0">
                    <div class="banner_img">
                        <img src="{{ asset('assets/img/1.jpg') }}" alt="Belajar Al-Qur'an bersama AL-HIKMAH"
                            onerror="this.src='{{ asset('assets/img/1.jpg') }}'" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. JADWAL SHOLAT REAL-TIME KEMENAG RI -->
    <!-- ============================================ -->
    <section id="jadwal-sholat" class="prayer-section py-5" aria-label="Jadwal Sholat Real-Time">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8 text-center" data-reveal>
                    <div class="section-badge mx-auto"><i class="bi bi-clock-history"></i> Waktu Ibadah Harian</div>
                    <h2 class="section-title">Jadwal Sholat &amp; Imsakiyah <span class="text-gradient">Real-Time</span>
                    </h2>
                    <p class="section-description mx-auto">Pantau waktu sholat fardhu secara akurat standar Kementerian
                        Agama RI dengan deteksi lokasi otomatis dan hitung mundur presisi.</p>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-11" data-reveal data-reveal-delay="100">
                    <div class="prayer-box">
                        <!-- Top Bar: Location, Hijri Date, Action Controls -->
                        <div class="prayer-header-bar">
                            <div class="prayer-location-info">
                                <div class="prayer-city-badge">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span id="prayer-city-name">DKI Jakarta</span>
                                    <span class="gps-live-dot" id="gps-status-dot" style="display: none;"
                                        title="GPS Real-Time Aktif">
                                        <span class="pulse"></span>
                                        <span class="dot"></span>
                                    </span>
                                </div>
                                <div class="prayer-date-badge" id="prayer-date-display">
                                    <i class="bi bi-calendar3"></i> Memuat tanggal...
                                </div>
                            </div>

                            <div class="prayer-controls">
                                <button type="button" class="btn-prayer-ctrl" id="btn-detect-gps"
                                    title="Deteksi Lokasi GPS Anda">
                                    <i class="bi bi-crosshair"></i> Deteksi GPS
                                </button>
                                <button type="button" class="btn-prayer-ctrl" data-bs-toggle="modal"
                                    data-bs-target="#cityModal" title="Pilih Kota / Kabupaten">
                                    <i class="bi bi-buildings"></i> Pilih Kota
                                </button>
                                <button type="button" class="btn-prayer-ctrl" data-bs-toggle="modal"
                                    data-bs-target="#qiblaModal" title="Cek Arah Kiblat">
                                    <i class="bi bi-compass"></i> Arah Kiblat
                                </button>
                                <button type="button" class="btn-prayer-ctrl" id="btn-prayer-sound"
                                    title="Aktifkan / Matikan Notifikasi Suara Waktu Sholat">
                                    <i class="bi bi-bell-slash"></i> Suara: Mati
                                </button>
                            </div>
                        </div>

                        <!-- Hero Banner: Countdown to Next Prayer & Live Digital Clock -->
                        <div class="prayer-hero-banner">
                            <div class="prayer-countdown-content">
                                <div class="prayer-countdown-tag">
                                    <i class="bi bi-hourglass-split"></i> MENUJU WAKTU <span
                                        id="next-prayer-name">...</span>
                                </div>
                                <div class="prayer-countdown-timer" id="prayer-countdown-timer">--:--:--</div>
                                <div class="prayer-countdown-target" id="next-prayer-time-target">Memperbarui jadwal
                                    sholat...</div>
                            </div>

                            <div class="prayer-live-clock">
                                <div class="live-digital-clock" id="live-digital-clock">--:--:--</div>
                                <span class="live-timezone-badge" id="live-timezone-badge">WIB</span>
                            </div>
                        </div>

                        <!-- 8 Prayer Times Cards Grid -->
                        <div class="prayer-grid" id="prayer-cards-grid">
                            <div class="prayer-card">
                                <div class="prayer-card-name">Imsak</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Subuh</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Terbit</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Dhuha</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Dzuhur</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Ashar</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Maghrib</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Isya</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                        </div>

                        <!-- Footer source info -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 text-muted"
                            style="font-size: 0.75rem; border-top: 1px dashed var(--border-color);">
                            <span><i class="bi bi-shield-check text-success me-1"></i>Standar Perhitungan Bimas Islam
                                Kemenag RI</span>
                            <span class="d-none d-sm-inline">Metode: Kementerian Agama RI</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. ETRAIN FEATURE PART (4 PILAR KEUNGGULAN) -->
    <!-- ============================================ -->
    <section class="feature_part" aria-label="Keunggulan AL-HIKMAH">
        <div class="container">
            <div class="row g-4">
                <div class="col-sm-6 col-xl-3 align-self-center" data-reveal>
                    <div class="single_feature_text">
                        <div class="section-badge mb-2"><i class="bi bi-award"></i> Keunggulan</div>
                        <h2 class="fw-bold mb-3">Kenapa Memilih <span class="text-gradient">AL-HIKMAH?</span></h2>
                        <p class="text-secondary mb-4">Mendampingi anak dengan keteladanan, kesabaran, dan kurikulum yang
                            ramah anak.</p>
                        <a href="{{ route('tentang-kami') }}" class="btn_1">Pelajari Nilai Kami</a>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3" data-reveal data-reveal-delay="100">
                    <div class="single_feature">
                        <div class="single_feature_icon">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <h4>Guru Berpengalaman</h4>
                        <p>Guru dan pendamping tersertifikasi, berakhlak baik, serta memiliki kemampuan mengajar yang sabar
                            dan menyenangkan.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3" data-reveal data-reveal-delay="200">
                    <div class="single_feature">
                        <div class="single_feature_icon">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <h4>Metode Personal (1-on-1)</h4>
                        <p>Setiap santri memiliki kecepatan belajar yang berbeda. Pendampingan disesuaikan dengan kebutuhan
                            dan karakter anak.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3" data-reveal data-reveal-delay="300">
                    <div class="single_feature">
                        <div class="single_feature_icon">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h4>Laporan &amp; Evaluasi Rutin</h4>
                        <p>Orang tua dapat memantau capaian hafalan, tajwid, dan catatan adab santri melalui portal
                            pemantauan yang transparan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. ETRAIN LEARNING PART (TENTANG KAMI) -->
    <!-- ============================================ -->
    <section class="learning_part" aria-label="Tentang AL-HIKMAH">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-6 mb-4 mb-lg-0" data-reveal>
                    <div class="learning_img">
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Suasana belajar Al-Qur'an"
                            onerror="this.src='{{ asset('assets/img/2.jpg') }}'">
                    </div>
                </div>
                <div class="col-md-6 col-lg-6" data-reveal data-reveal-delay="150">
                    <div class="ps-lg-4">
                        <div class="section-badge mb-2"><i class="bi bi-info-circle"></i> Tentang Kami</div>
                        <h2 class="section-title text-start mb-3">Karena Anak-Anak Kita Perlu <span
                                class="text-gradient">Didampingi</span> dengan Penuh Kasih.</h2>
                        <p class="text-secondary mb-4">Di tengah kesibukan kehidupan, tidak semua keluarga memiliki waktu
                            untuk mendampingi anak secara intensif. AL-HIKMAH hadir untuk menjadi partner terbaik orang tua
                            dalam membimbing generasi Qur'ani.</p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                                    <i class="bi bi-check2-circle fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Belajar dengan Pemahaman &amp; Tajwid</h6>
                                    <p class="small text-secondary mb-0">Bukan sekadar mengejar target halaman, tetapi
                                        memastikan makharijul huruf tepat.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                                    <i class="bi bi-check2-circle fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Penanaman Adab &amp; Nilai Akhlak</h6>
                                    <p class="small text-secondary mb-0">Membiasakan doa harian, adab terhadap orang tua,
                                        serta kecintaan beribadah.</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('tentang-kami') }}" class="btn_1">
                            <i class="bi bi-arrow-right"></i> Baca Kisah Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5. ETRAIN MEMBER COUNTER -->
    <!-- ============================================ -->
    <section class="member_counter" aria-label="Statistik Lembaga">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6" data-reveal>
                    <div class="single_member_counter">
                        <span>150+</span>
                        <h4>Santri Aktif</h4>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6" data-reveal data-reveal-delay="100">
                    <div class="single_member_counter">
                        <span>25+</span>
                        <h4>Guru &amp; Pendamping</h4>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6" data-reveal data-reveal-delay="200">
                    <div class="single_member_counter">
                        <span>1,200+</span>
                        <h4>Jam Pembelajaran</h4>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6" data-reveal data-reveal-delay="300">
                    <div class="single_member_counter">
                        <span>98%</span>
                        <h4>Tingkat Kepuasan Wali</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 6. PROGRAM BELAJAR (NETLIFY / AL-HIKMAH) -->
    <!-- ============================================ -->
    <section id="program" class="py-5" aria-label="Program Belajar">
        <div class="container text-center">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-7 text-center" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-journal-bookmark"></i> Program</div>
                    <h2 class="section-title">Perjalanan Belajar <span class="text-gradient">AL-HIKMAH</span></h2>
                    <p class="section-description mx-auto">Dirancang bertahap sesuai usia dan tingkat kemampuan membaca
                        Al-Qur'an.</p>
                </div>
            </div>

            <div class="row g-4 text-start">
                <div class="col-md-4" data-reveal>
                    <div class="program-card text-center h-100">
                        <div class="program-icon mx-auto"><i class="bi bi-book-half"></i></div>
                        <h4>Iqra &amp; Al-Qur'an</h4>
                        <p>Mengenal huruf hijaiyah hingga membaca Al-Qur'an secara bertahap dengan metode yang menyenangkan.
                        </p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="program-card text-center h-100">
                        <div class="program-icon mx-auto"><i class="bi bi-mic"></i></div>
                        <h4>Tahsin</h4>
                        <p>Memperbaiki bacaan agar fasih dan tartil sesuai kaidah tajwid serta makharijul huruf.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="program-card text-center h-100">
                        <div class="program-icon mx-auto"><i class="bi bi-clipboard2-pulse"></i></div>
                        <h4>Tahfidz</h4>
                        <p>Mendampingi hafalan Al-Qur'an dengan murajaah teratur dan bimbingan mutqin.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-reveal>
                <a href="{{ route('program') }}" class="btn_1">
                    <i class="bi bi-grid-fill me-1"></i> Lihat Semua Program <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 7. ETRAIN ADVANCE EDUCATOR LEARNING SYSTEM -->
    <!-- ============================================ -->
    <section class="learning_part advance_feature" aria-label="Sistem Belajar Modern">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 order-lg-1 order-2" data-reveal>
                    <div class="pe-lg-4">
                        <div class="section-badge mb-2"><i class="bi bi-laptop"></i> Fleksibilitas Belajar</div>
                        <h2 class="section-title text-start mb-3">Sistem Pendampingan Belajar yang <span
                                class="text-gradient">Fleksibel &amp; Nyaman</span></h2>
                        <p class="text-secondary mb-4">Kami menyediakan tiga pilihan metode pembelajaran yang dapat
                            disesuaikan dengan kenyamanan keluarga Anda di rumah maupun secara daring.</p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 bg-white rounded-4 shadow-sm border h-100">
                                    <div class="fs-3 text-success mb-2"><i class="bi bi-house-door-fill"></i></div>
                                    <h5 class="fw-bold fs-6 mb-1">Offline (Home Visit)</h5>
                                    <p class="small text-secondary mb-0">Guru datang langsung ke rumah Anda di wilayah
                                        Jabodetabek.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 bg-white rounded-4 shadow-sm border h-100">
                                    <div class="fs-3 text-primary mb-2"><i class="bi bi-camera-video-fill"></i></div>
                                    <h5 class="fw-bold fs-6 mb-1">Online Interactive</h5>
                                    <p class="small text-secondary mb-0">Sesi tatap muka virtual melalui Zoom/Google Meet
                                        dengan jadwal fleksibel.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('metode') }}" class="btn_2">
                                <i class="bi bi-info-circle"></i> Pelajari Metode Lengkap
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 order-lg-2 order-1 mb-4 mb-lg-0 text-center" data-reveal
                    data-reveal-delay="150">
                    <div class="learning_img">
                        <img src="{{ asset('assets/img/etrain/advance_feature_img.png') }}"
                            alt="Sistem Pembelajaran Al-Hikmah" onerror="this.src='{{ asset('assets/img/5.jpg') }}'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 8. ETRAIN TESTIMONIALS SECTION -->
    <!-- ============================================ -->
    <section class="testimonial_part" aria-label="Testimoni Keluarga">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-7 text-center" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-chat-quote"></i> Cerita Keluarga</div>
                    <h2 class="section-title">Apa Kata <span class="text-gradient">Orang Tua Santri?</span></h2>
                    <p class="section-description mx-auto">Pengalaman nyata para wali santri yang telah mempercayakan
                        bimbingan mengaji bersama AL-HIKMAH.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="testimonial_part_iner h-100">
                        <div class="testimonial_part_text">
                            <div class="text-warning fs-5 mb-3">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i>
                            </div>
                            <p>"Alhamdulillah anak saya sekarang lebih bersemangat setiap jam mengaji tiba. Pendampingnya
                                sangat sabar dan mampu membangun chemistry yang menyenangkan."</p>
                            <h4>Bunda Aisyah</h4>
                            <h5>Wali Santri Program Iqra (Jakarta Selatan)</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="testimonial_part_iner h-100">
                        <div class="testimonial_part_text">
                            <div class="text-warning fs-5 mb-3">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i>
                            </div>
                            <p>"Laporan perkembangan di website sangat memudahkan saya memantau hafalan anak meskipun saya
                                bekerja di kantor. Sangat transparan dan profesional."</p>
                            <h4>Ayah Hendra</h4>
                            <h5>Wali Santri Program Tahfidz (Depok)</h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="testimonial_part_iner h-100">
                        <div class="testimonial_part_text">
                            <div class="text-warning fs-5 mb-3">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i>
                            </div>
                            <p>"Jadwalnya sangat fleksibel, dan gurunya selalu tepat waktu. Metode tahsin yang diajarkan
                                sangat mudah dipahami oleh anak-anak usia remaja."</p>
                            <h4>Bunda Fatimah</h4>
                            <h5>Wali Santri Program Tahsin (Tangerang Selatan)</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 9. ETRAIN BLOG & EDUKASI ISLAMI -->
    <!-- ============================================ -->
    @php
        $latestArticles = (isset($latestArticles) && $latestArticles->count() > 0)
            ? $latestArticles
            : \App\Models\Article::published()->with(['category', 'user'])->latest('published_at')->take(3)->get();
    @endphp
    <section class="blog_part py-5" aria-label="Blog & Edukasi Qur'ani">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-7 text-center" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-journal-richtext"></i> Wawasan &amp; Edukasi</div>
                    <h2 class="section-title">Blog &amp; Artikel <span class="text-gradient">Terbaru</span></h2>
                    <p class="section-description mx-auto">Panduan belajar Al-Qur'an, tips mendampingi anak mengaji di rumah, metode tahsin/tahfidz, dan wawasan keislaman terkini.</p>
                </div>
            </div>

            <div class="row g-4">
                @if(isset($latestArticles) && $latestArticles->count() > 0)
                    @foreach($latestArticles as $index => $article)
                        <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                            <div class="single-home-blog h-100">
                                <div class="card h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="blog-card-img-wrap">
                                            <img src="{{ $article->cover_url }}" class="card-img-top" alt="{{ $article->title }}"
                                                 onerror="this.src='{{ asset('assets/img/' . (($index % 3) + 1) . '.jpg') }}'">
                                            <span class="blog-date-badge">
                                                <i class="bi bi-calendar3 me-1"></i> {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
                                            </span>
                                        </div>
                                        <div class="card-body p-4">
                                            @if($article->category)
                                                <a href="{{ route('blog.category', $article->category->slug) }}" class="btn_category_pill mb-3">
                                                    <i class="bi {{ $article->category->icon ?? 'bi-bookmark-check' }}"></i> {{ $article->category->name }}
                                                </a>
                                            @endif
                                            <h5 class="card-title">
                                                <a href="{{ route('blog.show', $article->slug) }}">
                                                    {{ Str::limit($article->title, 56) }}
                                                </a>
                                            </h5>
                                            <p class="card-text text-secondary small mb-3">
                                                {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 110) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent px-4 pb-4 pt-0 border-0">
                                        <ul class="blog-meta-list list-unstyled d-flex justify-content-between align-items-center m-0 pt-3 border-top small text-muted" style="border-color: var(--border-color) !important;">
                                            <li><i class="bi bi-clock me-1 text-success"></i> {{ $article->reading_time_label }}</li>
                                            <li><i class="bi bi-eye me-1 text-success"></i> {{ number_format($article->views_count) }} Pembaca</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <div class="p-4 bg-white rounded-4 border d-inline-block text-muted" style="border-color: var(--border-color) !important;">
                            <i class="bi bi-journal-text fs-1 text-success mb-2 d-block"></i>
                            <p class="mb-0">Artikel edukasi terbaru akan segera hadir.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="text-center mt-5" data-reveal>
                <a href="{{ route('blog.index') }}" class="btn_1">
                    <i class="bi bi-grid-fill me-1"></i> Jelajahi Semua Artikel <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 10. CTA SECTION -->
    <!-- ============================================ -->
    <section id="kontak" class="cta-section text-center" aria-label="CTA">
        <div class="cta-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="cta-content" data-reveal>
                <div class="cta-icon"><i class="bi bi-book"></i></div>
                <h2 class="cta-title">Mari Menanam Kebaikan<br><span class="text-gradient-light">Sejak Hari Ini</span>
                </h2>
                <p class="cta-subtitle">Dari satu huruf, satu ayat, satu doa — perjalanan besar menuju generasi Qur'ani
                    dimulai bersama AL-HIKMAH.</p>
                <div class="d-flex justify-content-center flex-wrap gap-3 mt-4">
                    <a href="#" class="btn_1" data-bs-toggle="modal" data-bs-target="#daftarModal">
                        <i class="bi bi-pencil-square"></i> Mulai Perjalanan Belajar
                    </a>
                    <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program belajar AL-HIKMAH') }}"
                        class="btn_2 text-white border-white" target="_blank" rel="noopener">
                        <i class="bi bi-whatsapp"></i> Konsultasi via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- MODALS: PILIH KOTA & ARAH KIBLAT -->
    <!-- ============================================ -->
    <div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"
                style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title font-display fw-bold" id="cityModalLabel">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>Pilih Kota / Kabupaten
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="city-search-input-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" id="city-search-input" class="city-search-input"
                            placeholder="Cari nama kota/kabupaten di Indonesia..." autocomplete="off">
                    </div>

                    <div class="city-quick-pills">
                        <button type="button" class="city-quick-pill active" data-region="all">Semua Wilayah</button>
                        <button type="button" class="city-quick-pill" data-region="Jabodetabek">Jabodetabek</button>
                        <button type="button" class="city-quick-pill" data-region="Jawa">Jawa</button>
                        <button type="button" class="city-quick-pill" data-region="Sumatera">Sumatera</button>
                        <button type="button" class="city-quick-pill" data-region="Kalimantan">Kalimantan</button>
                        <button type="button" class="city-quick-pill" data-region="Sulawesi">Sulawesi</button>
                        <button type="button" class="city-quick-pill" data-region="Bali/Nusa">Bali &amp; Nusa
                            Tenggara</button>
                        <button type="button" class="city-quick-pill" data-region="Maluku/Papua">Maluku &amp;
                            Papua</button>
                    </div>

                    <div class="city-list-container" id="city-list-container">
                        <!-- Populated by PrayerTimesApp.renderCityList() -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qiblaModal" tabindex="-1" aria-labelledby="qiblaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title font-display fw-bold" id="qiblaModalLabel">
                        <i class="bi bi-compass text-primary me-2"></i>Kompas Arah Kiblat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="qibla-compass-container">
                        <div class="qibla-dial-wrapper">
                            <span class="compass-cardinal n">U</span>
                            <span class="compass-cardinal s">S</span>
                            <span class="compass-cardinal e">T</span>
                            <span class="compass-cardinal w">B</span>
                            <div class="qibla-needle" id="qibla-needle-pointer">
                                <div class="qibla-needle-center"></div>
                            </div>
                        </div>
                        <div class="qibla-degree-display" id="qibla-degree-val">295.2°</div>
                        <p class="qibla-desc-display mb-0" id="qibla-desc-val">Menghitung arah Ka'bah dari lokasi Anda...
                        </p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 1px solid var(--border-color);">
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Sudut diukur searah jarum jam dari
                        arah Utara Geografis (0°).</small>
                </div>
            </div>
        </div>
    </div>
@endsection
