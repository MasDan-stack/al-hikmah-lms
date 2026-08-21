<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar" aria-label="Navigasi utama">
    <div class="container-fluid px-3 px-md-4 px-xl-5">
        <!-- Brand / Logo -->
        <a class="navbar-brand me-2 me-xl-4" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" height="46" class="brand-logo">
            <span class="brand-name">AL<span class="brand-highlight">HIKMAH</span></span>
        </a>

        <!-- Mobile Toggler + Theme Toggle Container for Small Screens -->
        <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-2">
            <button type="button" class="theme-toggle-btn" id="themeToggleMobile" title="Ganti Tema (Gelap/Terang)" aria-label="Ganti Tema">
                <i class="bi bi-moon-fill" id="themeIconMobile"></i>
            </button>
            @auth
                <livewire:notification-bell />
            @endauth
        </div>

        <!-- Toggler Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Menu Items & Right Actions -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                <!-- 1. Beranda -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                <!-- 2. Tentang Kami (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('tentang-kami*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-info-circle"></i>
                        <span>Tentang Kami</span>
                    </a>
                    <ul class="dropdown-menu shadow-sm border-0 rounded-4">
                        <li>
                            <a class="dropdown-item" href="{{ route('tentang-kami') }}">
                                <i class="bi bi-buildings text-success"></i> Profil AL-HIKMAH
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tentang-kami') }}#filosofi">
                                <i class="bi bi-lightbulb text-warning"></i> Filosofi & Visi Misi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tentang-kami') }}#nilai">
                                <i class="bi bi-patch-check text-primary"></i> Nilai-Nilai Lembaga
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- 3. Program (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('program*') || request()->routeIs('metode*') || request()->routeIs('tahfidz*') || request()->routeIs('biaya*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-journal-bookmark"></i>
                        <span>Program</span>
                    </a>
                    <ul class="dropdown-menu shadow-sm border-0 rounded-4">
                        <li>
                            <a class="dropdown-item" href="{{ route('program') }}">
                                <i class="bi bi-book text-success"></i> Program Belajar
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('metode') }}">
                                <i class="bi bi-laptop text-info"></i> Metode Belajar
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tahfidz') }}">
                                <i class="bi bi-mic text-primary"></i> Program Tahfidz
                            </a>
                        </li>

                        {{-- Tampil untuk Orang Tua & Admin --}}
                        @auth
                            @if (auth()->user()->isParent() || auth()->user()->isAdmin())
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('biaya') }}">
                                        <i class="bi bi-tags text-success"></i> Informasi Pendampingan
                                        @if (auth()->user()->isAdmin())
                                            <span class="badge bg-warning text-dark ms-1">Admin</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endauth

                        {{-- Tampil untuk Guest (Belum Login) --}}
                        @guest
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('bergabung') }}">
                                    <i class="bi bi-person-plus text-primary"></i> Menjadi Pendamping
                                </a>
                            </li>
                        @endguest
                    </ul>
                </li>

                <!-- 4. Alur Belajar (Roadmap) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('roadmap') ? 'active' : '' }}"
                        href="{{ route('roadmap') }}">
                        <i class="bi bi-signpost-2"></i>
                        <span>Alur Belajar</span>
                    </a>
                </li>

                <!-- 5. Galeri & FAQ (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('faq*') ? 'active' : '' }}" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-collection"></i>
                        <span>Galeri & FAQ</span>
                    </a>
                    <ul class="dropdown-menu shadow-sm border-0 rounded-4">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('galeri') ? 'active' : '' }}" href="{{ route('galeri') }}">
                                <i class="bi bi-images text-success"></i> Galeri Kegiatan
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('faq') ? 'active' : '' }}"
                                href="{{ route('faq') }}">
                                <i class="bi bi-question-circle text-info"></i> Tanya Jawab (FAQ)
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- 6. Kontak -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                        href="{{ route('contact') }}">
                        <i class="bi bi-chat-left-dots"></i>
                        <span>Kontak</span>
                    </a>
                </li>
            </ul>

            <!-- Actions (Theme Toggle + Notifications + Login/Dashboard) -->
            <div class="navbar-actions d-flex align-items-center gap-2 gap-lg-3">
                <!-- Theme Toggle Button (Desktop) -->
                <button type="button" class="theme-toggle-btn d-none d-lg-flex" id="themeToggle" title="Ganti Tema (Gelap/Terang)" aria-label="Ganti Tema">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>

                @auth
                    <!-- Livewire Realtime Notification Bell (Desktop) -->
                    <div class="d-none d-lg-block">
                        <livewire:notification-bell />
                    </div>

                    <!-- User Authenticated Profile Dropdown -->
                    <div class="dropdown w-100 w-lg-auto">
                        <button
                            class="btn btn-outline-custom dropdown-toggle d-flex align-items-center justify-content-between justify-content-lg-center gap-2 py-2 px-3 rounded-pill w-100 w-lg-auto"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-circle fs-5 text-success"></i>
                                <span class="fw-semibold text-dark small text-truncate" style="max-width: 130px;">{{ auth()->user()->name }}</span>
                            </div>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                {{ auth()->user()->role?->label ?? 'User' }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-4 border-0 mt-2 p-2">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-dark small">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size: 0.78rem;">{{ auth()->user()->email }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2 text-success"></i> Ke Dashboard
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
                @else
                    <!-- Guest Actions -->
                    <a href="{{ route('login') }}" class="btn btn-outline-custom rounded-pill px-3 py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                    </a>
                    <button type="button" class="btn btn-daftar rounded-pill px-3 py-2" data-bs-toggle="modal"
                        data-bs-target="#daftarModal">
                        <i class="bi bi-pencil-square me-1"></i> Mulai Perjalanan
                    </button>
                @endauth
            </div>
        </div>
    </div>
</nav>
