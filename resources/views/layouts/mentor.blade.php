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
            padding: 1.15rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .admin-sidebar-brand img {
            height: 38px;
            width: auto;
        }

        .admin-sidebar-brand-text {
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .admin-sidebar-nav {
            padding: 1rem 0.85rem;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .admin-sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .admin-sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.12);
            border-radius: 10px;
        }

        [data-bs-theme="dark"] .admin-sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
        }

        .admin-nav-section-title {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            padding: 14px 12px 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .admin-nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 13px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.88rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 3px;
            border-left: 3px solid transparent;
        }

        .admin-nav-item i {
            font-size: 1.05rem;
            width: 20px;
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

        /* Top Navbar Header Styling */
        .admin-header {
            height: 70px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .header-left-title {
            display: flex;
            flex-direction: column;
        }

        .header-page-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-primary);
            line-height: 1.25;
            margin: 0;
        }

        .header-page-subtitle {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin: 0;
        }

        .header-actions-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-header-home {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 50px;
            color: var(--primary);
            background: var(--primary-lighter);
            border: 1px solid rgba(13, 122, 62, 0.15);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-header-home:hover {
            background: var(--primary);
            color: #ffffff !important;
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 122, 62, 0.2);
        }

        .btn-header-home:hover i {
            color: #ffffff !important;
        }

        .user-profile-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 14px 4px 4px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .user-profile-toggle:hover,
        .user-profile-toggle[aria-expanded="true"] {
            background: var(--card-bg);
            border-color: var(--primary);
            box-shadow: 0 4px 16px rgba(13, 122, 62, 0.12);
        }

        .user-avatar-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.82rem;
            position: relative;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(13, 122, 62, 0.25);
        }

        .user-avatar-status {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            background: #10b981;
            border: 2px solid var(--card-bg);
            border-radius: 50%;
        }

        .user-profile-info {
            text-align: left;
            line-height: 1.2;
        }

        .user-profile-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            max-width: 130px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-profile-role-badge {
            font-size: 0.62rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .dropdown-menu-premium {
            min-width: 260px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 12px 36px -4px rgba(0, 0, 0, 0.12);
            padding: 8px;
            background: var(--card-bg);
            margin-top: 8px !important;
            animation: dropDownFade 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes dropDownFade {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-user-header {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 6px;
            border: 1px solid var(--border-color);
        }

        .dropdown-item-premium {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            font-size: 0.86rem;
            font-weight: 500;
            color: var(--text-secondary);
            border-radius: 10px;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .dropdown-item-premium i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            color: var(--primary);
        }

        .dropdown-item-premium:hover {
            background: var(--primary-lighter);
            color: var(--primary);
            font-weight: 600;
            transform: translateX(2px);
        }

        .dropdown-item-premium.is-logout {
            color: #ef4444;
        }

        .dropdown-item-premium.is-logout i {
            color: #ef4444;
        }

        .dropdown-item-premium.is-logout:hover {
            background: rgba(239, 68, 68, 0.08);
            color: #dc2626;
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
                    <div>
                        <div class="admin-sidebar-brand-text">AL<span style="color: var(--primary)">-HIKMAH</span></div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0" style="font-size: 0.62rem; font-weight: 600;">
                            <i class="bi bi-person-workspace me-1"></i>Portal Guru
                        </span>
                    </div>
                </a>
            </div>

            @php
                $currentMentor = auth()->user()?->mentor;
                $isOfficialMentor = $currentMentor && $currentMentor->is_active && in_array($currentMentor->status, ['active', 'probation']);
            @endphp

            <nav class="admin-sidebar-nav">
                <div class="admin-nav-section-title"><i class="bi bi-grid-fill me-1"></i> {{ $isOfficialMentor ? 'Menu Pendamping' : 'Portal Rekrutmen' }}</div>
                <a href="{{ route('mentor.dashboard') }}"
                    class="admin-nav-item {{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}">
                    <i class="bi {{ $isOfficialMentor ? 'bi-speedometer2' : 'bi-person-check-fill text-primary' }}"></i>
                    {{ $isOfficialMentor ? 'Dashboard Mengajar' : 'Dashboard Calon Guru' }}
                </a>

                @if($isOfficialMentor)
                    <div class="admin-nav-section-title mt-2"><i class="bi bi-calendar-range me-1"></i> Jadwal & Santri</div>
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

                    <div class="admin-nav-section-title mt-2"><i class="bi bi-robot me-1"></i> Evaluasi & Bank Soal AI</div>
                    <a href="{{ route('mentor.questions.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.questions.*') ? 'active' : '' }}">
                        <i class="bi bi-question-square-fill"></i> Bank Soal & Multi-AI
                    </a>

                    <div class="admin-nav-section-title mt-2"><i class="bi bi-journal-text me-1"></i> Pencatatan & Laporan</div>
                    <a href="{{ route('mentor.performance.index') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.performance.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow text-warning"></i> Kinerja Saya & Goals
                    </a>
                    <a href="{{ route('mentor.progress.create') }}"
                        class="admin-nav-item {{ request()->routeIs('mentor.progress.create') ? 'active' : '' }}">
                        <i class="bi bi-pencil-square"></i> Catat Progres Harian
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

                <div class="admin-nav-section-title mt-2"><i class="bi bi-person-gear me-1"></i> Akun & Umum</div>
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
                    <div class="header-left-title">
                        <h5 class="fw-bold mb-0 header-page-title">@yield('header', 'Dashboard Guru / Pendamping')</h5>
                        <small class="text-muted header-page-subtitle">@yield('subheader', 'Semangat membimbing ananda santri AL-HIKMAH')</small>
                    </div>
                </div>

                <div class="header-actions-group">
                    <a href="{{ route('home') }}"
                        class="btn-header-home d-none d-sm-inline-flex"
                        target="_blank"
                        title="Buka Website Publik">
                        <i class="bi bi-globe"></i>
                        <span>Lihat Website</span>
                    </a>

                    <livewire:notification-bell />

                    <button type="button" class="theme-toggle-btn" id="themeToggle" title="Ganti Tema (Gelap/Terang)"
                        aria-label="Ganti Tema">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </button>

                    @php
                        $user = auth()->user();
                        $nameParts = explode(' ', trim($user->name ?? 'Mentor'));
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                        if (empty($initials)) $initials = 'MT';
                    @endphp

                    <div class="dropdown">
                        <button class="user-profile-toggle dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-badge">
                                {{ $initials }}
                                <span class="user-avatar-status"></span>
                            </div>
                            <div class="d-none d-md-block user-profile-info">
                                <div class="user-profile-name">{{ $user->name ?? 'Ustadz/Ustadzah' }}</div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill user-profile-role-badge">
                                    {{ $user->role?->label ?? 'Mentor' }}
                                </span>
                            </div>
                            <i class="bi bi-chevron-down d-none d-md-inline ms-1 text-muted" style="font-size: 0.72rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-premium shadow-lg">
                            <li>
                                <div class="dropdown-user-header">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="user-avatar-badge" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                            {{ $initials }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-dark text-truncate small">{{ $user->name }}</div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.62rem;">
                                                <i class="bi bi-mortarboard-fill me-1"></i>{{ $user->role?->label ?? 'Guru / Mentor' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem; padding-left: 2px;">
                                        <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                    </div>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-premium" href="{{ route('mentor.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard Mengajar
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-item-premium" href="{{ route('mentor.profile') }}">
                                    <i class="bi bi-person-circle"></i> Profil Saya
                                </a>
                            </li>
                            @if($isOfficialMentor)
                                <li>
                                    <a class="dropdown-item dropdown-item-premium" href="{{ route('mentor.availability.index') }}">
                                        <i class="bi bi-clock-history"></i> Atur Ketersediaan
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dropdown-item-premium" href="{{ route('mentor.sessions.index') }}">
                                        <i class="bi bi-calendar-check"></i> Jadwal Sesi Mengajar
                                    </a>
                                </li>
                            @endif
                            <li>
                                <hr class="dropdown-divider my-1.5" style="border-color: var(--border-color); opacity: 0.6;">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item dropdown-item-premium is-logout w-100 border-0 bg-transparent">
                                        <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
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
