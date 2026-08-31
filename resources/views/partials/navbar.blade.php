<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar" aria-label="Navigasi utama">
    <div class="container-fluid px-3 px-md-4 px-xl-5">
        <!-- Brand / Logo -->
        <a class="navbar-brand me-2 me-xl-4 d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" height="42" class="brand-logo">
            <div class="d-flex flex-column">
                <span class="brand-name lh-1">AL<span class="brand-highlight">-HIKMAH</span></span>
                <span class="brand-tagline d-none d-sm-inline" style="font-size: 0.65rem; color: var(--text-muted); font-weight: 500; letter-spacing: 0.5px;">Bimbingan Al-Qur'an</span>
            </div>
        </a>

        <!-- Mobile Toggler + Actions Container for Small Screens -->
        <div class="d-flex align-items-center gap-2 d-lg-none ms-auto me-2">
            <button type="button" class="theme-toggle-btn" id="themeToggleMobile" title="Ganti Tema (Gelap/Terang)" aria-label="Ganti Tema">
                <i class="bi bi-moon-fill" id="themeIconMobile"></i>
            </button>
            @auth
                <livewire:notification-bell />
            @endauth
        </div>

        <!-- Toggler Mobile Button -->
        <button class="navbar-toggler border-0 shadow-none p-2 rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Menu Items & Right Actions -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto mb-3 mb-lg-0 align-items-lg-center">
                <!-- 1. Beranda -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                <!-- 2. Tentang Kami (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('tentang-kami*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-compass-fill"></i>
                        <span>Tentang Kami</span>
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-2 mt-2">
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('tentang-kami') }}">
                                <div class="nav-icon-badge bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-buildings-fill fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Profil Lembaga</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Mengenal AL-HIKMAH lebih dekat</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('tentang-kami') }}#filosofi">
                                <div class="nav-icon-badge bg-warning-subtle text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-lightbulb-fill fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Filosofi & Visi Misi</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Landasan bimbingan Al-Qur'an</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('tentang-kami') }}#nilai">
                                <div class="nav-icon-badge bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-patch-check-fill fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Nilai-Nilai Utama</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Integritas & adab Islami</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- 3. Program (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('program*') || request()->routeIs('metode*') || request()->routeIs('tahfidz*') || request()->routeIs('biaya*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-book-half"></i>
                        <span>Program</span>
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-2 mt-2">
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('program') }}">
                                <div class="nav-icon-badge bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-journal-text fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Program Belajar</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Tahsin, Tajwid & Fiqih</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('metode') }}">
                                <div class="nav-icon-badge bg-info-subtle text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-laptop-fill fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Metode Belajar</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Online & Privat Guru Datang</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('tahfidz') }}">
                                <div class="nav-icon-badge bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-mic-fill fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Program Tahfidz</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Hafalan 30 Juz & Mutqin</small>
                                </div>
                            </a>
                        </li>

                        {{-- Tampil untuk Orang Tua & Admin --}}
                        @auth
                            @if (auth()->user()->isParent() || auth()->user()->isAdmin())
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('biaya') }}">
                                        <div class="nav-icon-badge text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(16, 185, 129, 0.12);">
                                            <i class="bi bi-tag-fill fs-6 text-success"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark small d-flex align-items-center gap-2">
                                                Informasi Pendampingan
                                                @if (auth()->user()->isAdmin())
                                                    <span class="badge bg-warning text-dark px-2 py-0" style="font-size: 0.62rem;">Admin</span>
                                                @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 0.72rem;">Paket investasi & biaya</small>
                                        </div>
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                </li>

                <!-- 4. Rekrutmen Guru (v8.3) -->
                @guest
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('bergabung*') || request()->routeIs('mentor.recruitment.*') ? 'active' : '' }}"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-mortarboard-fill"></i>
                            <span>Karir Guru</span>
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-2 mt-2">
                            <li>
                                <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3 {{ request()->routeIs('bergabung') ? 'active' : '' }}" href="{{ route('bergabung') }}">
                                    <div class="nav-icon-badge bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                        <i class="bi bi-person-plus-fill fs-6"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark small">Pendaftaran Guru Baru</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">Bergabung membina Al-Qur'an</small>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3 {{ request()->routeIs('mentor.recruitment.status') ? 'active' : '' }}" href="{{ route('mentor.recruitment.status') }}">
                                    <div class="nav-icon-badge bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                        <i class="bi bi-search-heart-fill fs-6"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark small">Cek Status Lamaran</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">Pantau linimasa seleksi</small>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endguest

                <!-- 5. Alur Belajar (Roadmap) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('roadmap') ? 'active' : '' }}"
                        href="{{ route('roadmap') }}">
                        <i class="bi bi-signpost-split-fill"></i>
                        <span>Alur Belajar</span>
                    </a>
                </li>

                <!-- 6. Galeri & FAQ (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('faq*') || request()->routeIs('galeri*') ? 'active' : '' }}" href="#"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-fill"></i>
                        <span>Galeri & FAQ</span>
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-4 p-2 mt-2">
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3 {{ request()->routeIs('galeri') ? 'active' : '' }}" href="{{ route('galeri') }}">
                                <div class="nav-icon-badge bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-images fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Galeri Kegiatan</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Dokumentasi belajar santri</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3 {{ request()->routeIs('faq') ? 'active' : '' }}"
                                href="{{ route('faq') }}">
                                <div class="nav-icon-badge bg-info-subtle text-info rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-question-circle-fill fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Tanya Jawab (FAQ)</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Informasi umum & bimbingan</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 px-3 d-flex align-items-center gap-3" href="{{ route('home') }}#jadwal-sholat">
                                <div class="nav-icon-badge bg-warning-subtle text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                    <i class="bi bi-clock-history fs-6"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark small">Jadwal Sholat & Kiblat</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Pengingat ibadah harian</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- 7. Blog & Artikel -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('blog*') ? 'active' : '' }}"
                        href="{{ route('blog.index') }}">
                        <i class="bi bi-newspaper"></i>
                        <span>Blog & Artikel</span>
                    </a>
                </li>

                <!-- 8. Kontak -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                        href="{{ route('contact') }}">
                        <i class="bi bi-chat-left-text-fill"></i>
                        <span>Kontak</span>
                    </a>
                </li>
            </ul>

            <!-- Actions (Theme Toggle + Notifications + Login/Dashboard) -->
            <div class="navbar-actions d-flex align-items-center gap-2 gap-lg-3 mt-3 mt-lg-0 pt-2 pt-lg-0 border-top border-lg-0">
                <!-- Theme Toggle Button (Desktop) -->
                <button type="button" class="theme-toggle-btn d-none d-lg-flex shadow-sm" id="themeToggle" title="Ganti Tema (Gelap/Terang)" aria-label="Ganti Tema">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>

                @auth
                    <!-- Livewire Realtime Notification Bell (Desktop) -->
                    <div class="d-none d-lg-block">
                        <livewire:notification-bell />
                    </div>

                    @php
                        $user = auth()->user();
                        $nameParts = explode(' ', trim($user->name ?? 'User'));
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                        if (empty($initials)) $initials = 'AH';
                    @endphp

                    <!-- User Authenticated Profile Dropdown -->
                    <div class="dropdown w-100 w-lg-auto">
                        <button
                            class="btn btn-outline-custom dropdown-toggle d-flex align-items-center justify-content-between justify-content-lg-center gap-2 py-2 px-3 rounded-pill w-100 w-lg-auto shadow-sm"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle"
                                    style="width: 30px; height: 30px; background: linear-gradient(135deg, var(--primary) 0%, #059669 100%); font-size: 0.78rem;">
                                    {{ $initials }}
                                </div>
                                <span class="fw-semibold text-dark small text-truncate" style="max-width: 120px;">{{ $user->name }}</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0" style="font-size: 0.68rem; font-weight: 600;">
                                {{ $user->role?->label ?? 'User' }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg rounded-4 border-0 mt-2 p-2" style="min-width: 250px;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold rounded-circle flex-shrink-0"
                                        style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary) 0%, #059669 100%); font-size: 0.85rem;">
                                        {{ $initials }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate small">{{ $user->name }}</div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.62rem;">
                                            <i class="bi bi-shield-check me-1"></i>{{ $user->role?->label ?? 'User' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-muted text-truncate ps-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 text-success fs-6"></i>
                                    <span class="fw-medium">Buka Dashboard</span>
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
                @else
                    <!-- Guest Actions -->
                    <a href="{{ route('login') }}" class="btn btn-outline-custom rounded-pill px-3 py-2 d-flex align-items-center gap-1 shadow-sm">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk</span>
                    </a>
                    <button type="button" class="btn btn-daftar rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
                        data-bs-target="#daftarModal">
                        <i class="bi bi-pencil-square"></i>
                        <span>Mulai Belajar</span>
                    </button>
                @endauth
            </div>
        </div>
    </div>
</nav>
