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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/poppins@5.1.1/index.min.css" rel="stylesheet">
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
            max-width: 440px;
            background: var(--glass-bg-strong);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            padding: 2.5rem 2rem;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: authFadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes authFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-logo {
            height: 64px;
            width: auto;
            margin-bottom: 1rem;
        }

        .auth-title {
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
            font-size: 1.5rem;
        }

        .auth-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
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
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-theme-toggle">
                <button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle dark mode">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>
            </div>

            <div class="auth-header">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" class="auth-logo">
                </a>
                <h3 class="auth-title">AL<span style="color: var(--primary)">-HIKMAH</span></h3>
                <p class="auth-subtitle">@yield('subtitle', 'Platform Manajemen Belajar Al-Qur\'an')</p>
            </div>

            @yield('auth-content')
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    @stack('scripts')
</body>

</html>
