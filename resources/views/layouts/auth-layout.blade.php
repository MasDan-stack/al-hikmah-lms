<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('alhikmah-theme');
                if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
    <title>@yield('title', 'AL-HIKMAH | Autentikasi')</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon_io/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicon_io/site.webmanifest') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/poppins@5.1.1/index.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>
    <div id="loadingScreen" class="loading-screen">
        <div class="loader-container">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH" height="80"
                style="margin-bottom: 20px;">
            <div class="loader-text">AL-HIKMAH</div>
            <div class="loader-subtext">Memuat...</div>
        </div>
    </div>

    <div class="auth-split-wrapper">
        <div class="auth-split-card">
            <!-- Left Side: Form Content -->
            <div class="auth-form-side">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                    <button type="button" class="theme-toggle-btn shadow-sm" id="themeToggle" title="Ganti Tema (Gelap/Terang)" aria-label="Ganti Tema">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </button>
                </div>

                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="d-inline-block mb-2">
                        <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" height="54" class="brand-logo">
                    </a>
                    <h2 class="fw-bold mb-1" style="color: var(--text-primary); letter-spacing: -0.5px;">AL<span style="color: var(--primary);">-HIKMAH</span></h2>
                    <p class="text-muted small mb-0">@yield('subtitle', 'Platform Bimbingan Belajar Al-Qur\'an Privat & Tahfidz')</p>
                </div>

                @yield('auth-content')
                @yield('content')
            </div>

            <!-- Right Side: Visual & Brand Showcase -->
            <div class="auth-image-side" style="background-image: url('{{ asset('assets/img/auth-bg.jpg') }}');">
                <div class="auth-image-overlay"></div>
                <div class="auth-image-content">
                    <div>
                        <div class="badge bg-white text-success px-3 py-2 rounded-pill fw-semibold mb-3 shadow-sm" style="font-size: 0.78rem;">
                            <i class="bi bi-stars me-1 text-warning"></i> Platform Bimbingan Al-Qur'an
                        </div>
                        <h3 class="fw-bold text-white mb-2" style="letter-spacing: -0.5px;">Menemani Buah Hati Mencintai Al-Qur'an</h3>
                        <p class="text-white-50 small mb-0">Pendekatan personal, hangat, dan beradab untuk membentuk generasi pembelajar Al-Qur'an yang mutqin.</p>
                    </div>

                    <!-- Hadith Quote Box -->
                    <div class="auth-quote-card my-4">
                        <i class="bi bi-quote fs-2 text-white-50 d-block mb-1"></i>
                        <p class="small text-white fst-italic mb-2 lh-base">
                            "Sebaik-baik kalian adalah orang yang mempelajari Al-Qur'an dan mengajarkannya."
                        </p>
                        <span class="d-block small text-white-50 fw-semibold" style="font-size: 0.75rem;">
                            Hadits Riwayat Al-Bukhari No. 5027
                        </span>
                    </div>

                    <!-- 4 Pillars Benefits -->
                    <div>
                        <div class="auth-benefit-item">
                            <span class="auth-benefit-icon"><i class="bi bi-check2"></i></span>
                            <span>Bimbingan Privat 1-on-1 Intensif &amp; Terarah</span>
                        </div>
                        <div class="auth-benefit-item">
                            <span class="auth-benefit-icon"><i class="bi bi-check2"></i></span>
                            <span>Guru Al-Qur'an Teruji, Beradab, &amp; Bersanad</span>
                        </div>
                        <div class="auth-benefit-item">
                            <span class="auth-benefit-icon"><i class="bi bi-check2"></i></span>
                            <span>Mutaba'ah Harian &amp; Laporan Belajar Digital</span>
                        </div>
                        <div class="auth-benefit-item mb-0">
                            <span class="auth-benefit-icon"><i class="bi bi-check2"></i></span>
                            <span>Jadwal Fleksibel Sesuai Kebutuhan Keluarga</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    @stack('scripts')
</body>

</html>
