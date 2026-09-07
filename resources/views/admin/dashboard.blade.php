@extends('layouts.admin')

@section('title', 'Dashboard Utama Administrator')
@section('header', 'Dashboard AL-HIKMAH')
@section('subheader', 'Selamat datang kembali, ' . (auth()->user()->name ?? 'Admin') . '! Berikut ikhtisar kinerja operasional dan finansial lembaga hari ini.')

@section('content')
<div class="container-fluid px-0">
    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-grid-1x2-fill text-primary me-2"></i>Pusat Operasional & Pengawasan Akademik
            </h3>
            <p class="text-muted small mb-0">Pantau administrasi santri, jadwal guru, mutaba'ah tahfidz, dan status tagihan secara akurat.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('admin.recruitment.applications.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 position-relative">
                <i class="bi bi-mortarboard-fill me-1"></i>Rekrutmen Guru
                @if(($pendingApplicationsCount ?? 0) > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $pendingApplicationsCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.revenue.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                <i class="bi bi-graph-up-arrow me-1"></i>Analisis Keuangan
            </a>
            <a href="{{ route('admin.broadcast.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="bi bi-whatsapp me-1"></i>Kirim Broadcast
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-arrow-down me-1"></i>Ekspor Laporan
            </a>
        </div>
    </div>

    <!-- 🔴 Operational Alerts Critical Banner -->
    @if (($allAlerts['critical_count'] ?? 0) > 0)
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="badge bg-danger text-white rounded-circle p-2 fs-5">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-danger">Perhatian: Ditemukan {{ $allAlerts['critical_count'] }} Catatan Operasional Kritis</h6>
                    <span class="small text-muted">Mencakup tagihan tertunda &gt;30 hari, santri non-aktif, atau kapasitas bimbingan penuh.</span>
                </div>
            </div>
            <a href="{{ route('admin.alerts.index') }}" class="btn btn-sm btn-danger rounded-pill px-3">
                Buka Pusat Peringatan <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    @endif

    <!-- 1. Executive KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Pendapatan Bulan Ini & MoM -->
        <div class="col-xl-3 col-md-6">
            <div class="lms-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="lms-stat-label">Pendapatan Bulan Ini</span>
                    <div class="lms-stat-icon success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="lms-stat-value text-success">Rp {{ number_format($revenueMetrics['this_month_revenue'] ?? 0, 0, ',', '.') }}</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 small mt-2 pt-2 border-top">
                    @if (($revenueMetrics['mom_growth_percent'] ?? 0) >= 0)
                        <span class="lms-badge success">
                            <i class="bi bi-arrow-up-right"></i> +{{ $revenueMetrics['mom_growth_percent'] }}% MoM
                        </span>
                    @else
                        <span class="lms-badge danger">
                            <i class="bi bi-arrow-down-right"></i> {{ $revenueMetrics['mom_growth_percent'] }}% MoM
                        </span>
                    @endif
                    <span class="text-muted">ARPU: Rp {{ number_format($revenueMetrics['arpu'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Total Santri Binaan -->
        <div class="col-xl-3 col-md-6">
            <div class="lms-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="lms-stat-label">Santri Terdaftar</span>
                    <div class="lms-stat-icon primary">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="lms-stat-value text-primary">{{ number_format($totalStudents) }} <span class="fs-6 fw-normal text-muted">Santri</span></div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 small mt-2 pt-2 border-top">
                    <span class="text-muted">{{ $staffSummary['mentor_student_ratio'] ?? '1 : 0' }} rasio guru:santri</span>
                    <a href="{{ route('admin.students.index') }}" class="text-decoration-none fw-semibold small text-primary">Lihat Data &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Guru & SDM -->
        <div class="col-xl-3 col-md-6">
            <div class="lms-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="lms-stat-label">Guru Pembimbing</span>
                    <div class="lms-stat-icon purple">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
                <div class="lms-stat-value" style="color: #8b5cf6;">{{ $staffSummary['active_mentors'] ?? $totalMentors }} <span class="fs-6 fw-normal text-muted">Aktif</span></div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 small mt-2 pt-2 border-top">
                    <span class="text-muted">{{ $staffSummary['mentors_on_leave_today'] ?? 0 }} guru cuti hari ini</span>
                    <a href="{{ route('admin.staff.index') }}" class="text-decoration-none fw-semibold small" style="color: #8b5cf6;" title="Lihat Database SDM & Detail Akun Guru">
                        Kelola SDM &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Tagihan Overdue & Piutang -->
        <div class="col-xl-3 col-md-6">
            <div class="lms-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="lms-stat-label">Tagihan Tertunda</span>
                    <div class="lms-stat-icon danger">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
                <div class="lms-stat-value text-danger">Rp {{ number_format($revenueMetrics['overdue_invoices_amount'] ?? 0, 0, ',', '.') }}</div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 small mt-2 pt-2 border-top">
                    <span class="text-danger">{{ $revenueMetrics['overdue_invoices_count'] ?? 0 }} lewat tempo</span>
                    <a href="{{ route('admin.payments.index') }}" class="text-decoration-none fw-semibold small text-danger">Detail &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 📅 Section Widget Monitoring Guru Libur & Hari Bebas Hari Ini -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: transparent;">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                            <i class="bi bi-calendar-x text-warning me-2"></i>Monitoring Guru Libur & Hari Bebas Hari Ini ({{ count($offDutyMentors ?? []) }})
                        </h5>
                        <small class="text-muted">Daftar guru yang memilih jadwal libur sendiri atau sedang cuti pada hari {{ today()->locale('id')->translatedFormat('l, d F Y') }}</small>
                    </div>
                    <a href="{{ route('admin.mentors.availability') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                        <i class="bi bi-calendar-week me-1"></i> Buka Matriks Ketersediaan
                    </a>
                </div>
                <div class="card-body p-4">
                    @if(empty($offDutyMentors) || count($offDutyMentors) === 0)
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 p-3 mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                            <span class="small fw-semibold">Alhamdulillah, seluruh guru pembimbing aktif dan siap mengajar pada hari ini. Tidak ada jadwal libur / cuti.</span>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0 datatable" data-page-length="5">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Pengajar</th>
                                        <th>Spesialisasi</th>
                                        <th>Jenis Libur</th>
                                        <th>Alasan / Keterangan</th>
                                        <th>No. WhatsApp</th>
                                        <th class="text-end no-sort">Status Sesi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($offDutyMentors as $item)
                                        @php $m = $item['mentor']; @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <a href="{{ route('admin.staff.show', $m->id) }}" class="text-dark text-decoration-none hover-primary" title="Lihat Profil, CV, Sanad & Rekening Bank">
                                                        {{ $m->getDisplayName() }} <i class="bi bi-box-arrow-up-right text-muted ms-1" style="font-size: 0.68rem;"></i>
                                                    </a>
                                                </div>
                                                <small class="text-muted">{{ $m->user?->email ?? '-' }}</small>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $m->specialization ?? 'Al-Qur\'an' }}</span></td>
                                            <td><span class="badge {{ $item['badge'] ?? 'bg-secondary' }} px-3 py-1 rounded-pill">{{ $item['type'] }}</span></td>
                                            <td class="small text-secondary">{{ $item['reason'] }}</td>
                                            <td>
                                                @if($m->user?->phone)
                                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $m->user->phone)) }}" target="_blank" class="text-success text-decoration-none fw-semibold">
                                                        <i class="bi bi-whatsapp me-1"></i>{{ $m->user->phone }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('admin.staff.show', $m->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2.5 me-1" title="Lihat Detail Profil & Rekening">
                                                    <i class="bi bi-person-lines-fill me-1"></i>Detail Akun
                                                </a>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold">
                                                    <i class="bi bi-dash-circle me-1"></i>Tidak Mengajar Hari Ini
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Section Widget Tabel Pengguna & Role Terdaftar -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: transparent;">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                            <i class="bi bi-people-fill text-primary me-2"></i>Daftar Pengguna & Hak Akses Role
                        </h5>
                        <small class="text-muted">Ringkasan akun pengguna terdaftar di sistem AL-HIKMAH LMS</small>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                        Lihat Semua Pengguna ({{ $totalUsers }}) <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0 datatable" data-page-length="5">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Pengguna</th>
                                    <th>Hak Akses (Role)</th>
                                    <th>Alamat Email</th>
                                    <th>No. Telepon</th>
                                    <th class="text-end no-sort">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $u)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $u->name }}</td>
                                        <td>
                                            @php
                                                $rName = strtolower($u->role?->name ?? '');
                                            @endphp
                                            @if($rName === 'admin')
                                                <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-semibold">Admin</span>
                                            @elseif($rName === 'mentor')
                                                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill fw-semibold">Mentor</span>
                                            @elseif($rName === 'parent')
                                                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-semibold">Parent</span>
                                            @else
                                                <span class="badge bg-info-subtle text-info px-3 py-1 rounded-pill fw-semibold">Santri</span>
                                            @endif
                                        </td>
                                        <td class="text-secondary fw-semibold">{{ $u->email }}</td>
                                        <td>{{ $u->phone ?? '-' }}</td>
                                        <td class="text-end text-nowrap">
                                            @if($rName === 'mentor' && $u->mentor)
                                                <a href="{{ route('admin.staff.show', $u->mentor->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold me-1" title="Lihat Detail Profil, CV & Rekening Bank">
                                                    <i class="bi bi-person-badge me-1"></i> Detail Akun
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.users.index', ['search' => $u->email]) }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold text-primary">
                                                <i class="bi bi-gear me-1"></i> Kelola Role
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Widget Monitor Aktivitas Orang Tua (Parent Monitoring Widget) -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: transparent;">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);"><i class="bi bi-person-heart me-2 text-primary"></i>Parent Monitoring Panel</h5>
                        <p class="text-muted small mb-0">Pantau interaksi, konfirmasi absensi, serta kepatuhan pembayaran dari para Wali Santri</p>
                    </div>
                    <div>
                        <ul class="nav nav-pills" id="parentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill px-3" id="confirmations-tab" data-bs-toggle="tab" data-bs-target="#confirmations-pane" type="button" role="tab">
                                    <i class="bi bi-check2-circle me-1"></i> Konfirmasi Kehadiran
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-3" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments-pane" type="button" role="tab">
                                    <i class="bi bi-credit-card me-1"></i> Pembayaran SPP
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-3" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages-pane" type="button" role="tab">
                                    <i class="bi bi-chat-dots me-1"></i> Pesan & Konsultasi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-3" id="feedbacks-tab" data-bs-toggle="tab" data-bs-target="#feedbacks-pane" type="button" role="tab">
                                    <i class="bi bi-star-fill text-warning me-1"></i> Rating & Ulasan Guru
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tickets-pane" class="nav-link rounded-pill px-3 position-relative" id="tickets-tab" data-bs-toggle="tab" data-bs-target="#tickets-pane" role="tab">
                                    <i class="bi bi-ticket-detailed-fill text-danger me-1"></i> Tiket Intervensi
                                    @if(($openTicketsCount ?? 0) > 0)
                                        <span class="badge bg-danger rounded-pill ms-1">{{ $openTicketsCount }} Terbuka</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="parentTabsContent">
                        <!-- Tab 1: Konfirmasi Kehadiran Sesi Anak -->
                        <div class="tab-pane fade show active" id="confirmations-pane" role="tabpanel">
                            @if($recentConfirmations->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    Belum ada riwayat konfirmasi kehadiran anak dari Orang Tua.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover datatable" data-page-length="5">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Wali Santri</th>
                                                <th>Santri Binaan</th>
                                                <th>Status Konfirmasi</th>
                                                <th>Catatan Orang Tua</th>
                                                <th>Waktu Respon</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentConfirmations as $conf)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ $conf->parent?->user?->name ?? 'Wali Santri' }}</td>
                                                    <td class="text-primary fw-semibold">{{ $conf->session?->student?->user?->name ?? $conf->session?->student?->full_name }}</td>
                                                    <td>
                                                        @if($conf->status === 'hadir')
                                                            <span class="badge bg-success-subtle text-success rounded-pill px-3">HADIR</span>
                                                        @elseif($conf->status === 'izin')
                                                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3">IZIN</span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3">SAKIT</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-secondary">{{ $conf->notes ?? '-' }}</td>
                                                    <td class="small text-muted">{{ $conf->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Tab 2: Pembayaran SPP Orang Tua -->
                        <div class="tab-pane fade" id="payments-pane" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small text-muted fw-bold">Daftar Tagihan & Transaksi SPP Terbaru</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-wallet2 me-1"></i> Kelola Pembayaran Full
                                    </a>
                                    <form action="{{ route('admin.payments.send-reminder') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill fw-bold">
                                            <i class="bi bi-send me-1"></i> Kirim Pengingat Tagihan ke Semua
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if($recentPayments->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    Belum ada riwayat transaksi pembayaran dari Orang Tua.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover datatable" data-page-length="5">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No. Invoice</th>
                                                <th>Santri</th>
                                                <th>Nominal Tagihan</th>
                                                <th>Status Pembayaran</th>
                                                <th>Metode</th>
                                                <th>Waktu Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentPayments as $pay)
                                                <tr>
                                                    <td class="fw-bold text-primary">#{{ $pay->invoice_number ?? ('INV-' . $pay->id) }}</td>
                                                    <td>{{ $pay->student?->user?->name ?? $pay->student?->full_name }}</td>
                                                    <td class="fw-bold text-dark">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                                    <td>
                                                        @if($pay->status === 'paid')
                                                            <span class="badge bg-success rounded-pill px-3">LUNAS</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark rounded-pill px-3">PENDING</span>
                                                        @endif
                                                    </td>
                                                    <td class="small text-secondary">{{ $pay->payment_method ?? 'Pembayaran Digital' }}</td>
                                                    <td class="small text-muted">{{ $pay->updated_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Tab 3: Pesan & Konsultasi Orang Tua -->
                        <div class="tab-pane fade" id="messages-pane" role="tabpanel">
                            @if($recentParentMessages->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    Belum ada pesan atau konsultasi masuk dari Orang Tua.
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($recentParentMessages as $msg)
                                        <div class="list-group-item px-0 py-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="fw-bold text-dark">{{ $msg->sender?->name ?? 'Wali Santri' }}</div>
                                                <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="small text-secondary mb-1">"{{ $msg->message }}"</p>
                                            @if($msg->student)
                                                <small class="text-muted"><i class="bi bi-person me-1"></i>Santri Terkait: {{ $msg->student?->user?->name ?? $msg->student?->full_name }}</small>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Tab 4: Rating & Ulasan Guru -->
                        <div class="tab-pane fade" id="feedbacks-pane" role="tabpanel">
                            @if(empty($recentFeedbacks) || $recentFeedbacks->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    Belum ada ulasan yang diberikan oleh Wali Santri.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover datatable" data-page-length="5">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Wali Santri</th>
                                                <th>Santri Binaan</th>
                                                <th>Guru Pembimbing</th>
                                                <th>Rating Bintang</th>
                                                <th>Kutipan Catatan</th>
                                                <th>Waktu Respon</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentFeedbacks as $fb)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ $fb->is_anonymous ? 'Wali Santri (Anonim)' : ($fb->parent?->user?->name ?? 'Wali Santri') }}</td>
                                                    <td class="text-primary fw-semibold">{{ $fb->student?->user?->name ?? $fb->student?->full_name }}</td>
                                                    <td class="fw-semibold">
                                                        @if($fb->mentor_id)
                                                            <a href="{{ route('admin.staff.show', $fb->mentor_id) }}" class="text-decoration-none text-dark fw-semibold hover-primary" title="Lihat Profil Lengkap & Rekening Guru">
                                                                {{ $fb->mentor?->getDisplayName() ?? 'Ustaz' }} <i class="bi bi-box-arrow-up-right text-muted ms-1" style="font-size: 0.68rem;"></i>
                                                            </a>
                                                        @else
                                                            {{ $fb->mentor?->getDisplayName() ?? 'Ustaz' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="text-warning fw-bold">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="bi bi-star{{ $i <= $fb->overall_rating ? '-fill' : '' }}"></i>
                                                            @endfor
                                                            {{ number_format($fb->overall_rating, 1) }}
                                                        </span>
                                                    </td>
                                                    <td class="small text-secondary fst-italic">
                                                        {{ $fb->comment ? '"' . \Illuminate\Support\Str::limit($fb->comment, 50) . '"' : '-' }}
                                                    </td>
                                                    <td class="small text-muted">{{ $fb->created_at->diffForHumans() }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Tab 5: Tiket Intervensi Komplain Wali Santri -->
                        <div class="tab-pane fade" id="tickets-pane" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small text-muted fw-bold">Daftar Tiket Keluhan Wali Santri yang Membutuhkan Tindakan Koordinator</span>
                                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="bi bi-ticket-detailed me-1"></i> Buka Manajemen Tiket ({{ $openTicketsCount ?? 0 }} Terbuka)
                                </a>
                            </div>

                            @if(empty($recentTickets) || $recentTickets->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-check-circle fs-2 d-block text-success mb-2"></i>
                                    Alhamdulillah, tidak ada tiket komplain aktif. Hubungan guru dan wali santri berjalan harmonis.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover datatable" data-page-length="5">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No. Tiket</th>
                                                <th>Wali & Santri</th>
                                                <th>Guru Pembimbing</th>
                                                <th>Kategori & Rating</th>
                                                <th>Ringkasan Masalah</th>
                                                <th>Status</th>
                                                <th class="text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentTickets as $t)
                                                <tr>
                                                    <td class="fw-bold text-primary">#{{ $t->ticket_number }}</td>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ $t->student?->getDisplayName() ?? 'Santri' }}</div>
                                                        <small class="text-muted">{{ $t->parent?->name ?? 'Wali Santri' }}</small>
                                                    </td>
                                                    <td class="fw-semibold text-dark">{{ $t->mentor?->getDisplayName() ?? 'Mentor' }}</td>
                                                    <td>
                                                        <span class="badge {{ $t->getSeverityBadgeClass() }} rounded-pill px-2">
                                                            {{ $t->getCategoryLabel() }}
                                                        </span>
                                                        <small class="text-warning fw-bold d-block mt-1">⭐ {{ $t->feedback?->overall_rating ?? '-' }}/5</small>
                                                    </td>
                                                    <td class="small text-secondary fst-italic" style="max-width: 240px;">
                                                        "{{ $t->parent_comment ? \Illuminate\Support\Str::limit($t->parent_comment, 50) : 'Peringatan rating rendah' }}"
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $t->getStatusBadgeClass() }} rounded-pill px-2">
                                                            {{ strtoupper(str_replace('_', ' ', $t->status)) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                            Tangani
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Widget Rekrutmen Guru & Onboarding (v8.3) -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: transparent;">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                            <i class="bi bi-mortarboard-fill text-primary me-2"></i>Pusat Rekrutmen Guru & Evaluasi AI (v8.3)
                        </h5>
                        <small class="text-muted">Pantau data pelamar baru, tes kompetensi AI, serta masa percobaan & orientasi calon guru</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.recruitment.tests.index') }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                            <i class="bi bi-robot me-1"></i> Tes AI
                        </a>
                        <a href="{{ route('admin.mentors.probation.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                            <i class="bi bi-person-workspace me-1"></i> Masa Probation ({{ $activeProbationsCount ?? 0 }})
                            @if(($expiringProbationsCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $expiringProbationsCount }} Perlu Evaluasi</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.recruitment.applications.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                            Kelola Semua Pelamar ({{ $totalApplicationsCount ?? 0 }}) <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Nav Tabs: Pelamar Baru vs Guru Probation & Orientasi -->
                <div class="px-4 pt-3">
                    <ul class="nav nav-tabs border-bottom gap-1" id="recruitmentDashboardTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-top-3 px-3 py-2 fw-semibold" id="tab-applications-tab" data-bs-toggle="tab" data-bs-target="#tab-applications" type="button" role="tab" aria-controls="tab-applications" aria-selected="true">
                                <i class="bi bi-person-lines-fill me-1"></i> Pelamar Calon Guru Baru ({{ $totalApplicationsCount ?? 0 }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-top-3 px-3 py-2 fw-semibold" id="tab-probations-tab" data-bs-toggle="tab" data-bs-target="#tab-probations" type="button" role="tab" aria-controls="tab-probations" aria-selected="false">
                                <i class="bi bi-person-workspace text-success me-1"></i> Guru Masa Percobaan & 4 Modul Orientasi ({{ $activeProbationsCount ?? 0 }})
                                @if(($expiringProbationsCount ?? 0) > 0)
                                    <span class="badge bg-danger rounded-pill ms-1" style="font-size: 0.65rem;">{{ $expiringProbationsCount }} H-14</span>
                                @endif
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="recruitmentDashboardTabsContent">
                        <!-- Tab 1: Pelamar Baru -->
                        <div class="tab-pane fade show active" id="tab-applications" role="tabpanel" aria-labelledby="tab-applications-tab">
                            @if(($recentApplications ?? collect())->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-inbox fs-2 d-block text-secondary mb-2"></i>
                                    Belum ada pendaftaran calon guru baru. Link formulir pendaftaran: 
                                    <a href="{{ route('bergabung') }}" target="_blank" class="fw-bold text-primary">{{ route('bergabung') }}</a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover mb-0 datatable" data-page-length="5">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kode Registrasi</th>
                                                <th>Nama Pelamar</th>
                                                <th>Spesialisasi & Hafalan</th>
                                                <th>No. WhatsApp</th>
                                                <th>Status Seleksi</th>
                                                <th class="text-end no-sort">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentApplications as $app)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $app->application_code }}</td>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ $app->full_name }}</div>
                                                        <small class="text-muted">{{ $app->education }} - {{ $app->institution }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">{{ $app->specialization }}</span>
                                                        <small class="d-block text-muted">{{ $app->hifz_total_juz }} Juz | {{ $app->experience_years }} thn pengalaman</small>
                                                    </td>
                                                    <td>
                                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $app->phone)) }}" target="_blank" class="text-success text-decoration-none fw-semibold">
                                                            <i class="bi bi-whatsapp me-1"></i>{{ $app->phone }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $stColors = [
                                                                'submitted' => 'bg-secondary',
                                                                'document_review' => 'bg-info text-dark',
                                                                'test_scheduled' => 'bg-warning text-dark',
                                                                'test_completed' => 'bg-primary',
                                                                'interview_scheduled' => 'bg-primary',
                                                                'approved' => 'bg-success',
                                                                'rejected' => 'bg-danger',
                                                            ];
                                                        @endphp
                                                        <span class="badge {{ $stColors[$app->status] ?? 'bg-secondary' }} px-3 py-1 rounded-pill">
                                                            {{ strtoupper(str_replace('_', ' ', $app->status)) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                                            <i class="bi bi-eye me-1"></i> Review
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <!-- Tab 2: Guru Masa Percobaan & 4 Modul Orientasi -->
                        <div class="tab-pane fade" id="tab-probations" role="tabpanel" aria-labelledby="tab-probations-tab">
                            @if(($activeProbationList ?? collect())->isEmpty())
                                <div class="text-center py-4 text-muted small">
                                    <i class="bi bi-person-check fs-2 d-block text-success mb-2"></i>
                                    Tidak ada guru dalam masa percobaan (probation) aktif saat ini. Seluruh guru telah diangkat menjadi Guru Tetap.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover mb-0 datatable" data-page-length="5">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Guru Pembimbing</th>
                                                <th>Sisa Waktu Evaluasi</th>
                                                <th>Status 4 Modul Orientasi</th>
                                                <th>Presensi Mengajar</th>
                                                <th>Rating Wali</th>
                                                <th class="text-end no-sort">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeProbationList as $prob)
                                                @php
                                                    $mentor = $prob->mentor;
                                                    $endDate = \Carbon\Carbon::parse($prob->end_date);
                                                    $daysLeft = (int) now()->diffInDays($endDate, false);
                                                    $isExpiring = $daysLeft > 0 && $daysLeft <= 14;
                                                    $completedMods = $prob->getCompletedModulesCount();
                                                    $percentMods = min(100, (int) round(($completedMods / 4) * 100));
                                                    $mod1 = $prob->isModuleCompleted('mod1');
                                                    $mod2 = $prob->isModuleCompleted('mod2');
                                                    $mod3 = $prob->isModuleCompleted('mod3');
                                                    $mod4 = $prob->isModuleCompleted('mod4');
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 0.82rem;">
                                                                {{ substr($mentor?->user?->name ?? 'G', 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark">
                                                                    @if($mentor)
                                                                        <a href="{{ route('admin.staff.show', $mentor->id) }}" class="text-dark text-decoration-none hover-primary" title="Lihat Profil Lengkap, CV, Sanad & Rekening Bank Guru">
                                                                            {{ $mentor->getDisplayName() }} <i class="bi bi-box-arrow-up-right text-muted ms-1" style="font-size: 0.68rem;"></i>
                                                                        </a>
                                                                    @else
                                                                        Guru Pembimbing
                                                                    @endif
                                                                </div>
                                                                <small class="text-muted">{{ $mentor?->specialization ?? 'Tahfidz' }} &bull; {{ $mentor?->user?->email ?? '-' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold {{ $isExpiring ? 'text-danger' : 'text-primary' }}">
                                                            <i class="bi bi-hourglass-split me-1"></i>{{ max(0, $daysLeft) }} Hari Tersisa
                                                        </div>
                                                        <small class="text-muted d-block">s.d. {{ $endDate->format('d M Y') }}</small>
                                                        @if($isExpiring)
                                                            <span class="badge bg-danger rounded-pill mt-0.5" style="font-size: 0.65rem;">H-14 Perlu Evaluasi</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            @if($completedMods === 4)
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                                                    <i class="bi bi-patch-check-fill me-1"></i> 4/4 Tuntas
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.75rem;">
                                                                    <i class="bi bi-hourglass-split me-1"></i> {{ $completedMods }}/4 Selesai
                                                                </span>
                                                            @endif
                                                            <small class="text-muted fw-semibold">({{ $percentMods }}%)</small>
                                                        </div>
                                                        <div class="progress rounded-pill mb-1" style="height: 5px; width: 110px;">
                                                            <div class="progress-bar {{ $completedMods === 4 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $percentMods }}%;"></div>
                                                        </div>
                                                        <div class="d-flex gap-1" style="font-size: 0.62rem;">
                                                            <span class="badge {{ $mod1 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 1: SOP Pengajaran">M1</span>
                                                            <span class="badge {{ $mod2 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 2: Tajwid & Mutaba'ah">M2</span>
                                                            <span class="badge {{ $mod3 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 3: Sesi Perdana LMS">M3</span>
                                                            <span class="badge {{ $mod4 ? 'bg-success' : 'bg-secondary bg-opacity-25 text-muted' }}" title="Modul 4: Komunikasi Wali">M4</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold {{ ($prob->attendance_rate ?? 100) >= 90 ? 'text-success' : 'text-danger' }}">
                                                            {{ number_format($prob->attendance_rate ?? 100.0, 1) }}%
                                                        </div>
                                                        <small class="text-muted">Target &ge; 90%</small>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold {{ ($prob->average_rating ?? 5.0) >= 4.5 ? 'text-warning' : 'text-danger' }}">
                                                            ⭐ {{ number_format($prob->average_rating ?? 5.0, 2) }}
                                                        </div>
                                                        <small class="text-muted">Target &ge; 4.50</small>
                                                    </td>
                                                    <td class="text-end text-nowrap">
                                                        <a href="{{ route('admin.mentors.probation.show', $prob->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                                                            <i class="bi bi-pencil-square me-1"></i> Tinjau Evaluasi
                                                        </a>
                                                        @if($mentor)
                                                            <a href="{{ route('admin.staff.show', $mentor->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 ms-1" title="Detail Akun & Rekening Guru">
                                                                <i class="bi bi-person-badge"></i> Profil
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Activity & Quick Actions -->
    <div class="row g-4">
        <!-- Aktivitas Terbaru (Livewire) -->
        <div class="col-12 col-lg-8">
            @livewire('progress-tracker')
        </div>

        <!-- Quick Actions & Info -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: var(--text-primary);">Aksi Cepat Admin</h5>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-danger text-white text-start py-2.5 px-3 mb-1 rounded-pill shadow-sm">
                            <i class="bi bi-ticket-detailed-fill me-2 fs-5"></i> Tiket Intervensi Komplain
                            @if(($openTicketsCount ?? 0) > 0)
                                <span class="badge bg-white text-danger rounded-pill float-end mt-1 fw-bold">{{ $openTicketsCount }} Terbuka</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.recruitment.applications.index') }}" class="btn btn-primary text-white text-start py-2.5 px-3 mb-1 rounded-pill shadow-sm">
                            <i class="bi bi-mortarboard-fill me-2 fs-5"></i> Rekrutmen Guru & Tes AI
                            @if(($pendingApplicationsCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill float-end mt-1">{{ $pendingApplicationsCount }} Baru</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-primary text-start py-2.5 px-3 mb-1 rounded-pill">
                            <i class="bi bi-person-badge-fill me-2 fs-5 text-primary"></i> Database Guru & Detail Akun
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-daftar text-white text-start py-2.5 px-3 mb-1 rounded-pill">
                            <i class="bi bi-person-plus-fill me-2 fs-5"></i> Kelola & Tambah Santri
                        </a>
                        <a href="{{ route('admin.broadcast.index') }}" class="btn btn-outline-success text-start py-2.5 px-3 rounded-pill mb-1">
                            <i class="bi bi-whatsapp me-2 fs-5"></i> Broadcast Pengumuman WA
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary text-start py-2.5 px-3 rounded-pill mb-1">
                            <i class="bi bi-file-earmark-spreadsheet me-2 fs-5"></i> Rekapitulasi Laporan (Excel/PDF)
                        </a>
                        <a href="{{ route('report.download') }}" target="_blank" class="btn btn-outline-secondary text-start py-2.5 px-3 rounded-pill mb-1">
                            <i class="bi bi-file-earmark-pdf me-2 fs-5"></i> Cetak Laporan Bulanan (PDF)
                        </a>
                        <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-warning text-start py-2.5 px-3 rounded-pill">
                            <i class="bi bi-bell-fill me-2 fs-5"></i> Pusat Peringatan Operasional
                        </a>
                    </div>

                    <hr class="my-4" style="border-color: var(--border-color);">

                    <div class="p-3 rounded-3" style="background: var(--primary-lighter); border: 1px solid var(--border-color);">
                        <div class="d-flex items-center gap-2 mb-1">
                            <i class="bi bi-shield-check text-success"></i>
                            <span class="fw-bold text-success small">AL-HIKMAH LMS v8.2</span>
                        </div>
                        <p class="small text-secondary mb-0">
                            Versi Enterprise Analytics & Operational Intelligence aktif dengan ApexCharts terintegrasi dan query caching otomatis.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function () {
            if (window.$ && $.fn.dataTable) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
            }
        });
    });
</script>
@endpush