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
                <i class="bi bi-speedometer2 text-primary me-2"></i>Executive Control Center (v8.2)
            </h3>
            <p class="text-muted small mb-0">Pantau arus kas, performa pengajar, peringatan operasional, dan mutaba'ah santri secara real-time.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('admin.revenue.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                <i class="bi bi-graph-up-arrow me-1"></i>Analitik Finansial
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
                    <h6 class="fw-bold mb-0 text-danger">Perhatian: Ditemukan {{ $allAlerts['critical_count'] }} Isu Operasional Kritis!</h6>
                    <span class="small text-muted">Termasuk tagihan overdue &gt;30 hari, santri tidak aktif, atau kapasitas guru overload.</span>
                </div>
            </div>
            <a href="{{ route('admin.alerts.index') }}" class="btn btn-sm btn-danger rounded-pill px-3">
                Buka Alerts Center <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    @endif

    <!-- 1. Executive KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Pendapatan Bulan Ini & MoM -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%); border: 1px solid rgba(16, 185, 129, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pendapatan Bulan Ini</span>
                    <div class="badge rounded-pill bg-success bg-opacity-10 text-success p-2">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($revenueMetrics['this_month_revenue'] ?? 0, 0, ',', '.') }}</h3>
                <div class="d-flex align-items-center gap-1 small">
                    @if (($revenueMetrics['mom_growth_percent'] ?? 0) >= 0)
                        <span class="badge bg-success text-white px-2 py-0.5 rounded-pill">
                            <i class="bi bi-arrow-up-right"></i> +{{ $revenueMetrics['mom_growth_percent'] }}% MoM
                        </span>
                    @else
                        <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill">
                            <i class="bi bi-arrow-down-right"></i> {{ $revenueMetrics['mom_growth_percent'] }}% MoM
                        </span>
                    @endif
                    <span class="text-muted">ARPU: Rp {{ number_format($revenueMetrics['arpu'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Total Santri Binaan -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(30, 144, 255, 0.08) 0%, rgba(30, 144, 255, 0.02) 100%); border: 1px solid rgba(30, 144, 255, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Santri Terdaftar</span>
                    <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-primary">{{ number_format($totalStudents) }} <span class="fs-6 fw-normal text-muted">Santri</span></h3>
                <span class="small text-muted">{{ $staffSummary['mentor_student_ratio'] ?? '1 : 0' }} rasio guru:santri</span>
            </div>
        </div>

        <!-- Guru & SDM -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(139, 92, 246, 0.02) 100%); border: 1px solid rgba(139, 92, 246, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Guru Pembimbing</span>
                    <div class="badge rounded-pill bg-purple bg-opacity-10 text-purple p-2" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <i class="bi bi-person-badge-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #8b5cf6;">{{ $staffSummary['active_mentors'] ?? $totalMentors }} <span class="fs-6 fw-normal text-muted">Aktif</span></h3>
                <span class="small text-muted">{{ $staffSummary['mentors_on_leave_today'] ?? 0 }} guru cuti hari ini</span>
            </div>
        </div>

        <!-- Tagihan Overdue & Piutang -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(239, 68, 68, 0.02) 100%); border: 1px solid rgba(239, 68, 68, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Tagihan Overdue</span>
                    <div class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format($revenueMetrics['overdue_invoices_amount'] ?? 0, 0, ',', '.') }}</h3>
                <span class="small text-danger">{{ $revenueMetrics['overdue_invoices_count'] ?? 0 }} tagihan perlu pengingat</span>
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
                                        <td class="text-end">
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