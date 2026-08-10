<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AL-HIKMAH | Autentikasi')</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/poppins@5.1.1/index.min.css" rel="stylesheet">

    <!-- Custom CSS AL-HIKMAH -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 2;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            background: var(--glass-bg-strong);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 2.5rem 2rem;
            position: relative;
            transition: var(--transition);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            height: 60px;
            width: auto;
            margin-bottom: 1rem;
        }

        .auth-title {
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 1px;
            margin-bottom: 0.25rem;
        }

        .auth-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .auth-theme-toggle {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
        }
    </style>

    @stack('styles')
</head>

<body>
    <div id="loadingScreen" class="loading-screen">
        <div class="loader-container">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH" height="80" style="margin-bottom: 20px;">
            <div class="loader-text">AL-HIKMAH</div>
            <div class="loader-subtext">Memuat...</div>
        </div>
    </div>

    <!-- Background Islamic Animation -->
    <div class="bg-islamic-animation" aria-hidden="true">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        <div class="floating-shape shape-3"></div>
        <div class="floating-shape shape-4"></div>
        <div class="floating-shape shape-5"></div>
        <div class="floating-shape shape-6"></div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Dark Mode Toggle Button -->
            <div class="auth-theme-toggle">
                <button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle dark mode">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>
            </div>

            <!-- Header Branding -->
            <div class="auth-header">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" class="auth-logo">
                </a>
                <h3 class="auth-title">AL<span style="color: var(--text-primary)">-HIKMAH</span></h3>
                <p class="auth-subtitle">@yield('subtitle', 'Platform Manajemen Belajar Al-Qur\'an')</p>
            </div>

            <!-- Form / Page Content -->
            @yield('auth-content')
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    @stack('scripts')
</body>

</html>
