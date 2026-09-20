@extends('layouts.landing')

@section('title', 'Tentang Kami | AL-HIKMAH Bimbingan Al-Qur\'an')
@section('description', 'Mengenal komitmen AL-HIKMAH dalam mendampingi buah hati belajar Al-Qur\'an dengan metode talaqqi privat, tartil tajwid, dan adab Islami.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. BREADCRUMB / HERO SECTION -->
    <!-- ============================================ -->
    <section class="editorial-page-header page-hero" aria-label="Header Profil Lembaga">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center" data-reveal>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item text-muted">Profil Lembaga</li>
                            <li class="breadcrumb-item active" aria-current="page">Tentang Kami</li>
                        </ol>
                    </nav>

                    <div class="editorial-badge mx-auto">
                        <i class="bi bi-compass"></i>
                        <span>Profil &amp; Amanah Lembaga</span>
                    </div>

                    <h1 class="editorial-title">Mendampingi Buah Hati Belajar Al-Qur'an dengan Adab dan Tartil</h1>
                    <p class="editorial-subtitle mx-auto">
                        Lembaga bimbingan Al-Qur'an privat untuk anak dan remaja bersama <span class="fw-bold text-emerald-deep">AL-HIKMAH</span>, memadukan ketepatan kaidah tajwid, pembiasaan akhlak Islami, serta transparansi mutaba'ah bagi orang tua.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. KISAH KAMI & LATAR BELAKANG -->
    <!-- ============================================ -->
    <section id="profil" class="py-5" aria-label="Latar Belakang AL-HIKMAH">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-reveal>
                    <div class="about-image-wrapper editorial-hero-frame position-relative">
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Pendampingan bimbingan belajar Al-Qur'an anak"
                             onerror="this.src='{{ asset('assets/img/1.jpg') }}'" class="about-image img-fluid">
                        <div class="position-absolute bottom-0 start-0 m-3 editorial-floating-badge text-start d-none d-sm-flex align-items-center gap-2.5" style="max-width: 290px;">
                            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 38px; height: 38px; background: var(--primary-lighter); color: var(--primary);">
                                <i class="bi bi-heart-fill fs-6" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-heading">Belajar Tanpa Rasa Tertekan</div>
                                <div class="text-secondary" style="font-size: 0.72rem; line-height: 1.3;">Menghargai ritme unik setiap ananda</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="150">
                    <div class="ps-lg-4">
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                            <i class="bi bi-heart-pulse-fill me-1"></i> Latar Belakang
                        </span>
                        <h2 class="editorial-title text-start mb-3">
                            Kebutuhan Bimbingan Mengaji yang Dekat, Sabar, dan Terarah
                        </h2>
                        <p class="text-secondary mb-3" style="line-height: 1.7;">
                            Setiap orang tua mendambakan putra-putrinya mampu melafalkan Al-Qur'an dengan fasih, menguasai kaidah tajwid, serta menyelesaikan hafalan juz pilihan. Namun dalam praktiknya, banyak keluarga menemui kendala nyata: keterbatasan waktu orang tua untuk menyimak setoran hafalan secara konsisten, sulitnya mencari guru mengaji privat yang bersyahadah dan sabar menghadapi karakter anak, atau metode pengajaran yang terlalu kaku sehingga anak cepat jenuh.
                        </p>
                        <p class="text-secondary mb-4" style="line-height: 1.7;">
                            AL-HIKMAH dirintis untuk menjawab kebutuhan tersebut. Kami menyelenggarakan pendampingan Al-Qur'an privat secara bertahap, di mana setiap santri dibimbing secara personal sesuai kecepatan belajarnya tanpa tekanan yang membuat anak enggan membaca Al-Qur'an.
                        </p>
                        <div class="p-3 p-md-3.5 rounded-3 border bg-body-tertiary d-flex gap-3 align-items-start">
                            <span class="text-primary mt-1" style="font-size: 1.25rem; line-height: 1;"><i class="bi bi-quote"></i></span>
                            <p class="mb-0 text-secondary small fst-italic" style="line-height: 1.6;">
                                &ldquo;Fokus utama pendampingan kami bukan sekadar mengejar banyaknya hafalan, melainkan ketepatan makhraj huruf, adab santri terhadap Al-Qur'an, dan kenyamanan anak dalam menjalani proses belajar.&rdquo;
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Rekapitulasi Data Lembaga -->
            <div class="row g-3 justify-content-center mt-5 pt-2">
                <div class="col-4 col-md-4" data-reveal>
                    <div class="lms-stat-card text-center h-100 p-3 p-md-4">
                        <div class="editorial-icon-badge mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1.1rem;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-value tnum-price text-heading mb-1" style="font-size: clamp(1.35rem, 2.5vw, 1.95rem);">{{ $totalStudents }}</div>
                        <div class="stat-label text-muted small fw-semibold">Santri Terdaftar</div>
                        <div class="stat-meta text-secondary small d-none d-md-block mt-1">Mengikuti bimbingan privat aktif</div>
                    </div>
                </div>
                <div class="col-4 col-md-4" data-reveal data-reveal-delay="100">
                    <div class="lms-stat-card text-center h-100 p-3 p-md-4">
                        <div class="editorial-icon-badge mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1.1rem;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div class="stat-value tnum-price text-heading mb-1" style="font-size: clamp(1.35rem, 2.5vw, 1.95rem);">{{ $totalMentors }}</div>
                        <div class="stat-label text-muted small fw-semibold">Pendamping Aktif</div>
                        <div class="stat-meta text-secondary small d-none d-md-block mt-1">Ustadz & ustadzah terkurasi</div>
                    </div>
                </div>
                <div class="col-4 col-md-4" data-reveal data-reveal-delay="200">
                    <div class="lms-stat-card text-center h-100 p-3 p-md-4">
                        <div class="editorial-icon-badge mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1.1rem;">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <div class="stat-value tnum-price text-heading mb-1" style="font-size: clamp(1.35rem, 2.5vw, 1.95rem);">{{ $totalPrograms }}</div>
                        <div class="stat-label text-muted small fw-semibold">Program Belajar</div>
                        <div class="stat-meta text-secondary small d-none d-md-block mt-1">Talaqqi, tahsin, dan tahfidz</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. TIGA PENDEKATAN UTAMA / FILOSOFI -->
    <!-- Anchor #filosofi directly matches Navbar Dropdown -->
    <!-- ============================================ -->
    <section id="filosofi" class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Metode Pembelajaran">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-reveal>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                        <i class="bi bi-book-half me-1"></i> Metode Bimbingan
                    </span>
                    <h2 class="editorial-title mb-2">Tiga Pendekatan Utama dalam Setiap Sesi</h2>
                    <p class="editorial-subtitle mx-auto">
                        Kurikulum bimbingan kami dirancang bertahap agar santri memahami kaidah bacaan secara benar sebelum melangkah ke hafalan baru.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="why-card">
                        <div class="why-icon" aria-hidden="true">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h4>Kaidah Tajwid &amp; Makhraj</h4>
                        <p>Santri dibimbing melatih ketepatan artikulasi huruf hijaiyah dan hukum tajwid aplikatif pada setiap ayat, sehingga terbiasa membaca secara tartil.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="why-card">
                        <div class="why-icon" aria-hidden="true">
                            <i class="bi bi-person-video3"></i>
                        </div>
                        <h4>Talaqqi Privat 1-on-1</h4>
                        <p>Satu guru mendampingi satu santri secara intensif. Guru menyimak langsung bacaan, memperbaiki kekeliruan seketika, dan membimbing ritme belajar anak secara personal.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="why-card">
                        <div class="why-icon" aria-hidden="true">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <h4>Pembiasaan Adab Harian</h4>
                        <p>Setiap pertemuan diawali dengan doa adab belajar, menjaga wudhu dan kesucian mushaf, serta membiasakan santri menghormati guru dan orang tua.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. STANDAR KUALIFIKASI GURU / NILAI UTAMA -->
    <!-- Anchor #nilai directly matches Navbar Dropdown -->
    <!-- ============================================ -->
    <section id="nilai" class="py-5" aria-label="Standar Pengajar">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-reveal>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                        <i class="bi bi-award me-1"></i> Kualifikasi Pendidik
                    </span>
                    <h2 class="editorial-title mb-2">Standar Pengajar yang Terkurasi</h2>
                    <p class="editorial-subtitle mx-auto">
                        Kami memastikan setiap pendamping yang berinteraksi dengan santri memiliki pemahaman keilmuan Al-Qur'an yang sahih serta pendekatan bimbingan yang beretika.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="nilai-card">
                        <div class="nilai-icon" aria-hidden="true">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <h4>Kompetensi Tajwid &amp; Syahadah</h4>
                        <p>Guru memiliki latar belakang pesantren atau program studi Islam dengan penguasaan bacaan tartil dan sanad atau syahadah resmi.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="nilai-card">
                        <div class="nilai-icon" aria-hidden="true">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4>Lolos 5 Tahap Seleksi</h4>
                        <p>Setiap calon guru melewati verifikasi portofolio, asesmen tertulis kaidah tajwid, wawancara komitmen, serta simulasi microteaching mengajar anak.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="nilai-card">
                        <div class="nilai-icon" aria-hidden="true">
                            <i class="bi bi-emoji-smile"></i>
                        </div>
                        <h4>Pendekatan Ramah Anak</h4>
                        <p>Guru dilatih untuk membimbing dengan sabar, mendengar kendala santri saat membaca, dan memberikan apresiasi yang menumbuhkan rasa percaya diri anak.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5. TRANSPARANSI BAGI ORANG TUA -->
    <!-- ============================================ -->
    <section id="wali" class="py-5 bg-body-tertiary border-top border-bottom" aria-label="Dukungan untuk Orang Tua">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5" data-reveal>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 small fw-semibold mb-2 d-inline-block">
                        <i class="bi bi-display me-1"></i> Portal Terpadu
                    </span>
                    <h2 class="editorial-title text-start mb-3">
                        Transparansi Belajar yang Memudahkan Orang Tua
                    </h2>
                    <p class="text-secondary mb-4" style="line-height: 1.7;">
                        Kami percaya keberhasilan belajar Al-Qur'an lahir dari kerja sama yang baik antara guru dan orang tua. Seluruh catatan pembelajaran disajikan secara terbuka sehingga orang tua dapat memantau setiap perkembangan ananda dari rumah.
                    </p>
                    <div class="d-flex flex-wrap gap-2.5 mt-4">
                        <a href="{{ wa_url('Assalamualaikum panitia AL-HIKMAH, saya ingin berkonsultasi mengenai bimbingan mengaji untuk anak.') }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="btn-editorial-whatsapp">
                            <i class="bi bi-whatsapp me-1"></i> Konsultasi Program
                        </a>
                        @auth
                            @if (auth()->user()->isParent() || auth()->user()->isAdmin())
                                <a href="{{ route('biaya') }}" class="btn-editorial-secondary">
                                    <i class="bi bi-tag me-1"></i> Lihat Paket Belajar
                                </a>
                            @endif
                        @else
                            <a href="{{ route('register') }}" class="btn-editorial-secondary">
                                <i class="bi bi-person-plus me-1"></i> Daftar Akun Wali Santri
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-7" data-reveal data-reveal-delay="150">
                    <div class="harapan-list">
                        <div class="harapan-item">
                            <div class="harapan-icon" aria-hidden="true">
                                <i class="bi bi-journal-check"></i>
                            </div>
                            <div class="harapan-text">
                                <h5>Jurnal Mutaba'ah Harian Digital</h5>
                                <p>Setiap sesi selesai, guru mencatat capaian surat, ayat, kelancaran tajwid, dan kehadiran yang dapat langsung ditinjau oleh wali santri.</p>
                            </div>
                        </div>
                        <div class="harapan-item">
                            <div class="harapan-icon" aria-hidden="true">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div class="harapan-text">
                                <h5>Jadwal Belajar yang Fleksibel</h5>
                                <p>Waktu sesi bimbingan disepakati bersama antara wali santri dan guru pembimbing, menyesuaikan jam luang anak setelah sekolah.</p>
                            </div>
                        </div>
                        <div class="harapan-item">
                            <div class="harapan-icon" aria-hidden="true">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <div class="harapan-text">
                                <h5>Ruang Komunikasi Rutin</h5>
                                <p>Orang tua dapat berdiskusi langsung dengan guru pendamping untuk memantau kendala anak atau menyelaraskan target hafalan.</p>
                            </div>
                        </div>
                        <div class="harapan-item">
                            <div class="harapan-icon" aria-hidden="true">
                                <i class="bi bi-award"></i>
                            </div>
                            <div class="harapan-text">
                                <h5>Evaluasi Capaian Berkala</h5>
                                <p>Evaluasi diselenggarakan setiap santri menuntaskan jilid tartil atau target juz tertentu untuk memastikan ketahanan hafalan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 6. AJAKAN BERGABUNG / KONSULTASI -->
    <!-- ============================================ -->
    <section id="konsultasi" class="cta-section text-center" aria-label="Konsultasi Bimbingan">
        <div class="cta-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="cta-content" data-reveal>
                <div class="cta-icon mx-auto mb-3" aria-hidden="true">
                    <i class="bi bi-chat-square-heart"></i>
                </div>
                <h2 class="editorial-title text-white mb-3">
                    Ingin Mengonsultasikan Kebutuhan<br>
                    <span style="color: #6ee7b7;">Belajar Ananda?</span>
                </h2>
                <p class="editorial-subtitle text-white mx-auto mb-4" style="max-width: 660px; color: rgba(255, 255, 255, 0.92) !important;">
                    Tim kurikulum AL-HIKMAH siap berdiskusi bersama Anda untuk memetakan tingkat bacaan anak serta merekomendasikan jadwal dan guru pembimbing yang tepat, tanpa tekanan untuk langsung mendaftar.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ wa_url('Assalamualaikum panitia AL-HIKMAH, saya ingin berkonsultasi mengenai bimbingan mengaji untuk anak.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="btn-editorial-primary btn-lg">
                        <i class="bi bi-whatsapp me-2"></i> Buka Percakapan WhatsApp
                    </a>
                    <a href="{{ route('program') }}" class="btn-editorial-secondary btn-lg">
                        <i class="bi bi-journal-bookmark me-2"></i> Telusuri Pilihan Program
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
