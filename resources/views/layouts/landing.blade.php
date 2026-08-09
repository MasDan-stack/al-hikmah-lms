<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="AL-HIKMAH — Menemani perjalanan anak usia 10–15 tahun untuk mengenal, mencintai, dan menghidupkan nilai-nilai Al-Qur'an dalam kehidupan.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/poppins@5.1.1/index.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

    <noscript>
        <div
            style="background: #fff3cd; color: #856404; padding: 15px; text-align: center; border-bottom: 3px solid #ffc107;">
            ⚠️ Beberapa fitur website ini memerlukan JavaScript. Silakan aktifkan JavaScript di browser Anda untuk
            pengalaman terbaik.
        </div>
    </noscript>

    <canvas id="bgCanvas" class="bg-3d-canvas" aria-hidden="true"></canvas>

    <div class="logo-watermark" aria-hidden="true">
        <div class="watermark-container">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH">
            <div class="watermark-text">AL-HIKMAH</div>
        </div>
    </div>

    <div id="loadingScreen" class="loading-screen">
        <div class="loader-container">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH" height="80"
                style="margin-bottom: 20px;">
            <div class="loader-text">AL-HIKMAH</div>
            <div class="loader-subtext">Memuat...</div>
        </div>
    </div>

    <div class="bg-islamic-animation" aria-hidden="true">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
        <div class="floating-shape shape-5"></div>
        <div class="floating-shape shape-6"></div>
        <div class="floating-shape shape-7"></div>
        <div class="floating-shape shape-8"></div>
    </div>

    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

    <!-- Floating WA + Back to Top -->
    <a href="https://wa.me/6285786689008?text=Assalamualaikum,%20saya%20ingin%20bertanya%20tentang%20program%20belajar%20AL-HIKMAH"
        class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Hubungi via WhatsApp">
        <i class="bi bi-whatsapp"></i>
        <span class="wa-tooltip">Berbincang dengan Kami</span>
    </a>
    <button class="back-to-top" id="backToTop" aria-label="Kembali ke atas">
        <i class="bi bi-chevron-up"></i>
    </button>

    <!-- Modal Pendaftaran -->
    @include('partials.modal-daftar')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('themeToggle');
            const icon = document.getElementById('themeIcon');
            const html = document.documentElement;

            // Load saved theme
            const savedTheme = localStorage.getItem('alhikmah-theme');
            if (savedTheme === 'dark') {
                html.setAttribute('data-bs-theme', 'dark');
                icon.className = 'bi bi-sun-fill';
            }

            if (toggle) {
                toggle.addEventListener('click', function() {
                    const isDark = html.getAttribute('data-bs-theme') === 'dark';
                    const newTheme = isDark ? 'light' : 'dark';
                    html.setAttribute('data-bs-theme', newTheme);
                    icon.className = newTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
                    localStorage.setItem('alhikmah-theme', newTheme);
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
