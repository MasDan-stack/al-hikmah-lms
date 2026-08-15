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
            width: 270px;
            background: var(--card-bg, #ffffff);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1020;
            display: flex;
            flex-direction: column;
            transition: var(--transition, all 0.3s ease);
        }

        .admin-sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-sidebar-brand img {
            height: 42px;
            width: auto;
        }

        .admin-sidebar-brand-text {
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--text-primary);
        }

        .admin-sidebar-nav {
            padding: 1.25rem 0.85rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .admin-nav-section-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            padding: 8px 14px 4px;
        }

        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 14px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.88rem;
            border-radius: var(--radius-md, 10px);
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 3px;
        }

        .admin-nav-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            color: var(--text-muted);
            transition: var(--transition, all 0.2s ease);
        }

        .admin-nav-item:hover,
        .admin-nav-item.active {
            background: var(--primary-lighter);
            color: var(--primary);
            font-weight: 600;
        }

        .admin-nav-item:hover i,
        .admin-nav-item.active i {
            color: var(--primary);
        }

        .admin-main {
            margin-left: 270px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--bg-secondary);
            transition: var(--transition, all 0.3s ease);
        }

        .admin-header {
            height: 72px;
            background: var(--glass-bg-strong);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1010;
            box-shadow: 0 2px 15px -3px rgba(0, 0, 0, 0.04);
        }

        .admin-content {
            padding: 2rem 1.75rem;
            flex-grow: 1;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.25);
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-header {
                padding: 0 1rem;
            }

            .admin-content {
                padding: 1.25rem 1rem;
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
            <div class="loader-subtext">Memuat Panel Admin...</div>
        </div>
    </div>

    <div class="admin-wrapper">
        <!-- Sidebar Navigation Admin -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-brand">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo">
                    <div class="admin-sidebar-brand-text">
                        AL<span style="color: var(--primary)">-HIKMAH</span>
                    </div>
                </a>
            </div>

            <nav class="admin-sidebar-nav">
                <div class="admin-nav-section-title">Menu Utama</div>

                <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard Utama
                </a>

                <div class="admin-nav-section-title mt-2">Manajemen Akademik</div>

                <a href="{{ route('admin.enrollments.index') }}" class="admin-nav-item {{ request()->routeIs('admin.enrollments.index') || request()->routeIs('admin.enrollments.edit') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i> Permohonan Jadwal
                </a>
                <a href="{{ route('admin.active-enrollments.index') }}" class="admin-nav-item {{ request()->routeIs('admin.active-enrollments.*') ? 'active' : '' }}">
                    <i class="bi bi-person-check-fill"></i> Santri & Sesi Aktif
                </a>
                <a href="{{ route('admin.students.index') }}" class="admin-nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Database Santri
                </a>
                <a href="{{ route('admin.mentors.index') }}" class="admin-nav-item {{ request()->routeIs('admin.mentors.index') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Guru Pendamping
                </a>
                <a href="{{ route('admin.mentors.availability') }}" class="admin-nav-item {{ request()->routeIs('admin.mentors.availability') ? 'active' : '' }}">
                    <i class="bi bi-calendar-week"></i> Ketersediaan & Alokasi
                </a>
                <a href="{{ route('admin.programs.index') }}" class="admin-nav-item {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark"></i> Program Belajar
                </a>

                <div class="admin-nav-section-title mt-2">Keuangan & Komunikasi</div>

                <a href="{{ route('admin.payments.index') }}" class="admin-nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Tagihan & SPP Santri
                </a>
                <a href="{{ route('admin.contacts.index') }}" class="admin-nav-item {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-paper"></i> Pesan Konsultasi
                </a>

                <div class="admin-nav-section-title mt-2">Sistem & Pengaturan</div>

                <a href="{{ route('admin.users.index') }}" class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> Pengguna & Hak Akses
                </a>
                <a href="{{ route('admin.settings.index') }}" class="admin-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Pengaturan Website
                </a>
                <a href="{{ route('home') }}" class="admin-nav-item mt-1" target="_blank">
                    <i class="bi bi-globe"></i> Buka Halaman Depan
                </a>

                <hr class="my-3" style="border-color: var(--border-color);">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-item text-danger border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right text-danger"></i> Keluar (Logout)
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Top Header Navbar -->
            <header class="admin-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none rounded-circle" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">@yield('header', 'Panel Administrator')</h5>
                        <small class="text-muted">@yield('subheader', 'Sistem Manajemen Pembelajaran Terpadu AL-HIKMAH')</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <!-- Home Route Link Button -->
                    <a href="{{ route('home') }}" class="btn btn-outline-custom rounded-pill px-3 py-1 btn-sm d-none d-sm-inline-flex align-items-center" target="_blank">
                        <i class="bi bi-house-door me-1 text-success"></i> Beranda
                    </a>

                    <!-- Livewire Realtime Notification Bell Drawer -->
                    <livewire:notification-bell />

                    <!-- Theme Toggle Button -->
                    <button type="button" class="theme-toggle-btn" id="themeToggle" title="Ganti Tema (Gelap/Terang)" aria-label="Ganti Tema">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-custom dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 rounded-pill"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5 text-success"></i>
                            <div class="d-none d-md-block text-start">
                                <div class="fw-semibold text-dark small leading-tight">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">Admin</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-4 border-0 mt-2 p-2">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark small">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.78rem;">{{ auth()->user()->email }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2" href="{{ route('admin.settings.index') }}">
                                    <i class="bi bi-gear text-primary me-2"></i> Pengaturan
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-1">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </li>
                        </ul>
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

    <!-- Global Flash Toast Notification -->
    <x-flash-toast />

    @stack('scripts')
</body>

</html>