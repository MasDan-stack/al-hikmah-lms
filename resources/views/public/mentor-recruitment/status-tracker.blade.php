@extends('layouts.landing')

@section('title', 'Cek Status Seleksi Guru Pembimbing - AL-HIKMAH LMS')

@section('content')
<!-- Page Header / Hero -->
<section class="tracker-hero">
    <div class="container text-center">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb justify-content-center mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bergabung') }}" class="text-decoration-none text-muted">Rekrutmen Guru</a></li>
                <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Pelacakan Status</li>
            </ol>
        </nav>

        <div class="tracker-badge mx-auto mb-3">
            <i class="bi bi-clock-history"></i>
            <span>Portal Transparansi Rekrutmen</span>
        </div>

        <h1 class="tracker-title">Pelacak Status Lamaran Guru</h1>
        <p class="tracker-subtitle">Pantau progres seleksi berkas administrasi, ujian kompetensi, hingga jadwal wawancara secara mandiri dan berkala.</p>
    </div>
</section>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-11">

            <!-- System Feedback Notifications -->
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center justify-content-between border-0 shadow-sm rounded-3 mb-4 p-3" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle-fill fs-5 flex-shrink-0"></i>
                        <span class="small fw-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center justify-content-between border-0 shadow-sm rounded-3 mb-4 p-3" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5 flex-shrink-0"></i>
                        <span class="small fw-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            <!-- Search Form Card -->
            <div class="tracker-card card mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1 text-heading">Masukkan Nomor WhatsApp atau Kode Registrasi</h2>
                            <p class="small text-muted mb-0">Gunakan nomor telepon aktif atau kode pendaftaran (contoh: <code>APP-...</code>) saat mendaftar.</p>
                        </div>
                        <span class="badge bg-light text-muted border px-2 py-1 small">
                            <i class="bi bi-shield-lock me-1"></i> Data Terenkripsi
                        </span>
                    </div>

                    <form action="{{ route('mentor.recruitment.check-status') }}" method="POST" id="formCekStatus">
                        @csrf
                        <div class="tracker-search-bar">
                            <span class="tracker-search-icon" aria-hidden="true">
                                <i class="bi bi-whatsapp"></i>
                            </span>
                            <input type="text" 
                                   name="phone" 
                                   id="phoneInput"
                                   class="form-control tracker-search-input @error('phone') is-invalid @enderror" 
                                   placeholder="Ketik nomor WhatsApp (08xx...) atau Kode Pendaftaran (APP-...)" 
                                   value="{{ old('phone', request('phone')) }}" 
                                   autocomplete="tel"
                                   required>
                            <button type="submit" class="tracker-search-btn" id="btnCekStatus">
                                <i class="bi bi-search"></i>
                                <span>Periksa Status</span>
                            </button>
                        </div>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2 small text-muted d-flex align-items-center gap-1">
                            <i class="bi bi-info-circle"></i>
                            <span>Format nomor fleksibel: diawali <strong>08...</strong> atau <strong>62...</strong> tanpa tanda hubung.</span>
                        </div>
                    </form>
                </div>
            </div>

            <!-- HASIL PENCARIAN STATUS -->
            @isset($application)
                @php
                    $stage = (int) ($application->current_stage ?? 1);
                    $status = $application->status;
                    $isRejected = ($status === 'rejected');
                    $isApproved = ($status === 'approved');

                    // Calculate stepper progress bar width (0% to 100%)
                    if ($isApproved) {
                        $progressWidth = 100;
                    } elseif ($isRejected) {
                        $progressWidth = max(0, min(100, (($stage - 1) / 4) * 100));
                    } else {
                        $progressWidth = max(0, min(100, (($stage - 1) / 4) * 100));
                    }

                    // Stages definition
                    $stages = [
                        [
                            'step' => 1,
                            'title' => 'Pendaftaran',
                            'desc' => 'Pengisian data diri & berkas awal',
                            'icon' => 'bi-file-earmark-text',
                            'is_done' => $stage > 1 || $isApproved,
                            'is_active' => $stage === 1 && !$isRejected && !$isApproved,
                            'is_danger' => false,
                        ],
                        [
                            'step' => 2,
                            'title' => 'Verifikasi Berkas',
                            'desc' => 'Pemeriksaan ijazah, CV, dan sanad',
                            'icon' => 'bi-folder-check',
                            'is_done' => $stage > 2 || $isApproved,
                            'is_active' => $stage === 2 && !$isRejected && !$isApproved,
                            'is_danger' => false,
                        ],
                        [
                            'step' => 3,
                            'title' => 'Ujian Kompetensi',
                            'desc' => 'Tes Tajwid & Fiqih Pembelajaran',
                            'icon' => 'bi-journal-check',
                            'is_done' => $stage > 3 || $isApproved,
                            'is_active' => $stage === 3 && !$isRejected && !$isApproved,
                            'is_danger' => false,
                        ],
                        [
                            'step' => 4,
                            'title' => 'Wawancara',
                            'desc' => 'Tatap maya & microteaching',
                            'icon' => 'bi-chat-square-quote',
                            'is_done' => $stage > 4 || $isApproved,
                            'is_active' => $stage === 4 && !$isRejected && !$isApproved,
                            'is_danger' => false,
                        ],
                        [
                            'step' => 5,
                            'title' => 'Keputusan Akhir',
                            'desc' => 'Penetapan guru pembimbing',
                            'icon' => $isApproved ? 'bi-patch-check-fill' : ($isRejected ? 'bi-x-circle-fill' : 'bi-award'),
                            'is_done' => $isApproved,
                            'is_active' => ($stage === 5 && !$isRejected && !$isApproved),
                            'is_danger' => $isRejected,
                        ],
                    ];

                    $initials = '';
                    $nameWords = explode(' ', trim($application->full_name));
                    foreach (array_slice($nameWords, 0, 2) as $word) {
                        $initials .= strtoupper(substr($word, 0, 1));
                    }
                @endphp

                <div class="tracker-applicant-card card mb-4">
                    <!-- Applicant Header Info -->
                    <div class="tracker-applicant-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="tracker-avatar-initials" aria-hidden="true">
                                {{ $initials ?: 'CG' }}
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h3 class="h5 fw-bold mb-0 text-heading">{{ $application->full_name }}</h3>
                                    <div class="tracker-code-badge" title="Kode Pendaftaran">
                                        <span>{{ $application->application_code }}</span>
                                        <button type="button" 
                                                class="tracker-btn-copy" 
                                                onclick="copyRegistrationCode('{{ $application->application_code }}', this)" 
                                                aria-label="Salin Kode Registrasi" 
                                                title="Salin Kode">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="small text-muted d-flex align-items-center gap-2 flex-wrap">
                                    <span>Peminatan: <strong class="text-secondary">{{ $application->specialization }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Hafalan: <strong class="text-secondary">{{ $application->hifz_total_juz }} Juz</strong></span>
                                    <span>&bull;</span>
                                    <span>Daftar: <strong class="text-secondary">{{ $application->submitted_at ? \Carbon\Carbon::parse($application->submitted_at)->format('d M Y') : '-' }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-start text-md-end">
                            <div class="mb-1">
                                {!! $application->status_badge !!}
                            </div>
                            <small class="text-muted d-block">Tahap Aktif: Ke-{{ $stage }} dari 5</small>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <h4 class="h6 fw-bold text-uppercase text-muted letter-spacing-1 mb-4 text-center">
                            Progres Tahapan Seleksi
                        </h4>

                        <!-- DESKTOP STEPPER (>= 768px) -->
                        <div class="tracker-stepper-desktop" aria-label="Linimasa Tahapan Seleksi">
                            <div class="tracker-stepper-track" aria-hidden="true">
                                <div class="tracker-stepper-progress" style="width: {{ $progressWidth }}%;"></div>
                            </div>

                            @foreach($stages as $st)
                                @php
                                    $itemClass = '';
                                    if ($st['is_danger']) {
                                        $itemClass = 'is-rejected';
                                    } elseif ($st['is_done']) {
                                        $itemClass = 'is-completed';
                                    } elseif ($st['is_active']) {
                                        $itemClass = 'is-active';
                                    }
                                @endphp
                                <div class="tracker-step-item {{ $itemClass }}">
                                    <div class="tracker-step-node" aria-hidden="true">
                                        @if($st['is_danger'])
                                            <i class="bi bi-x-lg"></i>
                                        @elseif($st['is_done'])
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            <i class="bi {{ $st['icon'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="tracker-step-title">{{ $st['step'] }}. {{ $st['title'] }}</div>
                                    <div>
                                        @if($st['is_danger'])
                                            <span class="tracker-step-badge bg-danger text-white">Belum Lolos</span>
                                        @elseif($st['is_done'])
                                            <span class="tracker-step-badge bg-success-subtle text-success">Selesai</span>
                                        @elseif($st['is_active'])
                                            <span class="tracker-step-badge bg-primary text-white">Berlangsung</span>
                                        @else
                                            <span class="tracker-step-badge bg-light text-muted">Menunggu</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- MOBILE STEPPER (< 768px) -->
                        <div class="tracker-stepper-mobile" aria-label="Linimasa Tahapan Seleksi Mobile">
                            <ul class="tracker-stepper-mobile-list">
                                @foreach($stages as $st)
                                    @php
                                        $mobileClass = '';
                                        if ($st['is_danger']) {
                                            $mobileClass = 'is-rejected';
                                        } elseif ($st['is_done']) {
                                            $mobileClass = 'is-completed';
                                        } elseif ($st['is_active']) {
                                            $mobileClass = 'is-active';
                                        }
                                    @endphp
                                    <li class="tracker-mobile-step-item {{ $mobileClass }}">
                                        <div class="tracker-mobile-step-node" aria-hidden="true">
                                            @if($st['is_danger'])
                                                <i class="bi bi-x-lg"></i>
                                            @elseif($st['is_done'])
                                                <i class="bi bi-check-lg"></i>
                                            @else
                                                <span>{{ $st['step'] }}</span>
                                            @endif
                                        </div>
                                        <div class="tracker-mobile-step-content">
                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                <h5 class="h6 fw-bold mb-0 text-heading">{{ $st['step'] }}. {{ $st['title'] }}</h5>
                                                @if($st['is_danger'])
                                                    <span class="badge bg-danger text-white">Belum Lolos</span>
                                                @elseif($st['is_done'])
                                                    <span class="badge bg-success text-white">Selesai</span>
                                                @elseif($st['is_active'])
                                                    <span class="badge bg-primary text-white">Berlangsung</span>
                                                @else
                                                    <span class="badge bg-light text-muted border">Menunggu</span>
                                                @endif
                                            </div>
                                            <p class="small text-muted mb-0 mt-1">{{ $st['desc'] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Detailed Action / Notice Box Depending on Status -->
                        <div class="tracker-status-box status-{{ $status }} mt-4">
                            @if($status === 'submitted')
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-success fs-3 flex-shrink-0" aria-hidden="true">
                                        <i class="bi bi-check2-all"></i>
                                    </div>
                                    <div>
                                        <h5 class="h6 fw-bold mb-1 text-heading">Lamaran Anda Berhasil Diterima & Masuk Antrean Panitia</h5>
                                        <p class="small text-muted mb-2">
                                            Alhamdulillah, berkas administrasi Anda telah tersimpan dengan aman di sistem AL-HIKMAH LMS. Tim panitia seleksi sedang mengantrekan peninjauan dokumen dalam rentang waktu <strong>1–3 hari kerja</strong>.
                                        </p>
                                        <div class="small text-secondary bg-white bg-opacity-75 p-2 rounded border border-light-subtle">
                                            <i class="bi bi-lightbulb me-1 text-primary"></i> <strong>Langkah Selanjutnya:</strong> Pastikan nomor WhatsApp Anda tetap aktif. Jadwal tes kompetensi akan dikirimkan dan diperbarui langsung di halaman ini.
                                        </div>
                                    </div>
                                </div>
                            @elseif($status === 'document_review')
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-primary fs-3 flex-shrink-0" aria-hidden="true">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div>
                                        <h5 class="h6 fw-bold mb-1 text-heading">Pemeriksaan Berkas & Portofolio Sedang Berjalan</h5>
                                        <p class="small text-muted mb-2">
                                            Tim kurikulum AL-HIKMAH sedang memvalidasi data ijazah, sertifikat keilmuan, dan sanad Al-Qur'an yang Anda lampirkan. 
                                        </p>
                                        <p class="small text-muted mb-0">
                                            Setelah tahap berkas dinyatakan lolos, akun portal Anda akan diaktifkan untuk pengerjaan ujian tes kompetensi pedagogik dan tilawah.
                                        </p>
                                    </div>
                                </div>
                            @elseif($status === 'test_scheduled')
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-warning fs-3 flex-shrink-0" aria-hidden="true">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div class="w-100">
                                        <h5 class="h6 fw-bold mb-1 text-heading">Sesi Ujian Tes Kompetensi Telah Dibuka</h5>
                                        <p class="small text-muted mb-3">
                                            Paket soal ujian kompetensi mandiri telah siap dikerjakan. Anda dapat masuk menggunakan akun portal calon guru untuk mulai menyelesaikan ujian daring.
                                        </p>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3 fw-semibold">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Portal & Kerjakan Ujian
                                            </a>
                                            <span class="small text-muted">Durasi pengerjaan: 45–60 Menit</span>
                                        </div>
                                    </div>
                                </div>
                            @elseif($status === 'test_completed')
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-info fs-3 flex-shrink-0" aria-hidden="true">
                                        <i class="bi bi-patch-question"></i>
                                    </div>
                                    <div>
                                        <h5 class="h6 fw-bold mb-1 text-heading">Ujian Tertulis Telah Selesai Dikerjakan</h5>
                                        <p class="small text-muted mb-2">
                                            Nilai asesmen kompetensi Anda: <strong>{{ $application->final_score ? number_format($application->final_score, 1) : '-' }} / 100</strong>.
                                        </p>
                                        <p class="small text-muted mb-0">
                                            Panitia seleksi sedang menyusun jadwal wawancara tatap maya (video conference) bersama Dewan Penguji AL-HIKMAH. Mohon pantau notifikasi WhatsApp berkala.
                                        </p>
                                    </div>
                                </div>
                            @elseif($status === 'interview_scheduled')
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-warning fs-3 flex-shrink-0" aria-hidden="true">
                                        <i class="bi bi-camera-video-fill"></i>
                                    </div>
                                    <div class="w-100">
                                        <h5 class="h6 fw-bold mb-1 text-heading">Undangan Sesi Wawancara & Microteaching</h5>
                                        <p class="small text-muted mb-2">
                                            Anda dijadwalkan mengikuti wawancara komitmen dan simulasi mengajar tilawah Al-Qur'an secara tatap maya.
                                        </p>

                                        @if($application->interview_scheduled_at)
                                            <div class="bg-white bg-opacity-75 p-3 rounded border border-light-subtle mb-3">
                                                <div class="row g-2 small">
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block">Waktu Pelaksanaan:</span>
                                                        <strong class="text-heading">{{ \Carbon\Carbon::parse($application->interview_scheduled_at)->isoFormat('dddd, D MMMM Y - HH:mm') }} WIB</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-muted d-block">Metode:</span>
                                                        <strong class="text-heading">{{ ucfirst($application->interview_type ?? 'Online Video Meeting') }}</strong>
                                                    </div>
                                                </div>
                                                @if($application->interview_meeting_link)
                                                    <div class="mt-2 pt-2 border-top">
                                                        <a href="{{ $application->interview_meeting_link }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Tautan Ruang Pertemuan (Zoom/Meet)
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="small text-secondary">
                                            <strong>Persiapan Peserta:</strong> Mohon siapkan mushaf standar (Utsmani), perangkat berkamera & mikrofon yang jelas, serta berada di ruangan yang hening 10 menit sebelum sesi dimulai.
                                        </div>
                                    </div>
                                </div>
                            @elseif($status === 'approved')
                                <div class="text-center py-2">
                                    <div class="text-success fs-1 mb-2" aria-hidden="true">
                                        <i class="bi bi-patch-check-fill"></i>
                                    </div>
                                    <h5 class="h5 fw-bold mb-2 text-heading">Ahlan wa Sahlan! Anda Resmi Diterima Sebagai Guru Pembimbing</h5>
                                    <p class="small text-muted mx-auto mb-3" style="max-width: 600px;">
                                        Alhamdulillah, atas izin Allah Subhanahu wa Ta'ala serta berdasarkan seluruh tahapan asesmen, Anda dinyatakan lolos sebagai Guru Pembimbing AL-HIKMAH LMS (Masa Penyesuaian 3 Bulan).
                                    </p>
                                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                        <a href="{{ route('login') }}" class="btn btn-success fw-semibold px-4 py-2">
                                            <i class="bi bi-box-arrow-in-right me-1"></i> Buka Portal Guru & Jadwal Halaqah
                                        </a>
                                        <a href="{{ wa_url('Assalamualaikum panitia AL-HIKMAH, saya ' . $application->full_name . ' (' . $application->application_code . ') ingin mengonfirmasi penerimaan guru.') }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer" 
                                           class="btn btn-outline-secondary px-3 py-2">
                                            <i class="bi bi-whatsapp me-1 text-success"></i> Konfirmasi ke Panitia
                                        </a>
                                    </div>
                                </div>
                            @elseif($status === 'rejected')
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-danger fs-3 flex-shrink-0" aria-hidden="true">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div>
                                        <h5 class="h6 fw-bold mb-1 text-heading">Pemberitahuan Hasil Evaluasi Rekrutmen</h5>
                                        <p class="small text-muted mb-2">
                                            Jazakumullah khairan katsiran atas kesungguhan dan ikhtiar Anda dalam mengikuti seleksi calon guru bimbingan Al-Qur'an AL-HIKMAH LMS.
                                        </p>
                                        <p class="small text-muted mb-0">
                                            Berdasarkan hasil pertimbangan panitia dan keterbatasan formasi yang tersedia pada periode ini, saat ini kami belum dapat melanjutkan ke tahap penetapan. Seluruh data portofolio Anda tetap tersimpan dalam basis talenta kami untuk kesempatan formasi yang akan datang.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-secondary fs-3 flex-shrink-0">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div>
                                        <h5 class="h6 fw-bold mb-1 text-heading">Status Lamaran: {{ ucfirst($status) }}</h5>
                                        <p class="small text-muted mb-0">Silakan hubungi panitia rekrutmen apabila membutuhkan informasi detail lebih lanjut.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Data Summary & Details Accordion -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row g-3 small">
                                <div class="col-sm-6 col-md-3">
                                    <span class="text-muted d-block mb-1">Domisili</span>
                                    <strong class="text-heading">{{ $application->city ?? '-' }}</strong>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <span class="text-muted d-block mb-1">Pendidikan</span>
                                    <strong class="text-heading">{{ $application->education ?? '-' }}</strong>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <span class="text-muted d-block mb-1">Pengalaman Mengajar</span>
                                    <strong class="text-heading">{{ $application->experience_years ?? 0 }} Tahun</strong>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <span class="text-muted d-block mb-1">Kelengkapan Berkas</span>
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i> CV & Dokumen Terunggah
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Action in Card -->
                    <div class="card-footer bg-light p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="small text-muted">
                            <i class="bi bi-shield-check text-primary me-1"></i> Data diverifikasi oleh Tim Rekrutmen & Kurikulum AL-HIKMAH
                        </span>
                        <a href="{{ route('mentor.recruitment.status') }}" class="btn btn-link btn-sm text-decoration-none text-muted p-0">
                            <i class="bi bi-arrow-left me-1"></i> Periksa Nomor Lain
                        </a>
                    </div>
                </div>
            @endisset

            <!-- DEFAULT / INFORMATIONAL CONTENT (Alur & Panduan Rekrutmen) -->
            <div class="mt-5">
                <div class="text-center mb-4">
                    <span class="tracker-badge mb-2">
                        <i class="bi bi-compass"></i>
                        <span>Informasi & Ketentuan</span>
                    </span>
                    <h3 class="h5 fw-bold text-heading">Alur Standar Rekrutmen Guru AL-HIKMAH</h3>
                    <p class="small text-muted mx-auto" style="max-width: 600px;">
                        Setiap calon pengajar melalui tahapan seleksi yang berlandaskan adab, ketepatan bacaan Al-Qur'an, dan kesiapan mendampingi santri.
                    </p>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-md-3 col-sm-6">
                        <div class="tracker-stage-card">
                            <div class="tracker-stage-number">01</div>
                            <h4 class="h6 fw-bold text-heading mb-1">Pendaftaran Berkas</h4>
                            <p class="small text-muted mb-0">Pengisian profil, riwayat pendidikan, dan portofolio sanad atau keilmuan Al-Qur'an.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="tracker-stage-card">
                            <div class="tracker-stage-number">02</div>
                            <h4 class="h6 fw-bold text-heading mb-1">Verifikasi (1–3 Hari)</h4>
                            <p class="small text-muted mb-0">Penelaahan kesesuaian berkas administrasi dan portofolio oleh tim panitia kurikulum.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="tracker-stage-card">
                            <div class="tracker-stage-number">03</div>
                            <h4 class="h6 fw-bold text-heading mb-1">Ujian Kompetensi</h4>
                            <p class="small text-muted mb-0">Asesmen daring mencakup kaidah tajwid aplikatif, makharijul huruf, dan adab bimbingan.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="tracker-stage-card">
                            <div class="tracker-stage-number">04</div>
                            <h4 class="h6 fw-bold text-heading mb-1">Microteaching</h4>
                            <p class="small text-muted mb-0">Simulasi mengajar tatap maya untuk mengukur interaksi pengajaran kepada santri anak.</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Rekrutmen -->
                <div class="tracker-card card p-4 p-md-5 mb-4">
                    <h4 class="h6 fw-bold text-heading mb-3">
                        <i class="bi bi-question-circle text-primary me-2"></i>Pertanyaan yang Sering Diajukan
                    </h4>
                    <div class="accordion accordion-flush" id="trackerFaqAccordion">
                        <div class="accordion-item border-bottom">
                            <h5 class="accordion-header" id="faqHeadingOne">
                                <button class="accordion-button collapsed px-0 fw-semibold text-secondary small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="false" aria-controls="faqCollapseOne">
                                    Berapa lama waktu yang dibutuhkan hingga ada panggilan ujian atau wawancara?
                                </button>
                            </h5>
                            <div id="faqCollapseOne" class="accordion-collapse collapse" aria-labelledby="faqHeadingOne" data-bs-parent="#trackerFaqAccordion">
                                <div class="accordion-body px-0 small text-muted">
                                    Peninjauan berkas tahap pertama memerlukan waktu 1–3 hari kerja. Jika berkas Anda memenuhi standar formasi yang dibuka, tautan ujian kompetensi akan langsung aktif di portal dan pemberitahuan dikirimkan via WhatsApp.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h5 class="accordion-header" id="faqHeadingTwo">
                                <button class="accordion-button collapsed px-0 fw-semibold text-secondary small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                                    Apakah ada pungutan biaya dalam proses rekrutmen ini?
                                </button>
                            </h5>
                            <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#trackerFaqAccordion">
                                <div class="accordion-body px-0 small text-muted">
                                    Seluruh tahapan rekrutmen Guru Pembimbing di AL-HIKMAH LMS bersifat <strong>100% Gratis</strong> dan tidak dipungut biaya apa pun. Hati-hati terhadap pihak yang mengatasnamakan lembaga.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h5 class="accordion-header" id="faqHeadingThree">
                                <button class="accordion-button collapsed px-0 fw-semibold text-secondary small" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                                    Bagaimana jika nomor WhatsApp saya keliru saat pendaftaran?
                                </button>
                            </h5>
                            <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#trackerFaqAccordion">
                                <div class="accordion-body px-0 small text-muted">
                                    Anda dapat menghubungi narahubung Customer Service AL-HIKMAH dengan menyertakan nama lengkap, alamat email terdaftar, dan foto kartu identitas (KTP) untuk verifikasi pembaruan data kontak.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kontak Bantuan Panitia -->
                <div class="card border-0 bg-primary-subtle text-primary-emphasis p-4 rounded-3 text-center">
                    <h5 class="h6 fw-bold mb-1">Mengalami Kendala Teknis Saat Memantau Status?</h5>
                    <p class="small text-muted mb-3">Tim Helpdesk Rekrutmen AL-HIKMAH siap membantu Anda setiap Senin–Jumat pukul 08.00–16.00 WIB.</p>
                    <div>
                        <a href="{{ wa_url('Assalamualaikum panitia AL-HIKMAH, saya calon pelamar guru ingin menanyakan bantuan terkait status pendaftaran.') }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="btn btn-primary btn-sm px-3 fw-semibold">
                            <i class="bi bi-whatsapp me-1"></i> Hubungi Helpdesk via WhatsApp
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyRegistrationCode(code, buttonElement) {
        if (!navigator.clipboard) {
            return;
        }
        navigator.clipboard.writeText(code).then(function() {
            var originalHtml = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="bi bi-check2 text-success"></i>';
            buttonElement.setAttribute('title', 'Kode Tersalin');
            setTimeout(function() {
                buttonElement.innerHTML = originalHtml;
                buttonElement.setAttribute('title', 'Salin Kode');
            }, 2000);
        }).catch(function(err) {
            console.error('Gagal menyalin:', err);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('formCekStatus');
        var btn = document.getElementById('btnCekStatus');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>Memeriksa...</span>';
            });
        }
    });
</script>
@endpush
