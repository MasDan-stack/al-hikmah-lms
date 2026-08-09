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
                            <a class="dropdown-item" href="{{ route('home') }}#tentang">
                                <i class="bi bi-building"></i> Profil AL-HIKMAH
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}#filosofi">
                                <i class="bi bi-lightbulb"></i> Filosofi & Nilai
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('home') }}#harapan">
                                <i class="bi bi-flag"></i> Harapan Kami
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
                            <a class="dropdown-item" href="{{ route('home') }}#program">
                                <i class="bi bi-book-half"></i> Iqra & Al-Qur'an
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('metode') }}"><i class="bi bi-display"></i> Metode
                                Belajar</a></li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('tahfidz') }}"><i class="bi bi-mic"></i> Program
                                Tahfidz</a></li>
                        <li><a class="dropdown-item" href="{{ route('biaya') }}"><i class="bi bi-info-circle"></i>
                                Informasi
                                Pendampingan</a></li>

                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="bi bi-images"></i> Galeri & FAQ</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="galeri.html"><i class="bi bi-images"></i> Galeri</a></li>
                        <li><a class="dropdown-item" href="faq.html"><i class="bi bi-question-circle"></i> FAQ</a>
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

            <!-- Actions (Theme Toggle + Login/Dashboard) -->
            <div class="navbar-actions">
                <button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle dark mode"><i
                        class="bi bi-moon-fill" id="themeIcon"></i></button>

                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-daftar">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                    <a href="#" class="btn btn-daftar" data-bs-toggle="modal" data-bs-target="#daftarModal"><i
                            class="bi bi-pencil-square me-1"></i> Mulai Perjalanan</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-daftar">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
