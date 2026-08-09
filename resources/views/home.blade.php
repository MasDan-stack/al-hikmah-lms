@extends('layouts.landing')

@section('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')

@section('content')
    <!-- HERO -->
    <section id="beranda" class="hero-section" aria-label="Hero">
        <div class="hero-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 hero-content">
                    <div class="hero-badge"><i class="bi bi-star-fill"></i> Bismillāhirraḥmānirraḥīm</div>
                    <h1 class="hero-title">Menumbuhkan Cinta kepada <span class="text-gradient">Al-Qur'an</span>,<br>Satu
                        Langkah Setiap Hari.</h1>
                    <p class="hero-subtitle">Kami percaya bahwa belajar Al-Qur'an adalah perjalanan untuk mengenal Allah,
                        membentuk adab, dan menumbuhkan akhlak yang baik. AL-HIKMAH hadir untuk mendampingi anak dan
                        keluarga dalam perjalanan tersebut.</p>
                    <p class="hero-subtitle" style="font-size: 0.85rem; opacity: 0.8;">Program utama untuk anak usia 10–15
                        tahun. Program tambahan tersedia untuk dewasa dan muslimah.</p>
                    <div class="hero-buttons">
                        <a href="#" class="btn btn-primary-custom btn-lg" data-bs-toggle="modal"
                            data-bs-target="#daftarModal">
                            <i class="bi bi-pencil-square me-2"></i>Mulai Perjalanan Belajar
                        </a>
                        <a href="{{ route('home') }}#tentang" class="btn btn-outline-custom btn-lg">
                            <i class="bi bi-info-circle me-2"></i>Kenali AL-HIKMAH
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image-wrapper">
                    <div class="hero-image-container">
                        <div class="hero-glow" aria-hidden="true"></div>
                        <img src="{{ asset('assets/img/1.jpg') }}" alt="Belajar Al-Qur'an bersama AL-HIKMAH"
                            class="hero-image" loading="eager">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LANDASAN -->
    <section id="landasan" class="section-padding section-alt text-center" aria-label="Landasan">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-reveal>
                    <div class="section-badge mx-auto"><i class="bi bi-book"></i> Landasan</div>
                    <h2 class="section-title">Sebaik-Baik Perjalanan Adalah<br>Perjalanan Bersama <span
                            class="text-gradient">Al-Qur'an</span></h2>
                    <div class="ayat-quote"
                        style="margin: 24px 0; padding: 24px; background: var(--card-bg); border-radius: var(--radius-lg); border-left: 4px solid var(--primary);">
                        <p style="font-size: 1.6rem; line-height: 2.2; direction: rtl;">خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ
                            وَعَلَّمَهُ</p>
                    </div>
                    <p style="font-style: italic; color: var(--text-secondary);">"Sebaik-baik kalian adalah orang yang
                        belajar Al-Qur'an dan mengajarkannya."</p>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">HR. Bukhari</p>
                </div>
            </div>
        </div>
    </section>

    <!-- MENGAPA HADIR -->
    <section id="tentang" class="section-padding" aria-label="Mengapa AL-HIKMAH Hadir">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-reveal>
                    <div class="about-image-wrapper">
                        <img src="assets/img/2.jpg" alt="Suasana belajar Al-Qur'an" class="about-image" loading="lazy">
                    </div>
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="200">
                    <div class="section-badge"><i class="bi bi-info-circle"></i> Tentang Kami</div>
                    <h2 class="section-title">Karena Anak-Anak Kita Tidak Hanya Perlu Diajar, Tetapi Juga <span
                            class="text-gradient">Didampingi.</span></h2>
                    <p>Di tengah kesibukan, tidak semua keluarga bisa mendampingi anak belajar Al-Qur'an secara
                        konsisten. Dari situlah AL-HIKMAH hadir — bukan sekadar mengajarkan membaca, tetapi menemani
                        setiap langkah kecil dalam perjalanan mengenal dan mencintai Al-Qur'an.</p>
                    <a href="{{ route('tentang-kami') }}" class="btn btn-outline-custom mt-3">Baca Selengkapnya <i
                            class="bi bi-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- FILOSOFI & NILAI -->
    <section id="filosofi" class="section-padding section-alt text-center" aria-label="Filosofi & Nilai-Nilai">
        <div class="container">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-lightbulb"></i> Filosofi & Nilai-Nilai</div>
            <h2 class="section-title" data-reveal>Apa Arti <span class="text-gradient">AL-HIKMAH</span> bagi Kami?</h2>
            <p class="section-description mx-auto" data-reveal>Al-Hikmah adalah ketika ilmu membimbing seseorang
                mengenal kebenaran, memperbaiki akhlak, dan menghadirkan kebaikan.</p>
            <div class="row g-4 mt-4">
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="why-card text-center">
                        <div class="why-icon"><i class="bi bi-book"></i></div>
                        <h4>Belajar dengan Ilmu</h4>
                        <p>Setiap huruf dipelajari dengan pemahaman, bukan sekadar pengulangan.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="why-card text-center">
                        <div class="why-icon"><i class="bi bi-person-lines-fill"></i></div>
                        <h4>Bertumbuh dengan Adab</h4>
                        <p>Ilmu yang baik melahirkan akhlak yang baik.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="300">
                    <div class="why-card text-center">
                        <div class="why-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <h4>Berjalan dengan Istiqamah</h4>
                        <p>Langkah kecil terus-menerus lebih berharga.</p>
                    </div>
                </div>
            </div>
            <div class="row g-3 mt-4 justify-content-center">
                <div class="col-6 col-md-2" data-reveal><span class="badge-nilai">Ikhlas</span></div>
                <div class="col-6 col-md-2" data-reveal data-reveal-delay="100"><span class="badge-nilai">Adab</span>
                </div>
                <div class="col-6 col-md-2" data-reveal data-reveal-delay="200"><span class="badge-nilai">Kasih
                        Sayang</span></div>
                <div class="col-6 col-md-2" data-reveal data-reveal-delay="300"><span class="badge-nilai">Amanah</span>
                </div>
                <div class="col-6 col-md-2" data-reveal data-reveal-delay="400"><span
                        class="badge-nilai">Istiqamah</span></div>
                <div class="col-6 col-md-2" data-reveal data-reveal-delay="500"><span
                        class="badge-nilai">Keteladanan</span></div>
            </div>
            <a href="tentang-kami.html" class="btn btn-outline-custom mt-4">Pelajari Nilai-Nilai Kami <i
                    class="bi bi-arrow-right ms-2"></i></a>
        </div>
    </section>

    <!-- HARAPAN -->
    <section id="harapan" class="section-padding" aria-label="Harapan">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-lightbulb"></i> Harapan Kami</div>
            <h2 class="section-title" data-reveal>Bukan Sekadar Bisa <span class="text-gradient">Membaca
                    Al-Qur'an</span></h2>
            <p class="section-description mx-auto" data-reveal>Kami ingin menumbuhkan kecintaan, adab, kebiasaan
                beribadah, dan kedekatan dengan Allah — agar Al-Qur'an hidup dalam keseharian.</p>
        </div>
    </section>

    <!-- PROGRAM -->
    <section id="program" class="section-padding section-alt" aria-label="Program">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-journal-bookmark"></i> Program</div>
            <h2 class="section-title" data-reveal>Perjalanan Belajar <span class="text-gradient">AL-HIKMAH</span></h2>
            <div class="row g-4 mt-4">
                <div class="col-md-4" data-reveal>
                    <div class="program-card text-center">
                        <div class="program-icon"><i class="bi bi-book-half"></i></div>
                        <h4>Iqra & Al-Qur'an</h4>
                        <p>Mengenal huruf hijaiyah hingga membaca Al-Qur'an.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="program-card text-center">
                        <div class="program-icon"><i class="bi bi-mic"></i></div>
                        <h4>Tahsin</h4>
                        <p>Memperbaiki bacaan sesuai kaidah tajwid.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="program-card text-center">
                        <div class="program-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                        <h4>Tahfidz</h4>
                        <p>Menghafal Al-Qur'an dengan murajaah.</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('program') }}" class="btn btn-outline-custom mt-4">Lihat Semua Program <i
                    class="bi bi-arrow-right ms-2"></i></a>
        </div>
    </section>

    <!-- METODE -->
    <section id="metode" class="section-padding" aria-label="Metode & Pendampingan">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-grid-3x3-gap-fill"></i> Cara Belajar</div>
            <h2 class="section-title" data-reveal>Belajar dengan Cara yang <span class="text-gradient">Lebih
                    Dekat</span></h2>
            <div class="row g-4 mt-4">
                <div class="col-md-4" data-reveal>
                    <div class="kelas-card text-center">
                        <div class="kelas-icon"><i class="bi bi-laptop"></i></div>
                        <h3>Online</h3>
                        <p>Belajar dari mana saja via Zoom/Meet.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="kelas-card featured-kelas text-center">
                        <div class="kelas-icon"><i class="bi bi-house-door"></i></div>
                        <h3>Offline</h3>
                        <p>Pendamping datang ke rumah Anda (Jabodetabek).</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="kelas-card text-center">
                        <div class="kelas-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <h3>Hybrid</h3>
                        <p>Kombinasi online & offline sesuai kebutuhan.</p>
                    </div>
                </div>
            </div>
            <a href="metode.html" class="btn btn-outline-custom mt-4">Detail Metode & Jadwal <i
                    class="bi bi-arrow-right ms-2"></i></a>
        </div>
    </section>

    <!-- TAHFIDZ -->
    <section id="tahfidz" class="section-padding section-alt tahfidz-section" aria-label="Tahfidz">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2" data-reveal>
                    <div class="section-badge"><i class="bi bi-clipboard2-pulse"></i> Program Unggulan</div>
                    <h2 class="section-title">Menghafal Bukan Sekadar <span class="text-gradient">Mengingat</span>,
                        Tetapi Menjaga</h2>
                    <p>Perjalanan menghafal Al-Qur'an membutuhkan kesabaran dan istiqamah. Kami mendampingi dengan
                        setoran rutin, murajaah, dan target yang disesuaikan.</p>
                    <a href="tahfidz.html" class="btn btn-primary-custom mt-3">Pelajari Program Tahfidz <i
                            class="bi bi-arrow-right ms-2"></i></a>
                </div>
                <div class="col-lg-6 order-lg-1 mb-4 mb-lg-0" data-reveal data-reveal-delay="200">
                    <img src="assets/img/62.jpg" alt="Program Tahfidz" class="tahfidz-image" loading="lazy"
                        onerror="this.src='https://placehold.co/600x500/0d7a3e/white?text=Tahfidz'">
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONI -->
    <section class="section-padding" aria-label="Testimoni">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-chat-quote"></i> Cerita dari Keluarga</div>
            <h2 class="section-title" data-reveal>Setiap Perubahan Kecil<br><span class="text-gradient">Adalah Sebuah
                    Kebahagiaan</span></h2>
            <div class="row g-4 mt-4">
                <div class="col-md-4" data-reveal>
                    <div class="testimonial-card">
                        <div class="testimonial-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i></div>
                        <p class="testimonial-text">"Anak saya lebih semangat belajar Al-Qur'an. Pendekatannya sabar dan
                            membuat anak nyaman."</p>
                        <div class="testimonial-author">
                            <div class="author-avatar"><i class="bi bi-person-circle"></i></div>
                            <div class="author-info">
                                <h6>Orang Tua Murid</h6><span>Program Anak</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="testimonial-card">
                        <div class="testimonial-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i></div>
                        <p class="testimonial-text">"Saya belajar dari nol di usia dewasa. Pendampingnya sabar, tidak
                            pernah membuat saya malu."</p>
                        <div class="testimonial-author">
                            <div class="author-avatar"><i class="bi bi-person-circle"></i></div>
                            <div class="author-info">
                                <h6>Peserta Dewasa</h6><span>Program Tahsin</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="testimonial-card">
                        <div class="testimonial-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                class="bi bi-star-fill"></i></div>
                        <p class="testimonial-text">"Jadwal fleksibel, pendamping profesional. Anak saya lebih disiplin
                            sekarang."</p>
                        <div class="testimonial-author">
                            <div class="author-avatar"><i class="bi bi-person-circle"></i></div>
                            <div class="author-info">
                                <h6>Orang Tua Murid</h6><span>Home Visit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p style="font-size: 0.85rem; color: var(--text-muted); font-style: italic; margin-top: 20px;">Testimoni di
                atas adalah representasi dari pengalaman belajar yang ingin kami tumbuhkan.</p>
        </div>
    </section>

    <!-- CTA -->
    <section id="kontak" class="cta-section text-center" aria-label="CTA">
        <div class="cta-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="cta-content" data-reveal>
                <div class="cta-icon"><i class="bi bi-book"></i></div>
                <h2 class="cta-title">Mari Menanam Kebaikan<br><span class="text-gradient-light">Sejak Hari Ini</span>
                </h2>
                <p class="cta-subtitle">Dari satu huruf, satu ayat, satu doa — perjalanan besar dimulai.</p>
                <div class="cta-buttons">
                    <a href="#" class="btn btn-primary-custom btn-lg" data-bs-toggle="modal"
                        data-bs-target="#daftarModal"><i class="bi bi-pencil-square me-2"></i>Mulai Perjalanan</a>
                    <a href="https://wa.me/6285786689008" class="btn btn-outline-light-custom btn-lg" target="_blank"><i
                            class="bi bi-whatsapp me-2"></i>Berbincang dengan Kami</a>
                </div>
            </div>
        </div>
    </section>
