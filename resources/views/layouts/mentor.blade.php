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
    <title>@yield('title', 'Dashboard Mentor') | AL-HIKMAH LMS</title>

    <!-- Google Font Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- DataTables CSS (Bootstrap 5 & Responsive) -->
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet">

    <!-- Custom CSS AL-HIKMAH (Cache Busting) -->
    <link href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}" rel="stylesheet">
    @stack('styles')

    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 280px;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .admin-sidebar-brand img {
            height: 40px;
            width: auto;
        }

        .admin-sidebar-brand-text {
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .admin-sidebar-nav {
            padding: 1rem 1rem;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .admin-nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            padding: 12px 14px 6px;
        }

        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 4px;
            border-left: 3px solid transparent;
        }

        .admin-nav-item i {
            font-size: 1.1rem;
            width: 22px;
            text-align: center;
            color: var(--text-muted);
            transition: color 0.2s ease;
        }

        .admin-nav-item:hover,
        .admin-nav-item.active {
            background: var(--primary-lighter);
            color: var(--primary);
            font-weight: 600;
            border-left-color: var(--primary);
        }

        .admin-nav-item:hover i,
        .admin-nav-item.active i {
            color: var(--primary);
        }

        .admin-main {
            margin-left: 280px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--bg-secondary);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-header {
            height: 72px;
            background: var(--glass-bg-strong);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border-color);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.03);
        }

        .admin-content {
            padding: 2rem;
            flex-grow: 1;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1025;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-header {
                padding: 0 1.25rem;
                height: 64px;
            }

            .admin-content {
                padding: 1.5rem 1.25rem;
            }
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
            <div class="loader-subtext">Memuat Dashboard Mentor...</div>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-wrapper">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-brand">
                <a href="{{ route('mentor.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo">
                    <div class="admin-sidebar-brand-text">AL<span style="color: var(--primary)">-HIKMAH</span></div>
                </a>
            </div>

            @php
                $currentMentor = auth()->user()?->mentor;
                $isOfficialMentor = $currentMentor && $currentMentor->is_active && in_array($currentMentor->status, ['active', 'probation']);
            @endphp

            <nav class="admin-sidebar-nav">
                <div class="admin-nav-section-title">{{ $isOfficialMentor ? 'Menu Pendamping' : 'Portal Rekrutmen' }}</div>
                <a href="{{ route('mentor.dashboard') }}"
                    class="admin-nav-item {{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}">
                    <i class="bi {{ $isOfficialMentor ? 'bi-speedometer2' : 'bi-person-check-fill text-primary' }}"></i>
                    {{ $isOfficialMentor ? 'Dashboard Mengajar' : 'Dashboard Calon Guru' }}
                </a>

                @if($isOfficialMentor)
                    <a href="{{ route('mentor.sessions.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.sessions.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i> Jadwal Sesi Mengajar
                    </a>
                    <a href="{{ route('mentor.students.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.students.index') || request()->routeIs('mentor.students.show') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Santri Binaan
                    </a>
                    <a href="{{ route('mentor.students.parents') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.students.parents') ? 'active' : '' }}">
                        <i class="bi bi-person-lines-fill"></i> Data Orang Tua
                    </a>
                    <a href="{{ route('mentor.availability.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.availability.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i> Atur Ketersediaan
                    </a>
                    <a href="{{ route('mentor.leaves.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.leaves.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-x-fill text-warning"></i> Pengajuan Cuti & Pengganti
                    </a>

                    <div class="admin-nav-section-title mt-2">Evaluasi & Bank Soal AI</div>
                    <a href="{{ route('mentor.questions.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.questions.*') ? 'active' : '' }}">
                        <i class="bi bi-question-square-fill"></i> Bank Soal & AI Generator
                    </a>

                    <div class="admin-nav-section-title mt-2">Pencatatan & Laporan</div>
                    <a href="{{ route('mentor.progress.create') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.progress.create') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square"></i> Catat Progres
                    </a>
                    <a href="{{ route('mentor.progress.bulk-create') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.progress.bulk*') ? 'active' : '' }}">
                        <i class="bi bi-layers-fill"></i> Catat Progres Massal
                    </a>
                    <a href="{{ route('mentor.reports.export') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-pdf"></i> Laporan Kinerja
                    </a>
                    <a href="{{ route('mentor.messages.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.messages.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots-fill"></i> Pesan & Diskusi
                    </a>
                @endif

                <div class="admin-nav-section-title mt-2">Akun & Umum</div>
                <a href="{{ route('mentor.profile') }}"
                    class="admin-nav-item {{ request()->routeIs('mentor.profile') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>
                <a href="{{ route('home') }}" class="admin-nav-item mt-1" target="_blank">
                    <i class="bi bi-globe"></i> Buka Halaman Depan
                </a>

                <hr class="my-3" style="border-color: var(--border-color); opacity: 0.5;">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-item text-danger border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right text-danger"></i> Keluar (Logout)
                    </button>
                </form>
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none rounded-circle" id="sidebarToggleBtn"
                        aria-label="Toggle Sidebar"
                        style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="fw-bold mb-0 text-primary" style="font-size: 1.1rem;">@yield('header', 'Dashboard Guru / Pendamping')</h5>
                        <small class="text-muted" style="font-size: 0.8rem;">@yield('subheader', 'Semangat membimbing ananda santri AL-HIKMAH')</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <a href="{{ route('home') }}"
                        class="btn btn-outline-custom rounded-pill px-3 py-1 btn-sm d-none d-sm-inline-flex align-items-center"
                        target="_blank">
                        <i class="bi bi-house-door me-1" style="color: var(--primary);"></i> Beranda
                    </a>
                    <livewire:notification-bell />
                    <button type="button" class="theme-toggle-btn" id="themeToggle" title="Ganti Tema"
                        aria-label="Ganti Tema">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </button>

                    <div class="dropdown">
                        <button
                            class="btn btn-outline-custom dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 rounded-pill"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5" style="color: var(--primary);"></i>
                            <div class="d-none d-md-block text-start">
                                <div class="fw-semibold small leading-tight" style="color: var(--text-primary);">
                                    {{ auth()->user()->name ?? 'Ustadz/Ustadzah' }}</div>
                            </div>
                            <span class="badge rounded-pill px-2 py-1 small"
                                style="background: var(--primary-lighter); color: var(--primary);">Mentor</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-4 border-0 mt-2 p-2"
                            style="min-width: 220px;">
                            <li class="px-3 py-2 border-bottom mb-1"
                                style="border-color: var(--border-color) !important;">
                                <div class="fw-bold small" style="color: var(--text-primary);">
                                    {{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2" href="{{ route('mentor.profile') }}">
                                    <i class="bi bi-person me-2" style="color: var(--primary);"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2"
                                    href="{{ route('mentor.availability.index') }}">
                                    <i class="bi bi-clock-history me-2" style="color: var(--primary);"></i> Atur
                                    Ketersediaan
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-1" style="border-color: var(--border-color);">
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

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <x-flash-toast />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatables-init.js') }}?v={{ file_exists(public_path('assets/js/datatables-init.js')) ? filemtime(public_path('assets/js/datatables-init.js')) : time() }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}?v={{ file_exists(public_path('assets/js/scripts.js')) ? filemtime(public_path('assets/js/scripts.js')) : time() }}"></script>
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggleBtn');

        function toggleSidebar() {
            sidebar?.classList.toggle('show');
            overlay?.classList.toggle('show');
        }
        toggleBtn?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);
    </script>
    @stack('scripts')
</body>

</html>
