@extends('layouts.landing')

@section('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')
@section('meta_description', 'LMS Al-Hikmah: Platform bimbingan dan pembelajaran Al-Qur\'an privat dan kelompok dengan
    metode personal, jadwal fleksibel, dan guru terpercaya.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. ETRAIN HERO BANNER SECTION -->
    <!-- ============================================ -->
    <!-- ============================================ -->
    <!-- 1. EDITORIAL HERO SECTION (2026 MODERN MINIMALIST) -->
    <!-- ============================================ -->
    <section id="beranda" class="editorial-hero-wrapper" aria-label="Hero Bimbingan Al-Qur'an">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 col-xl-6">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        <div class="editorial-trust-pill">
                            <i class="bi bi-star-fill text-warning" aria-hidden="true"></i>
                            <span class="fw-semibold text-heading">4.9/5</span>
                            <span class="text-muted">|</span>
                            <span class="small fw-medium">Dipercaya 250+ Keluarga Santri</span>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold">
                            <i class="bi bi-gift me-1" aria-hidden="true"></i> Pendaftaran Santri Baru • 100% Bebas Biaya
                        </span>
                    </div>

                    <h1 class="editorial-title display-5 mb-3">
                        Mau Ajarkan Anak Ngaji Sendiri tapi <span class="text-emerald-deep">Sering Kehabisan Sabar</span> setelah Lelah Bekerja?
                    </h1>

                    <p class="editorial-subtitle mb-4">
                        Serahkan pada guru privat Al-Hikmah. Melalui 90 menit bimbingan sabar 1-on-1 bersama ustadz dan ustadzah pilihan, ananda disimak penuh tanpa terburu-buru, dan Ayah Bunda tinggal memantau perkembangan hafalan ananda langsung lewat HP.
                    </p>

                    <!-- Trust Badges Row -->
                    <div class="d-flex flex-wrap gap-4 my-4 py-3 border-top border-bottom"
                        style="border-color: var(--border-color) !important;">
                        <div class="d-flex align-items-center gap-2 text-secondary small fw-medium">
                            <i class="bi bi-clock-history text-success fs-5" aria-hidden="true"></i>
                            <span>Durasi 90 Menit Penuh</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-secondary small fw-medium">
                            <i class="bi bi-shield-check text-success fs-5" aria-hidden="true"></i>
                            <span>Garansi Kesesuaian Guru</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-secondary small fw-medium">
                            <i class="bi bi-journal-check text-success fs-5" aria-hidden="true"></i>
                            <span>Rapor Mutaba'ah Setiap Sesi</span>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-editorial-primary">
                                <i class="bi bi-speedometer2" aria-hidden="true"></i> Masuk Dashboard ({{ auth()->user()->role?->label ?? 'Akun Saya' }})
                            </a>
                        @else
                            <button type="button" class="btn-editorial-primary" data-bs-toggle="modal"
                                data-bs-target="#trialModal">
                                <i class="bi bi-pencil-square" aria-hidden="true"></i> Daftar Evaluasi Awal Tanpa Biaya
                            </button>
                            <a href="{{ wa_url('Assalamualaikum Admin Al-Hikmah, saya ingin konsultasi mengenai bimbingan Al-Qur\'an untuk ananda.') }}"
                                target="_blank" rel="noopener"
                                class="btn-editorial-whatsapp">
                                <i class="bi bi-whatsapp" aria-hidden="true"></i> Konsultasi via WhatsApp
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-5 col-xl-6 text-center">
                    <div class="editorial-hero-frame position-relative">
                        <img src="{{ asset('assets/img/1.jpg') }}" alt="Belajar Al-Qur'an bersama AL-HIKMAH" width="600"
                            height="420" fetchpriority="high" onerror="this.src='{{ asset('assets/img/1.jpg') }}'"
                            class="img-fluid">

                        <!-- Floating Badge Kiri Bawah: 90 Menit Fokus Penuh -->
                        <div class="position-absolute bottom-0 start-0 m-3 editorial-floating-badge text-start d-none d-sm-flex align-items-center gap-3"
                            style="max-width: 300px;">
                            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 42px; height: 42px;">
                                <i class="bi bi-clock-fill fs-5" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-heading">90 Menit Fokus Penuh</div>
                                <div class="text-secondary" style="font-size: 0.76rem; line-height: 1.35;">Disimak penuh tanpa antre, 3x lebih cepat lancar dan mutqin.</div>
                            </div>
                        </div>

                        <!-- Floating Badge Kanan Atas: Rapor Mutaba'ah Terpantau Rutin -->
                        <div class="position-absolute top-0 end-0 m-3 editorial-floating-badge text-start d-none d-md-flex align-items-center gap-2.5"
                            style="max-width: 260px;">
                            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 38px; height: 38px; background: var(--primary-lighter); color: var(--primary);">
                                <i class="bi bi-patch-check-fill fs-5" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-heading">Rapor Mutaba'ah Rutin</div>
                                <div class="text-secondary" style="font-size: 0.72rem; line-height: 1.3;">Setiap sesi terpantau orang tua</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. KEKHAWATIRAN ORANG TUA VS PENDEKATAN AL-HIKMAH (ISLAMIC EDITORIAL MINIMALIST) -->
    <!-- ============================================ -->
    <section class="section-editorial-spacing section-parent-concerns border-top border-bottom"
        aria-label="Kekhawatiran Orang Tua dan Solusi Al-Hikmah">
        <div class="container position-relative" style="z-index: 1;">
            <div class="row justify-content-center mb-5 text-center">
                <div class="col-lg-8" data-reveal>
                    <h2 class="editorial-title fs-2 mb-3">Memahami Kekhawatiran <span class="text-emerald-deep">Orang Tua</span></h2>
                    <p class="editorial-subtitle mx-auto text-secondary" style="max-width: 680px;">
                        Setiap anak memiliki ritme dan keunikan masing-masing. Di AL-HIKMAH, kami mendengarkan apa yang sering membuat orang tua cemas dan menghadirkan bimbingan yang menenangkan.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Card 1: Ritme & Suasana Belajar -->
                <div class="col-lg-6" data-reveal>
                    <div class="empathy-card">
                        <div class="empathy-card-top">
                            <div class="empathy-meta">
                                <span class="empathy-num">01</span>
                                <span class="empathy-tag">
                                    <i class="bi bi-heart-pulse-fill" aria-hidden="true"></i>
                                    <span>Kekhawatiran Umum</span>
                                </span>
                            </div>
                            <h3 class="empathy-concern-title">
                                &ldquo;Anak cepat jenuh, mogok, atau merasa tertekan saat diminta mengaji sore hari.&rdquo;
                            </h3>
                            <p class="empathy-concern-context">
                                Beban akademik sekolah sering menguras tenaga anak. Metode klasikal yang tergesa-gesa atau bernada tinggi rentan memicu trauma belajar dan keengganan membuka mushaf.
                            </p>
                        </div>

                        <div class="empathy-solution-panel">
                            <div class="empathy-solution-badge">
                                <span class="empathy-solution-icon">
                                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                                </span>
                                <span>Pendekatan Menenangkan Al-Hikmah</span>
                            </div>
                            <h4 class="empathy-solution-lead">Bimbingan Talaqqi 1-on-1 Penuh Kasih &amp; Apresiasi</h4>
                            <p class="empathy-solution-text">
                                Pendampingan privat 90 menit tanpa antre. Guru membimbing dengan sabar murni, menghargai suasana hati ananda, dan memotivasi lewat apresiasi tulus agar rasa cinta pada kalamullah bersemi secara alami.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Transparansi untuk Orang Tua Bekerja -->
                <div class="col-lg-6" data-reveal data-reveal-delay="100">
                    <div class="empathy-card">
                        <div class="empathy-card-top">
                            <div class="empathy-meta">
                                <span class="empathy-num">02</span>
                                <span class="empathy-tag">
                                    <i class="bi bi-heart-pulse-fill" aria-hidden="true"></i>
                                    <span>Kekhawatiran Umum</span>
                                </span>
                            </div>
                            <h3 class="empathy-concern-title">
                                &ldquo;Orang tua sibuk bekerja, sulit memantau apakah tajwid dan hafalan ananda benar-benar berkembang.&rdquo;
                            </h3>
                            <p class="empathy-concern-context">
                                Keterbatasan waktu di rumah kerap menimbulkan rasa bersalah dan ketidakpastian mengenai perkembangan makhraj, capaian juz, serta materi murajaah yang perlu diulang.
                            </p>
                        </div>

                        <div class="empathy-solution-panel">
                            <div class="empathy-solution-badge">
                                <span class="empathy-solution-icon">
                                    <i class="bi bi-journal-check" aria-hidden="true"></i>
                                </span>
                                <span>Pendekatan Menenangkan Al-Hikmah</span>
                            </div>
                            <h4 class="empathy-solution-lead">Jurnal Mutaba'ah Digital Real-Time Pasca Sesi</h4>
                            <p class="empathy-solution-text">
                                Setiap sesi selesai, guru menginput capaian makharijul huruf, halaman, ayat, dan evaluasi adab ke portal LMS. Ayah &amp; Bunda dapat memantau rapor mutaba'ah langsung dari smartphone kapan saja.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Kecocokan Karakter Guru -->
                <div class="col-lg-6" data-reveal data-reveal-delay="200">
                    <div class="empathy-card">
                        <div class="empathy-card-top">
                            <div class="empathy-meta">
                                <span class="empathy-num">03</span>
                                <span class="empathy-tag">
                                    <i class="bi bi-heart-pulse-fill" aria-hidden="true"></i>
                                    <span>Kekhawatiran Umum</span>
                                </span>
                            </div>
                            <h3 class="empathy-concern-title">
                                &ldquo;Khawatir karakter anak tidak cocok dengan gaya mengajar ustadz atau ustadzah pembimbing.&rdquo;
                            </h3>
                            <p class="empathy-concern-context">
                                Setiap anak memiliki tipe kepribadian berbeda, ada yang pemalu, sensitif, maupun sangat aktif. Guru yang kurang tepat cara komunikasinya bisa membuat anak menutup diri.
                            </p>
                        </div>

                        <div class="empathy-solution-panel">
                            <div class="empathy-solution-badge">
                                <span class="empathy-solution-icon">
                                    <i class="bi bi-person-heart" aria-hidden="true"></i>
                                </span>
                                <span>Pendekatan Menenangkan Al-Hikmah</span>
                            </div>
                            <h4 class="empathy-solution-lead">Garansi 100% Kesesuaian Guru &amp; Profiling Personal</h4>
                            <p class="empathy-solution-text">
                                Kami memetakan karakter anak dengan profil pedagogis pengajar. Orang tua memiliki hak konsultasi dan pengajuan penggantian guru pendamping kapan pun hingga ananda merasa benar-benar nyaman dan akrab.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Fleksibilitas Jadwal -->
                <div class="col-lg-6" data-reveal data-reveal-delay="300">
                    <div class="empathy-card">
                        <div class="empathy-card-top">
                            <div class="empathy-meta">
                                <span class="empathy-num">04</span>
                                <span class="empathy-tag">
                                    <i class="bi bi-heart-pulse-fill" aria-hidden="true"></i>
                                    <span>Kekhawatiran Umum</span>
                                </span>
                            </div>
                            <h3 class="empathy-concern-title">
                                &ldquo;Jadwal harian anak sudah sangat padat dan sering bentrok dengan kegiatan sekolah atau les akademik.&rdquo;
                            </h3>
                            <p class="empathy-concern-context">
                                Memaksa anak bepergian di jam macet untuk mengaji sering kali menyita energi keluarga dan memicu kelelahan berlebih pada anak sebelum proses belajar dimulai.
                            </p>
                        </div>

                        <div class="empathy-solution-panel">
                            <div class="empathy-solution-badge">
                                <span class="empathy-solution-icon">
                                    <i class="bi bi-house-heart" aria-hidden="true"></i>
                                </span>
                                <span>Pendekatan Menenangkan Al-Hikmah</span>
                            </div>
                            <h4 class="empathy-solution-lead">Pilihan Guru Datang ke Rumah (Home Visit) &amp; Waktu Fleksibel</h4>
                            <p class="empathy-solution-text">
                                Leluasa memilih slot jadwal (Pagi, Sore, atau Ba'da Maghrib/Isya). Tersedia pilihan guru privat hadir langsung ke rumah (*Offline Home Visit*) maupun kelas *Online Live 1-on-1* interaktif.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Reassurance Trust Ribbon -->
            <div class="empathy-trust-ribbon" data-reveal>
                <div class="d-flex align-items-center gap-3">
                    <div class="empathy-ribbon-icon">
                        <i class="bi bi-chat-heart-fill" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h5 class="empathy-ribbon-title">Setiap keluarga memiliki cerita dan kebutuhan yang unik.</h5>
                        <p class="empathy-ribbon-desc">Ingin mendiskusikan kondisi khusus ananda langsung bersama tim konselor Al-Hikmah?</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ wa_url('Assalamualaikum Admin Al-Hikmah, saya ingin konsultasi mengenai kebutuhan bimbingan Al-Qur\'an untuk ananda.') }}"
                        target="_blank" rel="noopener"
                        class="btn-editorial-whatsapp py-2 px-3">
                        <i class="bi bi-whatsapp me-1" aria-hidden="true"></i> Konsultasi Santun (WhatsApp)
                    </a>
                    <a href="{{ route('metode') }}" class="btn-editorial-secondary py-2 px-3">
                        Pelajari Metode Talaqqi <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. SESI EVALUASI & PENEMPATAN LEVEL (15 MENIT • 100% GRATIS) -->
    <!-- ============================================ -->
    <section class="section-editorial-spacing" aria-label="Sesi Penempatan Belajar Gratis">
        <div class="container">
            <div class="editorial-card-featured p-4 p-lg-5" data-reveal>
                <div class="row align-items-center mb-4 g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                            <i class="bi bi-gift-fill me-1" aria-hidden="true"></i> Evaluasi Awal • 100% Bebas Biaya
                        </span>
                        <h2 class="editorial-title fs-2 mb-2">
                            Mulai dari Sesi Penempatan 15 Menit, <span class="text-emerald-deep">Tanpa Biaya &amp; Tanpa Ikatan</span>
                        </h2>
                        <p class="editorial-subtitle mb-0 text-secondary" style="max-width: 650px;">
                            Masih ragu materi awal yang paling tepat untuk ananda? Jadwalkan satu sesi evaluasi santai bersama guru kami untuk memetakan kemampuan membaca Al-Qur'an secara objektif dan penuh kehangatan.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button type="button" class="btn-editorial-primary" data-bs-toggle="modal"
                            data-bs-target="#trialModal">
                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i> Daftar Evaluasi (15 Menit)
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3.5 rounded-3 bg-body border h-100">
                            <div class="fw-bold fs-5 text-emerald-deep mb-1">01</div>
                            <h3 class="fw-bold text-heading fs-6 mb-2">Kenalan Santai &amp; Suasana Hangat</h3>
                            <p class="small text-secondary mb-0">
                                Sapaan ramah dan obrolan ringan agar ananda ceria, nyaman, dan tidak merasa sedang diuji.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3.5 rounded-3 bg-body border h-100">
                            <div class="fw-bold fs-5 text-emerald-deep mb-1">02</div>
                            <h3 class="fw-bold text-heading fs-6 mb-2">Simak Tartil &amp; Pemetaan Makhraj</h3>
                            <p class="small text-secondary mb-0">
                                Guru menyimak bacaan ananda secara suportif guna mengetahui ketepatan makharijul huruf dan tajwid dasarnya.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3.5 rounded-3 bg-body border h-100">
                            <div class="fw-bold fs-5 text-emerald-deep mb-1">03</div>
                            <h3 class="fw-bold text-heading fs-6 mb-2">Rekomendasi Level Belajar Objektif</h3>
                            <p class="small text-secondary mb-0">
                                Ayah Bunda menerima saran jilid Iqra atau level program yang paling tepat, murni edukatif tanpa paksaan mendaftar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5.8 PRATINJAU RAPOR MUTABA'AH DIGITAL DI HP BUNDA -->
    <!-- ============================================ -->
    <section class="py-5 bg-white border-top border-bottom" aria-label="Pratinjau Rapor Mutaba'ah Digital">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1" data-reveal>
                    <div class="phone-mockup-wrapper">
                        <div class="phone-notch"></div>
                        <div class="phone-mockup-screen">
                            <!-- Header HP -->
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <div>
                                    <div class="fw-bold text-emerald-deep d-flex align-items-center gap-1"
                                        style="font-size: 0.85rem;">
                                        <i class="bi bi-book-half"></i> AL-HIKMAH LMS
                                    </div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Portal Wali Santri</small>
                                </div>
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1"
                                    style="font-size: 0.68rem;">
                                    <i class="bi bi-check2-all me-1"></i> Sesi #4 Selesai
                                </span>
                            </div>

                            <!-- Profil Singkat Sesi -->
                            <div class="p-2.5 rounded-3 bg-light border mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">Muhammad Fatih (7 Thn)
                                        </div>
                                        <div class="text-secondary" style="font-size: 0.72rem;">Paket Bimbingan Mumtaz •
                                            Durasi 90 Menit</div>
                                    </div>
                                    <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill"
                                        style="font-size: 0.68rem;">
                                        Kamis, 16:00 WIB
                                    </span>
                                </div>
                                <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2 text-secondary"
                                    style="font-size: 0.72rem;">
                                    <i class="bi bi-person-badge text-success"></i> Guru: <span
                                        class="fw-semibold text-dark">Ustadz Fauzan, S.Pd.I</span> (Hafidz 30 Juz)
                                </div>
                            </div>

                            <!-- Capaian Materi -->
                            <div class="mb-3">
                                <div class="fw-semibold text-secondary small mb-1">Materi Pertemuan:</div>
                                <div class="p-2 rounded-3 bg-white border d-flex align-items-center gap-2">
                                    <div class="rounded-2 p-1.5 bg-success-subtle text-success">
                                        <i class="bi bi-journal-text fs-6"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.82rem;">Iqra 3 (Hal 14 - 16)
                                            &amp; QS. An-Nasr</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Pemantapan huruf 'Ain, Ghain,
                                            dan Mad Thabi'i</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parameter Evaluasi Tajwid & Adab -->
                            <div class="p-2.5 rounded-3 bg-light border mb-3">
                                <div class="fw-semibold text-secondary small mb-2">Evaluasi Belajar Ananda:</div>
                                <div class="d-flex justify-content-between align-items-center mb-1.5 text-secondary"
                                    style="font-size: 0.75rem;">
                                    <span>Ketepatan Makhraj Huruf</span>
                                    <span class="text-warning"><i class="bi bi-star-fill"></i><i
                                            class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                            class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i> <strong
                                            class="text-dark ms-1">Fasih</strong></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1.5 text-secondary"
                                    style="font-size: 0.75rem;">
                                    <span>Ketukan Mad (Panjang-Pendek)</span>
                                    <span class="text-warning"><i class="bi bi-star-fill"></i><i
                                            class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                            class="bi bi-star-fill"></i><i class="bi bi-star-half"></i> <strong
                                            class="text-dark ms-1">Rapi</strong></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-secondary"
                                    style="font-size: 0.75rem;">
                                    <span>Fokus &amp; Adab Belajar</span>
                                    <span class="badge bg-success text-white px-2 py-0.5 rounded-pill"
                                        style="font-size: 0.68rem;">Sangat Antusias</span>
                                </div>
                            </div>

                            <!-- Catatan Pembimbing -->
                            <div class="p-2.5 rounded-3 border mb-3"
                                style="background: rgba(6, 78, 59, 0.04); border-color: rgba(6, 78, 59, 0.15) !important;">
                                <div class="small fw-bold text-emerald-deep mb-1"><i class="bi bi-chat-quote-fill me-1"></i>
                                    Catatan Ustadz untuk Bunda:</div>
                                <p class="text-secondary mb-0" style="font-size: 0.74rem; line-height: 1.4;">
                                    "Alhamdulillah hari ini Fatih sangat fokus saat membedakan huruf Ha dan Kha. Pelafalan
                                    ayat 1-3 QS. An-Nasr sudah tartil. Mohon terus dibantu murajaah santai 5 menit sebelum
                                    tidur ya Bunda."
                                </p>
                            </div>

                            <!-- Aksi Mockup -->
                            <div class="d-flex gap-2">
                                <div class="btn btn-sm btn-outline-secondary w-100 rounded-2 py-1 text-center"
                                    style="font-size: 0.72rem;">
                                    <i class="bi bi-download me-1"></i> Unduh PDF
                                </div>
                                <div class="btn btn-sm btn-editorial-primary w-100 rounded-2 py-1 text-center"
                                    style="font-size: 0.72rem;">
                                    <i class="bi bi-whatsapp me-1"></i> Balas Guru
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 order-1 order-lg-2" data-reveal data-reveal-delay="150">
                    <div class="ps-lg-4">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                            <i class="bi bi-phone-fill me-1" aria-hidden="true"></i> Rapor Digital Wali Santri
                        </span>
                        <h2 class="editorial-title text-start mb-3">Setiap Sesi Tercatat Rapi, <span
                                class="text-emerald-deep">Terpantau Langsung di Ponsel Bunda</span></h2>
                        <p class="editorial-subtitle text-start mb-4">
                            Pantau capaian makhraj, ketukan tajwid, target hafalan, dan catatan ustadz setiap selesai 90 menit bimbingan. Transparan, teratur, dan menenangkan hati orang tua.
                        </p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                    <i class="bi bi-bell-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Rapor Masuk Otomatis Pasca Sesi</h3>
                                    <p class="small text-secondary mb-0">Begitu sesi 90 menit tuntas, ustadz mengunggah catatan evaluasi dan presensi langsung ke ponsel Ayah Bunda.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Rekam Jejak Hafalan Permanen</h3>
                                    <p class="small text-secondary mb-0">Seluruh riwayat surat, jilid Iqra, dan target mutaba'ah tersimpan rapi sebagai kenangan tumbuh kembang ananda.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                    <i class="bi bi-chat-heart-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Catatan Khusus untuk Murajaah Rumah</h3>
                                    <p class="small text-secondary mb-0">Dapatkan tips santai 5 menit sebelum tidur dari ustadz agar ananda tetap senang mengingat materi yang dipelajari.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <button type="button" class="btn-editorial-primary" data-bs-toggle="modal"
                                data-bs-target="#trialModal">
                                <i class="bi bi-pencil-square me-1"></i> Daftar Evaluasi Awal Tanpa Biaya
                            </button>
                            <a href="{{ wa_url('Assalamualaikum Admin Al-Hikmah, saya ingin menanyakan jadwal bimbingan mengaji untuk ananda.') }}"
                                target="_blank" rel="noopener"
                                class="btn-editorial-whatsapp">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5. PROGRAM BELAJAR (HOME VISIT & ONLINE) -->
    <!-- ============================================ -->
    <span id="pilihan-program" aria-hidden="true"></span>
    <section id="program" class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Program Belajar">
        <div class="container text-center">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-reveal>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                        <i class="bi bi-mortarboard-fill me-1" aria-hidden="true"></i> Pilihan Program Bimbingan
                    </span>
                    <h2 class="editorial-title mb-2">Kurikulum Bertahap, <span class="text-emerald-deep">Sesuai Kesiapan Ananda</span></h2>
                    <p class="editorial-subtitle mx-auto">Tersedia pilihan Guru Datang ke Rumah (Home Visit Jabodetabek) maupun Kelas Online 1-on-1 Interaktif.</p>
                </div>
            </div>

            <div class="row g-4 text-start">
                <!-- Program 1: Iqra & Dasar -->
                <div class="col-md-4" data-reveal>
                    <div class="editorial-card h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="editorial-icon-badge mb-0"><i class="bi bi-book-half"></i></div>
                                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Usia 4 - 12 Tahun</span>
                            </div>
                            <h3 class="fw-bold fs-5 text-heading mb-2">Iqra &amp; Al-Qur'an Dasar</h3>
                            <p class="small text-secondary mb-3">
                                Mengenal huruf hijaiyah, harakat dasar, dan menyambung ayat dengan metode santai dan apresiatif tanpa tekanan.
                            </p>
                            <div class="d-flex flex-wrap gap-1.5 mb-3">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-house-door-fill me-1"></i> Home Visit
                                </span>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-camera-video-fill me-1"></i> Online 1-on-1
                                </span>
                            </div>
                        </div>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="small text-muted">Durasi 90 Menit Penuh</span>
                            <span class="small fw-bold text-emerald-deep">Privat 1-on-1</span>
                        </div>
                    </div>
                </div>

                <!-- Program 2: Tahsin & Tajwid -->
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="editorial-card h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="editorial-icon-badge mb-0"><i class="bi bi-mic"></i></div>
                                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Anak &amp; Remaja</span>
                            </div>
                            <h3 class="fw-bold fs-5 text-heading mb-2">Tahsin &amp; Tartil Tajwid</h3>
                            <p class="small text-secondary mb-3">
                                Memfasihkan makharijul huruf, ketukan mad panjang-pendek, hukum nun mati, dan ghunnah sesuai kaidah qira'ah.
                            </p>
                            <div class="d-flex flex-wrap gap-1.5 mb-3">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-house-door-fill me-1"></i> Home Visit
                                </span>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-camera-video-fill me-1"></i> Online 1-on-1
                                </span>
                            </div>
                        </div>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="small text-muted">Durasi 90 Menit Penuh</span>
                            <span class="small fw-bold text-emerald-deep">Standar Qira'ah</span>
                        </div>
                    </div>
                </div>

                <!-- Program 3: Tahfidz & Muraja'ah -->
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="editorial-card h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="editorial-icon-badge mb-0"><i class="bi bi-clipboard2-pulse"></i></div>
                                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">Juz 30 &amp; Pilihan</span>
                            </div>
                            <h3 class="fw-bold fs-5 text-heading mb-2">Tahfidz &amp; Muraja'ah Terstruktur</h3>
                            <p class="small text-secondary mb-3">
                                Menghafal Juz 'Amma dan surat pilihan dengan target harian realistis serta setoran muraja'ah mutqin yang terpantau.
                            </p>
                            <div class="d-flex flex-wrap gap-1.5 mb-3">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-house-door-fill me-1"></i> Home Visit
                                </span>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.7rem;">
                                    <i class="bi bi-camera-video-fill me-1"></i> Online 1-on-1
                                </span>
                            </div>
                        </div>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="small text-muted">Durasi 90 Menit Penuh</span>
                            <span class="small fw-bold text-emerald-deep">Mutqin Terpantau</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 d-flex flex-wrap justify-content-center align-items-center gap-3" data-reveal>
                <a href="{{ route('program') }}" class="btn-editorial-primary">
                    <i class="bi bi-grid-fill me-1"></i> Pelajari Rincian Kurikulum &amp; Jadwal
                </a>
                <a href="{{ route('metode') }}" class="btn-editorial-secondary">
                    <i class="bi bi-geo-alt-fill me-1"></i> Area Layanan Home Visit &amp; Online
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 6. TESTIMONI KELUARGA -->
    <!-- ============================================ -->
    <section class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Testimoni Keluarga">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-7 text-center" data-reveal>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                        <i class="bi bi-star-fill text-warning me-1" aria-hidden="true"></i> Ulasan Nyata
                    </span>
                    <h2 class="editorial-title mb-2">Cerita Nyata <span class="text-emerald-deep">Wali Santri</span></h2>
                    <p class="editorial-subtitle mx-auto">Kisah para orang tua yang merasakan langsung perkembangan mengaji dan adab buah hatinya.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="editorial-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-1 text-warning mb-3" aria-label="Rating 5 dari 5 bintang">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i>
                            </div>
                            <blockquote class="text-secondary small mb-4" style="line-height: 1.65; font-style: normal;">
                                &ldquo;Alhamdulillah anak saya sekarang lebih bersemangat setiap jam mengaji tiba. Guru pembimbingnya sangat sabar dan mampu membangun suasana belajar yang menyenangkan.&rdquo;
                            </blockquote>
                        </div>
                        <div class="pt-3 border-top">
                            <div class="fw-bold text-heading" style="font-size: 0.95rem;">Bunda Aisyah</div>
                            <div class="small text-secondary">Wali Santri Program Iqra (Jakarta Selatan)</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="editorial-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-1 text-warning mb-3" aria-label="Rating 5 dari 5 bintang">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i>
                            </div>
                            <blockquote class="text-secondary small mb-4" style="line-height: 1.65; font-style: normal;">
                                &ldquo;Laporan mutaba'ah di website sangat memudahkan saya memantau hafalan anak meskipun saya bekerja di kantor. Sangat transparan dan profesional.&rdquo;
                            </blockquote>
                        </div>
                        <div class="pt-3 border-top">
                            <div class="fw-bold text-heading" style="font-size: 0.95rem;">Ayah Hendra</div>
                            <div class="small text-secondary">Wali Santri Program Tahfidz (Depok)</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="editorial-card p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-1 text-warning mb-3" aria-label="Rating 5 dari 5 bintang">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                    class="bi bi-star-fill"></i>
                            </div>
                            <blockquote class="text-secondary small mb-4" style="line-height: 1.65; font-style: normal;">
                                &ldquo;Jadwalnya sangat fleksibel dan gurunya selalu tepat waktu. Kaidah tahsin yang diajarkan sangat mudah dipahami oleh anak usia sekolah.&rdquo;
                            </blockquote>
                        </div>
                        <div class="pt-3 border-top">
                            <div class="fw-bold text-heading" style="font-size: 0.95rem;">Bunda Fatimah</div>
                            <div class="small text-secondary">Wali Santri Program Tahsin (Tangerang Selatan)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 7. JADWAL SHOLAT & KIBLAT HARIAN (UTILITY) -->
    <!-- ============================================ -->
    <section class="py-4 bg-white border-bottom" aria-label="Informasi Jadwal Sholat">
        <div class="container">
            <div class="editorial-card p-3 p-md-4 d-flex flex-row align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="editorial-icon-badge mb-0" style="width: 44px; height: 44px;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-heading">Waktu Ibadah &amp; Arah Kiblat Harian</h6>
                        <p class="text-secondary small mb-0">Pantau jadwal sholat akurat standar Kemenag RI, hitung mundur azan, dan kompas kiblat real-time.</p>
                    </div>
                </div>
                <a href="{{ route('jadwal-sholat') }}" class="btn-editorial-secondary py-2 px-3">
                    Buka Jadwal Sholat &amp; Kiblat <i class="bi bi-chevron-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 8. BLOG & EDUKASI ISLAMI -->
    <!-- ============================================ -->
    @php
        $latestArticles =
            isset($latestArticles) && $latestArticles->count() > 0
                ? $latestArticles
                : \App\Models\Article::published()
                    ->with(['category', 'user'])
                    ->latest('published_at')
                    ->take(3)
                    ->get();
    @endphp
    <section class="blog_part py-5" aria-label="Blog & Edukasi Qur'ani">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-7 text-center" data-reveal>
                    <div class="section-badge mx-auto mb-3">
                        <i class="bi bi-journal-bookmark-fill me-1"></i> Artikel &amp; Panduan
                    </div>
                    <h2 class="editorial-title mb-2">Catatan &amp; Panduan Belajar <span class="text-emerald-deep">Al-Qur'an</span></h2>
                    <p class="editorial-subtitle mx-auto" style="max-width: 580px;">
                        Kumpulan artikel praktis seputar kaidah tajwid, metode hafalan mutqin, dan tips mendampingi anak mengaji di rumah.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                @if (isset($latestArticles) && $latestArticles->count() > 0)
                    @foreach ($latestArticles as $index => $article)
                        <div class="col-md-6 col-lg-4 d-flex" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                            <div class="blog-card w-100 d-flex flex-column">
                                <div class="blog-card-img-wrap position-relative">
                                    <a href="{{ route('blog.show', $article->slug) }}" class="d-block">
                                        <img src="{{ $article->cover_url }}" class="w-100 object-fit-cover" style="height: 210px;"
                                            alt="{{ $article->title }}"
                                            onerror="this.src='{{ asset('assets/img/' . (($index % 3) + 1) . '.jpg') }}'">
                                    </a>
                                    <span class="position-absolute top-0 end-0 m-3 badge bg-white text-dark shadow-sm border fw-semibold" style="font-size: 0.76rem;">
                                        <i class="bi bi-calendar3 me-1 text-emerald-deep"></i>
                                        {{ $article->published_at ? $article->published_at->translatedFormat('d M Y') : $article->created_at->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    @if ($article->category)
                                        <div class="mb-2">
                                            <a href="{{ route('blog.category', $article->category->slug) }}"
                                                class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none py-1.5 px-2.5">
                                                <i class="bi {{ $article->category->icon ?? 'bi-bookmark-check' }} me-1"></i>
                                                {{ $article->category->name }}
                                            </a>
                                        </div>
                                    @endif
                                    <h3 class="fw-bold fs-5 text-heading mb-2 lh-snug">
                                        <a href="{{ route('blog.show', $article->slug) }}" class="text-heading text-decoration-none hover-emerald">
                                            {{ Str::limit($article->title, 64) }}
                                        </a>
                                    </h3>
                                    <p class="small text-secondary flex-grow-1 mb-3" style="line-height: 1.65;">
                                        {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 120) }}
                                    </p>
                                    <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center small text-muted">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-clock text-emerald-deep"></i>
                                            {{ $article->reading_time_label }}
                                        </span>
                                        <a href="{{ route('blog.show', $article->slug) }}" class="fw-semibold text-emerald-deep text-decoration-none d-inline-flex align-items-center gap-1">
                                            Baca artikel <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <div class="p-4 bg-white rounded-4 border d-inline-block text-muted"
                            style="border-color: var(--border-color) !important;">
                            <i class="bi bi-journal-text fs-1 text-success mb-2 d-block"></i>
                            <p class="mb-0">Artikel edukasi terbaru akan segera hadir.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="text-center mt-5" data-reveal>
                <a href="{{ route('blog.index') }}" class="btn-editorial-secondary">
                    <i class="bi bi-journal-text me-2"></i> Lihat Semua Artikel
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 9. RENUNGAN & LANGKAH AWAL (CTA SECTION) -->
    <!-- ============================================ -->
    <section id="kontak" class="cta-section text-center" aria-label="CTA">
        <div class="cta-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="cta-content" data-reveal>
                <div class="cta-icon mx-auto"><i class="bi bi-book"></i></div>

                @auth
                    <h2 class="editorial-title text-white mb-3">
                        Selamat Datang Kembali,<br><span style="color: #6ee7b7;">{{ auth()->user()->name }}</span>
                    </h2>
                    <p class="editorial-subtitle text-white mx-auto mb-4" style="max-width: 620px; color: rgba(255, 255, 255, 0.92) !important;">
                        Lanjutkan sesi bimbingan Al-Qur'an dan pantau catatan mutaba'ah hari ini.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @if (auth()->user()->isParent())
                            <a href="{{ route('parent.dashboard') }}" class="btn-editorial-primary btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i> Buka Dashboard Orang Tua
                            </a>
                            <a href="{{ route('parent.schedules.index') }}" class="btn-editorial-secondary btn-lg">
                                <i class="bi bi-calendar-week me-2"></i> Jadwal Belajar Ananda
                            </a>
                        @elseif(auth()->user()->isStudent())
                            <a href="{{ route('student.dashboard') }}" class="btn-editorial-primary btn-lg">
                                <i class="bi bi-journal-check me-2"></i> Masuk Ruang Santri
                            </a>
                            <a href="{{ route('student.targets.today') }}" class="btn-editorial-secondary btn-lg">
                                <i class="bi bi-bullseye me-2"></i> Target Hafalan Hari Ini
                            </a>
                        @elseif(auth()->user()->isMentor())
                            <a href="{{ route('mentor.dashboard') }}" class="btn-editorial-primary btn-lg">
                                <i class="bi bi-mortarboard me-2"></i> Dashboard Mengajar
                            </a>
                            <a href="{{ route('mentor.sessions.index') }}" class="btn-editorial-secondary btn-lg">
                                <i class="bi bi-calendar3 me-2"></i> Jadwal Bimbingan
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn-editorial-primary btn-lg">
                                <i class="bi bi-gear-fill me-2"></i> Dashboard Admin
                            </a>
                            <a href="{{ route('admin.enrollments.index') }}" class="btn-editorial-secondary btn-lg">
                                <i class="bi bi-people-fill me-2"></i> Kelola Pendaftaran
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-editorial-primary btn-lg">
                                <i class="bi bi-speedometer2 me-2"></i> Buka Dashboard
                            </a>
                        @endif
                    </div>
                @else
                    <h2 class="editorial-title text-white mb-3">
                        Bekal Terindah Ananda Membaca Surat Cinta-Nya
                    </h2>
                    <p class="editorial-subtitle text-white mx-auto mb-4" style="max-width: 660px; color: rgba(255, 255, 255, 0.92) !important;">
                        Bimbel pelajaran umum jutaan rupiah kita siapkan, les musik dan bakat rutin kita dukung. Sudahkah kita menyisihkan yang terbaik untuk bekal ananda membaca ayat-ayat-Nya dengan tartil kelak di hadapan Allah? Mulai langkah awal ananda melalui sesi evaluasi 15 menit tanpa biaya.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <button type="button" class="btn-editorial-primary btn-lg" data-bs-toggle="modal" data-bs-target="#trialModal">
                            <i class="bi bi-calendar2-check-fill me-2"></i> Daftar Evaluasi Awal Tanpa Biaya
                        </button>
                        <a href="{{ route('program') }}" class="btn-editorial-secondary btn-lg">
                            <i class="bi bi-grid-fill me-2"></i> Lihat Pilihan Program
                        </a>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan mengaji AL-HIKMAH untuk ananda.') }}"
                            class="btn-editorial-secondary btn-lg" target="_blank" rel="noopener"
                            aria-label="Konsultasi program belajar AL-HIKMAH via WhatsApp">
                            <i class="bi bi-whatsapp me-2"></i> Tanya via WhatsApp
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- STICKY MOBILE ACTION BAR (RAMAH ORANG TUA) -->
    <!-- ============================================ -->
    <div class="sticky-mobile-cta-bar d-flex d-md-none align-items-center gap-2">
        <a href="{{ wa_url('Assalamualaikum Admin Al-Hikmah, saya ingin menanyakan jadwal bimbingan mengaji untuk ananda.') }}"
            target="_blank" rel="noopener"
            class="btn-editorial-whatsapp flex-grow-1 text-center justify-content-center"
            style="font-size: 0.82rem; min-height: 44px;">
            <i class="bi bi-whatsapp"></i> Tanya Admin
        </a>
        <button type="button"
            class="btn-editorial-primary flex-grow-1 text-center justify-content-center"
            data-bs-toggle="modal" data-bs-target="#trialModal"
            style="font-size: 0.82rem; min-height: 44px;">
            <i class="bi bi-pencil-square"></i> Daftar Gratis
        </button>
    </div>
@endsection
