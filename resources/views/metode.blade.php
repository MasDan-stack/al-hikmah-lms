@extends('layouts.landing')

@section('title', 'Metode Belajar | AL-HIKMAH')
@section('description', 'Metode belajar AL-HIKMAH dengan pilihan Online, Offline (Home Visit), dan Hybrid. Sistem pendampingan fleksibel untuk keluarga.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. BREADCRUMB HEADER -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg" aria-label="Header Metode Belajar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-grid-3x3-gap-fill"></i> Metode Pembelajaran</div>
                        <h2>Cara Kami <span class="text-gradient">Mendampingi</span></h2>
                        <p>Pendekatan personal, hangat, dan fleksibel untuk kenyamanan belajar santri dan keluarga di rumah.</p>
                    </div>
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
                <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small mb-2">
                    <i class="bi bi-compass-fill me-1"></i> Jalur Belajar Fleksibel
                </div>
                <h3 class="fw-bold" style="color: var(--text-primary);">Pilih Format Belajar yang Paling Nyaman</h3>
                <p class="text-secondary mx-auto" style="max-width: 680px;">
                    Setiap keluarga memiliki ritme dan kesibukan yang berbeda. AL-HIKMAH menyediakan tiga pilihan bimbingan privat agar ananda belajar Al-Qur'an tanpa rasa terbebani.
                </p>
            </div>

            <div class="row g-4">
                <!-- 1. Online Learning -->
                <div class="col-lg-4 col-md-6" data-reveal>
                    <div class="metode-card hover-lift">
                        <div>
                            <div class="metode-icon-box">
                                <i class="bi bi-laptop"></i>
                            </div>
                            <h4 class="fw-bold mb-2" style="color: var(--text-primary);">Online Learning</h4>
                            <p class="text-secondary small mb-4">
                                Bimbingan tatap maya interaktif berkualitas tinggi via Zoom / Google Meet dengan pantauan layar ganda.
                            </p>

                            <div class="mb-4">
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Pendampingan 1 guru 1 santri secara privat tanpa distraksi</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Akses tanpa batas wilayah dari seluruh kota di Indonesia</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Papan tulis digital interaktif &amp; rekaman evaluasi tajwid</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Jadwal sangat fleksibel bagi keluarga yang sering bepergian</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="p-2 rounded-3 bg-body-tertiary mb-3 text-center border">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Rekomendasi Santri:</small>
                                <span class="fw-semibold small" style="color: var(--text-primary);">Usia 10+ Tahun, Remaja &amp; Dewasa</span>
                            </div>
                            <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Online') }}"
                               class="btn_2 w-100 text-center py-2 rounded-pill shadow-sm" target="_blank">
                                <i class="bi bi-whatsapp text-success me-1"></i> Pilih Online
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. Offline (Home Visit) - Rekomendasi Utama -->
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="metode-card metode-featured hover-lift shadow">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-success text-white px-3 py-1 rounded-pill small badge-pulse">
                                ⭐ Rekomendasi Utama
                            </span>
                        </div>

                        <div>
                            <div class="metode-icon-box" style="background: var(--primary); color: #ffffff;">
                                <i class="bi bi-house-door-fill"></i>
                            </div>
                            <h4 class="fw-bold mb-2" style="color: var(--text-primary);">Offline (Home Visit)</h4>
                            <p class="text-secondary small mb-4">
                                Guru bersanad hadir langsung ke kediaman Anda untuk mendampingi santri secara tatap muka penuh.
                            </p>

                            <div class="mb-4">
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Koreksi makhrajul huruf &amp; sifat huruf secara presisi dan detail</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Santri merasa nyaman, tenang, dan aman di lingkungan rumah sendiri</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Interaksi adab Islami dan keteladanan guru tersampaikan utuh</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Orang tua dapat memantau langsung proses belajar secara transparan</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="p-2 rounded-3 bg-success-subtle mb-3 text-center border border-success-subtle">
                                <small class="text-success d-block fw-semibold" style="font-size: 0.75rem;">Area Layanan:</small>
                                <span class="fw-bold small text-success">Jabodetabek &amp; Sekitarnya</span>
                            </div>
                            <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Offline (Home Visit)') }}"
                               class="btn_1 w-100 text-center py-2 rounded-pill shadow" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> Pilih Home Visit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. Hybrid Flexible -->
                <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="metode-card hover-lift">
                        <div>
                            <div class="metode-icon-box">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <h4 class="fw-bold mb-2" style="color: var(--text-primary);">Hybrid Flexible</h4>
                            <p class="text-secondary small mb-4">
                                Paduan seimbang antara sesi tatap muka langsung di rumah dan sesi daring saat jadwal padat.
                            </p>

                            <div class="mb-4">
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Solusi sempurna bagi orang tua dengan mobilitas kerja tinggi</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Setoran hafalan tetap berjalan tanpa tertunda saat tugas ke luar kota</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Pemantauan progres terpusat melalui satu akun LMS Al-Hikmah</span>
                                </div>
                                <div class="metode-check-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Kombinasi fleksibel yang dapat disepakati bersama mentor privat</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="p-2 rounded-3 bg-body-tertiary mb-3 text-center border">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Rekomendasi Santri:</small>
                                <span class="fw-semibold small" style="color: var(--text-primary);">Keluarga dengan Jadwal Dinamis</span>
                            </div>
                            <a href="{{ wa_url('Assalamualaikum, saya ingin menanyakan bimbingan metode Hybrid') }}"
                               class="btn_2 w-100 text-center py-2 rounded-pill shadow-sm" target="_blank">
                                <i class="bi bi-whatsapp text-success me-1"></i> Pilih Hybrid
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Navigasi Lanjutan -->
            <div class="text-center mt-5" data-reveal>
                <a href="{{ route('program') }}" class="btn_2 me-2 mb-2">
                    <i class="bi bi-journal-bookmark me-1"></i> Lihat Program
                </a>
                @auth
                    @if (auth()->user()->isParent())
                        <a href="{{ route('biaya') }}" class="btn_1 mb-2">
                            <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan
                        </a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('biaya') }}" class="btn_1 mb-2">
                            <i class="bi bi-info-circle me-1"></i> Informasi Pendampingan (Kamu Administrator)
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. TABEL PERBANDINGAN METODE SECARA DETAIL -->
    <!-- ============================================ -->
    <section class="section-padding bg-body-tertiary" aria-label="Tabel Perbandingan Metode">
        <div class="container">
            <div class="text-center mb-4" data-reveal>
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small mb-2" style="background-color: var(--primary-lighter) !important; color: var(--primary) !important; border-color: var(--border-color) !important;">
                    <i class="bi bi-table me-1"></i> Perbandingan Lengkap
                </div>
                <h3 class="fw-bold" style="color: var(--text-primary);">Tabel Perbandingan Metode Belajar</h3>
                <p class="text-secondary small mx-auto" style="max-width: 600px;">
                    Bandingkan karakteristik tiap metode untuk menentukan format yang paling tepat untuk ananda.
                </p>
            </div>

            <div class="table-comparison-wrapper shadow-sm" data-reveal>
                <table class="table-comparison">
                    <thead>
                        <tr>
                            <th style="width: 28%;">Parameter Penilaian</th>
                            <th style="width: 24%;"><i class="bi bi-laptop me-1 text-success"></i> Online Learning</th>
                            <th style="width: 24%;" class="bg-success-subtle text-success"><i class="bi bi-house-door-fill me-1"></i> Home Visit (Favorit)</th>
                            <th style="width: 24%;"><i class="bi bi-arrow-repeat me-1 text-warning"></i> Hybrid Flexible</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-body">Koreksi Makhraj &amp; Tajwid</td>
                            <td>Tinggi (Audio visual HD)</td>
                            <td class="fw-bold text-success">Maksimal (Tatap muka langsung)</td>
                            <td>Tinggi &amp; Fleksibel</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-body">Kenyamanan Tempat Belajar</td>
                            <td>Di mana saja via gawai</td>
                            <td class="fw-bold text-success">Kediaman keluarga sendiri</td>
                            <td>Kombinasi rumah &amp; online</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-body">Interaksi Adab &amp; Hubungan Emosional</td>
                            <td>Baik &amp; Fokus Privat</td>
                            <td class="fw-bold text-success">Sangat Dekat &amp; Meneladani</td>
                            <td>Seimbang &amp; Adaptif</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-body">Kebutuhan Fasilitas</td>
                            <td>Laptop/Tablet &amp; Internet stabil</td>
                            <td class="fw-bold text-success">Ruang belajar tenang di rumah</td>
                            <td>Gawai &amp; Ruang belajar rumah</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-body">Cakupan Wilayah</td>
                            <td>Seluruh Indonesia &amp; Luar Negeri</td>
                            <td class="fw-bold text-success">Jabodetabek &amp; Sekitarnya</td>
                            <td>Jabodetabek (Tatap muka) + Online</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-body">Jaminan Pengganti Sesi</td>
                            <td>Tersedia penjadwalan ulang</td>
                            <td class="fw-bold text-success">Tersedia penjadwalan ulang</td>
                            <td>Tersedia penjadwalan ulang</td>
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
                <div class="section-badge mx-auto mb-2"><i class="bi bi-calendar-range"></i> Intensitas Belajar</div>
                <h3 class="fw-bold" style="color: var(--text-primary);">Ritme Belajar yang Mengedepankan Keistiqamahan</h3>
                <p class="text-secondary mx-auto" style="max-width: 640px;">
                    Yang kami kejar bukan sekadar kecepatan menuntaskan halaman, melainkan kecintaan anak membaca Al-Qur'an setiap hari.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-reveal>
                    <div class="p-4 rounded-4 border h-100 text-center hover-lift" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="p-3 rounded-circle bg-success-subtle text-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                            <i class="bi bi-1-circle-fill fs-3"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">1x Seminggu</h5>
                        <p class="text-secondary small mb-0">Membangun ritme dan kebiasaan awal santri untuk mencintai Al-Qur'an tanpa tekanan.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-reveal data-reveal-delay="100">
                    <div class="p-4 rounded-4 border h-100 text-center hover-lift" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="p-3 rounded-circle bg-warning-subtle text-warning mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                            <i class="bi bi-calendar2-week-fill fs-3"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">2–3x Seminggu</h5>
                        <p class="text-secondary small mb-0">Rekomendasi ideal untuk santri tahsin dan tahfidz agar bacaan lancar dan mutqin.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-reveal data-reveal-delay="200">
                    <div class="p-4 rounded-4 border h-100 text-center hover-lift" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="p-3 rounded-circle bg-success-subtle text-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Jadwal Khusus</h5>
                        <p class="text-secondary small mb-0">Waktu fleksibel ba'da Ashar, Maghrib, atau akhir pekan yang dapat disesuaikan agenda sekolah.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-reveal data-reveal-delay="300">
                    <div class="p-4 rounded-4 border h-100 text-center hover-lift" style="background: var(--card-bg); border-color: var(--border-color) !important;">
                        <div class="p-3 rounded-circle bg-warning-subtle text-warning mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                            <i class="bi bi-person-check-fill fs-3"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Privat 1-on-1</h5>
                        <p class="text-secondary small mb-0">Fokus perhatian penuh satu guru untuk satu anak sesuai kecepatan daya tangkap ananda.</p>
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
            <div class="p-4 p-md-5 rounded-4 border shadow-sm" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%); color: #ffffff;" data-reveal>
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold small">
                                <i class="bi bi-geo-alt-fill me-1"></i> Cakupan Layanan
                            </span>
                        </div>
                        <h3 class="fw-bold text-white mb-2">Melayani Home Visit di Seluruh Area Jabodetabek</h3>
                        <p class="text-white-50 mb-3" style="font-size: 0.95rem;">
                            Guru privat AL-HIKMAH siap datang langsung ke rumah Anda di Jakarta, Bogor, Depok, Tangerang, dan Bekasi. Bagi Anda yang berdomisili di luar Jabodetabek, bimbingan privat online tersedia dengan kualitas interaksi yang sama baiknya.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">DKI Jakarta</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Kota &amp; Kab. Bogor</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Depok</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Tangerang &amp; Tangsel</span>
                            <span class="badge bg-white text-dark px-3 py-2 rounded-pill small fw-semibold">Kota &amp; Kab. Bekasi</span>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill small fw-semibold">+ Seluruh Indonesia (Online)</span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin mengecek ketersediaan guru home visit di domisili saya') }}"
                           class="btn btn-light text-success fw-bold px-4 py-3 rounded-pill shadow"
                           target="_blank">
                            <i class="bi bi-whatsapp me-2"></i> Cek Ketersediaan Area
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

