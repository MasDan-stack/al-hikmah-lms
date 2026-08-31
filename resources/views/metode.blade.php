@extends('layouts.landing')

@section('title', 'Metode Belajar | AL-HIKMAH')
@section('description', 'Metode belajar AL-HIKMAH — Online, Offline (Home Visit), dan Hybrid. Sistem pendampingan fleksibel untuk keluarga.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. ETRAIN BREADCRUMB HEADER -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg" aria-label="Header Metode Belajar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-grid-3x3-gap-fill"></i> Metode Pembelajaran</div>
                        <h2>Cara Kami <span class="text-gradient">Mendampingi</span></h2>
                        <p>Pendekatan personal, hangat, dan fleksibel untuk kenyamanan belajar santri dan keluarga di rumah.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. PILIHAN METODE BELAJAR -->
    <!-- ============================================ -->
    <section class="feature_part py-5" aria-label="Metode Belajar">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-reveal>
                    <div class="single_feature text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="single_feature_icon mx-auto mb-3">
                                <i class="bi bi-laptop"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Online Learning</h4>
                            <p class="mb-4">Belajar interaktif dari mana saja via platform video conference berkualitas tinggi.</p>
                            <ul class="list-unstyled text-start small mb-4">
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Pendampingan belajar fleksibel</span></li>
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Akses tanpa batas geografis</span></li>
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Rekaman &amp; evaluasi berkala</span></li>
                            </ul>
                        </div>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Online') }}"
                           class="btn_2 w-100 text-center mt-2" target="_blank">
                            <i class="bi bi-whatsapp me-1"></i> Pilih Online
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="single_feature featured-recommendation text-center h-100 d-flex flex-column justify-content-between shadow">
                        <div>
                            <div class="badge bg-success text-white mb-3 px-3 py-2 rounded-pill small">Rekomendasi Utama</div>
                            <div class="single_feature_icon mx-auto mb-3">
                                <i class="bi bi-house-door"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Offline (Home Visit)</h4>
                            <p class="mb-4">Guru privat datang langsung ke rumah dengan kenyamanan dan pendampingan tatap muka penuh.</p>
                            <ul class="list-unstyled text-start small mb-4">
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Koreksi makhraj sangat presisi</span></li>
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Suasana belajar intim di rumah</span></li>
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Hubungan guru-santri lebih dekat</span></li>
                            </ul>
                        </div>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Offline (Home Visit)') }}"
                           class="btn_1 w-100 text-center mt-2" target="_blank">
                            <i class="bi bi-whatsapp me-1"></i> Pilih Home Visit
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="single_feature text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="single_feature_icon mx-auto mb-3">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Hybrid Flexible</h4>
                            <p class="mb-4">Kombinasi fleksibel antara tatap muka langsung dan sesi daring sesuai agenda keluarga.</p>
                            <ul class="list-unstyled text-start small mb-4">
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Perpaduan online &amp; offline</span></li>
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Fleksibilitas jadwal tinggi</span></li>
                                <li class="mb-2 d-flex align-items-center"><i class="bi bi-check-circle-fill text-success me-2 flex-shrink-0"></i> <span>Tetap terpantau secara konsisten</span></li>
                            </ul>
                        </div>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Hybrid') }}"
                           class="btn_2 w-100 text-center mt-2" target="_blank">
                            <i class="bi bi-whatsapp me-1"></i> Pilih Hybrid
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-reveal>
                <a href="{{ route('program') }}" class="btn_2 me-2">
                    <i class="bi bi-journal-bookmark me-1"></i> Lihat Program
                </a>
                @auth
                    @if (auth()->user()->isParent())
                        <a href="{{ route('biaya') }}" class="btn_1">
                            <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan
                        </a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('biaya') }}" class="btn_1">
                            <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. SISTEM INTENSITAS PENDAMPINGAN -->
    <!-- ============================================ -->
    <section class="learning_part py-5" aria-label="Sistem Pendampingan">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <div class="section-badge mx-auto mb-2"><i class="bi bi-calendar-range"></i> Intensitas Belajar</div>
                <h2 class="section-title">Sistem <span class="text-gradient">Pendampingan</span></h2>
                <p class="section-description mx-auto">Yang kami kejar bukan sekadar kecepatan, tetapi keistiqamahan dalam belajar.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-3 col-sm-6" data-reveal>
                    <div class="single_feature text-center p-4 h-100">
                        <div class="fs-1 text-success mb-2"><i class="bi bi-1-circle"></i></div>
                        <h5 class="fw-bold mb-2">1x Seminggu</h5>
                        <p class="small mb-0">Membangun ritme dan kebiasaan awal mencintai Al-Qur'an.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" data-reveal data-reveal-delay="100">
                    <div class="single_feature text-center p-4 h-100">
                        <div class="fs-1 text-primary mb-2"><i class="bi bi-2-circle"></i></div>
                        <h5 class="fw-bold mb-2">2–3x Seminggu</h5>
                        <p class="small mb-0">Untuk keluarga yang menginginkan proses bimbingan lebih intensif.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" data-reveal data-reveal-delay="200">
                    <div class="single_feature text-center p-4 h-100">
                        <div class="fs-1 text-warning mb-2"><i class="bi bi-calendar2-check"></i></div>
                        <h5 class="fw-bold mb-2">Jadwal Khusus</h5>
                        <p class="small mb-0">Waktu fleksibel yang dapat disesuaikan dengan aktivitas santri.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" data-reveal data-reveal-delay="300">
                    <div class="single_feature text-center p-4 h-100">
                        <div class="fs-1 text-info mb-2"><i class="bi bi-person-check"></i></div>
                        <h5 class="fw-bold mb-2">Personal 1-on-1</h5>
                        <p class="small mb-0">Perhatian eksklusif sesuai kecepatan belajar masing-masing anak.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. JABODETABEK BANNER -->
    <!-- ============================================ -->
    <section class="jabodetabek-ribbon py-4" aria-label="Informasi layanan Jabodetabek">
        <div class="container">
            <div class="jabodetabek-content d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start" data-reveal>
                <div class="d-flex align-items-center gap-3">
                    <div class="jabodetabek-icon">
                        <i class="bi bi-geo-alt-fill text-white"></i>
                    </div>
                    <div class="jabodetabek-text">
                        <h4 class="fw-bold mb-1 text-white">📍 Melayani Area <strong>Jabodetabek</strong> &amp; Sekitarnya</h4>
                        <p class="mb-0 text-white" style="opacity: 0.9;">Pendamping datang langsung ke rumah Anda. Tersedia juga kelas online untuk seluruh wilayah Indonesia.</p>
                    </div>
                </div>
                <a href="{{ wa_url('Assalamualaikum, apakah area saya tercakup AL-HIKMAH?') }}"
                   class="btn_1 bg-white text-success shadow text-nowrap"
                   style="background-image: none !important; background-color: #ffffff !important; color: #0d7a3e !important;"
                   target="_blank">
                    <i class="bi bi-whatsapp me-1"></i> Cek Ketersediaan Area
                </a>
            </div>
        </div>
    </section>
@endsection

