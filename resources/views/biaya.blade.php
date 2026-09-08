@extends('layouts.landing')

@section('title', 'Biaya Pendampingan Belajar | AL-HIKMAH')
@section('description', 'Biaya dan Paket Belajar AL-HIKMAH: Informasi transparan tentang pilihan pendampingan belajar Al-Qur\'an.')

@section('content')
    <!-- ============================================ -->
    <!-- 1. BREADCRUMB HEADER -->
    <!-- ============================================ -->
    <section class="breadcrumb_bg" aria-label="Header Biaya Belajar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb_iner_item" data-reveal>
                        <div class="section-badge mx-auto mb-2"><i class="bi bi-tag-fill"></i> Investasi Pendidikan</div>
                        <h2>Pilihan <span class="text-gradient">Investasi &amp; Paket Belajar</span></h2>
                        <p>Transparan, terjangkau, dan fleksibel untuk mendukung kelancaran belajar Al-Qur'an keluarga Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 2. BIAYA PENDAFTARAN & ASSESSMENT AWAL -->
    <!-- ============================================ -->
    <section class="section-padding pb-0" aria-label="Biaya Pendaftaran">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10" data-reveal>
                    <div class="biaya-reg-box hover-lift">
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="p-3 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                        <i class="bi bi-clipboard2-check-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-0 mb-1" style="font-size: 0.72rem;">
                                            Biaya Registrasi Awal
                                        </div>
                                        <h4 class="fw-bold mb-0" style="color: var(--text-primary);">Administrasi &amp; Assessment Santri</h4>
                                    </div>
                                </div>
                                <div class="row g-2 small text-secondary">
                                    <div class="col-sm-6 d-flex align-items-center gap-2">
                                        <i class="bi bi-check2-circle text-success fs-6"></i>
                                        <span>Assessment makhraj &amp; tajwid</span>
                                    </div>
                                    <div class="col-sm-6 d-flex align-items-center gap-2">
                                        <i class="bi bi-check2-circle text-success fs-6"></i>
                                        <span>Penyusunan kurikulum personal</span>
                                    </div>
                                    <div class="col-sm-6 d-flex align-items-center gap-2">
                                        <i class="bi bi-check2-circle text-success fs-6"></i>
                                        <span>Akun LMS murid &amp; orang tua</span>
                                    </div>
                                    <div class="col-sm-6 d-flex align-items-center gap-2">
                                        <i class="bi bi-check2-circle text-success fs-6"></i>
                                        <span>Penyesuaian jadwal guru privat</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 text-md-end border-start-md ps-md-4">
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small fw-semibold mb-2">
                                    Sekali Bayar di Awal
                                </span>
                                <div class="fs-2 fw-bold text-success mb-1">
                                    Rp {{ number_format($registrationFee, 0, ',', '.') }}
                                </div>
                                <small class="text-muted d-block" style="font-size: 0.78rem;">
                                    *Hanya dibayarkan satu kali saat santri pertama kali terdaftar di AL-HIKMAH.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 3. DAFTAR PAKET INVESTASI BELAJAR PER PROGRAM -->
    <!-- ============================================ -->
    <section class="section-padding" aria-label="Paket Belajar">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small mb-2">
                    <i class="bi bi-journal-bookmark me-1"></i> Paket Bimbingan Intensif
                </div>
                <h3 class="fw-bold" style="color: var(--text-primary);">Daftar Pilihan Program &amp; Investasi Belajar</h3>
                <p class="text-secondary mx-auto" style="max-width: 640px;">
                    Investasi sudah mencakup seluruh sesi bimbingan, lembar mutabaah harian, serta evaluasi kemajuan berkala.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($programs as $index => $program)
                    @php
                        $parentEnrollment = isset($parentEnrollments) ? $parentEnrollments->firstWhere('program_id', $program->id) : null;
                    @endphp
                    <div class="col-lg-4 col-md-6" data-reveal data-reveal-delay="{{ ($index % 3) * 100 }}">
                        <div class="paket-card-v2 {{ $program->is_popular ? 'is-popular' : '' }} {{ $parentEnrollment ? 'border-success shadow' : '' }}">
                            @if($parentEnrollment)
                                <div class="paket-ribbon-v2" style="background: linear-gradient(135deg, #0d7a3e 0%, #15803d 100%);">
                                    <span>✔ Terdaftar</span>
                                </div>
                            @elseif($program->is_popular)
                                <div class="paket-ribbon-v2">
                                    <span>⭐ Paling Diminati</span>
                                </div>
                            @endif

                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 small fw-semibold">
                                        {{ $program->level }}
                                    </span>
                                    <span class="badge bg-body-secondary text-secondary rounded-pill px-3 py-1 small">
                                        <i class="bi bi-calendar-check me-1"></i>{{ $program->duration_weeks }} Minggu Terstruktur
                                    </span>
                                </div>

                                <h4 class="fw-bold mb-3" style="color: var(--text-primary);">{{ $program->name }}</h4>

                                @if($parentEnrollment)
                                    @if($parentEnrollment->isWaitingAdmin())
                                        <div class="alert alert-warning border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-dark mb-1">
                                                <i class="bi bi-hourglass-split text-warning"></i> Status: Sedang Direview
                                            </div>
                                            <div class="text-secondary" style="font-size: 0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Lembaga sedang mereview jadwal &amp; ketersediaan guru.
                                            </div>
                                        </div>
                                    @elseif($parentEnrollment->isWaitingParent())
                                        <div class="alert alert-info border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-info-emphasis mb-1">
                                                <i class="bi bi-chat-dots-fill text-info"></i> Status: Menunggu Respon Anda
                                            </div>
                                            <div class="text-secondary" style="font-size: 0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Ada tawaran alternatif jadwal dari lembaga.
                                            </div>
                                        </div>
                                    @elseif($parentEnrollment->isConfirmed())
                                        <div class="alert alert-primary border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-primary-emphasis mb-1">
                                                <i class="bi bi-check-circle-fill text-primary"></i> Status: Jadwal Deal (Siap Bayar)
                                            </div>
                                            <div class="text-secondary" style="font-size: 0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Guru: <strong>{{ $parentEnrollment->mentor?->getDisplayName() ?? 'Ditentukan Lembaga' }}</strong>
                                            </div>
                                        </div>
                                    @elseif($parentEnrollment->isActive())
                                        <div class="alert alert-success border-0 py-2 px-3 mb-3 rounded-3 text-start small">
                                            <div class="d-flex align-items-center gap-2 fw-bold text-success mb-1">
                                                <i class="bi bi-award-fill text-success"></i> Status: Bimbingan Aktif Berjalan
                                            </div>
                                            <div class="text-secondary" style="font-size: 0.8rem;">
                                                Santri: <strong>{{ $parentEnrollment->student?->getDisplayName() }}</strong><br>
                                                Guru: <strong>{{ $parentEnrollment->mentor?->getDisplayName() ?? 'Guru Aktif' }}</strong>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="d-flex align-items-baseline gap-1">
                                        <span class="fs-2 fw-bold text-success">{{ $program->formatted_price }}</span>
                                        <span class="text-muted small">/ paket</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.8rem;">Investasi bimbingan privat terarah</small>
                                </div>

                                <div class="mb-4">
                                    <div class="metode-check-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Model Privat Intensif (1 Guru 1 Santri)</span>
                                    </div>
                                    <div class="metode-check-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Durasi belajar {{ $program->duration_weeks }} minggu terstruktur</span>
                                    </div>
                                    <div class="metode-check-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Modul materi &amp; lembar mutabaah hafalan</span>
                                    </div>
                                    <div class="metode-check-item">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Laporan evaluasi tajwid berkala ke orang tua</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                @auth
                                    @if(auth()->user()->isParent())
                                        @if($parentEnrollment)
                                            @if($parentEnrollment->isWaitingAdmin())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-warning text-dark w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-eye me-1"></i> Pantau Status Jadwal
                                                </a>
                                            @elseif($parentEnrollment->isWaitingParent())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-info text-white w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-chat-dots me-1"></i> Konfirmasi Tawaran Jadwal
                                                </a>
                                            @elseif($parentEnrollment->isConfirmed())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-primary-custom w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                                                </a>
                                            @elseif($parentEnrollment->isActive())
                                                <a href="{{ route('parent.enrollments.show', $parentEnrollment->id) }}" 
                                                   class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm mb-2">
                                                    <i class="bi bi-calendar-check me-1"></i> Lihat Sesi Bimbingan
                                                </a>
                                            @else
                                                <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}" 
                                                   class="btn {{ $program->is_popular ? 'btn-primary-custom' : 'btn-outline-custom' }} w-100 py-2 rounded-pill mb-2">
                                                    <i class="bi bi-calendar-plus me-1"></i> Pilih Program & Jadwal
                                                </a>
                                            @endif
                                            <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}" 
                                               class="btn btn-outline-secondary btn-sm w-100 py-1 rounded-pill" style="font-size: 0.8rem;">
                                                <i class="bi bi-person-plus me-1"></i> + Daftar Santri Lain
                                            </a>
                                        @else
                                            <a href="{{ route('parent.enrollments.create', ['program_id' => $program->id]) }}" 
                                               class="btn {{ $program->is_popular ? 'btn-primary-custom' : 'btn-outline-custom' }} w-100 py-2 rounded-pill">
                                                <i class="bi bi-calendar-plus me-1"></i> Pilih Program & Jadwal
                                            </a>
                                        @endif
                                    @elseif(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.enrollments.index') }}" 
                                           class="btn btn-outline-warning w-100 py-2 rounded-pill">
                                            <i class="bi bi-gear me-1"></i> Kelola Pendaftaran (Admin)
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <p class="text-muted small fst-italic">
                    "Setiap santri memiliki kecepatan belajar yang berbeda. Tim pendamping kami siap membantu mencocokkan program berdasarkan hasil assessment awal ananda."
                </p>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 4. FASILITAS & JAMINAN LAYANAN BELAJAR -->
    <!-- ============================================ -->
    <section class="section-padding bg-body-tertiary" aria-label="Jaminan Layanan">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <div class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small mb-2">
                    <i class="bi bi-shield-check me-1"></i> Komitmen Layanan
                </div>
                <h3 class="fw-bold" style="color: var(--text-primary);">Fasilitas &amp; Jaminan Kualitas Pendampingan</h3>
                <p class="text-secondary mx-auto" style="max-width: 620px;">
                    Kami berkomitmen memberikan kenyamanan terbaik untuk orang tua dan anak dalam setiap sesi bimbingan.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-reveal>
                    <div class="guarantee-card hover-lift">
                        <div class="guarantee-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Garansi Kesesuaian Guru</h5>
                        <p class="text-secondary small mb-0">
                            Bila ananda merasa kurang nyaman dengan pendekatan guru dalam 2 pertemuan pertama, orang tua dapat mengajukan pergantian tanpa biaya tambahan.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="100">
                    <div class="guarantee-card hover-lift">
                        <div class="guarantee-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Sesi Pengganti Syar'i</h5>
                        <p class="text-secondary small mb-0">
                            Santri yang berhalangan hadir karena sakit atau agenda penting dapat mengajukan jadwal pengganti dengan konfirmasi minimal 6 jam sebelum sesi.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="200">
                    <div class="guarantee-card hover-lift">
                        <div class="guarantee-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Rapor Mutabaah Berkala</h5>
                        <p class="text-secondary small mb-0">
                            Setiap pertemuan dicatat dalam sistem LMS kami, meliputi capaian halaman, catatan makhraj, serta rekomendasi murajaah di rumah.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3" data-reveal data-reveal-delay="300">
                    <div class="guarantee-card hover-lift">
                        <div class="guarantee-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Tanpa Biaya Tersembunyi</h5>
                        <p class="text-secondary small mb-0">
                            Nominal paket investasi belajar bersifat tetap dan transparan, sudah mencakup seluruh bahan ajar digital dan sertifikat tamat program.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================ -->
    <!-- 5. TANYA JAWAB SEPUTAR INVESTASI (FAQ BIAYA) -->
    <!-- ============================================ -->
    <section class="section-padding" aria-label="Tanya Jawab Biaya">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5" data-reveal>
                        <div class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill small mb-2">
                            <i class="bi bi-question-circle-fill me-1"></i> FAQ Investasi
                        </div>
                        <h3 class="fw-bold" style="color: var(--text-primary);">Pertanyaan Umum Seputar Biaya</h3>
                    </div>

                    <div class="accordion accordion-flush" id="accordionFaqBiaya" data-reveal>
                        <div class="accordion-item mb-3 rounded-3 border overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqBiaya1" aria-expanded="false">
                                    Apakah ada diskon khusus untuk pendaftaran kakak beradik (2 santri atau lebih)?
                                </button>
                            </h2>
                            <div id="faqBiaya1" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Ya, kami menyediakan penyesuaian khusus bagi keluarga yang mendaftarkan lebih dari satu anak dalam satu sesi kunjungan (Home Visit) atau jadwal online berturutan. Silakan hubungi admin kami untuk rekomendasi paket keluarga.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 rounded-3 border overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqBiaya2" aria-expanded="false">
                                    Bagaimana alur pembayaran paket belajar?
                                </button>
                            </h2>
                            <div id="faqBiaya2" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Setelah pemilihan program dan verifikasi jadwal oleh admin disetujui, orang tua dapat melakukan pembayaran resmi melalui transfer bank atau dompet digital yang terintegrasi aman di dalam dasbor LMS Al-Hikmah.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 rounded-3 border overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqBiaya3" aria-expanded="false">
                                    Bagaimana jika santri berhalangan hadir pada salah satu sesi pertemuan?
                                </button>
                            </h2>
                            <div id="faqBiaya3" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Sesi belajar tidak akan hangus apabila orang tua memberitahukan kepada guru privat atau admin minimal 6 jam sebelum jadwal dimulai. Sesi pengganti akan dijadwalkan bersama di hari lain yang disepakati.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item rounded-3 border overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faqBiaya4" aria-expanded="false">
                                    Apakah guru home visit membawa sarana belajar sendiri?
                                </button>
                            </h2>
                            <div id="faqBiaya4" class="accordion-collapse collapse" data-bs-parent="#accordionFaqBiaya">
                                <div class="accordion-body text-secondary small">
                                    Guru kami membawa panduan kurikulum, lembar target mutabaah fisik, dan alat bantu makhraj. Orang tua cukup menyediakan mushaf Al-Qur'an / Iqra milik ananda serta ruangan yang tenang untuk proses bimbingan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Khusus Pendaftaran Program -->
    <div class="modal fade" id="modalProgramDaftar" tabindex="-1" aria-labelledby="modalProgramDaftarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-premium border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold text-success" id="modalProgramDaftarLabel">
                            <i class="bi bi-check2-circle me-2"></i>Formulir Pendaftaran Program
                        </h5>
                        <p class="text-muted small mb-0">Program yang dipilih: <span id="labelSelectedProgram" class="badge bg-success-subtle text-success fs-6 fw-bold">Program Al-Qur'an</span></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formProgramRegistration" action="{{ route('program.pre-register') }}" method="POST">
                        @csrf
                        <input type="hidden" name="program_id" id="inputModalProgramId" value="">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalNamaWali">Nama Orang Tua / Wali <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalNamaWali" name="nama" required placeholder="Nama Ayah/Bunda..." value="{{ auth()->check() ? auth()->user()->name : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalNamaAnak">Nama Murid / Calon Santri <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalNamaAnak" name="nama_anak" required placeholder="Nama lengkap anak/peserta...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalWhatsApp">Nomor WhatsApp Aktif <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="modalWhatsApp" name="whatsapp" required placeholder="08123456789" value="{{ auth()->check() ? auth()->user()->phone : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalUsia">Rentang Usia</label>
                                <select class="form-select" id="modalUsia" name="usia">
                                    <option value="Di bawah 10 tahun (4-9 tahun)">Di bawah 10 tahun (Anak-anak / 4-9 tahun)</option>
                                    <option value="10-15 tahun (Anak)" selected>10-15 tahun (Anak / Remaja)</option>
                                    <option value="Dewasa (16-30 tahun)">Dewasa (16-30 tahun)</option>
                                    <option value="Dewasa (31-50 tahun)">Dewasa (31-50 tahun)</option>
                                    <option value="50+ tahun">50+ tahun (Lansia)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalGender">Jenis Kelamin</label>
                                <select class="form-select" id="modalGender" name="gender">
                                    <option value="L">Laki-laki (Ikhwan)</option>
                                    <option value="P">Perempuan (Akhwat)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small" for="modalLokasi">Kota / Kecamatan Tinggal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modalLokasi" name="lokasi" required placeholder="Contoh: Semarang Barat / Jakarta Selatan">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small" for="modalMetode">Preferensi Metode Belajar</label>
                                <select class="form-select" id="modalMetode" name="metode">
                                    <option value="Online (Zoom / Meet)" selected>Online (Tatap Maya Interaktif)</option>
                                    <option value="Offline (Guru Datang ke Rumah)">Offline (Guru Datang ke Rumah)</option>
                                    <option value="Hybrid (Kombinasi)">Hybrid (Kombinasi Online &amp; Offline)</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-success-subtle border-0 d-flex align-items-center mt-4 mb-0 py-2">
                            <i class="bi bi-shield-check text-success fs-4 me-2"></i>
                            <span class="small text-success-emphasis">Data Anda aman. Setelah mengirim form ini, Anda akan diarahkan untuk konfirmasi akun murid di LMS AL-HIKMAH.</span>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 rounded-3">
                            <i class="bi bi-arrow-right-circle me-2"></i> Lanjutkan Pendaftaran Akun LMS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Interaktif Modal (Idiomatic Bootstrap 5 Event Delegation) -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('modalProgramDaftar');
            if (!modalEl) return;

            modalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const programId    = button.getAttribute('data-program-id');
                const programName  = button.getAttribute('data-program-name');
                const programPrice = button.getAttribute('data-program-price');

                const inputId = modalEl.querySelector('#inputModalProgramId');
                const labelEl = modalEl.querySelector('#labelSelectedProgram');

                if (inputId) inputId.value = programId;
                if (labelEl) labelEl.textContent = `${programName} (${programPrice})`;
            });
        });
    </script>
    @endpush
@endsection

