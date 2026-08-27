@extends('layouts.parent')

@section('title', 'Detail Permohonan Pendaftaran | AL-HIKMAH')
@section('header', 'Detail Pendaftaran & Negosiasi Jadwal')
@section('subheader', 'Informasi permohonan bimbingan dan alokasi guru pembimbing ananda')

@section('content')
<div class="container-fluid p-0">
    <!-- Main Card Header & Summary -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="p-4 p-md-5 text-white position-relative" style="background: var(--primary-gradient);">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative z-2">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-white text-dark px-3 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="bi {{ $enrollment->status->icon() }} text-success me-1"></i> {{ $enrollment->status->label() }}
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill border border-white border-opacity-25">
                            #ENR-{{ str_pad($enrollment->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $enrollment->program->name }}</h3>
                    <p class="mb-0 text-white-50 fs-6">
                        <i class="bi bi-person-fill me-1"></i> Santri: <strong>{{ $enrollment->student->getDisplayName() }}</strong> ({{ $enrollment->student->age }} Tahun)
                    </p>
                </div>
                <div class="text-md-end bg-white bg-opacity-10 p-3 rounded-4 border border-white border-opacity-25">
                    <span class="text-white-50 small d-block mb-1"><i class="bi bi-tag-fill me-1"></i> Investasi Program Terkunci</span>
                    <span class="fw-bold fs-4 text-white">{{ $enrollment->formatted_price }}</span>
                </div>
            </div>
        </div>

        <!-- Progress Timeline Bar -->
        <div class="card-body p-4 border-bottom border-subtle bg-card">
            <div class="status-timeline-bar">
                <div class="timeline-connector"></div>
                
                <!-- Step 1: Diajukan -->
                <div class="timeline-point {{ $enrollment->status->value ? 'passed' : '' }}">
                    <div class="timeline-dot"><i class="bi bi-check-lg"></i></div>
                    <div class="timeline-text">1. Diajukan</div>
                </div>

                <!-- Step 2: Peninjauan Lembaga -->
                <div class="timeline-point {{ in_array($enrollment->status->value, ['waiting_parent', 'confirmed', 'active']) ? 'passed' : ($enrollment->status->value === 'waiting_admin' ? 'active' : '') }}">
                    <div class="timeline-dot">
                        <i class="bi {{ in_array($enrollment->status->value, ['waiting_parent', 'confirmed', 'active']) ? 'bi-check-lg' : 'bi-hourglass-split' }}"></i>
                    </div>
                    <div class="timeline-text">2. Review & Guru</div>
                </div>

                <!-- Step 3: Kesepakatan Jadwal -->
                <div class="timeline-point {{ in_array($enrollment->status->value, ['confirmed', 'active']) ? 'passed' : ($enrollment->status->value === 'waiting_parent' ? 'active' : '') }}">
                    <div class="timeline-dot">
                        <i class="bi {{ in_array($enrollment->status->value, ['confirmed', 'active']) ? 'bi-check-lg' : 'bi-hand-thumbs-up' }}"></i>
                    </div>
                    <div class="timeline-text">3. Deal Jadwal</div>
                </div>

                <!-- Step 4: Pembayaran SPP -->
                <div class="timeline-point {{ $enrollment->status->value === 'active' ? 'passed' : ($enrollment->status->value === 'confirmed' ? 'active' : '') }}">
                    <div class="timeline-dot">
                        <i class="bi {{ $enrollment->status->value === 'active' ? 'bi-check-lg' : 'bi-wallet2' }}"></i>
                    </div>
                    <div class="timeline-text">4. Pembayaran</div>
                </div>

                <!-- Step 5: Bimbingan Aktif -->
                <div class="timeline-point {{ $enrollment->status->value === 'active' ? 'active' : '' }}">
                    <div class="timeline-dot"><i class="bi bi-mortarboard-fill"></i></div>
                    <div class="timeline-text">5. Kelas Aktif</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-2 p-3" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4 shadow-sm border-0 d-flex align-items-center gap-2 p-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
                    <div class="fw-semibold">{{ session('warning') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- 2-Column Comparison Matrix -->
            <div class="row g-4 mb-4">
                <!-- Kolom Request Parent -->
                <div class="col-lg-6">
                    <div class="schedule-spec-card">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-subtle">
                            <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                                <i class="bi bi-person-check-fill fs-5"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold text-heading mb-0">Permohonan Jadwal dari Anda</h6>
                                <small class="text-muted">Preferensi yang Anda ajukan saat mendaftar</small>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="text-muted"><i class="bi bi-calendar-check fs-5"></i></div>
                                <div>
                                    <span class="text-muted small d-block">Hari Pilihan</span>
                                    <span class="fw-bold text-heading">{{ $enrollment->requested_days_label }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="text-muted"><i class="bi bi-clock-history fs-5"></i></div>
                                <div>
                                    <span class="text-muted small d-block">Estimasi Jam</span>
                                    <span class="fw-bold text-heading">{{ $enrollment->requested_time_label }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="text-muted"><i class="bi bi-laptop fs-5"></i></div>
                                <div>
                                    <span class="text-muted small d-block">Metode Belajar</span>
                                    <span class="badge bg-light text-dark border border-subtle px-3 py-1 rounded-pill fw-semibold">
                                        {{ ucfirst($enrollment->learning_method ?? 'offline') }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="text-muted"><i class="bi bi-chat-left-dots fs-5"></i></div>
                                <div>
                                    <span class="text-muted small d-block">Catatan Anda</span>
                                    <span class="text-heading small">{{ $enrollment->parent_notes ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Respon Lembaga -->
                <div class="col-lg-6">
                    <div class="schedule-spec-card {{ $enrollment->isWaitingParent() ? 'highlight-offer' : '' }}">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom border-subtle">
                            <span class="badge {{ $enrollment->isWaitingParent() ? 'bg-warning-subtle text-warning' : 'bg-primary-subtle text-primary' }} p-2 rounded-circle">
                                <i class="bi bi-building-check fs-5"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold text-heading mb-0">Respon & Alokasi Lembaga</h6>
                                <small class="text-muted">Pencocokan kuota & guru pembimbing Al-Hikmah</small>
                            </div>
                        </div>

                        @if($enrollment->offered_days || $enrollment->admin_notes || $enrollment->mentor_id || $enrollment->isConfirmed() || $enrollment->isActive())
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-muted"><i class="bi bi-calendar-week fs-5"></i></div>
                                    <div>
                                        <span class="text-muted small d-block">Hari Belajar Ditetapkan</span>
                                        <span class="fw-bold text-success">{{ $enrollment->effective_days_label }}</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-muted"><i class="bi bi-alarm fs-5"></i></div>
                                    <div>
                                        <span class="text-muted small d-block">Jam Bimbingan</span>
                                        <span class="fw-bold text-success">{{ $enrollment->effective_time_label }}</span>
                                    </div>
                                </div>

                                @if($enrollment->mentor)
                                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 bg-light border border-subtle">
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold fs-6 flex-shrink-0" style="width: 42px; height: 42px;">
                                            <i class="bi bi-person-workspace"></i>
                                        </div>
                                        <div>
                                            <span class="text-muted small d-block">Guru Pembimbing (Mentor)</span>
                                            <span class="fw-bold text-heading">Ustadz/ah {{ $enrollment->mentor->getDisplayName() }}</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-muted"><i class="bi bi-play-circle fs-5"></i></div>
                                    <div>
                                        <span class="text-muted small d-block">Mulai Sesi Belajar</span>
                                        <span class="fw-bold text-heading">{{ $enrollment->start_date_label }}</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start gap-3">
                                    <div class="text-muted"><i class="bi bi-sticky fs-5"></i></div>
                                    <div>
                                        <span class="text-muted small d-block">Catatan Pengelola</span>
                                        <span class="text-heading small">{{ $enrollment->admin_notes ?? 'Jadwal dan alokasi mentor telah disesuaikan.' }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="spinner-border text-success mb-2" role="status" style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h6 class="fw-bold text-heading mb-1">Sedang Dalam Peninjauan</h6>
                                <p class="text-muted small mb-0">Tim pengelola AL-HIKMAH sedang mencocokkan jadwal dan ketersediaan guru pembimbing terbaik untuk ananda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Card 1: Konfirmasi Jadwal Alternatif (Jika Menunggu Respon Parent) -->
            @if($enrollment->isWaitingParent())
                <div class="card border-warning bg-warning bg-opacity-10 rounded-4 p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold text-warning-emphasis mb-1">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Tindakan Diperlukan: Konfirmasi Jadwal Alternatif
                            </h6>
                            <p class="small text-muted mb-0">
                                Pengelola lembaga menawarkan penyesuaian jadwal seperti tertera di atas. Silakan konfirmasi persetujuan Anda.
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <form action="{{ route('parent.enrollments.accept-offer', $enrollment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success px-4 py-2 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-check-circle-fill me-1"></i> Setujui Jadwal Ini
                                </button>
                            </form>
                            <button type="button" class="btn btn-outline-danger px-4 py-2 rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-arrow-repeat me-1"></i> Minta Alternatif Lain
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Tolak / Minta Alternatif Lain -->
                <div class="modal fade" id="rejectModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('parent.enrollments.reject-offer', $enrollment->id) }}" method="POST">
                            @csrf
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom border-subtle">
                                    <h5 class="modal-title fw-bold text-heading">
                                        <i class="bi bi-calendar-range text-warning me-2"></i>Minta Alternatif Jadwal Lain
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <label class="form-label small fw-bold text-secondary">
                                        Preferensi Hari / Jam Tambahan & Catatan Anda:
                                    </label>
                                    <textarea name="rejection_reason" class="form-control rounded-4 p-3 border-subtle" rows="3" placeholder="Contoh: Jika hari Selasa ustadz berhalangan, kami lebih fleksibel di hari Jumat jam 16.00 WIB." required></textarea>
                                </div>
                                <div class="modal-footer border-top border-subtle">
                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Kembali</button>
                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Kirim Permintaan Ulang</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Action Card 2: Bayar Tagihan (Jika Sudah Disepakati) -->
            @if($enrollment->isConfirmed() && $enrollment->payment)
                <div class="card border-success bg-success bg-opacity-10 rounded-4 p-4 mb-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <span class="badge bg-success px-3 py-1 rounded-pill mb-2 fw-semibold">
                                <i class="bi bi-check-all me-1"></i> Jadwal Telah Disepakati
                            </span>
                            <h5 class="fw-bold text-success mb-1">Invoice Tagihan Belajar Telah Diterbitkan</h5>
                            <p class="small text-muted mb-0">
                                Selesaikan pembayaran sebesar <strong>Rp {{ number_format($enrollment->payment->amount, 0, ',', '.') }}</strong> untuk mengaktifkan sesi kelas dan jadwal ananda.
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('parent.payments.show', $enrollment->payment->id) }}" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm" style="background: var(--primary-gradient); border: none;">
                                <i class="bi bi-wallet2 me-2"></i> Bayar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Footer Navigation -->
            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-subtle">
                <a href="{{ route('parent.enrollments.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
                @if($enrollment->isActive())
                    <a href="{{ route('parent.schedules.index') }}" class="btn btn-outline-success px-4 rounded-pill fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i> Lihat Jadwal Kelas Ananda
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
