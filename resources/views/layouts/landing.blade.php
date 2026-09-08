<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'AL-HIKMAH: Menemani perjalanan belajar Al-Qur\'an anak dan keluarga dengan metode terarah, guru bersanad, dan pemantauan berkala.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:site_name" content="AL-HIKMAH">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')">
    <meta property="og:description" content="@yield('meta_description', 'AL-HIKMAH: Menemani perjalanan belajar Al-Qur\'an anak dan keluarga dengan metode terarah, guru bersanad, dan pemantauan berkala.')">
    <meta property="og:image" content="{{ asset('assets/img/auth-bg.jpg') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')">
    <meta name="twitter:description" content="@yield('meta_description', 'AL-HIKMAH: Menemani perjalanan belajar Al-Qur\'an anak dan keluarga dengan metode terarah, guru bersanad, dan pemantauan berkala.')">
    <meta name="twitter:image" content="{{ asset('assets/img/auth-bg.jpg') }}">

    <!-- JSON-LD Structured Data Schema -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "EducationalOrganization",
      "name": "AL-HIKMAH",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('assets/img/logo/logo.png') }}",
      "description": "Lembaga Bimbingan Belajar Al-Qur'an Privat Online, Home Visit, dan Tahfidz.",
      "telephone": "{{ site_setting('whatsapp_number', '+6285786689008') }}",
      "address": {
        "@@type": "PostalAddress",
        "addressCountry": "ID"
      }
    }
    </script>

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
        window.ALHIKMAH_CONFIG = {
            whatsappNumber: "{{ site_setting('whatsapp_number', '6285786689008') }}"
        };
    </script>
    <title>@yield('title', 'AL-HIKMAH | Menemani Perjalanan Belajar Al-Qur\'an')</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon_io/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicon_io/site.webmanifest') }}">

    <!-- Bootstrap 5 & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/poppins@5.1.1/index.min.css" rel="stylesheet">

    <!-- Custom CSS with Auto Cache Busting -->
    <link href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}" rel="stylesheet">

    @stack('styles')
</head>

<body>
    <!-- Skip to Main Content Link for Keyboard Accessibility -->
    <a href="#main-content" class="skip-to-main">Langsung ke konten utama</a>

    <noscript>
        <div style="background: #fff3cd; color: #856404; padding: 15px; text-align: center; border-bottom: 3px solid #ffc107;">
            Perhatian: Beberapa fitur interaktif website ini memerlukan JavaScript aktif untuk pengalaman terbaik.
        </div>
    </noscript>

    <div id="loadingScreen" class="loading-screen">
        <div class="loader-container">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" height="80" style="margin-bottom: 20px;">
            <div class="loader-text">AL-HIKMAH</div>
            <div class="loader-subtext">Memuat...</div>
        </div>
    </div>

    @include('partials.navbar')

    <main id="main-content">
        @yield('content')
    </main>

    @include('partials.footer')

    <!-- Floating WA + Back to Top -->
    <a href="{{ wa_url('Assalamualaikum, saya ingin bertanya tentang program belajar AL-HIKMAH') }}"
        class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Hubungi via WhatsApp">
        <i class="bi bi-whatsapp"></i>
        <span class="wa-tooltip">Berbincang dengan Kami</span>
    </a>
    <button class="back-to-top" id="backToTop" aria-label="Kembali ke atas">
        <i class="bi bi-chevron-up"></i>
    </button>

    <!-- Modal Pendaftaran / Konsultasi (Selalu Siap di DOM) -->
    @include('partials.modal-daftar')

    <!-- Global Flash Toast Notification -->
    <x-flash-toast />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>

    @stack('scripts')
</body>

</html>
