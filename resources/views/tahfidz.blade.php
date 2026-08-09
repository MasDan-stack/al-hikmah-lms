@extends('layouts.landing')

@section('title', 'Program Tahfidz | AL-HIKMAH')
@section('description', 'Program Tahfidz AL-HIKMAH — Pendampingan menghafal Al-Qur\'an dengan setoran rutin, murajaah,
    dan target yang disesuaikan.')

@section('content')
    <!-- Page Header -->
    <section class="page-header section-padding"
        style="padding-top:120px;background:linear-gradient(170deg,var(--bg-primary)0%,var(--primary-lighter)100%)">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-clipboard2-pulse"></i> Program Unggulan</div>
            <h1 class="section-title" data-reveal>Program <span class="text-gradient">Tahfidz Al-Qur'an</span></h1>
            <p class="section-description mx-auto" data-reveal>Menghafal bukan sekadar mengingat, tetapi menjaga.</p>
        </div>
    </section>

    <!-- Tahfidz Content -->
    <section class="section-padding" aria-label="Tahfidz">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0" data-reveal>
                    <img src="{{ asset('assets/img/62.jpg') }}" alt="Program Tahfidz" class="tahfidz-image" loading="lazy"
                        onerror="this.src='https://placehold.co/600x500/0d7a3e/white?text=Tahfidz'">
                </div>
                <div class="col-lg-6" data-reveal data-reveal-delay="200">
                    <h2 class="section-title">Menghafal Bukan Sekadar <span class="text-gradient">Mengingat</span>, Tetapi
                        Menjaga</h2>
                    <p class="section-description" style="max-width:100%">Menghafal Al-Qur'an adalah perjalanan yang
                        membutuhkan kesabaran, doa, dan istiqamah. Kami mendampingi dengan setoran rutin, murajaah, dan
                        target yang disesuaikan.</p>
                    <div class="tahfidz-features mt-4">
                        <div class="tahfidz-item"><i class="bi bi-check-circle-fill"></i> <span>Setoran hafalan secara
                                rutin</span></div>
                        <div class="tahfidz-item"><i class="bi bi-check-circle-fill"></i> <span>Murajaah untuk menjaga
                                hafalan</span></div>
                        <div class="tahfidz-item"><i class="bi bi-check-circle-fill"></i> <span>Target yang disesuaikan
                                kemampuan</span></div>
                        <div class="tahfidz-item"><i class="bi bi-check-circle-fill"></i> <span>Pendampingan bertahap</span>
                        </div>
                        <div class="tahfidz-item"><i class="bi bi-check-circle-fill"></i> <span>Evaluasi perkembangan
                                berkala</span></div>
                    </div>
                    <p style="font-style:italic;color:var(--text-muted);margin-top:20px">Semoga ayat yang dihafal menjadi
                        cahaya dalam kehidupan.</p>
                    <div class="mt-4">
                        <a href="#" class="btn btn-primary-custom" data-bs-toggle="modal"
                            data-bs-target="#daftarModal">Daftar Program Tahfidz <i class="bi bi-arrow-right ms-2"></i></a>
                        <a href="{{ route('biaya') }}" class="btn btn-outline-custom ms-2">Informasi Pendampingan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section text-center" aria-label="CTA">
        <div class="cta-overlay" aria-hidden="true"></div>
        <div class="container">
            <div class="cta-content" data-reveal>
                <h2 class="cta-title">Mulai Perjalanan <span class="text-gradient-light">Menghafal Al-Qur'an</span></h2>
                <p class="cta-subtitle">Dari satu ayat, satu halaman, satu juz — setiap langkah adalah kebaikan.</p>
                <a href="https://wa.me/6285786689008" class="btn btn-outline-light-custom btn-lg" target="_blank"><i
                        class="bi bi-whatsapp me-2"></i>Konsultasi Program Tahfidz</a>
            </div>
        </div>
    </section>
@endsection
