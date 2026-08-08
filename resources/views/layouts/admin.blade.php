<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AL-HIKMAH LMS')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #0d7a3e;
            min-height: 100vh;
            width: 280px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h4 {
            color: white;
            font-weight: 700;
        }
        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
        }
        .sidebar-nav {
            padding: 1rem;
        }
        .sidebar-nav a {
            display: block;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
            margin-bottom: 4px;
        }
        .sidebar-nav a:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar-nav a.active {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .sidebar-nav a i {
            margin-right: 10px;
            width: 20px;
        }
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }
        .top-header {
            background-color: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .page-content {
            padding: 2rem;
        }
        .badge-role {
            background-color: rgba(13, 122, 62, 0.1);
            color: #0d7a3e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-toggle {
                display: block !important;
            }
        }
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #0d7a3e;
        }
    </style>
</head>
<body>

<!-- Mobile Sidebar Toggle -->
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h4>AL-HIKMAH</h4>
        <small>Learning Management System</small>
    </div>
    
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="#" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Murid
        </a>
        <a href="#" class="{{ request()->routeIs('admin.mentors.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Pendamping
        </a>
        <a href="#" class="{{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i> Jadwal Sesi
        </a>
        <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-white text-decoration-none" style="padding: 0.75rem 1rem; display: flex; align-items: center; gap: 10px; width: 100%; border: none; background: none; color: rgba(255,255,255,0.7);">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <header class="top-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">@yield('header', 'Dashboard')</h5>
            <small class="text-muted">@yield('subheader', 'Selamat datang!')</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-sm fw-semibold">{{ auth()->user()->name }}</span>
            <span class="badge-role">{{ auth()->user()->role->display_name ?? 'Admin' }}</span>
        </div>
    </header>

    <!-- Page Content -->
    <div class="page-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>

</body>
</html>