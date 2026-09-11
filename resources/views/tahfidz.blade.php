@extends('layouts.landing')

@section('title', 'Program Tahfidz | AL-HIKMAH')
@section('description', 'Program Tahfidz AL-HIKMAH: Pendampingan menghafal Al-Qur\'an dengan setoran rutin, murajaah, dan target yang disesuaikan.')

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
                                    <a href="{{ route('biaya') }}" class="btn_2">
                                        <i class="bi bi-info-circle me-1"></i> Rincian Paket &amp; Biaya
                                    </a>
                                @elseif(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="btn_1">
                                        Dashboard Admin <i class="bi bi-speedometer2 ms-1"></i>
                                    </a>
                                    <a href="{{ route('biaya') }}" class="btn_2">
                                        <i class="bi bi-info-circle me-1"></i> Halaman Biaya (Admin)
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="btn_1">
                                        Masuk ke Dashboard <i class="bi bi-speedometer2 ms-1"></i>
                                    </a>
                                @endif
                            @else
                                <button type="button" class="btn_1" data-bs-toggle="modal" data-bs-target="#tahfidzDaftarModal">
                                    Daftar Program Tahfidz <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                                <a href="{{ route('register') }}" class="btn_2">
                                    <i class="bi bi-person-plus me-1"></i> Daftar Akun Wali Santri
                                </a>
                                <button type="button" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#trialModal">
                                    <i class="bi bi-gift-fill me-1"></i> Daftar Gratis
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Program Unggulan: Mahir Tahfidz Al-Qur'an (18x / Bulan) -->
            <div class="row justify-content-center mt-5 pt-3" data-reveal>
                <div class="col-lg-10">
                    <div class="card border-2 border-success shadow-sm rounded-4 overflow-hidden" style="background: var(--card-bg);">
                        <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: rgba(13, 122, 62, 0.06);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bi bi-award-fill fs-5"></i>
                                </div>
                                <div>
                                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-1 small mb-1">Program Unggulan Eksklusif</span>
                                    <h4 class="fw-bold mb-0 text-success fs-5">Mahir Tahfidz Al-Qur'an (18 Pertemuan / Bulan)</h4>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fs-4 fw-bold text-success">Rp 2.700.000 <span class="text-muted small fs-6">/ bulan</span></div>
                                <small class="text-muted" style="font-size: 0.78rem;">Flat Rp 150.000 / sesi privat (90 Menit)</small>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-7">
                                    <p class="text-secondary small mb-3">
                                        Program halaqah privat intensif bagi santri yang berazam menghafal Al-Qur'an secara mutqin dengan bimbingan talaqqi 1-on-1 bersama ustadz/ustadzah hafidz/hafidzah pilihan.
                                    </p>
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-2 small text-secondary">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span>18 Sesi Privat (90 Menit)</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-2 small text-secondary">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span>Talaqqi Hafalan Baru</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-2 small text-secondary">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span>Murajaah Terjadwal Mutqin</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="d-flex align-items-center gap-2 small text-secondary">
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <span>Buku Mutaba'ah &amp; Rapor</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-end">
                                    <div class="p-3 rounded-3 bg-light border text-start mb-3">
                                        <div class="small fw-semibold text-success mb-1"><i class="bi bi-heart-fill me-1"></i> Amanah &amp; Keberkahan Bersama</div>
                                        <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                            Setiap langkah bimbingan ananda turut mendukung syiar dakwah Al-Qur'an dan kepedulian bagi santri yatim.
                                        </p>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                        @auth
                                            @if (auth()->user()->isParent() || auth()->user()->isAdmin())
                                                <a href="{{ route('biaya') }}" class="btn btn-primary-custom py-2 px-3 rounded-pill fw-bold shadow-sm">
                                                    <i class="bi bi-pencil-square me-1"></i> Pilih &amp; Mulai Program
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('register') }}" class="btn btn-primary-custom py-2 px-3 rounded-pill fw-bold shadow-sm">
                                                <i class="bi bi-person-plus me-1"></i> Daftar Sekarang
                                            </a>
                                        @endauth
                                        <button type="button" class="btn btn-outline-custom py-2 px-3 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#trialModal">
                                            <i class="bi bi-gift me-1"></i> Daftar Gratis
                                        </button>
                                    </div>
                                </div>
                            </div>
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
                <p class="lead text-white-50 max-w-700 mx-auto mb-4">Dari satu ayat, satu halaman, hingga satu juz, setiap langkah ikhtiar adalah kebaikan abadi.</p>
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
