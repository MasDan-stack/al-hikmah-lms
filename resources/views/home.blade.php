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
                        Membimbing Buah Hati Menjadi <span class="text-emerald-deep">Generasi Qur'ani</span> dengan Penuh Kasih dan Adab.
                    </h1>

                    <p class="editorial-subtitle mb-4">
                        Belajar Al-Qur'an adalah perjalanan mengenal Allah, memperbagus makhraj tartil, dan menumbuhkan akhlak mulia. AL-HIKMAH mendampingi ananda melalui bimbingan privat 1-on-1 berdurasi 90 menit per sesi bersama pendidik pilihan yang sabar dan ramah anak.
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
                                <i class="bi bi-pencil-square" aria-hidden="true"></i> Daftar Gratis Sekarang
                            </button>
                            <a href="#pilihan-program" class="btn-editorial-secondary">
                                <i class="bi bi-journal-text" aria-hidden="true"></i> Pilihan Program
                            </a>
                            <a href="{{ wa_url('Assalamualaikum Admin Al-Hikmah, saya ingin konsultasi mengenai bimbingan Al-Qur\'an untuk ananda.') }}"
                                target="_blank" rel="noopener"
                                class="btn-editorial-whatsapp">
                                <i class="bi bi-whatsapp" aria-hidden="true"></i> Konsultasi WhatsApp
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-5 col-xl-6 text-center">
                    <div class="editorial-hero-frame position-relative">
                        <img src="{{ asset('assets/img/1.jpg') }}" alt="Belajar Al-Qur'an bersama AL-HIKMAH" width="600"
                            height="420" fetchpriority="high" onerror="this.src='{{ asset('assets/img/1.jpg') }}'"
                            class="img-fluid">

                        <!-- Floating Badge Kiri Bawah: Pendampingan Santun 1 Guru 1 Santri -->
                        <div class="position-absolute bottom-0 start-0 m-3 editorial-floating-badge text-start d-none d-sm-flex align-items-center gap-3"
                            style="max-width: 290px;">
                            <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 42px; height: 42px;">
                                <i class="bi bi-heart-fill fs-5" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-heading">Pendampingan Santun</div>
                                <div class="text-secondary" style="font-size: 0.76rem; line-height: 1.35;">1 Guru fokus mendampingi 1 Santri (90 Menit).</div>
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
    <!-- 2. JADWAL SHOLAT BANNER RINGKAS -->
    <!-- ============================================ -->
    <!-- ============================================ -->
    <!-- 2. JADWAL SHOLAT BANNER RINGKAS -->
    <!-- ============================================ -->
    <section class="py-4"
        style="background: var(--bg-primary); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);"
        aria-label="Informasi Jadwal Sholat">
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
    <!-- 3. PILAR KEUNGGULAN (ISLAMIC EDITORIAL) -->
    <!-- ============================================ -->
    <section class="section-editorial-spacing" aria-label="Keunggulan AL-HIKMAH">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-3 col-md-6 d-flex flex-column justify-content-center">
                    <div class="pe-lg-3">
                        <h2 class="editorial-title mb-3 fs-2">Pendampingan yang <span class="text-emerald-deep">Menenangkan</span></h2>
                        <p class="editorial-subtitle small mb-4">Mendampingi ananda dengan keteladanan akhlak, kesabaran murni, dan kurikulum yang ramah perkembangan anak.</p>
                        <a href="{{ route('tentang-kami') }}" class="btn-editorial-secondary py-2 px-3">
                            Pelajari Nilai Kami <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="editorial-card">
                        <div class="editorial-icon-badge">
                            <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-bold fs-5 mb-2 text-heading">Guru Terkurasi &amp; Ramah</h3>
                        <p class="text-secondary small mb-0">Pendidik tersertifikasi, berakhlak mulia, dan berpendekatan santun sehingga ananda tidak merasa tertekan saat belajar.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="editorial-card">
                        <div class="editorial-icon-badge">
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-bold fs-5 mb-2 text-heading">Privat Penuh (90 Menit)</h3>
                        <p class="text-secondary small mb-0">Satu sesi 90 menit intensif 1 Guru 1 Santri, memberikan waktu cukup untuk talaqqi, perbaikan tajwid, dan nasihat adab.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="editorial-card">
                        <div class="editorial-icon-badge">
                            <i class="bi bi-journal-check" aria-hidden="true"></i>
                        </div>
                        <h3 class="fw-bold fs-5 mb-2 text-heading">Rapor Mutaba'ah Terbuka</h3>
                        <p class="text-secondary small mb-0">Orang tua dapat memantau capaian makhraj, juz atau halaman, serta saran murajaah di rumah secara transparan lewat portal LMS.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3.5 KEKHAWATIRAN ORANG TUA VS PENDEKATAN AL-HIKMAH -->
    <!-- ============================================ -->
    <!-- ============================================ -->
    <!-- 3.5 KEKHAWATIRAN ORANG TUA VS PENDEKATAN AL-HIKMAH -->
    <!-- ============================================ -->
    <section class="section-editorial-spacing border-top border-bottom"
        style="background: var(--bg-tertiary);"
        aria-label="Kekhawatiran Orang Tua dan Solusi Al-Hikmah">
        <div class="container">
            <div class="row justify-content-center mb-5 text-center">
                <div class="col-lg-8" data-reveal>
                    <h2 class="editorial-title fs-2 mb-3">Memahami Kekhawatiran <span class="text-emerald-deep">Orang Tua</span></h2>
                    <p class="editorial-subtitle mx-auto">Setiap anak memiliki ritme dan keunikan masing-masing. Di AL-HIKMAH, kami mendengarkan apa yang sering membuat orang tua cemas dan menghadirkan bimbingan yang menenangkan.</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-md-6 col-lg-3" data-reveal>
                    <div class="editorial-card">
                        <div class="pb-3 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2 text-danger small fw-bold mb-1">
                                <i class="bi bi-x-circle-fill"></i> Keresahan Umum
                            </div>
                            <p class="small text-secondary mb-0">Anak cepat bosan, mogok, atau merasa tertekan saat diminta mengaji sore hari.</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 text-success small fw-bold mb-1">
                                <i class="bi bi-check-circle-fill"></i> Solusi Al-Hikmah
                            </div>
                            <p class="small text-secondary mb-0">Bimbingan 1-on-1 dengan pendekatan talaqqi yang ramah, sabar, penuh apresiasi, dan menghargai ritme belajar ananda.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="100">
                    <div class="editorial-card">
                        <div class="pb-3 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2 text-danger small fw-bold mb-1">
                                <i class="bi bi-x-circle-fill"></i> Keresahan Umum
                            </div>
                            <p class="small text-secondary mb-0">Orang tua sibuk bekerja, tidak tahu perkembangan tajwid dan hafalan anak sudah sampai mana.</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 text-success small fw-bold mb-1">
                                <i class="bi bi-check-circle-fill"></i> Solusi Al-Hikmah
                            </div>
                            <p class="small text-secondary mb-0">Jurnal Mutaba'ah Digital otomatis tercatat setiap sesi 90 menit selesai, bisa dipantau langsung dari ponsel orang tua.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="200">
                    <div class="editorial-card">
                        <div class="pb-3 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2 text-danger small fw-bold mb-1">
                                <i class="bi bi-x-circle-fill"></i> Keresahan Umum
                            </div>
                            <p class="small text-secondary mb-0">Khawatir karakter anak tidak cocok dengan cara mengajar ustadz atau ustadzah pembimbing.</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 text-success small fw-bold mb-1">
                                <i class="bi bi-check-circle-fill"></i> Solusi Al-Hikmah
                            </div>
                            <p class="small text-secondary mb-0">Garansi Kesesuaian Guru 100%. Orang tua leluasa berkonsultasi untuk penyesuaian guru agar ananda nyaman belajar.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="300">
                    <div class="editorial-card">
                        <div class="pb-3 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2 text-danger small fw-bold mb-1">
                                <i class="bi bi-x-circle-fill"></i> Keresahan Umum
                            </div>
                            <p class="small text-secondary mb-0">Jadwal les anak padat dan sering bentrok dengan kegiatan sekolah atau les akademik.</p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 text-success small fw-bold mb-1">
                                <i class="bi bi-check-circle-fill"></i> Solusi Al-Hikmah
                            </div>
                            <p class="small text-secondary mb-0">Waktu fleksibel (Pagi, Sore, atau Malam) dengan pilihan bimbingan online interaktif maupun guru datang ke rumah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. TENTANG KAMI (ISLAMIC EDITORIAL) -->
    <!-- ============================================ -->
    <section class="section-editorial-spacing" aria-label="Tentang AL-HIKMAH">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-md-6" data-reveal>
                    <div class="editorial-hero-frame">
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Suasana belajar Al-Qur'an"
                            onerror="this.src='{{ asset('assets/img/2.jpg') }}'" class="img-fluid">
                    </div>
                </div>
                <div class="col-md-6" data-reveal data-reveal-delay="150">
                    <div class="ps-lg-3">
                        <h2 class="editorial-title fs-2 mb-3">Karena Anak Kita Perlu <span class="text-emerald-deep">Didampingi</span> dengan Penuh Kasih.</h2>
                        <p class="editorial-subtitle mb-4">Di tengah kesibukan hidup, tidak semua keluarga memiliki waktu untuk mendampingi anak secara intensif. AL-HIKMAH hadir untuk menjadi mitra terpercaya orang tua dalam membimbing generasi Qur'ani.</p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge mb-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-check2 fs-5"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 mb-1 text-heading">Belajar dengan Pemahaman &amp; Tajwid</h3>
                                    <p class="small text-secondary mb-0">Bukan sekadar mengejar target halaman, tetapi memastikan makharijul huruf tepat dan tartil.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge mb-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-check2 fs-5"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 mb-1 text-heading">Penanaman Adab &amp; Nilai Akhlak</h3>
                                    <p class="small text-secondary mb-0">Membiasakan doa harian, adab terhadap orang tua, serta kecintaan beribadah sejak dini.</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('tentang-kami') }}" class="btn-editorial-secondary">
                            Baca Kisah Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5. PILAR PEMBELAJARAN (ISLAMIC EDITORIAL) -->
    <!-- ============================================ -->
    <section class="py-5 border-top border-bottom" style="background: var(--bg-tertiary);" aria-label="Pilar Pendampingan AL-HIKMAH">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-lg-3 col-sm-6" data-reveal>
                    <div class="editorial-card text-center align-items-center">
                        <div class="editorial-icon-badge mx-auto">
                            <i class="bi bi-person-check fs-4"></i>
                        </div>
                        <h4 class="fs-6 fw-bold text-heading mb-1">Bimbingan 1-on-1</h4>
                        <p class="small text-secondary mb-0">Fokus personal sesuai karakter santri</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6" data-reveal data-reveal-delay="100">
                    <div class="editorial-card text-center align-items-center">
                        <div class="editorial-icon-badge mx-auto">
                            <i class="bi bi-journal-bookmark fs-4"></i>
                        </div>
                        <h4 class="fs-6 fw-bold text-heading mb-1">Tahsin &amp; Tajwid</h4>
                        <p class="small text-secondary mb-0">Penekanan makharijul huruf tartil</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6" data-reveal data-reveal-delay="200">
                    <div class="editorial-card text-center align-items-center">
                        <div class="editorial-icon-badge mx-auto">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <h4 class="fs-6 fw-bold text-heading mb-1">Guru Terkurasi</h4>
                        <p class="small text-secondary mb-0">Pendidik hafidz dan hafidzah beradab</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6" data-reveal data-reveal-delay="300">
                    <div class="editorial-card text-center align-items-center">
                        <div class="editorial-icon-badge mx-auto">
                            <i class="bi bi-clipboard2-check fs-4"></i>
                        </div>
                        <h4 class="fs-6 fw-bold text-heading mb-1">Laporan Mutaba'ah</h4>
                        <p class="small text-secondary mb-0">Evaluasi sesi terbuka untuk wali santri</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5.5 ALUR BELAJAR & PENDAFTARAN -->
    <!-- ============================================ -->
    <section class="section-editorial-spacing" aria-label="Alur Pendaftaran dan Belajar">
        <div class="container">
            <div class="row justify-content-center mb-5 text-center">
                <div class="col-lg-8" data-reveal>
                    <h2 class="editorial-title fs-2 mb-3">Alur Bimbingan <span class="text-emerald-deep">Santri Baru</span></h2>
                    <p class="editorial-subtitle mx-auto">Tahapan terstruktur dan transparan demi kenyamanan keluarga serta kesiapan belajar ananda.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-reveal>
                    <div class="editorial-card h-100 position-relative">
                        <div class="fw-bold fs-4 text-emerald-deep mb-2">01</div>
                        <h3 class="fw-bold fs-5 mb-2 text-heading">Pendaftaran Awal</h3>
                        <p class="small text-secondary mb-0">Isi data calon santri dan tentukan target serta preferensi belajar yang diinginkan keluarga.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="100">
                    <div class="editorial-card h-100 position-relative">
                        <div class="fw-bold fs-4 text-emerald-deep mb-2">02</div>
                        <h3 class="fw-bold fs-5 mb-2 text-heading">Penempatan (Placement)</h3>
                        <p class="small text-secondary mb-0">Asesmen kemampuan bacaan santri secara ramah dan sabar untuk menentukan kurikulum awal yang tepat.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="200">
                    <div class="editorial-card h-100 position-relative">
                        <div class="fw-bold fs-4 text-emerald-deep mb-2">03</div>
                        <h3 class="fw-bold fs-5 mb-2 text-heading">Jadwal &amp; Bimbingan</h3>
                        <p class="small text-secondary mb-0">Pilih sesi reguler (Home Visit atau Online) dan mulai bimbingan intensif 90 menit bersama pendidik.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="300">
                    <div class="editorial-card h-100 position-relative">
                        <div class="fw-bold fs-4 text-emerald-deep mb-2">04</div>
                        <h3 class="fw-bold fs-5 mb-2 text-heading">Laporan Mutaba'ah</h3>
                        <p class="small text-secondary mb-0">Wali santri dapat memantau catatan tajwid, hafalan, dan adab santri melalui portal pemantauan setiap selesai sesi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5.6 SESI EVALUASI & PENEMPATAN LEVEL (GRATIS) -->
    <!-- ============================================ -->
    <section class="py-5" aria-label="Sesi Penempatan Belajar Gratis">
        <div class="container">
            <div class="editorial-card-featured p-4 p-lg-5" data-reveal>
                <div class="row align-items-center mb-4 g-4">
                    <div class="col-lg-8">
                        <h2 class="editorial-title fs-2 mb-2">
                            Daftar Akun &amp; <span class="text-emerald-deep">Penempatan Level</span> Gratis
                        </h2>
                        <p class="editorial-subtitle mb-0">
                            Masih ragu menentukan materi awal yang sesuai untuk ananda? Jadwalkan satu sesi evaluasi santai bersama guru kami untuk mengukur tingkat kemampuan membaca Al-Qur'an secara objektif dan penuh kehangatan.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <button type="button" class="btn-editorial-primary" data-bs-toggle="modal"
                            data-bs-target="#trialModal">
                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i> Daftar Sekarang (Gratis)
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-body border h-100">
                            <div class="fw-bold fs-5 text-emerald-deep mb-1">Langkah 1</div>
                            <h3 class="fw-bold text-heading fs-6 mb-2">Kenalan &amp; Suasana Nyaman</h3>
                            <p class="small text-secondary mb-0">
                                Sapaan hangat dan obrolan ringan untuk mencairkan suasana agar ananda merasa senang dan tidak merasa sedang diuji.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-body border h-100">
                            <div class="fw-bold fs-5 text-emerald-deep mb-1">Langkah 2</div>
                            <h3 class="fw-bold text-heading fs-6 mb-2">Simak Tartil &amp; Makhraj</h3>
                            <p class="small text-secondary mb-0">
                                Guru menyimak bacaan ananda secara suportif guna memetakan ketepatan tajwid dan kefasihan huruf.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 bg-body border h-100">
                            <div class="fw-bold fs-5 text-emerald-deep mb-1">Langkah 3</div>
                            <h3 class="fw-bold text-heading fs-6 mb-2">Rekomendasi Level Belajar</h3>
                            <p class="small text-secondary mb-0">
                                Orang tua menerima saran penempatan jilid Iqra atau level bimbingan yang tepat, murni edukatif tanpa paksaan.
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
                        <h2 class="editorial-title text-start mb-3">Laporan Pembelajaran Rapi, <span
                                class="text-emerald-deep">Langsung di Genggaman</span> Ayah &amp; Bunda</h2>
                        <p class="editorial-subtitle text-start mb-4">
                            Bunda tidak perlu lagi menebak atau khawatir tentang perkembangan mengaji ananda. Setiap kali sesi
                            90 menit selesai, guru pembimbing langsung memperbarui rapor mutaba'ah digital yang mencakup
                            capaian materi, ketukan tajwid, hingga catatan adab.
                        </p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                    <i class="bi bi-phone-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Pantau Walau Sedang Sibuk Bekerja</h3>
                                    <p class="small text-secondary mb-0">Laporan bisa diakses kapan saja dari ponsel orang
                                        tua, memberikan ketenangan batin bagi keluarga yang beraktivitas di luar rumah.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                    <i class="bi bi-archive-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Rekam Jejak Hafalan Tersimpan Rapi</h3>
                                    <p class="small text-secondary mb-0">Seluruh riwayat surat yang dibaca dan target
                                        hafalan tersimpan permanen di portal orang tua sebagai kenangan indah tumbuh kembang
                                        ananda.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="editorial-icon-badge flex-shrink-0" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                    <i class="bi bi-chat-heart-fill"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Komunikasi Dua Arah yang Hangat</h3>
                                    <p class="small text-secondary mb-0">Orang tua dapat memberikan masukan atau
                                        berkonsultasi mengenai tantangan belajar anak langsung bersama ustadz/ustadzah.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <button type="button" class="btn-editorial-primary" data-bs-toggle="modal"
                                data-bs-target="#trialModal">
                                <i class="bi bi-pencil-square me-1"></i> Daftar Akun Santri Gratis
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
    <!-- 6. PROGRAM BELAJAR -->
    <!-- ============================================ -->
    <span id="pilihan-program" aria-hidden="true"></span>
    <section id="program" class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Program Belajar">
        <div class="container text-center">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-reveal>
                    <h2 class="editorial-title mb-2">Kurikulum Bertahap, <span class="text-emerald-deep">Fasilitasi Segala Tingkatan</span></h2>
                    <p class="editorial-subtitle mx-auto">Dirancang bertahap sesuai usia dan tingkat kemampuan membaca Al-Qur'an, dari pengenalan hijaiyah hingga mutqin hafalan.</p>
                </div>
            </div>

            <div class="row g-4 text-start">
                <div class="col-md-4" data-reveal>
                    <div class="editorial-card h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="editorial-icon-badge mb-3"><i class="bi bi-book-half"></i></div>
                            <h3 class="fw-bold fs-5 text-heading mb-2">Iqra &amp; Al-Qur'an Dasar</h3>
                            <p class="small text-secondary mb-3">
                                Mengenal huruf hijaiyah, makhraj dasar, harakat, dan menyambung bacaan secara menyenangkan tanpa tekanan bagi ananda.
                            </p>
                        </div>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="badge bg-light text-secondary border">Usia 4 - 12 Tahun</span>
                            <span class="small fw-bold text-emerald-deep">1-on-1 Privat</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="editorial-card h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="editorial-icon-badge mb-3"><i class="bi bi-mic"></i></div>
                            <h3 class="fw-bold fs-5 text-heading mb-2">Tahsin &amp; Tartil Tajwid</h3>
                            <p class="small text-secondary mb-3">
                                Memperbaiki kefasihan makhraj, hukum nun mati/tanwin, mad thabi'i, hingga ghunnah agar bacaan tartil sesuai kaidah tajwid.
                            </p>
                        </div>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="badge bg-light text-secondary border">Anak &amp; Remaja</span>
                            <span class="small fw-bold text-emerald-deep">Standar Qira'ah</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="editorial-card h-100 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="editorial-icon-badge mb-3"><i class="bi bi-clipboard2-pulse"></i></div>
                            <h3 class="fw-bold fs-5 text-heading mb-2">Tahfidz &amp; Muraja'ah Terstruktur</h3>
                            <p class="small text-secondary mb-3">
                                Bimbingan hafalan Juz 'Amma dan surat pilihan dengan pembagian target harian terukur serta mutaba'ah muraja'ah mutqin.
                            </p>
                        </div>
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <span class="badge bg-light text-secondary border">Juz 30 &amp; Pilihan</span>
                            <span class="small fw-bold text-emerald-deep">Mutqin Terpantau</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-reveal>
                <a href="{{ route('program') }}" class="btn-editorial-secondary">
                    <i class="bi bi-grid-fill me-1"></i> Pelajari Rincian Kurikulum &amp; Jadwal
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 7. FLEKSIBILITAS METODE BELAJAR -->
    <!-- ============================================ -->
    <section class="py-5" aria-label="Sistem Belajar Fleksibel">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-1 order-2" data-reveal>
                    <div class="pe-lg-4">
                        <h2 class="editorial-title text-start mb-3">Sistem Pembelajaran yang <span
                                class="text-emerald-deep">Fleksibel &amp; Nyaman</span></h2>
                        <p class="editorial-subtitle text-start mb-4">Pilih metode pembelajaran yang paling sesuai dengan ritme dan kenyamanan keluarga Anda di rumah maupun secara daring.</p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="editorial-card p-3 h-100">
                                    <div class="editorial-icon-badge mb-2"><i class="bi bi-house-door-fill"></i></div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Offline (Home Visit)</h3>
                                    <p class="small text-secondary mb-0">Guru pembimbing hadir langsung ke kediaman Anda di seluruh area Jabodetabek dengan standar adab yang terjaga.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="editorial-card p-3 h-100">
                                    <div class="editorial-icon-badge mb-2"><i class="bi bi-camera-video-fill"></i></div>
                                    <h3 class="fw-bold fs-6 text-heading mb-1">Online Interaktif</h3>
                                    <p class="small text-secondary mb-0">Sesi tatap muka virtual via Zoom/Google Meet 1-on-1 dengan jadwal fleksibel dari mana saja.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('metode') }}" class="btn-editorial-secondary">
                                <i class="bi bi-info-circle me-1"></i> Pelajari Metode &amp; Protokol Kunjungan
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-2 order-1 text-center" data-reveal data-reveal-delay="150">
                    <div class="editorial-hero-frame">
                        <img src="{{ asset('assets/img/etrain/advance_feature_img.png') }}"
                            alt="Sistem Pembelajaran Al-Hikmah" class="img-fluid w-100 rounded-3"
                            onerror="this.src='{{ asset('assets/img/5.jpg') }}'">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 8. TESTIMONI KELUARGA -->
    <!-- ============================================ -->
    <section class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Testimoni Keluarga">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-7 text-center" data-reveal>
                    <h2 class="editorial-title mb-2">Cerita Nyata <span class="text-emerald-deep">Wali Santri</span></h2>
                    <p class="editorial-subtitle mx-auto">Pengalaman nyata para orang tua yang telah mempercayakan bimbingan mengaji bersama AL-HIKMAH.</p>
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
                                "Alhamdulillah anak saya sekarang lebih bersemangat setiap jam mengaji tiba. Pendampingnya
                                sangat sabar dan mampu membangun chemistry yang menyenangkan."
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
                                "Laporan perkembangan di website sangat memudahkan saya memantau hafalan anak meskipun saya
                                bekerja di kantor. Sangat transparan dan profesional."
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
                                "Jadwalnya sangat fleksibel, dan gurunya selalu tepat waktu. Metode tahsin yang diajarkan
                                sangat mudah dipahami oleh anak-anak usia remaja."
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
    <!-- 9. BLOG & EDUKASI ISLAMI -->
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
                    <h2 class="editorial-title mb-2">Wawasan &amp; Edukasi <span class="text-emerald-deep">Qur'ani</span></h2>
                    <p class="editorial-subtitle mx-auto">Panduan belajar Al-Qur'an, tips mendampingi anak mengaji di
                        rumah, metode tahsin/tahfidz, dan wawasan keislaman terkini.</p>
                </div>
            </div>

            <div class="row g-4">
                @if (isset($latestArticles) && $latestArticles->count() > 0)
                    @foreach ($latestArticles as $index => $article)
                        <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                            <div class="editorial-card h-100 d-flex flex-column justify-content-between p-0 overflow-hidden">
                                <div>
                                    <div class="blog-card-img-wrap position-relative">
                                        <img src="{{ $article->cover_url }}" class="w-100" style="height: 200px; object-fit: cover;"
                                            alt="{{ $article->title }}"
                                            onerror="this.src='{{ asset('assets/img/' . (($index % 3) + 1) . '.jpg') }}'">
                                        <span class="blog-date-badge position-absolute top-0 end-0 m-3 badge bg-white text-dark shadow-sm border">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="p-4">
                                        @if ($article->category)
                                            <a href="{{ route('blog.category', $article->category->slug) }}"
                                                class="badge bg-light text-emerald-deep border text-decoration-none mb-3">
                                                <i class="bi {{ $article->category->icon ?? 'bi-bookmark-check' }} me-1"></i>
                                                {{ $article->category->name }}
                                            </a>
                                        @endif
                                        <h3 class="fw-bold fs-5 text-heading mb-2">
                                            <a href="{{ route('blog.show', $article->slug) }}" class="text-inherit text-decoration-none">
                                                {{ Str::limit($article->title, 56) }}
                                            </a>
                                        </h3>
                                        <p class="small text-secondary mb-0">
                                            {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 110) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="px-4 pb-4 pt-0">
                                    <ul class="blog-meta-list list-unstyled d-flex justify-content-between align-items-center m-0 pt-3 border-top small text-muted"
                                        style="border-color: var(--border-color) !important;">
                                        <li><i class="bi bi-clock me-1 text-emerald-deep"></i>
                                            {{ $article->reading_time_label }}</li>
                                        <li><i class="bi bi-eye me-1 text-emerald-deep"></i>
                                            <span class="tnum-price">{{ number_format($article->views_count) }}</span> Pembaca</li>
                                    </ul>
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
                    <i class="bi bi-grid-fill me-1"></i> Jelajahi Semua Artikel
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
                <div class="cta-icon mx-auto"><i class="bi bi-book"></i></div>
                <h2 class="editorial-title text-white mb-3">
                    @auth
                        Selamat Datang Kembali,<br><span style="color: #6ee7b7;">{{ auth()->user()->name }}</span>
                    @else
                        Mari Menanam Kebaikan<br><span style="color: #6ee7b7;">Sejak Hari Ini</span>
                    @endauth
                </h2>
                <p class="editorial-subtitle text-white mx-auto mb-4" style="max-width: 640px; color: rgba(255, 255, 255, 0.92) !important;">
                    @auth
                        Lanjutkan aktivitas pembelajaran Al-Qur'an dan pantau perkembangan mutaba'ah hari ini.
                    @else
                        Dari satu huruf, satu ayat, satu doa, perjalanan besar menuju generasi Qur'ani dimulai bersama AL-HIKMAH.
                    @endauth
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    @guest
                        <button type="button" class="btn-editorial-primary btn-lg" data-bs-toggle="modal" data-bs-target="#trialModal">
                            <i class="bi bi-calendar2-check-fill me-1"></i> Daftar Gratis Sekarang
                        </button>
                        <a href="{{ route('program') }}" class="btn-editorial-secondary btn-lg">
                            <i class="bi bi-grid-fill me-1"></i> Mulai Belajar
                        </a>
                    @endguest @auth
                        @if (auth()->user()->isParent())
                            <a href="{{ route('parent.enrollments.index') }}" class="btn-editorial-primary">
                                <i class="bi bi-journal-plus me-1"></i> Daftarkan Program Baru Anak
                            </a>
                            <a href="{{ route('parent.dashboard') }}" class="btn-editorial-secondary text-white border-white">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard Orang Tua
                            </a>
                        @elseif(auth()->user()->isStudent())
                            <a href="{{ route('student.dashboard') }}" class="btn-editorial-primary">
                                <i class="bi bi-journal-check me-1"></i> Masuk Ruang Santri
                            </a>
                            <a href="{{ route('student.targets.today') }}" class="btn-editorial-secondary text-white border-white">
                                <i class="bi bi-bullseye me-1"></i> Target Hafalan Hari Ini
                            </a>
                        @elseif(auth()->user()->isMentor())
                            <a href="{{ route('mentor.dashboard') }}" class="btn-editorial-primary">
                                <i class="bi bi-mortarboard me-1"></i> Dashboard Mengajar
                            </a>
                            <a href="{{ route('mentor.sessions.index') }}" class="btn-editorial-secondary text-white border-white">
                                <i class="bi bi-calendar3 me-1"></i> Jadwal Mengajar
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn-editorial-primary">
                                <i class="bi bi-gear-fill me-1"></i> Dashboard Admin
                            </a>
                            <a href="{{ route('admin.enrollments.index') }}" class="btn-editorial-secondary text-white border-white">
                                <i class="bi bi-people-fill me-1"></i> Kelola Pendaftaran
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-editorial-primary">
                                <i class="bi bi-speedometer2 me-1"></i> Ke Dashboard
                            </a>
                        @endif
                    @else
                        <button type="button" class="btn-editorial-primary" data-bs-toggle="modal" data-bs-target="#trialModal">
                            <i class="bi bi-pencil-square me-1"></i> Daftar Gratis Sekarang
                        </button>
                        <button type="button" class="btn-editorial-secondary text-white border-white" data-bs-toggle="modal"
                            data-bs-target="#daftarModal">
                            <i class="bi bi-person-plus me-1"></i> Mulai Belajar
                        </button>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai program belajar AL-HIKMAH') }}"
                            class="btn-editorial-whatsapp" target="_blank" rel="noopener"
                            aria-label="Konsultasi program belajar AL-HIKMAH via WhatsApp">
                            <i class="bi bi-whatsapp me-1" aria-hidden="true"></i> Konsultasi via WhatsApp
                        </a>
                    @endauth
                </div>
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
