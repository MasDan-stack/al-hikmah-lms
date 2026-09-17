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

                        {{-- Notifikasi Khusus Orang Tua yang Sudah Terdaftar & Memiliki Program Berjalan --}}
                        @auth
                            @if(auth()->user()->isParent())
                                @php
                                    $parentUser = auth()->user();
                                    $parentProfile = $parentUser?->parentProfile;
                                    $activeEnrollments = collect();
                                    if ($parentProfile) {
                                        $studentIds = $parentProfile->students()->pluck('id');
                                        $activeEnrollments = \App\Models\Enrollment::with(['student.user', 'program'])
                                            ->whereIn('student_id', $studentIds)
                                            ->whereIn('status', [
                                                \App\Enums\EnrollmentStatus::ACTIVE->value,
                                                \App\Enums\EnrollmentStatus::CONFIRMED->value,
                                                \App\Enums\EnrollmentStatus::WAITING_CONFIRMATION->value,
                                                \App\Enums\EnrollmentStatus::WAITING_PAYMENT->value,
                                            ])
                                            ->latest()
                                            ->get();
                                    }
                                @endphp

                                @if($activeEnrollments->isNotEmpty())
                                    <div class="p-3 p-md-4 rounded-4 border mb-4 shadow-sm text-start" style="background: rgba(13, 122, 62, 0.05); border-color: rgba(13, 122, 62, 0.2) !important;">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="p-2 rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                                <i class="bi bi-info-circle-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-success mb-1">Status Bimbingan Ananda Saat Ini</h6>
                                                <p class="text-secondary small mb-2" style="line-height: 1.5;">
                                                    Alhamdulillah, ananda tercatat aktif mengikuti bimbingan:
                                                    @foreach($activeEnrollments as $enr)
                                                        <strong class="text-dark">{{ $enr->student?->getDisplayName() }}</strong> ({{ $enr->program?->name ?? 'Program Bimbingan' }})@if(!$loop->last), @endif
                                                    @endforeach.
                                                </p>
                                                <p class="text-secondary small mb-0" style="line-height: 1.5;">
                                                    Ayah/Bunda dapat mendaftarkan ananda ke program Tahfidz tambahan kapan saja. Namun, agar beban hafalan dan jadwal istirahat ananda tetap seimbang, kami menyarankan untuk berkonsultasi terlebih dahulu dengan koordinator kurikulum Al-Hikmah.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endauth

                        {{-- 2 Aksi Utama Berjenjang --}}
                        <div class="d-flex flex-wrap gap-3">
                            @auth
                                @if(auth()->user()->isParent())
                                    @if(isset($activeEnrollments) && $activeEnrollments->isNotEmpty())
                                        <a href="{{ wa_url("Assalamualaikum Admin Al-Hikmah, ananda sudah aktif di bimbingan Al-Hikmah. Saya ingin konsultasi penambahan program Tahfidz Al-Qur'an untuk ananda.") }}"
                                           target="_blank" rel="noopener noreferrer" class="btn_2" style="background: rgba(13, 122, 62, 0.1); color: #0d7a3e; border-color: #0d7a3e;">
                                            <i class="bi bi-whatsapp me-1"></i> Konsultasi via WhatsApp
                                        </a>
                                    @endif
                                    <button type="button" class="btn_1" data-bs-toggle="modal" data-bs-target="#tahfidzLoggedInModal">
                                        <i class="bi bi-book-half me-1"></i> Daftarkan Anak ke Tahfidz
                                    </button>
                                    <a href="{{ route('biaya') }}" class="btn_2">
                                        <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan &amp; Biaya
                                    </a>
                                @elseif(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="btn_1">
                                        <i class="bi bi-speedometer2 me-1"></i> Dashboard Admin
                                    </a>
                                    <a href="{{ route('biaya') }}" class="btn_2">
                                        <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="btn_1">
                                        <i class="bi bi-speedometer2 me-1"></i> Masuk ke Dashboard
                                    </a>
                                    <a href="{{ route('biaya') }}" class="btn_2">
                                        <i class="bi bi-info-circle me-1"></i> Lihat Rincian Biaya
                                    </a>
                                @endif
                            @else
                                {{-- Primary CTA (Konversi Tertinggi / Tanpa Beban Biaya Awal) --}}
                                <button type="button" class="btn_1" data-bs-toggle="modal" data-bs-target="#trialModal" data-focus="tahfidz_hafalan">
                                    <i class="bi bi-clock-history me-1"></i> Coba Sesi Uji Coba Gratis 15 Menit
                                </button>
                                {{-- Secondary CTA (Pendekatan Ramah via WhatsApp) --}}
                                <a href="{{ wa_url("Assalamualaikum Admin, saya ingin konsultasi mengenai program Tahfidz Al-Qur'an untuk ananda") }}"
                                   target="_blank" rel="noopener noreferrer" class="btn_2">
                                    <i class="bi bi-whatsapp me-1"></i> Konsultasi via WhatsApp
                                </a>
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
                <p class="lead text-white-50 max-w-700 mx-auto mb-4">Dari satu ayat, satu halaman, hingga satu juz, setiap langkah ikhtiar adalah kebaikan abadi.</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    @auth
                        @if(auth()->user()->isParent())
                            <button type="button" class="btn_1 bg-warning text-dark border-0 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#tahfidzLoggedInModal" style="background-image: none !important; background-color: #ffc107 !important; color: #1a1a2e !important;">
                                <i class="bi bi-book-half me-1"></i> Daftarkan Anak ke Tahfidz
                            </button>
                            <a href="{{ wa_url("Assalamualaikum Admin, saya ingin konsultasi mengenai program Tahfidz Al-Qur'an untuk ananda") }}"
                               target="_blank" rel="noopener noreferrer" class="btn_2 text-white border-white">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi via WhatsApp
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn_1 bg-warning text-dark border-0 fw-bold shadow" style="background-image: none !important; background-color: #ffc107 !important; color: #1a1a2e !important;">
                                <i class="bi bi-speedometer2 me-1"></i> Buka Dashboard
                            </a>
                        @endif
                    @else
                        <button type="button" class="btn_1 bg-warning text-dark border-0 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#trialModal" data-focus="tahfidz_hafalan" style="background-image: none !important; background-color: #ffc107 !important; color: #1a1a2e !important;">
                            <i class="bi bi-clock-history me-1"></i> Coba Sesi Uji Coba Gratis 15 Menit
                        </button>
                        <a href="{{ wa_url("Assalamualaikum Admin, saya ingin konsultasi mengenai program Tahfidz Al-Qur'an untuk ananda") }}"
                           target="_blank" rel="noopener noreferrer" class="btn_2 text-white border-white">
                            <i class="bi bi-whatsapp me-1"></i> Konsultasi via WhatsApp
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Form Partials -->
    @include('partials.modal-tahfidz-daftar')
    @include('partials.modal-tahfidz-logged-in')
@endsection
