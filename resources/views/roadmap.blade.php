@extends('layouts.landing')

@section('title', 'Peta Alur & Panduan Langkah Belajar (Roadmap) | AL-HIKMAH')
@section('meta_description', 'Panduan alur langkah demi langkah untuk calon orang tua murid, calon guru pendamping, dan
    alur pembayaran di AL-HIKMAH LMS.')

@section('content')
    <!-- ============================================================ -->
    <!-- 1. ETRAIN BREADCRUMB HEADER -->
    <!-- ============================================================ -->
    <section class="breadcrumb_bg" aria-label="Header Peta Alur Belajar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-map"></i> Panduan Langkah Awal</div>
                        <h2>Peta Perjalanan Belajar <span class="text-gradient">AL-HIKMAH</span></h2>
                        <p>Panduan langkah terarah mulai dari pendaftaran, pencocokan guru &amp; jadwal, hingga proses bimbingan belajar berjalan lancar.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- ROADMAP CONTENT TABS -->
    <!-- ============================================================ -->
    <section class="section-padding pt-4" aria-label="Roadmap Section">
        <div class="container">

            <!-- ========================================================== -->
            <!-- 🔥 FIX: Navigation Pills - Menggunakan data-bs-toggle yang benar -->
            <!-- ========================================================== -->
            <div class="d-flex justify-content-center mb-5" data-reveal>
                <ul class="nav nav-pills gap-2" id="roadmapTab" role="tablist"
                    style="background:var(--bg-secondary);padding:8px;border-radius:50px;border:1px solid var(--border-color);flex-wrap:wrap;justify-content:center;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 py-2 fw-bold" id="parent-tab" data-bs-toggle="pill"
                            data-bs-target="#parent-journey" type="button" role="tab" aria-controls="parent-journey"
                            aria-selected="true">
                            <i class="bi bi-people-fill me-2"></i> Jalur Calon Orang Tua
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold" id="mentor-tab" data-bs-toggle="pill"
                            data-bs-target="#mentor-journey" type="button" role="tab" aria-controls="mentor-journey"
                            aria-selected="false">
                            <i class="bi bi-person-workspace me-2"></i> Jalur Guru / Pendamping
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold" id="payment-tab" data-bs-toggle="pill"
                            data-bs-target="#payment-journey" type="button" role="tab" aria-controls="payment-journey"
                            aria-selected="false">
                            <i class="bi bi-wallet2 me-2"></i> Alur Pembayaran &amp; SPP
                        </button>
                    </li>
                </ul>
            </div>

            <!-- ========================================================== -->
            <!-- 🔥 FIX: Tab Content - Class 'show active' hanya di parent -->
            <!-- ========================================================== -->
            <div class="tab-content" id="roadmapTabContent">

                <!-- ========================================================== -->
                <!-- TAB 1: JALUR CALON ORANG TUA -->
                <!-- ========================================================== -->
                <div class="tab-pane fade show active" id="parent-journey" role="tabpanel" aria-labelledby="parent-tab">
                    <div class="text-center mb-5">
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-shield-check me-1"></i> Alur Zero-Friction
                        </span>
                        <h3 class="fw-bold mt-2">6 Langkah Menuju Bimbingan Al-Qur'an Ananda</h3>
                        <p class="text-muted small">Mulai dari konsultasi gratis hingga ananda dibimbing oleh guru yang
                            tepat.</p>
                    </div>

                    <div class="row g-4">
                        <!-- Step 1 - 6 (sama seperti sebelumnya, tidak diubah) -->
                        @for ($i = 1; $i <= 6; $i++)
                            <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($i - 1) * 100 }}">
                                <div
                                    class="card h-100 border-0 shadow-sm rounded-4 p-4 position-relative card-hover-up bg-white">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 46px; height: 46px; font-size: 1.2rem; flex-shrink:0;">
                                            {{ $i }}</div>
                                        <div>
                                            <span class="text-muted small d-block">Langkah
                                                {{ ['Pertama', 'Kedua', 'Ketiga', 'Keempat', 'Kelima', 'Keenam'][$i - 1] }}</span>
                                            <h5 class="fw-bold mb-0">
                                                @switch($i)
                                                    @case(1)
                                                        Eksplorasi Program
                                                    @break

                                                    @case(2)
                                                        Pilih Jadwal &amp; Guru
                                                    @break

                                                    @case(3)
                                                        Registrasi Akun
                                                    @break

                                                    @case(4)
                                                        Review &amp; Deal Jadwal
                                                    @break

                                                    @case(5)
                                                        Pembayaran Tagihan
                                                    @break

                                                    @case(6)
                                                        Mulai Belajar &amp; Progres
                                                    @break
                                                @endswitch
                                            </h5>
                                        </div>
                                    </div>
                                    <p class="text-secondary small mb-3">
                                        @switch($i)
                                            @case(1)
                                                Telusuri kurikulum bimbingan pada menu <strong>Program</strong> (Iqra, Tahsin,
                                                Tahfidz, Bahasa Arab) atau rincian investasi di halaman <strong>Biaya</strong>.
                                            @break

                                            @case(2)
                                                Klik tombol <em>"Pilih Program &amp; Ajukan Jadwal"</em>. Tentukan hari bimbingan
                                                (misal: Senin &amp; Kamis) dan jam yang diinginkan keluarga.
                                            @break

                                            @case(3)
                                                Sistem otomatis membuat akun <strong>Orang Tua</strong> dan akun
                                                <strong>Santri</strong>. Anda langsung diarahkan ke halaman pemantauan pendaftaran.
                                            @break

                                            @case(4)
                                                Admin mencocokkan kuota guru pembimbing. Saat jadwal disetujui, Anda menerima
                                                notifikasi beserta rincian nama Guru dan Tanggal Mulai.
                                            @break

                                            @case(5)
                                                Lakukan pembayaran mudah melalui <strong>Payment Gateway Otomatis</strong> (QRIS,
                                                GoPay, Transfer Bank VA). Tagihan otomatis lunas seketika.
                                            @break

                                            @case(6)
                                                Jadwal otomatis dibuat untuk 4 minggu ke depan. Pantau nilai tajwid, hafalan,
                                                kehadiran ananda, dan unduh laporan PDF resmi kapan saja.
                                            @break
                                        @endswitch
                                    </p>
                                    <div class="mt-auto pt-2 border-top">
                                        @switch($i)
                                            @case(1)
                                                <a href="{{ route('program') }}"
                                                    class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                    <i class="bi bi-book-half me-1"></i> Lihat Program
                                                </a>
                                            @break

                                            @case(2)
                                                 @auth
                                                     @if (auth()->user()->isParent())
                                                         @php
                                                             $latestEnrollment = isset($parentEnrollments) ? $parentEnrollments->first() : null;
                                                         @endphp

                                                         @if ($latestEnrollment && $latestEnrollment->isWaitingAdmin())
                                                              <a href="{{ route('parent.enrollments.show', $latestEnrollment->id) }}"
                                                                  class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm">
                                                                  <i class="bi bi-hourglass-split me-1"></i> Sedang Direview ({{ $latestEnrollment->program?->name }})
                                                              </a>
                                                              <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                                  <i class="bi bi-info-circle me-1"></i> Lembaga sedang mereview jadwal &amp; kuota guru
                                                              </div>
                                                          @elseif ($latestEnrollment && $latestEnrollment->isWaitingParent())
                                                              <a href="{{ route('parent.enrollments.show', $latestEnrollment->id) }}"
                                                                  class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold shadow-sm">
                                                                  <i class="bi bi-chat-dots me-1"></i> Konfirmasi Jadwal ({{ $latestEnrollment->program?->name }})
                                                              </a>
                                                              <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                                  <i class="bi bi-exclamation-circle me-1"></i> Ada alternatif jadwal dari lembaga
                                                              </div>
                                                          @elseif ($latestEnrollment && $latestEnrollment->isConfirmed())
                                                              <a href="{{ route('parent.enrollments.show', $latestEnrollment->id) }}"
                                                                  class="btn btn-sm btn-primary-custom rounded-pill px-3 fw-bold shadow-sm">
                                                                  <i class="bi bi-wallet2 me-1"></i> Siap Bayar: {{ $latestEnrollment->program?->name }}
                                                              </a>
                                                              <div class="text-success mt-1 fw-medium" style="font-size: 0.75rem;">
                                                                  <i class="bi bi-check-circle me-1"></i> Jadwal disetujui! Lanjutkan ke pembayaran
                                                              </div>
                                                          @elseif ($latestEnrollment && $latestEnrollment->isActive())
                                                              <a href="{{ route('parent.enrollments.show', $latestEnrollment->id) }}"
                                                                  class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                                                  <i class="bi bi-award-fill me-1"></i> Program Aktif: {{ $latestEnrollment->program?->name }}
                                                              </a>
                                                              <div class="text-success mt-1 fw-medium" style="font-size: 0.75rem;">
                                                                  <i class="bi bi-person-check me-1"></i> Santri: {{ $latestEnrollment->student?->getDisplayName() }}
                                                              </div>
                                                          @else
                                                             <a href="{{ route('biaya') }}"
                                                                 class="btn btn-sm btn-primary-custom rounded-pill px-3">
                                                                 <i class="bi bi-journal-check me-1"></i> Pilih Program
                                                             </a>
                                                         @endif
                                                     @elseif (auth()->user()->isAdmin())
                                                         <a href="{{ route('biaya') }}"
                                                             class="btn btn-sm btn-primary-custom rounded-pill px-3">
                                                             <i class="bi bi-journal-check me-1"></i> Pilih Program (Admin)
                                                         </a>
                                                     @else
                                                         <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                                                             <i class="bi bi-info-circle me-1"></i> Mode {{ auth()->user()->isMentor() ? 'Guru / Pendamping' : 'Santri' }}
                                                         </span>
                                                     @endif
                                                 @else
                                                     <button type="button" class="btn btn-sm btn-primary-custom rounded-pill px-3"
                                                         data-bs-toggle="modal" data-bs-target="#daftarModal">
                                                         <i class="bi bi-calendar-plus me-1"></i> Booking Jadwal
                                                     </button>
                                                 @endauth
                                             @break

                                            @case(3)
                                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-clock-history me-1"></i> Status: Menunggu Review Admin
                                                </span>
                                            @break

                                            @case(4)
                                                <span
                                                    class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-check-circle me-1"></i> Status: Jadwal Deal (CONFIRMED)
                                                </span>
                                            @break

                                            @case(5)
                                                <span class="badge bg-info-subtle text-info border px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-qr-code-scan me-1"></i> Instant Auto-Activation
                                                </span>
                                            @break

                                            @case(6)
                                                <span class="badge bg-success text-white px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-award-fill me-1"></i> Bimbingan Aktif Berjalan
                                                </span>
                                            @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- ========================================================== -->
                <!-- TAB 2: JALUR CALON GURU / PENDAMPING -->
                <!-- ========================================================== -->
                <div class="tab-pane fade" id="mentor-journey" role="tabpanel" aria-labelledby="mentor-tab">
                    <div class="text-center mb-5">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-person-heart me-1"></i> Karir &amp; Syiar Qur'ani
                        </span>
                        <h3 class="fw-bold mt-2">6 Tahapan Menjadi Guru Pendamping AL-HIKMAH</h3>
                        <p class="text-muted small">Mari bersama mendampingi generasi Qur'ani dengan jadwal fleksibel dan
                            teknologi modern.</p>
                    </div>

                    <div class="row g-4">
                        @for ($i = 1; $i <= 6; $i++)
                            <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($i - 1) * 100 }}">
                                <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 46px; height: 46px; font-size: 1.2rem; flex-shrink:0;">
                                            {{ $i }}</div>
                                        <div>
                                            <span class="text-muted small d-block">Tahap {{ $i }}</span>
                                            <h5 class="fw-bold mb-0">
                                                @switch($i)
                                                    @case(1)
                                                        Registrasi Guru
                                                    @break

                                                    @case(2)
                                                        Seleksi &amp; Verifikasi
                                                    @break

                                                    @case(3)
                                                        Atur Ketersediaan
                                                    @break

                                                    @case(4)
                                                        Terima Alokasi Santri
                                                    @break

                                                    @case(5)
                                                        Bimbingan &amp; Input Progres
                                                    @break

                                                    @case(6)
                                                        Edukasi Berkelanjutan
                                                    @break
                                                @endswitch
                                            </h5>
                                        </div>
                                    </div>
                                    <p class="text-secondary small mb-3">
                                        @switch($i)
                                            @case(1)
                                                Akses halaman <strong>Bergabung</strong> dan isi biodata, spesialisasi mengajar
                                                (Tahsin, Tahfidz, Anak/Dewasa), serta pengalaman bimbingan Anda.
                                            @break

                                            @case(2)
                                                Tim manajemen AL-HIKMAH akan meninjau kualifikasi bacaan Al-Qur'an dan wawancara
                                                komitmen pengajaran.
                                            @break

                                            @case(3)
                                                Login ke Portal Mentor dan atur hari mengajar yang bisa Anda sanggupi, batas
                                                maksimal santri per hari, dan jam luang di menu <em>Atur Jadwal</em>.
                                            @break

                                            @case(4)
                                                Saat santri baru memilih Anda dan menyelesaikan pembayaran, data santri binaan dan
                                                jadwal otomatis muncul di portal Anda lengkap dengan nomor WA wali santri.
                                            @break

                                            @case(5)
                                                Laksanakan sesi bimbingan (Online / Home Visit) dan gunakan fitur <strong>Catat
                                                    Progres Massal</strong> untuk menginput nilai tajwid &amp; hafalan dalam
                                                hitungan detik.
                                            @break

                                            @case(6)
                                                Ikuti pelatihan rutin dan workshop metodologi pengajaran Al-Qur'an yang
                                                diselenggarakan oleh AL-HIKMAH untuk pengembangan kapasitas mengajar.
                                            @break
                                        @endswitch
                                    </p>
                                    <div class="mt-auto pt-2 border-top">
                                        @switch($i)
                                            @case(1)
                                                <a href="{{ route('bergabung') }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="bi bi-pencil-square me-1"></i> Form Bergabung
                                                </a>
                                            @break

                                            @case(2)
                                                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-shield-check me-1"></i> Standar Kualitas Pengajar
                                                </span>
                                            @break

                                            @case(3)
                                                <span
                                                    class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-calendar3 me-1"></i> Fleksibilitas Waktu Penuh
                                                </span>
                                            @break

                                            @case(4)
                                                <span
                                                    class="badge bg-success-subtle text-success border px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-person-check-fill me-1"></i> Data Kontak Real-Time
                                                </span>
                                            @break

                                            @case(5)
                                                <span class="badge bg-info-subtle text-info border px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-graph-up-arrow me-1"></i> Laporan &amp; Rekap Otomatis
                                                </span>
                                            @break

                                            @case(6)
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small">
                                                    <i class="bi bi-mortarboard-fill me-1"></i> Pengembangan Profesional
                                                </span>
                                            @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- ========================================================== -->
                <!-- TAB 3: ALUR PEMBAYARAN & SPP -->
                <!-- ========================================================== -->
                <div class="tab-pane fade" id="payment-journey" role="tabpanel" aria-labelledby="payment-tab">
                    <div class="text-center mb-5">
                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-shield-lock-fill me-1"></i> Transparan &amp; Amanah
                        </span>
                        <h3 class="fw-bold mt-2">Siklus Pembayaran "Deal Dulu Baru Bayar"</h3>
                        <p class="text-muted small">Tidak ada biaya tersembunyi. Pembayaran hanya dilakukan setelah jadwal
                            dan guru 100% cocok.</p>
                    </div>

                    <div class="row g-4">
                        @for ($i = 1; $i <= 6; $i++)
                            @php
                                $colors = ['secondary', 'warning', 'success', 'info', 'primary', 'dark'];
                                $color = $colors[$i - 1];
                            @endphp
                            <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($i - 1) * 100 }}">
                                <div
                                    class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white border-top border-4 border-{{ $color }}">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="rounded-circle bg-{{ $color }} text-white fw-bold d-flex align-items-center justify-content-center shadow-sm"
                                            style="width: 46px; height: 46px; font-size: 1.2rem; flex-shrink:0;">
                                            {{ $i }}</div>
                                        <div>
                                            <span class="text-muted small d-block">Fase {{ $i }}</span>
                                            <h5 class="fw-bold mb-0">
                                                @switch($i)
                                                    @case(1)
                                                        Pengajuan Awal
                                                    @break

                                                    @case(2)
                                                        Terbit Tagihan (Deal)
                                                    @break

                                                    @case(3)
                                                        Pelunasan Otomatis (QRIS / VA)
                                                    @break

                                                    @case(4)
                                                        SPP Bulan Berikutnya
                                                    @break

                                                    @case(5)
                                                        Riwayat &amp; Monitoring
                                                    @break

                                                    @case(6)
                                                        Siklus Berkelanjutan
                                                    @break
                                                @endswitch
                                            </h5>
                                        </div>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        @switch($i)
                                            @case(1)
                                                Wali santri mengajukan jadwal. <strong>Belum ada tagihan</strong> yang diterbitkan
                                                oleh sistem di tabel payments.
                                            @break

                                            @case(2)
                                                Setelah Admin dan Wali sepakat jadwal &amp; guru, sistem menerbitkan tagihan
                                                pendaftaran 1x + SPP bulan pertama (Status: Pending).
                                            @break

                                            @case(3)
                                                Pembayaran lunas via QRIS/VA secara otomatis mengaktifkan status santri, mengunci
                                                jadwal guru, dan membuat 4 minggu sesi belajar.
                                            @break

                                            @case(4)
                                                Untuk bulan ke-2 dan seterusnya, tagihan hanya berupa <strong>SPP murni</strong>
                                                (tanpa biaya pendaftaran lagi) yang terbit 7 hari sebelum jatuh tempo.
                                            @break

                                            @case(5)
                                                Semua transaksi tercatat di <strong>Riwayat Pembayaran</strong>. Orang tua dapat
                                                memonitor status tagihan kapan saja.
                                            @break

                                            @case(6)
                                                Sistem otomatis menerbitkan tagihan SPP bulanan rutin. Orang tua tinggal bayar dan
                                                fokus pada perkembangan anak.
                                            @break
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

            </div><!-- end tab-content -->

            <!-- ============================================================ -->
            <!-- BOTTOM CALL TO ACTIONS -->
            <!-- ============================================================ -->
            <div class="mt-5 p-4 p-md-5 rounded-4 bg-primary-subtle border border-primary-subtle text-center" data-reveal>
                <h4 class="fw-bold text-primary-emphasis mb-2">Masih Memiliki Pertanyaan Khusus?</h4>
                <p class="text-secondary small mb-4">Tim konsultan AL-HIKMAH siap membantu Anda mencocokkan program dan
                    jadwal belajar yang tepat untuk keluarga.</p>
                <div class="d-flex justify-content-center flex-wrap gap-3">
                    @if (Route::has('faq'))
                        <a href="{{ route('faq') }}" class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold">
                            <i class="bi bi-question-circle me-1"></i> Buka Halaman FAQ
                        </a>
                    @endif
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                        <i class="bi bi-envelope-paper me-1"></i> Kirim Pesan Konsultasi
                    </a>
                    <a href="{{ wa_url('Assalamualaikum, saya ingin berkonsultasi mengenai alur pendaftaran AL-HIKMAH') }}"
                        target="_blank" class="btn btn-success rounded-pill px-4 py-2 fw-bold">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp CS
                    </a>
                </div>
            </div>

        </div><!-- end container -->
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('#roadmapTab button[data-bs-toggle="pill"]');
            const tabPanes = document.querySelectorAll('#roadmapTabContent > .tab-pane');

            // Set initial state: display active pane, hide all inactive panes
            tabPanes.forEach(function(pane) {
                if (pane.classList.contains('active')) {
                    pane.style.setProperty('display', 'block', 'important');
                    pane.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('revealed'));
                } else {
                    pane.style.setProperty('display', 'none', 'important');
                }
            });

            tabButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSelector = btn.getAttribute('data-bs-target');

                    // Reset buttons
                    tabButtons.forEach(b => {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });

                    // Activate clicked tab button
                    btn.classList.add('active');
                    btn.setAttribute('aria-selected', 'true');

                    // Hide ALL tab panes
                    tabPanes.forEach(pane => {
                        pane.classList.remove('show', 'active');
                        pane.style.setProperty('display', 'none', 'important');
                    });

                    // Show ONLY the target active tab pane
                    if (targetSelector) {
                        const targetPane = document.querySelector(targetSelector);
                        if (targetPane) {
                            targetPane.classList.add('show', 'active');
                            targetPane.style.setProperty('display', 'block', 'important');
                            targetPane.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('revealed'));
                        }
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* ============================================
           ROADMAP CUSTOM STYLES & STRICT TAB ISOLATION
           ============================================ */

        /* 🔥 Sembunyikan semua tab pane yang tidak aktif */
        #roadmapTabContent > .tab-pane {
            display: none !important;
        }

        #roadmapTabContent > .tab-pane.active,
        #roadmapTabContent > .tab-pane.show.active {
            display: block !important;
        }

        .card-hover-up {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover-up:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(13, 122, 62, 0.12) !important;
        }

        /* 🔥 Jangan override class Bootstrap .nav-pills */
        .nav-pills .nav-link {
            border-radius: 50px !important;
            transition: all 0.3s ease;
            color: var(--text-secondary) !important;
            background: transparent !important;
        }

        .nav-pills .nav-link:hover {
            background: var(--primary-lighter) !important;
            color: var(--primary) !important;
        }

        .nav-pills .nav-link.active {
            background: var(--primary-gradient) !important;
            color: #fff !important;
            box-shadow: 0 4px 20px rgba(13, 122, 62, 0.25);
        }

        .nav-pills .nav-link i {
            font-size: 0.95rem;
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .nav-pills .nav-link {
                font-size: 0.8rem;
                padding: 8px 16px !important;
            }

            .nav-pills .nav-link i {
                font-size: 0.8rem;
            }

            .section-title {
                font-size: 1.6rem;
            }

            .card {
                padding: 20px 16px !important;
            }

            .step-circle {
                width: 38px !important;
                height: 38px !important;
                font-size: 1rem !important;
                min-width: 38px !important;
            }
        }

        @media (max-width: 575.98px) {
            .nav-pills {
                flex-direction: column;
                align-items: stretch;
                gap: 4px;
                border-radius: 16px;
                padding: 8px !important;
            }

            .nav-pills .nav-item {
                width: 100%;
            }

            .nav-pills .nav-link {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush
