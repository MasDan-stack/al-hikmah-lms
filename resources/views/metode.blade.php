@extends('layouts.landing')

@section('title', 'Metode Belajar | AL-HIKMAH')
@section('description',
    'Metode belajar AL-HIKMAH — Online, Offline (Home Visit), dan Hybrid. Sistem pendampingan
    fleksibel untuk keluarga.')

@section('content')
    <!-- Page Header -->
    <section class="page-header section-padding"
        style="padding-top:120px;background:linear-gradient(170deg,var(--bg-primary)0%,var(--primary-lighter)100%)">
        <div class="container text-center">
            <div class="section-badge mx-auto" data-reveal><i class="bi bi-grid-3x3-gap-fill"></i> Metode</div>
            <h1 class="section-title" data-reveal>Cara Kami <span class="text-gradient">Mendampingi</span></h1>
            <p class="section-description mx-auto" data-reveal>Belajar dengan cara yang lebih dekat dan personal.</p>
        </div>
    </section>

    <!-- Metode Belajar -->
    <section class="section-padding" aria-label="Metode Belajar">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-reveal>
                    <div class="kelas-card">
                        <div class="kelas-icon"><i class="bi bi-laptop"></i></div>
                        <h3>Online</h3>
                        <p>Belajar dari Mana Saja</p>
                        <ul class="kelas-list mb-3">
                            <li><i class="bi bi-check-circle-fill"></i> Pendampingan belajar secara online</li>
                            <li><i class="bi bi-check-circle-fill"></i> Bagi keluarga di luar jangkauan tatap muka</li>
                            <li><i class="bi bi-check-circle-fill"></i> Jadwal fleksibel</li>
                        </ul>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Online') }}"
                            class="btn btn-outline-custom w-100 rounded-pill mt-2" target="_blank"><i
                                class="bi bi-whatsapp me-1"></i> Pilih Online</a>
                        <div class="kelas-badge">Online</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="kelas-card featured-kelas">
                        <div class="kelas-icon"><i class="bi bi-house-door"></i></div>
                        <h3>Offline</h3>
                        <p>Pendamping Datang ke Rumah</p>
                        <ul class="kelas-list mb-3">
                            <li><i class="bi bi-check-circle-fill"></i> Suasana belajar yang personal dan nyaman</li>
                            <li><i class="bi bi-check-circle-fill"></i> Pendamping hadir langsung di rumah</li>
                            <li><i class="bi bi-check-circle-fill"></i> Interaksi lebih dekat</li>
                        </ul>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Offline (Home Visit)') }}"
                            class="btn btn-primary-custom w-100 rounded-pill mt-2" target="_blank"><i
                                class="bi bi-whatsapp me-1"></i> Pilih Home Visit</a>
                        <div class="kelas-badge">Home Visit</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="kelas-card">
                        <div class="kelas-icon"><i class="bi bi-arrow-repeat"></i></div>
                        <h3>Hybrid</h3>
                        <p>Belajar dengan Fleksibilitas</p>
                        <ul class="kelas-list mb-3">
                            <li><i class="bi bi-check-circle-fill"></i> Perpaduan online dan offline</li>
                            <li><i class="bi bi-check-circle-fill"></i> Sesuai kondisi dan kebutuhan</li>
                            <li><i class="bi bi-check-circle-fill"></i> Pendampingan personal</li>
                        </ul>
                        <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Hybrid') }}"
                            class="btn btn-outline-custom w-100 rounded-pill mt-2" target="_blank"><i
                                class="bi bi-whatsapp me-1"></i> Pilih Hybrid</a>
                        <div class="kelas-badge">Fleksibel</div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4" data-reveal>
                <a href="{{ route('program') }}" class="btn btn-outline-custom me-2"><i
                        class="bi bi-journal-bookmark me-1"></i> Lihat Program</a>
                @auth
                    @if (auth()->user()->isParent())
                        <a href="{{ route('biaya') }}" class="btn btn-primary-custom"><i class="bi bi-info-circle me-1"></i>
                            Informasi Pendampingan</a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('biaya') }}" class="btn btn-primary-custom"><i class="bi bi-info-circle me-1"></i>
                            Informasi Pendampingan (Kamu Administrator)</a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <!-- Sistem Pendampingan -->
    <section class="section-padding section-alt" aria-label="Sistem Pendampingan">
        <div class="container">
            <h2 class="section-title text-center mb-4">Sistem <span class="text-gradient">Pendampingan</span></h2>
            <p class="section-description mx-auto text-center mb-5">Yang kami kejar bukan sekadar cepat, tetapi istiqamah.
            </p>
            <div class="row g-4">
                <div class="col-md-3" data-reveal>
                    <div class="meeting-option">
                        <div class="option-icon"><i class="bi bi-1-circle"></i></div>
                        <div class="option-content">
                            <h5>1x Seminggu</h5>
                            <p>Untuk membangun kebiasaan dan konsistensi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" data-reveal data-reveal-delay="100">
                    <div class="meeting-option">
                        <div class="option-icon"><i class="bi bi-2-circle"></i></div>
                        <div class="option-content">
                            <h5>2–3x Seminggu</h5>
                            <p>Untuk keluarga yang menginginkan pendampingan lebih rutin dan intensif.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" data-reveal data-reveal-delay="200">
                    <div class="meeting-option">
                        <div class="option-icon"><i class="bi bi-calendar2-check"></i></div>
                        <div class="option-content">
                            <h5>Jadwal yang Disesuaikan</h5>
                            <p>Menyesuaikan kondisi dan aktivitas keluarga.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" data-reveal data-reveal-delay="300">
                    <div class="meeting-option">
                        <div class="option-icon"><i class="bi bi-person-check"></i></div>
                        <div class="option-content">
                            <h5>Pendampingan Personal</h5>
                            <p>Memberikan perhatian sesuai kebutuhan masing-masing murid.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jabodetabek Ribbon -->
    <section class="jabodetabek-ribbon" aria-label="Informasi layanan Jabodetabek">
        <div class="container">
            <div class="jabodetabek-content" data-reveal>
                <div class="jabodetabek-icon"><i class="bi bi-geo-alt-fill"></i></div>
                <div class="jabodetabek-text">
                    <h4>📍 Melayani Area <strong>Jabodetabek</strong> & Sekitarnya</h4>
                    <p>Pendamping datang ke rumah Anda. Tersedia juga kelas online untuk seluruh Indonesia.</p>
                </div>
                <a href="{{ wa_url('Assalamualaikum, apakah area saya tercakup AL-HIKMAH?') }}"
                    class="btn btn-sm btn-primary-custom" target="_blank"><i class="bi bi-whatsapp me-1"></i> Cek
                    Area</a>
            </div>
        </div>
    </section>
@endsection
