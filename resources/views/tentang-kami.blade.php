@extends('layouts.landing')

@section('title', 'Tentang Kami | AL-HIKMAH Bimbingan Al-Qur\'an')
@section('description', 'Mengenal komitmen AL-HIKMAH dalam mendampingi anak usia 10–15 tahun belajar Al-Qur\'an dengan metode talaqqi privat, tartil tajwid, dan adab Islami.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. BREADCRUMB / HERO SECTION -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg page-hero" aria-label="Header Profil Lembaga">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <nav aria-label="breadcrumb" class="mb-3">
                            <ol class="breadcrumb justify-content-center mb-0 small">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
                                <li class="breadcrumb-item text-muted">Profil Lembaga</li>
                                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Tentang Kami</li>
                            </ol>
                        </nav>

                        <div class="section-badge mx-auto mb-3">
                            <i class="bi bi-compass"></i>
                            <span>Profil Lembaga</span>
                        </div>

                        <h1 class="section-title">Mendampingi Buah Hati Belajar Al-Qur'an dengan Adab dan Tartil</h1>
                        <p class="section-description mx-auto">
                            Lembaga bimbingan Al-Qur'an privat untuk anak dan remaja usia 10 hingga 15 tahun bersama <span class="text-gradient fw-semibold">AL-HIKMAH</span>, memadukan ketepatan kaidah tajwid, pembiasaan akhlak Islami, serta transparansi mutaba'ah bagi orang tua.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. KISAH KAMI & LATAR BELAKANG -->
    <!-- ============================================ -->
    <section id="profil" class="section-padding" aria-label="Latar Belakang AL-HIKMAH">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-reveal>
                    <div class="about-image-wrapper">
                        <div class="about-decoration" aria-hidden="true"></div>
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Pendampingan bimbingan belajar Al-Qur'an anak"
                             onerror="this.src='{{ asset('assets/img/1.jpg') }}'" class="about-image img-fluid">
                    </div>
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="150">
                    <div class="ps-lg-3">
                        <div class="section-badge mb-2"><i class="bi bi-heart-pulse"></i> Latar Belakang</div>
                        <h2 class="section-title text-start mb-3">
                            Kebutuhan Bimbingan Mengaji yang Dekat, Sabar, dan Terarah
                        </h2>
                        <p class="text-secondary mb-3">
                            Setiap orang tua mendambakan putra-putrinya mampu melafalkan Al-Qur'an dengan fasih, menguasai kaidah tajwid, serta menyelesaikan hafalan juz pilihan. Namun dalam praktiknya, banyak keluarga menemui kendala nyata: keterbatasan waktu orang tua untuk menyimak setoran hafalan secara konsisten, sulitnya mencari guru mengaji privat yang bersyahadah dan sabar menghadapi karakter anak, atau metode pengajaran yang terlalu kaku sehingga anak cepat jenuh.
                        </p>
                        <p class="text-secondary mb-3">
                            AL-HIKMAH dirintis untuk menjawab kebutuhan tersebut. Kami menyelenggarakan pendampingan Al-Qur'an privat secara bertahap, di mana setiap santri dibimbing secara personal sesuai kecepatan belajarnya tanpa tekanan yang membuat anak enggan membaca Al-Qur'an.
                        </p>
                        <div class="quote-wrapper mt-4" data-reveal data-reveal-delay="200">
                            <div class="quotes">
                                <p class="mb-0 text-secondary small fst-italic">
                                    "Fokus utama pendampingan kami bukan sekadar mengejar banyaknya hafalan, melainkan ketepatan makhraj huruf, adab santri terhadap Al-Qur'an, dan kenyamanan anak dalam menjalani proses belajar."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Rekapitulasi Data Lembaga (lms-stat-card from style.css) -->
            <div class="row g-4 justify-content-center mt-5 pt-3">
                <div class="col-md-4 col-sm-6" data-reveal>
                    <div class="lms-stat-card text-center h-100">
                        <div class="stat-icon-wrap mx-auto mb-2 bg-success-subtle text-success d-flex align-items-center justify-content-center">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <div class="stat-value text-primary mb-1">{{ $totalStudents }}</div>
                        <div class="stat-label text-muted">Santri Terdaftar</div>
                        <div class="stat-meta mt-1">Mengikuti bimbingan privat</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6" data-reveal data-reveal-delay="100">
                    <div class="lms-stat-card text-center h-100">
                        <div class="stat-icon-wrap mx-auto mb-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-badge-fill fs-5"></i>
                        </div>
                        <div class="stat-value text-primary mb-1">{{ $totalMentors }}</div>
                        <div class="stat-label text-muted">Pendamping Aktif</div>
                        <div class="stat-meta mt-1">Tersyahadah dan terverifikasi</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6" data-reveal data-reveal-delay="200">
                    <div class="lms-stat-card text-center h-100">
                        <div class="stat-icon-wrap mx-auto mb-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                            <i class="bi bi-journal-bookmark-fill fs-5"></i>
                        </div>
                        <div class="stat-value text-primary mb-1">{{ $totalPrograms }}</div>
                        <div class="stat-label text-muted">Program Belajar</div>
                        <div class="stat-meta mt-1">Talaqqi, tahsin, dan tahfidz</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. TIGA PENDEKATAN UTAMA / FILOSOFI (why-card from style.css) -->
    <!-- Anchor #filosofi directly matches Navbar Dropdown -->
    <!-- ============================================ -->
    <section id="filosofi" class="section-padding section-alt" aria-label="Metode Pembelajaran">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-book-half"></i> Metode Bimbingan</div>
                    <h2 class="section-title">Tiga Pendekatan Utama dalam Setiap Sesi</h2>
                    <p class="section-description mx-auto">
                        Kurikulum bimbingan kami dirancang bertahap agar santri memahami kaidah bacaan secara benar sebelum melangkah ke hafalan baru.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="why-card text-center">
                        <div class="why-icon" aria-hidden="true">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h4>Kaidah Tajwid &amp; Makhraj</h4>
                        <p>Santri dibimbing melatih ketepatan artikulasi huruf hijaiyah dan hukum tajwid aplikatif pada setiap ayat, sehingga terbiasa membaca secara tartil.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="why-card text-center">
                        <div class="why-icon" aria-hidden="true">
                            <i class="bi bi-person-video3"></i>
                        </div>
                        <h4>Talaqqi Privat 1-on-1</h4>
                        <p>Satu guru mendampingi satu santri secara intensif. Guru menyimak langsung bacaan, memperbaiki kekeliruan seketika, dan membimbing ritme belajar anak secara personal.</p>
                    </div>
                </div>
                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="why-card text-center">
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
    <!-- 4. STANDAR KUALIFIKASI GURU / NILAI UTAMA (nilai-card from style.css) -->
    <!-- Anchor #nilai directly matches Navbar Dropdown -->
    <!-- ============================================ -->
    <section id="nilai" class="section-padding" aria-label="Standar Pengajar">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-reveal>
                    <div class="section-badge mx-auto mb-2"><i class="bi bi-award"></i> Kualifikasi Pendidik</div>
                    <h2 class="section-title">Standar Pengajar yang Terkurasi</h2>
                    <p class="section-description mx-auto">
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
    <!-- 5. TRANSPARANSI BAGI ORANG TUA (harapan-list & custom buttons from style.css) -->
    <!-- ============================================ -->
    <section id="wali" class="section-padding section-alt" aria-label="Dukungan untuk Orang Tua">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5" data-reveal>
                    <div class="section-badge mb-2"><i class="bi bi-display"></i> Portal Terpadu</div>
                    <h2 class="section-title text-start mb-3">
                        Transparansi Belajar yang Memudahkan Orang Tua
                    </h2>
                    <p class="text-secondary mb-4">
                        Kami percaya keberhasilan belajar Al-Qur'an lahir dari kerja sama yang baik antara guru dan orang tua. Seluruh catatan pembelajaran disajikan secara terbuka sehingga orang tua dapat memantau setiap perkembangan ananda dari rumah.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ wa_url('Assalamualaikum panitia AL-HIKMAH, saya ingin berkonsultasi mengenai bimbingan mengaji untuk anak.') }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="btn-primary-custom">
                            <i class="bi bi-whatsapp me-2"></i> Konsultasi Program
                        </a>
                        <a href="{{ route('biaya') }}" class="btn-outline-custom">
                            <i class="bi bi-tag me-2"></i> Lihat Paket Belajar
                        </a>
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
    <!-- 6. AJAKAN BERGABUNG / KONSULTASI (cta-section from style.css) -->
    <!-- ============================================ -->
    <section id="konsultasi" class="cta-section text-center" aria-label="Konsultasi Bimbingan">
        <div class="cta-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="cta-content" data-reveal>
                <div class="cta-icon" aria-hidden="true">
                    <i class="bi bi-chat-square-heart"></i>
                </div>
                <h2 class="cta-title">
                    Ingin Mengonsultasikan Kebutuhan<br>
                    <span class="text-gradient-light">Belajar Ananda?</span>
                </h2>
                <p class="cta-subtitle">
                    Tim kurikulum AL-HIKMAH siap berdiskusi bersama Anda untuk memetakan tingkat bacaan anak serta merekomendasikan jadwal dan guru pembimbing yang tepat.
                </p>
                <div class="cta-buttons">
                    <a href="{{ wa_url('Assalamualaikum panitia AL-HIKMAH, saya ingin berkonsultasi mengenai bimbingan mengaji untuk anak.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="btn_1 bg-white text-success shadow text-nowrap"
                       style="background-image: none !important; background-color: #ffffff !important; color: #0d7a3e !important;">
                        <i class="bi bi-whatsapp me-2"></i> Buka Percakapan WhatsApp
                    </a>
                    <a href="{{ route('program') }}" class="btn-outline-light-custom">
                        <i class="bi bi-journal-bookmark me-2"></i> Telusuri Pilihan Program
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
