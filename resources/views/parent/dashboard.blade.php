@extends('layouts.parent')

@section('title', 'Dashboard Utama Orang Tua')
@section('header', 'Dashboard Utama')
@section('subheader', 'Ringkasan capaian hafalan anak, jadwal bimbingan, dan status tagihan')

@section('content')
    <div class="container-fluid p-0">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (!$hasPaidProgram)
            @if (!$hasPendingEnrollment)
                <!-- STATE 1: ONBOARDING (Belum Punya Program) -->
                @if ($totalChildrenCount == 0)
                    <!-- STATE 1A: Belum Mengisi Data Anak -->
                    <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-start gap-3"
                        role="alert">
                        <div class="rounded-circle p-2 bg-warning text-white d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 42px; height: 42px;">
                            <i class="bi bi-person-fill-exclamation fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Pendaftaran Akun Berhasil! Langkah 1: Daftarkan Data Anak
                            </h6>
                            <p class="text-muted small mb-0">Sebelum dapat memilih program belajar dan mengajukan jadwal
                                bimbingan, Anda wajib mengisi data lengkap anak binaan (calon santri) terlebih dahulu ke
                                dalam sistem.</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                        <!-- Stepper Alur Onboarding -->
                        <div class="row g-3 mb-4 text-center">
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-primary-subtle border border-primary-subtle h-100">
                                    <span class="badge bg-primary rounded-pill mb-2">Langkah 1 (Saat Ini)</span>
                                    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-person-plus me-1"></i> Isi Data
                                        Anak</h6>
                                    <p class="small text-muted mb-0">Lengkapi identitas calon santri yang akan belajar.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-light border h-100 opacity-75">
                                    <span class="badge bg-secondary rounded-pill mb-2">Langkah 2</span>
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-book me-1"></i> Pilih Program &
                                        Jadwal</h6>
                                    <p class="small text-muted mb-0">Tentukan paket program dan preferensi waktu.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-light border h-100 opacity-75">
                                    <span class="badge bg-secondary rounded-pill mb-2">Langkah 3</span>
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-check-circle me-1"></i> Konfirmasi &
                                        Belajar</h6>
                                    <p class="small text-muted mb-0">Verifikasi jadwal admin dan mulai belajar.</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center py-3">
                            <div class="rounded-circle p-3 bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 75px; height: 75px;">
                                <i class="bi bi-person-vcard fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Data Anak Belum Terdaftar</h4>
                            <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
                                Akun orang tua Anda telah aktif, namun Anda belum mendaftarkan ananda. Silakan isi data
                                nama, usia, jenis kelamin, dan domisili anak Anda agar sistem dapat menyiapkan kurikulum dan
                                pilihan pengajar yang tepat.
                            </p>
                            <div>
                                <a href="{{ route('parent.profile.children') }}"
                                    class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-person-plus-fill me-2"></i>Isi Data Lengkap Anak Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- STATE 1B: Sudah Ada Data Anak, Belum Memilih Program -->
                    <div class="alert alert-info border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-start gap-3"
                        role="alert">
                        <div class="rounded-circle p-2 bg-info text-white d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 42px; height: 42px;">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Pendaftaran Berhasil ! Langkah 2: Pilih Program Belajar</h6>
                            <p class="text-muted small mb-0">Anda telah mendaftarkan <strong>{{ $totalChildrenCount }} anak
                                    binaan</strong>. Silakan pilih paket program dan ajukan jadwal bimbingan untuk memulai
                                perjalanan belajar ananda.</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
                        <!-- Stepper Alur Onboarding -->
                        <div class="row g-3 mb-4 text-center">
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-success-subtle border border-success-subtle h-100">
                                    <span class="badge bg-success rounded-pill mb-2"><i class="bi bi-check me-1"></i>
                                        Selesai</span>
                                    <h6 class="fw-bold text-success mb-1"><i class="bi bi-person-check me-1"></i> Data Anak
                                        Terdaftar</h6>
                                    <p class="small text-muted mb-0">{{ $totalChildrenCount }} anak binaan aktif di akun
                                        Anda.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-primary-subtle border border-primary-subtle h-100">
                                    <span class="badge bg-primary rounded-pill mb-2">Langkah 2 (Saat Ini)</span>
                                    <h6 class="fw-bold text-primary mb-1"><i class="bi bi-book me-1"></i> Pilih Program &
                                        Jadwal</h6>
                                    <p class="small text-muted mb-0">Tentukan paket program dan preferensi waktu.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded-4 bg-light border h-100 opacity-75">
                                    <span class="badge bg-secondary rounded-pill mb-2">Langkah 3</span>
                                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-check-circle me-1"></i> Konfirmasi &
                                        Belajar</h6>
                                    <p class="small text-muted mb-0">Verifikasi jadwal admin dan mulai belajar.</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center py-3">
                            <div class="rounded-circle p-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 75px; height: 75px;">
                                <i class="bi bi-journal-bookmark-fill fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Pilih Program Belajar untuk Ananda</h4>
                            <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
                                Data anak Anda sudah siap. Silakan telusuri paket bimbingan Al-Qur'an (Tahsin, Tahfidz, atau
                                Bahasa Arab) dan tentukan hari serta jam belajar yang paling fleksibel untuk ananda.
                            </p>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="{{ url('/biaya') }}"
                                    class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-cart-plus me-1"></i> Pilih Program Sekarang
                                </a>
                                <a href="{{ route('parent.profile.children') }}"
                                    class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
                                    <i class="bi bi-people me-1"></i> Kelola Data Anak ({{ $totalChildrenCount }})
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- STATE 2: TRANSISI (Menunggu Konfirmasi/Bayar) -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Status Pendaftaran</h5>
                            <p class="text-muted mb-0 small">Anda sedang dalam proses pendaftaran. Silakan selesaikan
                                tahapan berikut.</p>
                        </div>
                    </div>

                    @if ($latestEnrollment)
                        <div class="p-4 bg-light rounded-3 mt-3 border">
                            <div class="mb-3 d-flex align-items-center gap-2">
                                <strong>Status Saat Ini:</strong>
                                <span
                                    class="badge bg-{{ $latestEnrollment->status_badge_class }}">{{ $latestEnrollment->status_label }}</span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progress: {{ $latestEnrollment->progress_step_label }}</span>
                                    <span>{{ $latestEnrollment->progress_percent }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $latestEnrollment->status_badge_class }}"
                                        role="progressbar" style="width: {{ $latestEnrollment->progress_percent }}%;"
                                        aria-valuenow="{{ $latestEnrollment->progress_percent }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>

                            @if ($latestEnrollment->status === 'draft')
                                <p class="small text-muted mb-3">Pendaftaran Anda masih berstatus DRAFT. Silakan lengkapi
                                    data santri dan pilih jadwal bimbingan.</p>
                                <a href="{{ route('parent.enrollments.index') }}"
                                    class="btn btn-primary rounded-pill px-4">Lanjutkan Pendaftaran</a>
                            @elseif(
                                $latestEnrollment->status === 'waiting_admin' ||
                                    (is_object($latestEnrollment->status) && $latestEnrollment->status->value === 'waiting_admin'))
                                <p class="small text-muted mb-3">Pendaftaran Anda sedang <strong>menunggu konfirmasi
                                        jadwal</strong> dari Admin. Mohon tunggu maksimal 1x24 jam.</p>
                                <a href="{{ route('parent.enrollments.index') }}"
                                    class="btn btn-outline-primary rounded-pill px-4">Cek Status Pendaftaran</a>
                            @elseif(
                                $latestEnrollment->status === 'waiting_parent' ||
                                    (is_object($latestEnrollment->status) && $latestEnrollment->status->value === 'waiting_parent'))
                                <p class="small text-muted mb-3">Terdapat penawaran jadwal baru dari admin. Silakan cek dan
                                    konfirmasi penawaran jadwal tersebut.</p>
                                <a href="{{ route('parent.enrollments.index') }}"
                                    class="btn btn-primary rounded-pill px-4">Lihat Penawaran Jadwal</a>
                            @elseif(
                                $latestEnrollment->status === 'confirmed' ||
                                    (is_object($latestEnrollment->status) && $latestEnrollment->status->value === 'confirmed'))
                                <p class="small text-muted mb-3">Jadwal Anda telah disetujui! Silakan lakukan pembayaran
                                    tagihan agar program dapat segera dimulai.</p>
                                <a href="{{ route('parent.payments.index') }}"
                                    class="btn btn-success rounded-pill px-4">Bayar Tagihan Sekarang</a>
                            @else
                                <a href="{{ route('parent.enrollments.index') }}"
                                    class="btn btn-outline-primary rounded-pill px-4">Kelola Pendaftaran</a>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @else
            <!-- STATE 3: AKTIF (Sudah Lunas) -->
            <!-- 1️⃣ Kartu Statistik Utama -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-3">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">Jumlah Anak Binaan</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $totalChildrenCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-success-subtle text-success fs-3">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">Sesi Bulan Ini</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $monthSessionsCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-3">
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">Rata-rata Tajwid Anak</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $avgTajwidScore }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-3 bg-danger-subtle text-danger fs-3">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div>
                                <div class="text-muted small fw-semibold">Tagihan Pending</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $pendingPaymentsCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2️⃣ Quick Action Buttons -->
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <a href="{{ route('parent.children.index') }}"
                    class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-people me-2"></i>Lihat Semua Anak
                </a>
                <a href="{{ route('parent.payments.history') }}"
                    class="btn btn-outline-success rounded-pill px-4 fw-bold">
                    <i class="bi bi-receipt me-2"></i>Histori Pembayaran
                </a>
                <a href="{{ route('parent.messages.create') }}" class="btn btn-outline-info rounded-pill px-4 fw-bold">
                    <i class="bi bi-chat-text me-2"></i>Hubungi Mentor
                </a>
                <a href="{{ route('parent.schedules.index') }}"
                    class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                    <i class="bi bi-calendar-week me-2"></i>Jadwal Bimbingan
                </a>
            </div>

            <div class="row g-4">
                <!-- 3️⃣ Progres Anak Terbaru -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div
                            class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i
                                    class="bi bi-journal-check me-2 text-success"></i>Capaian Hafalan & Progres Terbaru
                            </h5>
                            <a href="{{ route('parent.children.index') }}"
                                class="btn btn-sm btn-link text-decoration-none">Lihat Detail</a>
                        </div>
                        <div class="card-body p-4">
                            @if ($recentProgresses->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada catatan progres bimbingan terbaru untuk anak Anda.
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach ($recentProgresses as $prog)
                                        <div class="list-group-item px-0 py-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="fw-bold text-dark fs-6">
                                                    {{ $prog->student?->user?->name ?? $prog->student?->full_name }}</div>
                                                <small class="text-muted">{{ $prog->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="small text-secondary mb-2">
                                                <i class="bi bi-book me-1"></i>{{ $prog->surah_start ?? 'Surah' }} (Juz
                                                {{ $prog->juz ?? 1 }}) | Pembimbing:
                                                {{ $prog->mentor?->user?->name ?? 'Ustaz' }}
                                            </div>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-success-subtle text-success">Tajwid:
                                                    {{ $prog->nilai_tajwid ?? '-' }}</span>
                                                <span class="badge bg-primary-subtle text-primary">Fluent:
                                                    {{ $prog->nilai_fluent ?? '-' }}</span>
                                                <span class="badge bg-info-subtle text-info">Adab:
                                                    {{ $prog->nilai_adab ?? '-' }}</span>
                                            </div>
                                            @if ($prog->catatan_evaluasi)
                                                <div class="bg-light p-2 rounded-3 mt-2 small text-dark">
                                                    <i
                                                        class="bi bi-chat-square-text me-1 text-muted"></i><em>"{{ $prog->catatan_evaluasi }}"</em>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4️⃣ Jadwal Bimbingan Mendatang (7 Hari Ke Depan) -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div
                            class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Jadwal
                                7 Hari Ke Depan</h5>
                            <a href="{{ route('parent.schedules.index') }}"
                                class="btn btn-sm btn-link text-decoration-none">Kalender Full</a>
                        </div>
                        <div class="card-body p-4">
                            @if ($upcomingSessions->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    Tidak ada jadwal bimbingan mendatang dalam 7 hari ke depan.
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach ($upcomingSessions as $ses)
                                        <div class="list-group-item px-0 py-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span
                                                    class="fw-bold text-primary">{{ $ses->date ? \Carbon\Carbon::parse($ses->date)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}
                                                    ({{ date('H:i', strtotime($ses->time)) }} WIB)
                                                </span>
                                                @if ($ses->method === 'offline')
                                                    <span
                                                        class="badge bg-success-subtle text-success rounded-pill px-2 border border-success-subtle">Offline</span>
                                                @elseif($ses->method === 'online')
                                                    <span
                                                        class="badge bg-primary-subtle text-primary rounded-pill px-2 border border-primary-subtle">Online</span>
                                                @else
                                                    <span
                                                        class="badge bg-info-subtle text-info rounded-pill px-2 border border-info-subtle">Hybrid</span>
                                                @endif
                                            </div>
                                            <div class="fw-semibold text-dark">
                                                {{ $ses->student?->user?->name ?? $ses->student?->full_name }}</div>
                                            <small class="text-muted d-block">Mentor:
                                                {{ $ses->mentor?->user?->name ?? 'Ustaz/Ustazah' }}</small>
                                            <div class="mt-2">
                                                <a href="{{ route('parent.schedules.show', $ses->id) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    Detail & Konfirmasi Kehadiran
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
