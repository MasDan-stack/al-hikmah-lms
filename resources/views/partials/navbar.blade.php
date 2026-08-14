<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar" aria-label="Navigasi utama">
    <div class="container">
        <!-- Brand/Logo -->
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH Logo" height="50" class="brand-logo">
            <span class="brand-name">AL<span class="brand-highlight">HIKMAH</span></span>
        </a>

        <!-- Toggler Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <!-- Beranda -->
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('home') }}#beranda">
                        <i class="bi bi-house-heart"></i> Beranda
                    </a>
                </li>

                <!-- Tentang Kami (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-info-circle"></i> Tentang Kami
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('tentang-kami') }}">
                                <i class="bi bi-building"></i> Profil AL-HIKMAH
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tentang-kami') }}#filosofi">
                                <i class="bi bi-lightbulb"></i> Filosofi & Nilai
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tentang-kami') }}#nilai">
                                <i class="bi bi-heart"></i> Nilai-Nilai Kami
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Program (Dropdown) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-journal-bookmark-fill"></i> Program
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('program') }}">
                                <i class="bi bi-book-half"></i> Program Belajar
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('metode') }}">
                                <i class="bi bi-display"></i> Metode Belajar
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('tahfidz') }}">
                                <i class="bi bi-mic"></i> Program Tahfidz
                            </a>
                        </li>

                        {{-- Hanya Tampil untuk Orang Tua (Parent) dan Admin yang Sudah Login --}}
                        @auth
                            @if (auth()->user()->isParent())
                                <li>
                                    <a class="dropdown-item" href="{{ route('biaya') }}">
                                        <i class="bi bi-info-circle"></i> Informasi Pendampingan
                                    </a>
                                </li>
                            @elseif (auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('biaya') }}">
                                        <i class="bi bi-info-circle"></i> Informasi Pendampingan <span class="badge bg-warning text-dark ms-1">Admin</span>
                                    </a>
                                </li>
                            @endif
                        @endauth

                        {{-- Hanya Tampil untuk Guest (Belum Login / Publik) --}}
                        @guest
                            <li>
                                <a class="dropdown-item" href="{{ route('bergabung') }}">
                                    <i class="bi bi-person-plus"></i> Menjadi Pendamping
                                </a>
                            </li>
                        @endguest
                    </ul>
                </li>

                <!-- Galeri & FAQ Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="bi bi-images"></i> Galeri & FAQ</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}#galeri">
                                <i class="bi bi-images"></i> Galeri
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}#faq">
                                <i class="bi bi-question-circle"></i> FAQ
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Kontak -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#kontak">
                        <i class="bi bi-envelope-paper"></i> Kontak
                    </a>
                </li>
            </ul>

            <!-- Actions (Theme Toggle + Login/Dashboard/Profile) -->
            <div class="navbar-actions d-flex align-items-center gap-2">
                @auth
                    <!-- User Authenticated Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-custom dropdown-toggle d-flex align-items-center gap-2 py-2 px-3 rounded-pill" 
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5 text-success"></i>
                            <span class="fw-semibold text-dark small me-1">{{ auth()->user()->name }}</span>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                                {{ auth()->user()->role?->label ?? 'User' }}
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 border-0 mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                <div class="small text-muted">{{ auth()->user()->email }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2 text-success"></i> Ke Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
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
                    <button type="button" class="btn btn-daftar rounded-pill px-3 py-2" data-bs-toggle="modal" data-bs-target="#daftarModal">
                        <i class="bi bi-pencil-square me-1"></i> Mulai Perjalanan
                    </button>
                @endauth
            </div>
        </div>
    </div>
</nav>
