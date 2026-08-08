@extends('layouts.landing')

@section('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')

@section('content')
<!-- HERO -->
<section id="beranda" class="hero-section" aria-label="Hero">
    <div class="hero-overlay" aria-hidden="true"></div>
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6 hero-content">
                <div class="hero-badge"><i class="bi bi-star-fill"></i> Bismillāhirraḥmānirraḥīm</div>
                <h1 class="hero-title">Menumbuhkan Cinta kepada <span class="text-gradient">Al-Qur'an</span>,<br>Satu Langkah Setiap Hari.</h1>
                <p class="hero-subtitle">Kami percaya bahwa belajar Al-Qur'an adalah perjalanan untuk mengenal Allah, membentuk adab, dan menumbuhkan akhlak yang baik. AL-HIKMAH hadir untuk mendampingi anak dan keluarga dalam perjalanan tersebut.</p>
                <p class="hero-subtitle" style="font-size: 0.85rem; opacity: 0.8;">Program utama untuk anak usia 10–15 tahun. Program tambahan tersedia untuk dewasa dan muslimah.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary-custom btn-lg" data-bs-toggle="modal" data-bs-target="#daftarModal">
                        <i class="bi bi-pencil-square me-2"></i>Mulai Perjalanan Belajar
                    </a>
                    <a href="{{ route('home') }}#tentang" class="btn btn-outline-custom btn-lg">
                        <i class="bi bi-info-circle me-2"></i>Kenali AL-HIKMAH
                    </a>
                </div>
            </div>
            <div class="col-lg-6 hero-image-wrapper">
                <div class="hero-image-container">
                    <div class="hero-glow" aria-hidden="true"></div>
                    <img src="{{ asset('assets/img/1.jpg') }}" alt="Belajar Al-Qur'an bersama AL-HIKMAH" class="hero-image" loading="eager">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- LANDASAN -->
<section id="landasan" class="section-padding section-alt text-center" aria-label="Landasan">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-reveal>
                <div class="section-badge mx-auto"><i class="bi bi-book"></i> Landasan</div>
                <h2 class="section-title">Sebaik-Baik Perjalanan Adalah<br>Perjalanan Bersama <span class="text-gradient">Al-Qur'an</span></h2>
                <div class="ayat-quote" style="margin: 24px 0; padding: 24px; background: var(--card-bg); border-radius: var(--radius-lg); border-left: 4px solid var(--primary);">
                    <p style="font-size: 1.6rem; line-height: 2.2; direction: rtl;">خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ</p>
                </div>
                <p style="font-style: italic; color: var(--text-secondary);">"Sebaik-baik kalian adalah orang yang belajar Al-Qur'an dan mengajarkannya."</p>
                <p style="font-size: 0.85rem; color: var(--text-muted);">HR. Bukhari</p>
            </div>
        </div>
    </div>
</section>