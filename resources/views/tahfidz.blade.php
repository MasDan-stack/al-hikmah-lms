@extends('layouts.landing')

@section('title', 'Program Tahfidz | AL-HIKMAH')
@section('description', 'Program Tahfidz AL-HIKMAH — Pendampingan menghafal Al-Qur\'an dengan setoran rutin, murajaah, dan target yang disesuaikan.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. ETRAIN BREADCRUMB HEADER -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg" aria-label="Header Program Tahfidz">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-clipboard2-pulse"></i> Program Unggulan</div>
                        <h2>Program <span class="text-gradient">Tahfidz Al-Qur'an</span></h2>
                        <p>Menghafal bukan sekadar mengingat, tetapi menjaga dan menghidupkan firman Allah dalam hati.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. ETRAIN LEARNING / TAHFIDZ OVERVIEW -->
    <!-- ============================================ -->
    <section class="learning_part py-5" aria-label="Tahfidz Overview">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0" data-reveal>
                    <div class="learning_img">
                        <img src="{{ asset('assets/img/62.jpg') }}" alt="Program Tahfidz Al-Qur'an AL-HIKMAH"
                             onerror="this.src='{{ asset('assets/img/etrain/learning_img.png') }}'">
                    </div>
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="150">
                    <div class="ps-lg-4">
                        <div class="section-badge mb-2"><i class="bi bi-bookmark-star-fill"></i> Metode Menjaga Hafalan</div>
                        <h2 class="section-title text-start mb-3">
                            Menghafal Bukan Sekadar <span class="text-gradient">Mengingat</span>, Tetapi Menjaga
                        </h2>
                        <p class="text-secondary mb-4">
                            Menghafal Al-Qur'an adalah perjalanan panjang yang membutuhkan kesabaran, keikhlasan, dan bimbingan guru yang telaten. Kami mendampingi para santri dengan sistem setoran rutin, penguatan murajaah, serta target hafalan yang disesuaikan dengan kapasitas setiap anak.
                        </p>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 border d-flex align-items-center gap-3 shadow-sm" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                                    <div class="p-2 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                    </div>
                                    <span class="small fw-semibold" style="color: var(--text-primary);">Setoran Hafalan Rutin</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 border d-flex align-items-center gap-3 shadow-sm" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                                    <div class="p-2 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                    </div>
                                    <span class="small fw-semibold" style="color: var(--text-primary);">Murajaah Terstruktur</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 border d-flex align-items-center gap-3 shadow-sm" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                                    <div class="p-2 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                    </div>
                                    <span class="small fw-semibold" style="color: var(--text-primary);">Target Sesuai Kemampuan</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 border d-flex align-items-center gap-3 shadow-sm" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                                    <div class="p-2 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-check-circle-fill fs-5"></i>
                                    </div>
                                    <span class="small fw-semibold" style="color: var(--text-primary);">Evaluasi Tajwid &amp; Kelancaran</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            @auth
                                @if(auth()->user()->isParent())
                                    <button type="button" class="btn_1" data-bs-toggle="modal" data-bs-target="#tahfidzLoggedInModal">
                                        Daftar Program Tahfidz <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                @else
                                    @php
                                        $dashRoute = match(true) {
                                            auth()->user()->isAdmin() => route('admin.dashboard'),
                                            auth()->user()->isMentor() => route('mentor.dashboard'),
                                            auth()->user()->isStudent() => route('student.dashboard'),
                                            default => route('parent.dashboard'),
                                        };
                                    @endphp
                                    <a href="{{ $dashRoute }}" class="btn_1">
                                        Masuk ke Dashboard <i class="bi bi-speedometer2 ms-1"></i>
                                    </a>
                                @endif
                            @else
                                <button type="button" class="btn_1" data-bs-toggle="modal" data-bs-target="#tahfidzDaftarModal">
                                    Daftar Program Tahfidz <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            @endauth

                            @auth
                                @if (auth()->user()->isParent())
                                    <a href="{{ route('biaya') }}" class="btn_2">
                                        <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan
                                    </a>
                                @elseif (auth()->user()->isAdmin())
                                    <a href="{{ route('biaya') }}" class="btn_2">
                                        <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)
                                    </a>
                                @endif
                            @endauth

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. ETRAIN CALL TO ACTION BANNER -->
    <!-- ============================================ -->
    <section class="cta-section text-center" aria-label="CTA Tahfidz">
        <div class="cta-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="cta-content" data-reveal>
                <div class="cta-icon"><i class="bi bi-bookmark-star-fill"></i></div>
                <h2 class="display-6 fw-bold mb-3 text-white">Mulai Perjalanan <span class="text-warning">Menghafal Al-Qur'an</span></h2>
                <p class="lead text-white-50 max-w-700 mx-auto mb-4">Dari satu ayat, satu halaman, hingga satu juz — setiap langkah ikhtiar adalah kebaikan abadi.</p>
                @auth
                    @if(auth()->user()->isParent())
                        <button type="button" class="btn_1 bg-warning text-dark border-0 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#tahfidzLoggedInModal" style="background-image: none !important; background-color: #ffc107 !important; color: #1a1a2e !important;">
                            <i class="bi bi-book-half me-1"></i> Daftarkan Anak ke Tahfidz
                        </button>
                    @endif
                @else
                    <button type="button" class="btn_1 bg-warning text-dark border-0 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#tahfidzDaftarModal" style="background-image: none !important; background-color: #ffc107 !important; color: #1a1a2e !important;">
                        <i class="bi bi-pencil-square me-1"></i> Konsultasi &amp; Daftar Tahfidz
                    </button>
                @endauth
            </div>
        </div>
    </section>

    <!-- Modal Form Partials -->
    @include('partials.modal-tahfidz-daftar')
    @include('partials.modal-tahfidz-logged-in')
@endsection
