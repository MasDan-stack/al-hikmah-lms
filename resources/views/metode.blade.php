@extends('layouts.landing')

@section('title', 'Metode Belajar | AL-HIKMAH')
@section('description', 'Metode belajar AL-HIKMAH dengan pilihan Online, Offline (Home Visit), dan Hybrid. Sistem pendampingan fleksibel untuk keluarga.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. BREADCRUMB HEADER (EDITORIAL MINIMALIST) -->
    <!-- ============================================ -->
    <section class="editorial-page-header" aria-label="Header Metode Belajar">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center" data-reveal>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item text-muted">Program</li>
                            <li class="breadcrumb-item active" aria-current="page">Metode Belajar</li>
                        </ol>
                    </nav>

                    <div class="editorial-badge mx-auto">
                        <i class="bi bi-compass-fill"></i>
                        <span>Metode Pembelajaran</span>
                    </div>

                    <h1 class="editorial-title">Cara Kami Mendampingi Santri</h1>
                    <p class="editorial-subtitle mx-auto">
                        Pendekatan privat, sabar, dan bertahap untuk kenyamanan belajar santri dan ketenangan keluarga di rumah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. PILIHAN 3 METODE BELAJAR UTAMA -->
    <!-- ============================================ -->
    <section class="section-padding" aria-label="Pilihan Metode Belajar">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill small fw-semibold mb-2 d-inline-block">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Jalur Belajar Fleksibel
                </span>
                <h2 class="editorial-title text-center mb-2">Pilih Format Belajar yang Paling Sesuai</h2>
                <p class="text-secondary mx-auto" style="max-width: 680px; line-height: 1.65;">
                    Setiap keluarga memiliki ritme dan kesibukan yang berbeda. AL-HIKMAH menyediakan tiga pilihan format bimbingan agar ananda dapat belajar Al-Qur'an dengan tenang dan nyaman.
                </p>
            </div>

            <div class="row g-4">
                <!-- 1. Offline (Home Visit) -->
                <div class="col-lg-4 col-md-6" data-reveal>
                    <div class="why-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="why-icon mb-0">
                                    <i class="bi bi-house-door-fill"></i>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold">
                                    Pilihan Utama
                                </span>
                            </div>
                            <h4>Offline (Home Visit)</h4>
                            <p class="mb-4">
                                Guru pembimbing hadir langsung ke kediaman Anda untuk mendampingi santri secara tatap muka penuh 90 menit.
                            </p>

                            <div class="mb-4">
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Koreksi makhrajul huruf dan sifat huruf secara langsung dan detail</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Santri belajar di lingkungan rumah yang aman dan akrab</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Interaksi adab Islami dan keteladanan tersampaikan langsung</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Orang tua dapat memantau proses bimbingan secara langsung</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-top">
                            <div class="p-2.5 rounded-3 bg-body-tertiary mb-3 text-center border">
                                <small class="text-secondary d-block" style="font-size: 0.78rem;">Area Layanan:</small>
                                <span class="fw-semibold small text-heading">Jabodetabek &amp; Sekitarnya</span>
                            </div>
                            <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin menanyakan bimbingan metode Offline (Home Visit)') }}"
                               class="btn-editorial-primary w-100 text-center py-2.5" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-1.5"></i> Tanya Layanan Home Visit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. Online Learning -->
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="why-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="why-icon mb-0">
                                    <i class="bi bi-laptop"></i>
                                </div>
                                <span class="badge bg-body-tertiary text-secondary border rounded-pill px-2.5 py-1 small fw-semibold">
                                    Fleksibel Luar Kota
                                </span>
                            </div>
                            <h4>Online Interaktif</h4>
                            <p class="mb-4">
                                Bimbingan privat tatap maya via Zoom atau Google Meet dengan panduan mushaf digital dan mikrofon berkualitas.
                            </p>

                            <div class="mb-4">
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Pendampingan 1 guru 1 santri tanpa distraksi ruang kelas</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Dapat diakses dari seluruh kota di Indonesia dan mancanegara</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Papan mushaf digital interaktif untuk evaluasi tanda baca</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Jadwal adaptif bagi keluarga yang sering bepergian</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-top">
                            <div class="p-2.5 rounded-3 bg-body-tertiary mb-3 text-center border">
                                <small class="text-secondary d-block" style="font-size: 0.78rem;">Kebutuhan Perangkat:</small>
                                <span class="fw-semibold small text-heading">Laptop / Tablet &amp; Koneksi Internet</span>
                            </div>
                            <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin menanyakan bimbingan metode Online Interaktif') }}"
                               class="btn-editorial-secondary w-100 text-center py-2.5" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-1.5"></i> Tanya Layanan Online
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. Hybrid Flexible -->
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="why-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="why-icon mb-0">
                                    <i class="bi bi-arrow-repeat"></i>
                                </div>
                                <span class="badge bg-body-tertiary text-secondary border rounded-pill px-2.5 py-1 small fw-semibold">
                                    Kombinasi Adaptif
                                </span>
                            </div>
                            <h4>Hybrid Adaptif</h4>
                            <p class="mb-4">
                                Kombinasi terencana antara sesi tatap muka di rumah dan sesi daring saat ada agenda keluarga ke luar kota.
                            </p>

                            <div class="mb-4">
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Solusi bagi keluarga dengan mobilitas kerja dan perjalanan dinamis</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Rutinitas murajaah hafalan tetap terjaga tanpa terputus</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2.5 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Laporan mutaba'ah terpusat melalui satu portal akun orang tua</span>
                                </div>
                                <div class="d-flex align-items-start gap-2 small text-secondary">
                                    <i class="bi bi-check-circle-fill text-primary mt-0.5 flex-shrink-0"></i>
                                    <span>Penyesuaian sesi dapat dikomunikasikan bersama guru pembimbing</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-top">
                            <div class="p-2.5 rounded-3 bg-body-tertiary mb-3 text-center border">
                                <small class="text-secondary d-block" style="font-size: 0.78rem;">Rekomendasi Pengguna:</small>
                                <span class="fw-semibold small text-heading">Keluarga dengan Mobilitas Tinggi</span>
                            </div>
                            <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin menanyakan bimbingan metode Hybrid') }}"
                               class="btn-editorial-secondary w-100 text-center py-2.5" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-1.5"></i> Tanya Layanan Hybrid
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Navigasi Lanjutan -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-5" data-reveal>
                <a href="{{ route('program') }}" class="btn-editorial-secondary px-4 py-2.5">
                    <i class="bi bi-journal-bookmark me-1.5"></i> Lihat Pilihan Program
                </a>
                @auth
                    @if (auth()->user()->isParent() || auth()->user()->isAdmin())
                        <a href="{{ route('biaya') }}" class="btn-editorial-primary px-4 py-2.5">
                            <i class="bi bi-info-circle me-1.5"></i> Rincian Paket &amp; Investasi
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. TABEL PERBANDINGAN METODE SECARA DETAIL -->
    <!-- ============================================ -->
    <section class="section-padding bg-body-tertiary border-top border-bottom" aria-label="Tabel Perbandingan Metode">
        <div class="container">
            <div class="text-center mb-4" data-reveal>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill small fw-semibold mb-2 d-inline-block">
                    <i class="bi bi-table me-1"></i> Perbandingan Lengkap
                </span>
                <h3 class="editorial-title text-center mb-2">Perbandingan Karakteristik Setiap Format</h3>
                <p class="text-secondary small mx-auto" style="max-width: 600px; line-height: 1.6;">
                    Gunakan tabel perbandingan di bawah ini untuk menimbang format bimbingan yang paling nyaman bagi ananda dan keluarga.
                </p>
            </div>

            <div class="table-responsive rounded-4 border bg-card shadow-sm" data-reveal>
                <table class="table align-middle mb-0" style="min-width: 680px;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-3 px-4 text-heading fw-bold" style="width: 28%;">Parameter Penilaian</th>
                            <th scope="col" class="py-3 px-3 text-heading fw-bold" style="width: 24%;"><i class="bi bi-laptop me-1 text-primary"></i> Online Interaktif</th>
                            <th scope="col" class="py-3 px-3 text-heading fw-bold bg-success-subtle text-success" style="width: 24%;"><i class="bi bi-house-door-fill me-1"></i> Home Visit (Pilihan Utama)</th>
                            <th scope="col" class="py-3 px-3 text-heading fw-bold" style="width: 24%;"><i class="bi bi-arrow-repeat me-1 text-secondary"></i> Hybrid Adaptif</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <tr>
                            <td class="py-3 px-4 fw-semibold text-heading">Koreksi Makhraj &amp; Tajwid</td>
                            <td class="py-3 px-3 text-secondary">Jelas melalui audio visual definisi tinggi</td>
                            <td class="py-3 px-3 fw-bold text-success bg-success-subtle bg-opacity-25">Langsung dan mendalam secara tatap muka</td>
                            <td class="py-3 px-3 text-secondary">Menyesuaikan sesi yang sedang berjalan</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 fw-semibold text-heading">Kenyamanan Tempat Belajar</td>
                            <td class="py-3 px-3 text-secondary">Di mana saja dengan perangkat kerja</td>
                            <td class="py-3 px-3 fw-bold text-success bg-success-subtle bg-opacity-25">Kediaman keluarga sendiri yang tenang</td>
                            <td class="py-3 px-3 text-secondary">Kombinasi rumah dan fleksibilitas daring</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 fw-semibold text-heading">Interaksi Adab &amp; Keteladanan</td>
                            <td class="py-3 px-3 text-secondary">Terarah dan fokus satu santri</td>
                            <td class="py-3 px-3 fw-bold text-success bg-success-subtle bg-opacity-25">Sangat dekat dan mudah diteladani</td>
                            <td class="py-3 px-3 text-secondary">Seimbang dan terjadwal berkala</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 fw-semibold text-heading">Fasilitas yang Disiapkan</td>
                            <td class="py-3 px-3 text-secondary">Gawai / laptop dan koneksi internet stabil</td>
                            <td class="py-3 px-3 fw-bold text-success bg-success-subtle bg-opacity-25">Ruang belajar yang tenang dan meja mengaji</td>
                            <td class="py-3 px-3 text-secondary">Ruang belajar di rumah dan gawai daring</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 fw-semibold text-heading">Cakupan Wilayah Layanan</td>
                            <td class="py-3 px-3 text-secondary">Seluruh wilayah Indonesia dan mancanegara</td>
                            <td class="py-3 px-3 fw-bold text-success bg-success-subtle bg-opacity-25">Jabodetabek dan wilayah sekitarnya</td>
                            <td class="py-3 px-3 text-secondary">Jabodetabek (Home Visit) + Online</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 fw-semibold text-heading">Penjadwalan Pengganti Sesi</td>
                            <td class="py-3 px-3 text-secondary">Tersedia koordinasi jadwal pengganti</td>
                            <td class="py-3 px-3 fw-bold text-success bg-success-subtle bg-opacity-25">Tersedia koordinasi jadwal pengganti</td>
                            <td class="py-3 px-3 text-secondary">Tersedia koordinasi jadwal pengganti</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. SISTEM INTENSITAS PENDAMPINGAN -->
    <!-- ============================================ -->
    <section class="section-padding" aria-label="Sistem Intensitas Pendampingan">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill small fw-semibold mb-2 d-inline-block">
                    <i class="bi bi-calendar-range"></i> Intensitas Belajar
                </span>
                <h3 class="editorial-title text-center mb-2">Ritme Bimbingan yang Mengutamakan Istiqamah</h3>
                <p class="text-secondary mx-auto" style="max-width: 640px; line-height: 1.65;">
                    Fokus utama kami bukan sekadar kecepatan menghabiskan halaman, melainkan keteraturan ananda membaca Al-Qur'an dengan adab dan tartil.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-reveal>
                    <div class="why-card text-center p-4">
                        <div class="why-icon mx-auto mb-3">
                            <i class="bi bi-1-circle-fill"></i>
                        </div>
                        <h4 class="fs-6 fw-bold mb-2">1x Seminggu</h4>
                        <p class="small text-secondary mb-0">Membangun ritme dan kebiasaan awal santri untuk mencintai Al-Qur'an secara bertahap.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="why-card text-center p-4">
                        <div class="why-icon mx-auto mb-3">
                            <i class="bi bi-calendar2-week-fill"></i>
                        </div>
                        <h4 class="fs-6 fw-bold mb-2">2–3x Seminggu</h4>
                        <p class="small text-secondary mb-0">Ritme ideal untuk santri tahsin dan tahfidz agar bacaan lancar dan mutqin.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="why-card text-center p-4">
                        <div class="why-icon mx-auto mb-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4 class="fs-6 fw-bold mb-2">Jadwal Khusus</h4>
                        <p class="small text-secondary mb-0">Pilihan waktu ba'da Ashar, Maghrib, atau akhir pekan yang dapat diselaraskan dengan agenda sekolah.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-reveal data-reveal-delay="300">
                    <div class="why-card text-center p-4">
                        <div class="why-icon mx-auto mb-3">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <h4 class="fs-6 fw-bold mb-2">Privat 1-on-1</h4>
                        <p class="small text-secondary mb-0">Perhatian penuh satu guru untuk satu santri sesuai kecepatan daya tangkap ananda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5. JABODETABEK & CAKUPAN AREA CARD -->
    <!-- ============================================ -->
    <section class="section-padding pt-0" aria-label="Informasi Wilayah Layanan">
        <div class="container">
            <div class="p-4 p-md-5 rounded-4 border shadow-sm" style="background: linear-gradient(135deg, #064e3b 0%, #0d7a3e 100%); color: #ffffff;" data-reveal>
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold small">
                                <i class="bi bi-geo-alt-fill me-1"></i> Cakupan Layanan
                            </span>
                        </div>
                        <h3 class="fw-bold text-white mb-2">Melayani Home Visit di Area Jabodetabek</h3>
                        <p class="text-white-50 mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            Guru privat AL-HIKMAH siap datang langsung ke kediaman Anda di Jakarta, Bogor, Depok, Tangerang, dan Bekasi. Bagi keluarga yang berdomisili di luar Jabodetabek, bimbingan privat online tersedia dengan kualitas interaksi yang setara.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">DKI Jakarta</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Kota &amp; Kab. Bogor</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Depok</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Tangerang &amp; Tangsel</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Kota &amp; Kab. Bekasi</span>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill small fw-semibold">Seluruh Indonesia (Online)</span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin mengecek ketersediaan guru home visit di domisili saya') }}"
                           class="btn btn-light text-success fw-bold px-4 py-3 rounded-pill shadow d-inline-flex align-items-center gap-2"
                           target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp"></i> Cek Ketersediaan Wilayah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
