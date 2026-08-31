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
    <title>@yield('title', 'Ruang Belajar Santri') | AL-HIKMAH LMS</title>

    <!-- Google Font Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet">

    <!-- Custom CSS AL-HIKMAH -->
    <link href="{{ asset('assets/css/style.css') }}?v={{ file_exists(public_path('assets/css/style.css')) ? filemtime(public_path('assets/css/style.css')) : time() }}" rel="stylesheet">

    <!-- Canvas Confetti for celebrations -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    @stack('styles')

    <style>
        :root {
            --gamify-primary: #0d7a3e;
            --gamify-primary-rgb: 13, 122, 62;
            --gamify-accent: #f59e0b;
            --gamify-gold: #ffc107;
            --gamify-flame: #ef4444;
            --gamify-bg: #f8fafc;
            --gamify-card: #ffffff;
            --gamify-border: #e2e8f0;
            --gamify-text: #0f172a;
            --gamify-muted: #64748b;
        }

        [data-bs-theme="dark"] {
            --gamify-bg: #0b0f19;
            --gamify-card: #131b2e;
            --gamify-border: #1e293b;
            --gamify-text: #f8fafc;
            --gamify-muted: #94a3b8;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--gamify-bg);
            color: var(--gamify-text);
            min-height: 100vh;
        }

        .student-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .student-sidebar {
            width: 270px;
            background: var(--gamify-card);
            border-right: 1px solid var(--gamify-border);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .student-sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--gamify-border);
            flex-shrink: 0;
        }

        .student-sidebar-nav {
            padding: 1rem;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .student-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--gamify-muted);
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 4px;
            border-left: 3px solid transparent;
        }

        .student-nav-item i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }

        .student-nav-item:hover,
        .student-nav-item.active {
            background: rgba(var(--gamify-primary-rgb), 0.1);
            color: var(--gamify-primary);
            font-weight: 600;
            border-left-color: var(--gamify-primary);
        }

        .student-nav-item.active i {
            color: var(--gamify-primary);
        }

        .student-main {
            margin-left: 270px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: var(--gamify-bg);
            transition: margin-left 0.3s ease;
        }

        .student-header {
            height: 72px;
            background: var(--gamify-card);
            border-bottom: 1px solid var(--gamify-border);
            padding: 0 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .flame-streak {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            animation: pulse-glow 2s infinite ease-in-out;
        }

        .points-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        .btn-header-home {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            border-radius: 50px;
            color: var(--gamify-primary);
            background: rgba(var(--gamify-primary-rgb), 0.1);
            border: 1px solid rgba(var(--gamify-primary-rgb), 0.2);
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-header-home:hover {
            background: var(--gamify-primary);
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(var(--gamify-primary-rgb), 0.25);
        }

        .theme-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            padding: 0;
            background: var(--gamify-bg);
            color: var(--gamify-text);
            border: 1px solid var(--gamify-border);
            border-radius: 50%;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .theme-toggle-btn:hover {
            background: rgba(var(--gamify-primary-rgb), 0.1);
            color: var(--gamify-primary);
            border-color: var(--gamify-primary);
            transform: rotate(20deg);
        }

        .user-profile-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 14px 4px 4px;
            background: var(--gamify-bg);
            border: 1px solid var(--gamify-border);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .user-profile-toggle:hover,
        .user-profile-toggle[aria-expanded="true"] {
            background: var(--gamify-card);
            border-color: var(--gamify-primary);
            box-shadow: 0 4px 16px rgba(13, 122, 62, 0.12);
        }

        .user-avatar-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gamify-primary) 0%, #059669 100%);
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
            border: 2px solid var(--gamify-card);
            border-radius: 50%;
        }

        @media (max-width: 991.98px) {
            .student-sidebar {
                transform: translateX(-100%);
            }
            .student-sidebar.show {
                transform: translateX(0);
            }
            .student-main {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="student-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="student-sidebar" id="studentSidebar">
            <div class="student-sidebar-brand">
                <a href="{{ route('student.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" height="36">
                    <div>
                        <div class="fw-bold fs-6 text-success lh-1">AL-HIKMAH</div>
                        <small class="text-muted" style="font-size: 0.72rem; font-weight: 500;">Ruang Santri v8.4</small>
                    </div>
                </a>
            </div>

            <div class="student-sidebar-nav">
                <div class="text-uppercase text-muted fw-bold px-3 py-2" style="font-size: 0.68rem; letter-spacing: 0.8px;">
                    <i class="bi bi-grid-fill me-1"></i> Navigasi Utama
                </div>

                <a href="{{ route('student.dashboard') }}" class="student-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard Belajar</span>
                </a>

                <a href="{{ route('student.targets.today') }}" class="student-nav-item {{ request()->routeIs('student.targets.today') ? 'active' : '' }}">
                    <i class="bi bi-bullseye text-danger"></i>
                    <span>Target Hari Ini</span>
                </a>

                <a href="{{ route('student.progress.juz') }}" class="student-nav-item {{ request()->routeIs('student.progress.juz') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-steps text-success"></i>
                    <span>Progress 30 Juz</span>
                </a>

                <a href="{{ route('student.milestones') }}" class="student-nav-item {{ request()->routeIs('student.milestones') ? 'active' : '' }}">
                    <i class="bi bi-flag-fill text-warning"></i>
                    <span>Target Milestone</span>
                </a>

                <div class="text-uppercase text-muted fw-bold px-3 pt-3 pb-2" style="font-size: 0.68rem; letter-spacing: 0.8px;">
                    <i class="bi bi-trophy-fill me-1 text-warning"></i> Motivasi & Prestasi
                </div>

                <a href="{{ route('student.leaderboard') }}" class="student-nav-item {{ request()->routeIs('student.leaderboard') ? 'active' : '' }}">
                    <i class="bi bi-trophy-fill text-warning"></i>
                    <span>Leaderboard</span>
                </a>

                <a href="{{ route('student.badges') }}" class="student-nav-item {{ request()->routeIs('student.badges*') ? 'active' : '' }}">
                    <i class="bi bi-award-fill text-primary"></i>
                    <span>Koleksi Lencana</span>
                </a>

                <a href="{{ route('student.stats') }}" class="student-nav-item {{ request()->routeIs('student.stats') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow text-info"></i>
                    <span>Poin & Statistik</span>
                </a>

                <div class="text-uppercase text-muted fw-bold px-3 pt-3 pb-2" style="font-size: 0.68rem; letter-spacing: 0.8px;">
                    <i class="bi bi-gear-fill me-1"></i> Akun & Keamanan
                </div>

                <a href="{{ route('student.password.index') }}" class="student-nav-item {{ request()->routeIs('student.password.index') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill text-secondary"></i>
                    <span>Ganti Password</span>
                </a>

                <a href="{{ route('home') }}" class="student-nav-item mt-2" target="_blank">
                    <i class="bi bi-globe"></i>
                    <span>Buka Halaman Depan</span>
                </a>
            </div>

            <!-- Profile Footer -->
            <div class="p-3 border-top border-secondary-subtle d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                        style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--gamify-primary) 0%, #059669 100%); font-size: 0.85rem;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate small">{{ auth()->user()->name ?? 'Santri' }}</div>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">Santri Binaan</small>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 rounded-3" title="Keluar">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="student-main">
            <!-- Header -->
            <header class="student-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary btn-sm d-lg-none rounded-circle" id="sidebarToggleBtn"
                        style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;"
                        aria-label="Toggle Sidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold header-page-title" style="font-size: 1.1rem;">@yield('header', 'Ruang Belajar Santri')</h5>
                        <small class="text-muted" style="font-size: 0.78rem;">@yield('subheader', 'Semangat muroja\'ah dan menghafal Al-Qur\'an!')</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <!-- Quick Home Link -->
                    <a href="{{ route('home') }}"
                        class="btn-header-home d-none d-md-inline-flex"
                        target="_blank"
                        title="Buka Website Publik">
                        <i class="bi bi-globe"></i>
                        <span>Lihat Website</span>
                    </a>

                    @php
                        $activeStudent = auth()->user()->student ?? \App\Models\Student::where('user_id', auth()->id())->first();
                        $user = auth()->user();
                        $nameParts = explode(' ', trim($user->name ?? 'Santri'));
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                        if (empty($initials)) $initials = 'ST';
                    @endphp

                    @if($activeStudent)
                        <div class="flame-streak d-none d-sm-inline-flex shadow-sm" title="Streak hafalan berturut-turut">
                            <i class="bi bi-fire"></i>
                            <span>{{ $activeStudent->current_streak ?: 0 }} Hari</span>
                        </div>

                        <div class="points-badge d-none d-sm-inline-flex shadow-sm" title="Total Poin Gamifikasi">
                            <i class="bi bi-star-fill"></i>
                            <span>{{ number_format($activeStudent->total_points ?: 0) }} Pts</span>
                        </div>
                    @endif

                    <livewire:notification-bell />

                    <!-- Dark Mode Toggle Button -->
                    <button type="button" class="theme-toggle-btn shadow-sm" id="themeToggle" title="Ganti Tema (Gelap/Terang)" aria-label="Ganti Tema">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </button>

                    <!-- Student User Dropdown -->
                    <div class="dropdown">
                        <button class="user-profile-toggle dropdown-toggle border-0 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-badge">
                                {{ $initials }}
                                <span class="user-avatar-status"></span>
                            </div>
                            <div class="d-none d-md-block text-start">
                                <div class="fw-semibold text-dark text-truncate" style="font-size: 0.85rem; max-width: 120px;">{{ $user->name ?? 'Santri' }}</div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0" style="font-size: 0.62rem;">
                                    Santri
                                </span>
                            </div>
                            <i class="bi bi-chevron-down d-none d-md-inline ms-1 text-muted" style="font-size: 0.72rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-4 border-0 mt-2 p-2" style="min-width: 250px;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="user-avatar-badge" style="width: 34px; height: 34px; font-size: 0.8rem;">
                                        {{ $initials }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate small">{{ $user->name }}</div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.62rem;">
                                            <i class="bi bi-mortarboard-fill me-1"></i>Santri Binaan
                                        </span>
                                    </div>
                                </div>
                                <div class="text-muted text-truncate ps-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="{{ route('student.dashboard') }}">
                                    <i class="bi bi-speedometer2 text-success fs-6"></i>
                                    <span class="fw-medium">Dashboard Belajar</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="{{ route('student.password.index') }}">
                                    <i class="bi bi-shield-lock-fill text-warning fs-6"></i>
                                    <span class="fw-medium">Ganti Password</span>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-1">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded-3 py-2 text-danger d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right fs-6"></i>
                                        <span class="fw-medium">Keluar (Logout)</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Alerts -->
            <div class="container-fluid px-4 pt-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i> Mohon periksa kembali input Anda:
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="container-fluid px-4 py-3 flex-grow-1">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="py-3 px-4 border-top border-secondary-subtle text-center text-muted" style="font-size: 0.8rem;">
                &copy; {{ date('Y') }} <strong>AL-HIKMAH LMS</strong>. Modul Gamifikasi Islami & Ruang Belajar Santri.
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>

    <script>
        // Toggle Sidebar Mobile
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const studentSidebar = document.getElementById('studentSidebar');
        if (sidebarToggleBtn && studentSidebar) {
            sidebarToggleBtn.addEventListener('click', () => {
                studentSidebar.classList.toggle('show');
            });
        }

        // Dark Mode Switcher
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        function updateThemeIcon(theme) {
            if (themeIcon) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill text-warning';
                } else {
                    themeIcon.className = 'bi bi-moon-fill';
                }
            }
        }

        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        updateThemeIcon(currentTheme);

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const active = document.documentElement.getAttribute('data-bs-theme') || 'light';
                const next = active === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', next);
                localStorage.setItem('alhikmah-theme', next);
                updateThemeIcon(next);
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
