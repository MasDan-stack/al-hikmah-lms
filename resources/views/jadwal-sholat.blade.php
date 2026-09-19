@extends('layouts.landing')

@section('title', 'Jadwal Sholat & Kompas Arah Kiblat Real-Time - AL-HIKMAH')
@section('meta_description', 'Jadwal sholat fardhu dan waktu imsakiyah real-time seluruh Indonesia standar resmi Kemenag RI, dilengkapi kompas arah kiblat dan deteksi GPS.')

@section('content')
    <!-- SUBPAGE HERO / BREADCRUMB -->
    <section class="editorial-page-header" aria-label="Header Halaman Jadwal Sholat">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center" data-reveal>
                    <span class="editorial-badge">
                        <i class="bi bi-clock-history" aria-hidden="true"></i> Waktu Ibadah Harian
                    </span>
                    <h1>Jadwal Sholat &amp; Kompas Kiblat Real-Time</h1>
                    <p>
                        Pantau waktu sholat fardhu secara akurat standar Kementerian Agama RI dengan deteksi lokasi otomatis, hitung mundur azan, dan penunjuk arah kiblat.
                    </p>
                    <nav aria-label="breadcrumb" class="mt-3">
                        <ol class="breadcrumb justify-content-center mb-0 small">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Beranda</a></li>
                            <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Jadwal Sholat &amp; Kiblat</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION JADWAL SHOLAT INTERAKTIF -->
    <section class="prayer_part py-5 bg-gradient-subtle" id="jadwal-sholat" aria-label="Jadwal Sholat Real-Time">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11" data-reveal data-reveal-delay="100">
                    <div class="prayer-box shadow-sm">
                        <!-- Top Bar: Location, Hijri Date, Action Controls -->
                        <div class="prayer-header-bar">
                            <div class="prayer-location-info">
                                <div class="prayer-city-badge">
                                    <i class="bi bi-geo-alt-fill text-primary" aria-hidden="true"></i>
                                    <span id="prayer-city-name">DKI Jakarta</span>
                                    <span class="gps-live-dot" id="gps-status-dot" style="display: none;"
                                        title="GPS Real-Time Aktif">
                                        <span class="pulse"></span>
                                        <span class="dot"></span>
                                    </span>
                                </div>
                                <div class="prayer-date-badge" id="prayer-date-display">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i> Memuat tanggal...
                                </div>
                            </div>

                            <div class="prayer-controls">
                                <button type="button" class="btn-prayer-ctrl" id="btn-detect-gps"
                                    title="Deteksi Lokasi GPS Anda">
                                    <i class="bi bi-crosshair"></i> Deteksi GPS
                                </button>
                                <button type="button" class="btn-prayer-ctrl" data-bs-toggle="modal"
                                    data-bs-target="#cityModal" title="Pilih Kota atau Kabupaten">
                                    <i class="bi bi-buildings"></i> Pilih Kota
                                </button>
                                <button type="button" class="btn-prayer-ctrl" data-bs-toggle="modal"
                                    data-bs-target="#qiblaModal" title="Cek Arah Kiblat">
                                    <i class="bi bi-compass"></i> Arah Kiblat
                                </button>
                                <button type="button" class="btn-prayer-ctrl" id="btn-prayer-sound"
                                    title="Aktifkan atau Matikan Notifikasi Suara Sholat">
                                    <i class="bi bi-bell-slash"></i> Suara: Mati
                                </button>
                            </div>
                        </div>

                        <!-- Hero Banner: Countdown to Next Prayer & Live Digital Clock -->
                        <div class="prayer-hero-banner">
                            <div class="prayer-countdown-content">
                                <div class="prayer-countdown-tag">
                                    <i class="bi bi-hourglass-split"></i> MENUJU WAKTU <span id="next-prayer-name">...</span>
                                </div>
                                <div class="prayer-countdown-timer" id="prayer-countdown-timer">--:--:--</div>
                                <div class="prayer-countdown-target" id="next-prayer-time-target">
                                    Memperbarui jadwal sholat...
                                </div>
                            </div>

                            <div class="prayer-live-clock">
                                <div class="live-digital-clock" id="live-digital-clock">--:--:--</div>
                                <span class="live-timezone-badge" id="live-timezone-badge">WIB</span>
                            </div>
                        </div>

                        <!-- 8 Prayer Times Cards Grid -->
                        <div class="prayer-grid" id="prayer-cards-grid">
                            <div class="prayer-card">
                                <div class="prayer-card-name">Imsak</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Subuh</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Terbit</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Dhuha</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Dzuhur</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Ashar</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Maghrib</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                            <div class="prayer-card">
                                <div class="prayer-card-name">Isya</div>
                                <div class="prayer-card-time">--:--</div>
                            </div>
                        </div>

                        <!-- Footer source info -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 text-muted"
                            style="font-size: 0.75rem; border-top: 1px dashed var(--border-color);">
                            <span><i class="bi bi-shield-check text-success me-1"></i>Standar Perhitungan Bimas Islam Kemenag RI</span>
                            <span class="d-none d-sm-inline">Metode: Kementerian Agama Republik Indonesia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION FADHILAH & KEUTAMAAN SHOLAT -->
    <section class="py-5" aria-label="Keutamaan Sholat Tepat Waktu">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8 text-center" data-reveal>
                    <div class="section-badge mx-auto"><i class="bi bi-heart-pulse-fill"></i> Hikmah &amp; Ibadah</div>
                    <h2 class="section-title">Keutamaan Menjaga Sholat <span class="text-gradient">Tepat Waktu</span></h2>
                    <p class="section-description mx-auto">
                        Sholat adalah tiang agama dan amalan pertama yang dihisab. Menjaga waktu sholat melatih kedisiplinan dan menghadirkan keberkahan dalam keluarga.
                    </p>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-4" data-reveal data-reveal-delay="100">
                    <div class="why-card h-100 p-4 rounded-4 border bg-white shadow-xs">
                        <div class="why-icon mb-3 bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-clock-fill fs-5"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Amalan Paling Dicintai</h4>
                        <p class="text-secondary small mb-0">
                            Rasulullah SAW bersabda ketika ditanya amalan apa yang paling utama: "Sholat pada awal waktunya." (HR. Bukhari dan Muslim).
                        </p>
                    </div>
                </div>

                <div class="col-md-4" data-reveal data-reveal-delay="200">
                    <div class="why-card h-100 p-4 rounded-4 border bg-white shadow-xs">
                        <div class="why-icon mb-3 bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-shield-lock-fill fs-5"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Penjaga Diri dari Keburukan</h4>
                        <p class="text-secondary small mb-0">
                            "Sesungguhnya sholat itu mencegah dari perbuatan keji dan mungkar." (QS. Al-Ankabut: 45). Fondasi adab mulia bagi anak sejak usia dini.
                        </p>
                    </div>
                </div>

                <div class="col-md-4" data-reveal data-reveal-delay="300">
                    <div class="why-card h-100 p-4 rounded-4 border bg-white shadow-xs">
                        <div class="why-icon mb-3 bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-compass-fill fs-5"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Penyatu Kiblat Umat</h4>
                        <p class="text-secondary small mb-0">
                            Menghadap Ka'bah di Masjidil Haram menyatukan hati seluruh kaum muslimin di penjuru bumi dalam satu arah peribadatan kepada Allah SWT.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Call to Action Bimbingan Mengaji -->
            <div class="row justify-content-center mt-5">
                <div class="col-lg-10" data-reveal>
                    <div class="card border-0 rounded-4 text-center p-4 p-md-5" style="background: linear-gradient(135deg, rgba(13, 122, 62, 0.08) 0%, rgba(217, 119, 6, 0.08) 100%); border: 1px solid rgba(13, 122, 62, 0.15) !important;">
                        <h3 class="fw-bold mb-2 text-dark">Lengkapi Ibadah Ananda dengan Bacaan Al-Qur'an Tartil</h3>
                        <p class="text-secondary mx-auto mb-4" style="max-width: 620px;">
                            AL-HIKMAH siap mendampingi ananda belajar membaca, memahami tajwid, dan menghafal Al-Qur'an secara privat 1-on-1 dengan bimbingan ustadz dan ustazah terpercaya.
                        </p>
                        <div class="d-flex justify-content-center flex-wrap gap-3">
                            <button type="button" class="btn btn-primary-custom rounded-pill px-4 py-2.5 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#trialModal">
                                <i class="bi bi-pencil-square me-1"></i> Daftar Gratis Sekarang
                            </button>
                            <a href="{{ wa_url('Assalamualaikum Admin Al-Hikmah, saya ingin konsultasi mengenai bimbingan mengaji untuk keluarga.') }}" target="_blank" rel="noopener" class="btn btn-outline-custom rounded-pill px-4 py-2.5 fw-semibold">
                                <i class="bi bi-whatsapp me-1"></i> Konsultasi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODALS: PILIH KOTA & ARAH KIBLAT -->
    <div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"
                style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title font-display fw-bold" id="cityModalLabel">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>Pilih Kota / Kabupaten
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="input-group-editorial mb-3">
                        <span class="field-icon"><i class="bi bi-search"></i></span>
                        <input type="text" id="city-search-input" class="form-control"
                            placeholder="Cari nama kota atau kabupaten di Indonesia..." autocomplete="off"
                            aria-label="Cari nama kota atau kabupaten">
                    </div>

                    <div class="city-quick-pills">
                        <button type="button" class="city-quick-pill active" data-region="all">Semua Wilayah</button>
                        <button type="button" class="city-quick-pill" data-region="Jabodetabek">Jabodetabek</button>
                        <button type="button" class="city-quick-pill" data-region="Jawa">Jawa</button>
                        <button type="button" class="city-quick-pill" data-region="Sumatera">Sumatera</button>
                        <button type="button" class="city-quick-pill" data-region="Kalimantan">Kalimantan</button>
                        <button type="button" class="city-quick-pill" data-region="Sulawesi">Sulawesi</button>
                        <button type="button" class="city-quick-pill" data-region="Bali/Nusa">Bali &amp; Nusa Tenggara</button>
                        <button type="button" class="city-quick-pill" data-region="Maluku/Papua">Maluku &amp; Papua</button>
                    </div>

                    <div class="city-list-container" id="city-list-container">
                        <!-- Diisi otomatis oleh PrayerTimesApp.renderCityList() -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qiblaModal" tabindex="-1" aria-labelledby="qiblaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-xl);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title font-display fw-bold" id="qiblaModalLabel">
                        <i class="bi bi-compass text-primary me-2"></i>Kompas Arah Kiblat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="qibla-compass-container">
                        <div class="qibla-dial-wrapper">
                            <span class="compass-cardinal n">U</span>
                            <span class="compass-cardinal s">S</span>
                            <span class="compass-cardinal e">T</span>
                            <span class="compass-cardinal w">B</span>
                            <div class="qibla-needle" id="qibla-needle-pointer">
                                <div class="qibla-needle-center"></div>
                            </div>
                        </div>
                        <div class="qibla-degree-display" id="qibla-degree-val">295.2°</div>
                        <p class="qibla-desc-display mb-0" id="qibla-desc-val">
                            Menghitung arah Ka'bah dari lokasi Anda...
                        </p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center" style="border-top: 1px solid var(--border-color);">
                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Sudut diukur searah jarum jam dari arah Utara Geografis (0°).</small>
                </div>
            </div>
        </div>
    </div>
@endsection
