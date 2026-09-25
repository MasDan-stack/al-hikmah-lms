@extends('layouts.landing')

@section('title', 'Peta Alur & Panduan Langkah Belajar (Roadmap) | AL-HIKMAH')
@section('meta_description', 'Panduan alur langkah demi langkah untuk calon orang tua murid, calon guru pendamping, dan alur pembayaran di AL-HIKMAH LMS.')

@section('content')
    <!-- ============================================================ -->
    <!-- 1. EDITORIAL SUBPAGE HEADER -->
    <!-- ============================================================ -->
    <section class="editorial-page-header" aria-label="Header Peta Alur Belajar">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center" data-reveal>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                            <li class="breadcrumb-item text-muted">Panduan</li>
                            <li class="breadcrumb-item active" aria-current="page">Roadmap Belajar</li>
                        </ol>
                    </nav>

                    <div class="editorial-badge mx-auto">
                        <i class="bi bi-map-fill"></i>
                        <span>Panduan Langkah Awal</span>
                    </div>

                    <h1 class="editorial-title">Peta Perjalanan Belajar</h1>
                    <p class="editorial-subtitle mx-auto">
                        Panduan langkah terarah mulai dari eksplorasi program, pencocokan jadwal guru, hingga proses bimbingan belajar berjalan tertib dan lancar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- 2. ROADMAP CONTENT TABS -->
    <!-- ============================================================ -->
    <section class="section-padding pt-4" aria-label="Roadmap Section">
        <div class="container">

            <!-- Navigation Switcher Pills -->
            <div class="d-flex justify-content-center mb-5" data-reveal>
                <ul class="nav nav-pills gap-2 p-1.5 rounded-pill bg-body-tertiary border" id="roadmapTab" role="tablist"
                    style="flex-wrap: wrap; justify-content: center;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-3.5 py-2 fw-semibold small" id="parent-tab" data-bs-toggle="pill"
                            data-bs-target="#parent-journey" type="button" role="tab" aria-controls="parent-journey"
                            aria-selected="true">
                            <i class="bi bi-people-fill me-1.5"></i> Jalur Calon Orang Tua
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3.5 py-2 fw-semibold small" id="mentor-tab" data-bs-toggle="pill"
                            data-bs-target="#mentor-journey" type="button" role="tab" aria-controls="mentor-journey"
                            aria-selected="false">
                            <i class="bi bi-person-workspace me-1.5"></i> Jalur Guru / Pendamping
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3.5 py-2 fw-semibold small" id="payment-tab" data-bs-toggle="pill"
                            data-bs-target="#payment-journey" type="button" role="tab" aria-controls="payment-journey"
                            aria-selected="false">
                            <i class="bi bi-wallet2 me-1.5"></i> Alur Pembayaran &amp; SPP
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Contents -->
            <div class="tab-content" id="roadmapTabContent">

                <!-- ========================================================== -->
                <!-- TAB 1: JALUR CALON ORANG TUA -->
                <!-- ========================================================== -->
                <div class="tab-pane fade show active" id="parent-journey" role="tabpanel" aria-labelledby="parent-tab">
                    <div class="text-center mb-5" data-reveal>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold small mb-2 d-inline-block">
                            <i class="bi bi-shield-check me-1"></i> Alur Tanpa Kerumitan
                        </span>
                        <h2 class="editorial-title text-center mb-2">6 Langkah Menuju Bimbingan Al-Qur'an Ananda</h2>
                        <p class="text-secondary small mx-auto" style="max-width: 600px; line-height: 1.6;">
                            Dari konsultasi santun hingga ananda dibimbing langsung oleh guru yang sesuai dengan gaya belajar ananda.
                        </p>
                    </div>

                    <div class="row g-4">
                        @for ($i = 1; $i <= 6; $i++)
                            <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($i - 1) * 80 }}">
                                <div class="why-card h-100 d-flex flex-column justify-content-between p-4">
                                    <div>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="why-icon mb-0 fw-bold" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                                {{ $i }}
                                            </div>
                                            <div>
                                                <span class="text-muted small d-block" style="font-size: 0.78rem;">
                                                    Langkah {{ ['Pertama', 'Kedua', 'Ketiga', 'Keempat', 'Kelima', 'Keenam'][$i - 1] }}
                                                </span>
                                                <h5 class="fw-bold mb-0 text-heading fs-6">
                                                    @switch($i)
                                                        @case(1) Eksplorasi Program @break
                                                        @case(2) Pilih Jadwal &amp; Guru @break
                                                        @case(3) Registrasi Akun @break
                                                        @case(4) Review &amp; Deal Jadwal @break
                                                        @case(5) Pembayaran Tagihan @break
                                                        @case(6) Mulai Belajar &amp; Progres @break
                                                    @endswitch
                                                </h5>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-3" style="line-height: 1.65;">
                                            @switch($i)
                                                @case(1)
                                                    Pelajari kurikulum pada menu <strong>Program</strong> (Iqra, Tahsin, Tahfidz, Bahasa Arab) serta rincian paket di halaman <strong>Biaya</strong>.
                                                @break
                                                @case(2)
                                                    Ajukan kombinasi hari bimbingan (misalnya Senin &amp; Kamis) dan jam luang ananda yang paling cocok dengan agenda keluarga.
                                                @break
                                                @case(3)
                                                    Sistem otomatis menyiapkan akun <strong>Orang Tua</strong> dan akun <strong>Santri</strong> untuk memantau proses verifikasi jadwal.
                                                @break
                                                @case(4)
                                                    Admin mencocokkan jadwal pilihan Anda dengan ustadz/ustadzah. Setelah cocok, Anda menerima detail profil guru dan tanggal mulai.
                                                @break
                                                @case(5)
                                                    Pembayaran dilakukan melalui saluran otomatis resmi (QRIS, VA Bank, e-Wallet). Sistem langsung mencatat lunas seketika.
                                                @break
                                                @case(6)
                                                    Jadwal sesi 4 minggu otomatis aktif. Orang tua dapat memantau catatan tajwid, kehadiran, dan mutaba'ah harian langsung dari ponsel.
                                                @break
                                            @endswitch
                                        </p>
                                    </div>
                                    <div class="pt-3 border-top mt-auto">
                                        @switch($i)
                                            @case(1)
                                                <a href="{{ route('program') }}" class="btn-editorial-secondary w-100 text-center py-2 small">
                                                    <i class="bi bi-book-half me-1"></i> Telusuri Program
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
                                                                 class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold w-100 text-center py-2">
                                                                 <i class="bi bi-hourglass-split me-1"></i> Sedang Direview ({{ $latestEnrollment->program?->name }})
                                                             </a>
                                                         @elseif ($latestEnrollment && $latestEnrollment->isWaitingParent())
                                                             <a href="{{ route('parent.enrollments.show', $latestEnrollment->id) }}"
                                                                 class="btn btn-sm btn-info text-white rounded-pill px-3 fw-bold w-100 text-center py-2">
                                                                 <i class="bi bi-chat-dots me-1"></i> Konfirmasi Jadwal ({{ $latestEnrollment->program?->name }})
                                                             </a>
                                                         @elseif ($latestEnrollment && $latestEnrollment->isConfirmed())
                                                             <a href="{{ route('parent.enrollments.show', $latestEnrollment->id) }}"
                                                                 class="btn-editorial-primary w-100 text-center py-2 small">
                                                                 <i class="bi bi-wallet2 me-1"></i> Siap Bayar: {{ $latestEnrollment->program?->name }}
                                                             </a>
                                                         @elseif ($latestEnrollment && $latestEnrollment->isActive())
                                                             <a href="{{ route('parent.enrollments.show', $latestEnrollment->id) }}"
                                                                 class="btn-editorial-primary w-100 text-center py-2 small">
                                                                 <i class="bi bi-award-fill me-1"></i> Program Aktif: {{ $latestEnrollment->program?->name }}
                                                             </a>
                                                         @else
                                                             <a href="{{ route('biaya') }}" class="btn-editorial-primary w-100 text-center py-2 small">
                                                                 <i class="bi bi-journal-check me-1"></i> Pilih Program
                                                             </a>
                                                         @endif
                                                     @elseif (auth()->user()->isAdmin())
                                                         <a href="{{ route('biaya') }}" class="btn-editorial-primary w-100 text-center py-2 small">
                                                             <i class="bi bi-journal-check me-1"></i> Pilih Program (Admin)
                                                         </a>
                                                     @else
                                                         <button type="button" class="btn-editorial-primary w-100 text-center py-2 small" data-bs-toggle="modal" data-bs-target="#daftarModal">
                                                             <i class="bi bi-calendar-plus me-1"></i> Booking Jadwal
                                                         </button>
                                                     @endif
                                                 @else
                                                     <button type="button" class="btn-editorial-primary w-100 text-center py-2 small" data-bs-toggle="modal" data-bs-target="#daftarModal">
                                                         <i class="bi bi-calendar-plus me-1"></i> Booking Jadwal
                                                     </button>
                                                 @endauth
                                             @break
                                            @case(3)
                                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-clock-history me-1"></i> Menunggu Konfirmasi Jadwal
                                                </span>
                                            @break
                                            @case(4)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-check-circle me-1"></i> Jadwal Disepakati
                                                </span>
                                            @break
                                            @case(5)
                                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-qr-code-scan me-1"></i> Pelunasan Otomatis
                                                </span>
                                            @break
                                            @case(6)
                                                <span class="badge bg-success text-white px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-award-fill me-1"></i> Bimbingan Berjalan
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
                    <div class="text-center mb-5" data-reveal>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold small mb-2 d-inline-block">
                            <i class="bi bi-mortarboard-fill me-1"></i> Dakwah &amp; Khidmah Qur'ani
                        </span>
                        <h2 class="editorial-title text-center mb-2">6 Tahapan Menjadi Guru Pendamping AL-HIKMAH</h2>
                        <p class="text-secondary small mx-auto" style="max-width: 600px; line-height: 1.6;">
                            Bergabung mendampingi generasi Qur'ani dengan fleksibilitas jadwal mengajar dan transparansi honorarium.
                        </p>
                    </div>

                    <div class="row g-4">
                        @for ($i = 1; $i <= 6; $i++)
                            <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($i - 1) * 80 }}">
                                <div class="why-card h-100 d-flex flex-column justify-content-between p-4">
                                    <div>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="why-icon mb-0 fw-bold" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                                {{ $i }}
                                            </div>
                                            <div>
                                                <span class="text-muted small d-block" style="font-size: 0.78rem;">Tahap {{ $i }}</span>
                                                <h5 class="fw-bold mb-0 text-heading fs-6">
                                                    @switch($i)
                                                        @case(1) Registrasi Guru @break
                                                        @case(2) Seleksi &amp; Verifikasi @break
                                                        @case(3) Atur Ketersediaan @break
                                                        @case(4) Terima Alokasi Santri @break
                                                        @case(5) Bimbingan &amp; Input Progres @break
                                                        @case(6) Edukasi Berkelanjutan @break
                                                    @endswitch
                                                </h5>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-3" style="line-height: 1.65;">
                                            @switch($i)
                                                @case(1)
                                                    Akses halaman <strong>Bergabung</strong> untuk mengisi biodata, riwayat pendidikan, hafalan, serta spesialisasi bimbingan Anda.
                                                @break
                                                @case(2)
                                                    Tim akademik AL-HIKMAH memverifikasi berkas, menyimak bacaan tajwid/tahsin, dan melakukan wawancara komitmen mengajar.
                                                @break
                                                @case(3)
                                                    Setelah lolos, masuk ke portal guru untuk menentukan hari mengajar, slot jam kosong, dan batasan santri harian secara mandiri.
                                                @break
                                                @case(4)
                                                    Ketika ada jadwal santri yang cocok, detail kontak dan agenda bimbingan langsung muncul di akun portal mengajar Anda.
                                                @break
                                                @case(5)
                                                    Jalankan sesi bimbingan 90 menit (Home Visit atau Online) dan catat evaluasi mutaba'ah santri langsung melalui ponsel.
                                                @break
                                                @case(6)
                                                    Ikuti pembekalan berkala, pelatihan metodologi pengajaran, dan supervisi untuk menjaga mutu sanad dan adab mengajar.
                                                @break
                                            @endswitch
                                        </p>
                                    </div>
                                    <div class="pt-3 border-top mt-auto">
                                        @switch($i)
                                            @case(1)
                                                <a href="{{ route('bergabung') }}" class="btn-editorial-primary w-100 text-center py-2 small">
                                                    <i class="bi bi-pencil-square me-1"></i> Formulir Bergabung
                                                </a>
                                            @break
                                            @case(2)
                                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-shield-check me-1"></i> Standar Kualifikasi Guru
                                                </span>
                                            @break
                                            @case(3)
                                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-calendar3 me-1"></i> Fleksibilitas Slot Waktu
                                                </span>
                                            @break
                                            @case(4)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-person-check-fill me-1"></i> Kontak Terbuka
                                                </span>
                                            @break
                                            @case(5)
                                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-journal-check me-1"></i> Laporan Mutaba'ah
                                                </span>
                                            @break
                                            @case(6)
                                                <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small w-100 text-center">
                                                    <i class="bi bi-mortarboard me-1"></i> Pembekalan Berkala
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
                    <div class="text-center mb-5" data-reveal>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold small mb-2 d-inline-block">
                            <i class="bi bi-shield-lock-fill me-1"></i> Transparan &amp; Terpercaya
                        </span>
                        <h2 class="editorial-title text-center mb-2">Siklus Pembayaran "Deal Dulu, Baru Bayar"</h2>
                        <p class="text-secondary small mx-auto" style="max-width: 600px; line-height: 1.6;">
                            Tidak ada biaya tersembunyi. Pembayaran hanya dilakukan setelah jadwal bimbingan dan profil guru disepakati bersama.
                        </p>
                    </div>

                    <div class="row g-4">
                        @for ($i = 1; $i <= 6; $i++)
                            <div class="col-md-6 col-lg-4" data-reveal data-reveal-delay="{{ ($i - 1) * 80 }}">
                                <div class="why-card h-100 d-flex flex-column justify-content-between p-4">
                                    <div>
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="why-icon mb-0 fw-bold" style="width: 42px; height: 42px; font-size: 1.1rem;">
                                                {{ $i }}
                                            </div>
                                            <div>
                                                <span class="text-muted small d-block" style="font-size: 0.78rem;">Fase {{ $i }}</span>
                                                <h5 class="fw-bold mb-0 text-heading fs-6">
                                                    @switch($i)
                                                        @case(1) Pengajuan Awal @break
                                                        @case(2) Terbit Tagihan (Deal) @break
                                                        @case(3) Pelunasan Otomatis (QRIS / VA) @break
                                                        @case(4) SPP Bulan Berikutnya @break
                                                        @case(5) Riwayat &amp; Monitoring @break
                                                        @case(6) Siklus Berkelanjutan @break
                                                    @endswitch
                                                </h5>
                                            </div>
                                        </div>
                                        <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                            @switch($i)
                                                @case(1)
                                                    Wali santri mengajukan preferensi jadwal. Belum ada tagihan biaya yang diterbitkan oleh sistem pada tahap ini.
                                                @break
                                                @case(2)
                                                    Setelah jadwal dan kuota guru disepakati kedua pihak, sistem menerbitkan rincian tagihan paket secara transparan.
                                                @break
                                                @case(3)
                                                    Pembayaran lunas melalui QRIS atau Virtual Account Bank langsung mengaktifkan jadwal belajar dan mencatat sesi di kalender santri.
                                                @break
                                                @case(4)
                                                    Untuk bulan ke-2 dan seterusnya, tagihan hanya berupa SPP rutin yang diinformasikan menjelang akhir periode belajar.
                                                @break
                                                @case(5)
                                                    Seluruh mutasi pembayaran tercatat rapi di menu Riwayat Transaksi akun orang tua dan dapat diunduh kapan saja.
                                                @break
                                                @case(6)
                                                    Sistem menerbitkan invoice berkala secara otomatis sehingga orang tua dapat berfokus mendampingi perkembangan ananda.
                                                @break
                                            @endswitch
                                        </p>
                                    </div>
                                    <div class="pt-3 border-top mt-3">
                                        <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small w-100 text-center">
                                            <i class="bi bi-check2-circle text-primary me-1"></i> Terverifikasi Sistem
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

            </div><!-- end tab-content -->

            <!-- Bottom Call to Actions -->
            <div class="mt-5 p-4 p-md-5 rounded-4 bg-body-tertiary border text-center" data-reveal>
                <h3 class="fs-4 fw-bold text-heading mb-2">Masih Memiliki Pertanyaan Terkait Alur Belajar?</h3>
                <p class="text-secondary small mx-auto mb-4" style="max-width: 580px; line-height: 1.6;">
                    Tim konselor AL-HIKMAH siap mendengarkan kebutuhan keluarga Anda dan membantu mencocokkan jadwal terbaik untuk ananda.
                </p>
                <div class="d-flex justify-content-center flex-wrap gap-3">
                    <a href="{{ route('faq') }}" class="btn-editorial-secondary px-4 py-2.5">
                        <i class="bi bi-question-circle me-1.5"></i> Pusat Tanya Jawab (FAQ)
                    </a>
                    <a href="{{ route('contact') }}" class="btn-editorial-secondary px-4 py-2.5">
                        <i class="bi bi-envelope-paper me-1.5"></i> Kirim Pesan Konsultasi
                    </a>
                    <a href="{{ wa_url('Assalamualaikum admin AL-HIKMAH, saya ingin berkonsultasi mengenai alur pendaftaran bimbingan') }}"
                        target="_blank" rel="noopener" class="btn-editorial-whatsapp px-4 py-2.5">
                        <i class="bi bi-whatsapp me-1.5"></i> Hubungi WhatsApp CS
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

                    tabButtons.forEach(b => {
                        b.classList.remove('active');
                        b.setAttribute('aria-selected', 'false');
                    });

                    btn.classList.add('active');
                    btn.setAttribute('aria-selected', 'true');

                    tabPanes.forEach(pane => {
                        pane.classList.remove('show', 'active');
                        pane.style.setProperty('display', 'none', 'important');
                    });

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
