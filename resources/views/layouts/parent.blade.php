<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Orang Tua') | AL-HIKMAH LMS</title>

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/poppins@5.1.1/index.min.css" rel="stylesheet">

    <!-- Custom CSS AL-HIKMAH -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 260px; background: var(--card-bg); border-right: 1px solid var(--border-color);
            position: fixed; top: 0; bottom: 0; left: 0; z-index: 1020; display: flex; flex-direction: column;
        }
        .admin-sidebar-brand { padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--border-color); }
        .admin-sidebar-brand img { height: 40px; }
        .admin-sidebar-brand-text { font-weight: 800; font-size: 1.1rem; color: var(--text-primary); }
        .admin-sidebar-nav { padding: 1.25rem 1rem; flex-grow: 1; overflow-y: auto; }
        .admin-nav-item {
            display: flex; align-items: center; gap: 12px; padding: 10px 16px; color: var(--text-secondary);
            font-weight: 500; font-size: 0.9rem; border-radius: var(--radius-md); text-decoration: none; margin-bottom: 4px;
        }
        .admin-nav-item:hover, .admin-nav-item.active { background: var(--primary-lighter); color: var(--primary); }
        .admin-main { margin-left: 260px; flex-grow: 1; display: flex; flex-direction: column; min-height: 100vh; background: var(--bg-secondary); }
        .admin-header {
            height: 70px; background: var(--glass-bg-strong); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color); padding: 0 2rem; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1010;
        }
        .admin-content { padding: 2rem; flex-grow: 1; }
        .admin-user-badge {
            display: inline-flex; align-items: center; gap: 8px; background: var(--primary-lighter);
            color: var(--primary); padding: 6px 16px; border-radius: var(--radius-full); font-size: 0.85rem; font-weight: 600;
        }
        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigation Parent -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-brand">
                <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo">
                <div class="admin-sidebar-brand-text">
                    AL<span style="color: var(--primary)">-HIKMAH</span>
                </div>
            </div>

            <nav class="admin-sidebar-nav">
                <div class="text-xs text-uppercase fw-bold text-muted px-3 mb-2">Portal Orang Tua</div>

                <a href="{{ route('parent.dashboard') }}" class="admin-nav-item {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('parent.children.index') }}" class="admin-nav-item {{ request()->routeIs('parent.children.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Anak & Progress
                </a>
                <a href="{{ route('parent.schedules.index') }}" class="admin-nav-item {{ request()->routeIs('parent.schedules.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i> Jadwal Belajar
                </a>
                <a href="{{ route('parent.enrollments.index') }}" class="admin-nav-item {{ request()->routeIs('parent.enrollments.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-plus"></i> Pendaftaran & Negosiasi
                </a>
                <a href="{{ route('parent.payments.index') }}" class="admin-nav-item {{ request()->routeIs('parent.payments.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Tagihan & SPP
                </a>
                <a href="{{ route('parent.messages.index') }}" class="admin-nav-item {{ request()->routeIs('parent.messages.*') ? 'active' : '' }}">
                    <i class="bi bi-chat-dots"></i> Pesan & Chat
                </a>
                <a href="{{ route('parent.profile.edit') }}" class="admin-nav-item {{ request()->routeIs('parent.profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Profil & Akun
                </a>

                <a href="{{ route('home') }}" class="admin-nav-item mt-3" target="_blank">
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

        <!-- Main Area -->
        <div class="admin-main">
            <header class="admin-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">@yield('header', 'Dashboard Orang Tua')</h5>
                        <small class="text-muted">@yield('subheader', 'Selamat datang di Panel Orang Tua AL-HIKMAH')</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @livewire('parent.parent-notifications')
                    <div class="admin-user-badge">
                        <i class="bi bi-person-heart"></i>
                        <span>Wali Santri: {{ auth()->user()->name ?? 'Bpk/Ibu' }}</span>
                    </div>
                </div>
            </header>

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggleBtn')?.addEventListener('click', function() {
            document.getElementById('adminSidebar')?.classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
