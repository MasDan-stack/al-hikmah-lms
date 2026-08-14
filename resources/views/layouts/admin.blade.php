<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <title>@yield('title', 'Admin Dashboard') | AL-HIKMAH LMS</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/poppins@5.1.1/index.min.css" rel="stylesheet">

    <!-- Custom CSS AL-HIKMAH -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1020;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        .admin-sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-sidebar-brand img {
            height: 40px;
            width: auto;
        }

        .admin-sidebar-brand-text {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--text-primary);
        }

        .admin-sidebar-nav {
            padding: 1.25rem 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: var(--transition);
            margin-bottom: 4px;
        }

        .admin-nav-item i {
            font-size: 1.1rem;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .admin-nav-item:hover,
        .admin-nav-item.active {
            background: var(--primary-lighter);
            color: var(--primary);
        }

        .admin-nav-item:hover i,
        .admin-nav-item.active i {
            color: var(--primary);
        }

        .admin-main {
            margin-left: 260px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--bg-secondary);
            transition: var(--transition);
        }

        .admin-header {
            height: 70px;
            background: var(--glass-bg-strong);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1010;
        }

        .admin-content {
            padding: 2rem;
            flex-grow: 1;
        }

        .admin-user-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-lighter);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: var(--radius-full);
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid var(--border-color);
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen">
        <div class="loader-container">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH" height="80" style="margin-bottom: 20px;">
            <div class="loader-text">AL-HIKMAH</div>
            <div class="loader-subtext">Memuat Dashboard...</div>
        </div>
    </div>

    <div class="admin-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-brand">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo">
                <div class="admin-sidebar-brand-text">
                    AL<span style="color: var(--primary)">-HIKMAH</span>
                </div>
            </div>

            <nav class="admin-sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Manajemen User & Role
                </a>
                <a href="{{ route('admin.students.index') }}" class="admin-nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Data Santri
                </a>
                <a href="{{ route('admin.mentors.index') }}" class="admin-nav-item {{ request()->routeIs('admin.mentors.index') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Pendamping
                </a>
                <a href="{{ route('admin.mentors.availability') }}" class="admin-nav-item {{ request()->routeIs('admin.mentors.availability') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i> Ketersediaan & Alokasi
                </a>
                <a href="{{ route('admin.students.index') }}" class="admin-nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i> Jadwal Sesi
                </a>
                <a href="{{ route('admin.programs.index') }}" class="admin-nav-item {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i> Program Belajar
                </a>
                <a href="{{ route('admin.settings.index') }}" class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Pengaturan Website
                </a>

                <a href="{{ route('home') }}" class="admin-nav-item" target="_blank">
                    <i class="bi bi-globe"></i> Halaman Depan
                </a>

                <hr class="my-3" style="border-color: var(--border-color);">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-item text-danger border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right text-danger"></i> Keluar
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Top Header Navbar -->
            <header class="admin-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">@yield('header', 'Dashboard Utama')</h5>
                        <small class="text-muted">@yield('subheader', 'Selamat datang di Panel Admin AL-HIKMAH')</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Home Route Link Button -->
                    <a href="{{ route('home') }}" class="btn btn-outline-success btn-sm rounded-pill d-none d-sm-inline-flex align-items-center me-2">
                        <i class="bi bi-house-door me-1"></i> Halaman Depan
                    </a>

                    <!-- Theme Toggle Button -->
                    <button class="theme-toggle-btn" id="themeToggle" aria-label="Ganti Tema">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </button>

                    <!-- User Role Badge -->
                    <div class="admin-user-badge">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ auth()->user()->name ?? 'Admin AL-HIKMAH' }}</span>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 & Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script>
        document.getElementById('sidebarToggleBtn')?.addEventListener('click', function() {
            document.getElementById('adminSidebar')?.classList.toggle('show');
        });
    </script>

    @stack('scripts')
</body>

</html>