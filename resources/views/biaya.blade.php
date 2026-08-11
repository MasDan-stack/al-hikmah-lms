@extends('layouts.landing')

@section('title', 'Biaya | AL-HIKMAH')
@section('description', 'Biaya dan Paket Belajar AL-HIKMAH — Informasi transparan tentang pilihan pendampingan belajar Al-Qur\'an.')

@section('content')
    <!-- Page Header -->
    <section class="page-header section-padding"
        style="padding-top:120px;background:linear-gradient(170deg,var(--bg-primary)0%,var(--primary-lighter)100%)">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-info-circle"></i> Informasi</div>
            <h1 class="section-title" data-reveal>Pilihan <span class="text-gradient">Pendampingan Belajar</span></h1>
            <p class="section-description mx-auto" data-reveal>Kami berusaha menjaga informasi biaya tetap transparan agar
                setiap keluarga dapat mempertimbangkan pilihan yang sesuai.</p>
        </div>
    </section>

    <!-- Biaya Pendaftaran -->
    <section class="section-padding" aria-label="Biaya">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8" data-reveal>
                    <div class="biaya-card">
                        <div class="biaya-card-left">
                            <div class="biaya-icon-wrapper"><i class="bi bi-file-earmark-check"></i></div>
                            <div class="biaya-info">
                                <span class="biaya-label">Biaya Pendaftaran</span>
                                <div class="biaya-harga">Rp <span class="biaya-angka">150.000</span></div>
                                <span class="biaya-catatan">✔ Satu kali pembayaran untuk administrasi & assessment awal</span>
                            </div>
                        </div>
                        <div class="biaya-card-right">
                            <span class="biaya-badge">Sekali Bayar</span>
                            <small class="biaya-subnote">*Belum termasuk paket bulanan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Paket Belajar (Dinamis dari Master Data Database) -->
    <section class="section-padding section-alt" aria-label="Paket">
        <div class="container">
            @if(isset($programs) && $programs->count() > 0)
                <div class="row g-4 justify-content-center">
                    @foreach($programs as $index => $program)
                        <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                            <div class="paket-card {{ $index === 1 ? 'paket-popular' : '' }} h-100 d-flex flex-column justify-content-between">
                                @if($index === 1)
                                    <div class="paket-popular-ribbon"><span>⭐ Paling Populer</span></div>
                                @endif
                                <div>
                                    <div class="paket-card-header">
                                        <span class="paket-name">{{ $program->name }}</span>
                                        <span class="paket-badge {{ $index === 1 ? 'popular' : '' }}">{{ $program->duration_weeks }} Minggu</span>
                                    </div>
                                    <div class="paket-card-body">
                                        <div class="paket-price">
                                            <span class="price-amount">Rp {{ number_format($program->price, 0, ',', '.') }}</span>
                                            <span class="price-period">/ paket</span>
                                        </div>
                                        <div class="paket-detail">
                                            <span class="detail-label">Tingkat / Level</span>
                                            <span class="detail-value fw-bold text-success">{{ $program->level }}</span>
                                        </div>
                                        <div class="paket-detail">
                                            <span class="detail-label">Durasi Masa Belajar</span>
                                            <span class="detail-value">{{ $program->duration_weeks }} Minggu</span>
                                        </div>
                                        <div class="paket-detail">
                                            <span class="detail-label">Metode Pendampingan</span>
                                            <span class="detail-value">Private (1:1 / Online / Offline)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-3 pb-3">
                                    <a href="https://wa.me/6285786689008?text={{ urlencode('Assalamualaikum, saya ingin menanyakan mengenai paket biaya program ' . $program->name) }}"
                                       class="btn {{ $index === 1 ? 'btn-primary-custom' : 'btn-outline-custom' }} w-100" target="_blank">
                                        <i class="bi bi-whatsapp me-1"></i> Konsultasikan
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Fallback Static Cards -->
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-4 col-md-6" data-reveal>
                        <div class="paket-card">
                            <div class="paket-card-header"><span class="paket-name">Basic</span><span class="paket-badge">4x / bulan</span></div>
                            <div class="paket-card-body">
                                <div class="paket-price"><span class="price-amount">Rp 400.000</span><span class="price-period">/ bulan</span></div>
                                <div class="paket-detail"><span class="detail-label">Pertemuan</span><span class="detail-value">4x / bulan (1x/minggu)</span></div>
                                <div class="paket-detail"><span class="detail-label">Durasi</span><span class="detail-value">90 menit</span></div>
                                <div class="paket-detail"><span class="detail-label">Pendampingan</span><span class="detail-value">Private (1:1)</span></div>
                                <a href="https://wa.me/6285786689008" class="btn btn-outline-custom w-100" target="_blank">Konsultasikan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="100">
                        <div class="paket-card paket-popular">
                            <div class="paket-popular-ribbon"><span>⭐ Banyak Dipilih</span></div>
                            <div class="paket-card-header"><span class="paket-name">Standard</span><span class="paket-badge popular">8x / bulan</span></div>
                            <div class="paket-card-body">
                                <div class="paket-price"><span class="price-amount">Rp 800.000</span><span class="price-period">/ bulan</span></div>
                                <div class="paket-detail"><span class="detail-label">Pertemuan</span><span class="detail-value">8x / bulan (2x/minggu)</span></div>
                                <div class="paket-detail"><span class="detail-label">Durasi</span><span class="detail-value">90 menit</span></div>
                                <div class="paket-detail"><span class="detail-label">Pendampingan</span><span class="detail-value">Private (1:1)</span></div>
                                <a href="https://wa.me/6285786689008" class="btn btn-primary-custom w-100" target="_blank">Konsultasikan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="200">
                        <div class="paket-card">
                            <div class="paket-card-header"><span class="paket-name">Premium</span><span class="paket-badge">12x / bulan</span></div>
                            <div class="paket-card-body">
                                <div class="paket-price"><span class="price-amount">Rp 1.200.000</span><span class="price-period">/ bulan</span></div>
                                <div class="paket-detail"><span class="detail-label">Pertemuan</span><span class="detail-value">12x / bulan (3x/minggu)</span></div>
                                <div class="paket-detail"><span class="detail-label">Durasi</span><span class="detail-value">90 menit</span></div>
                                <div class="paket-detail"><span class="detail-label">Pendampingan</span><span class="detail-value">Private (1:1)</span></div>
                                <a href="https://wa.me/6285786689008" class="btn btn-outline-custom w-100" target="_blank">Konsultasikan</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <p style="font-style:italic;color:var(--text-muted)">Tidak ada paksaan memilih program tertentu.</p>
            </div>
        </div>
    </section>
@endsection
