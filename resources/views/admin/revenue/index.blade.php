@extends('layouts.admin')

@section('title', 'Dasbor Pendapatan & Analitik Keuangan')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-cash-stack text-success me-2"></i>Dasbor Pendapatan & Keuangan
            </h3>
            <p class="text-muted small mb-0">Analitik arus kas real-time, tren bulanan, performa program, dan jejak audit transaksi.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Filter Tanggal Form -->
            <form method="GET" action="{{ route('admin.revenue.index') }}" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar3"></i></span>
                    <input type="date" name="start_date" class="form-control form-control-sm border-start-0" value="{{ request('start_date', $startDate?->format('Y-m-d')) }}" title="Tanggal Mulai">
                    <span class="input-group-text bg-light border-start-0 border-end-0">-</span>
                    <input type="date" name="end_date" class="form-control form-control-sm border-start-0" value="{{ request('end_date', $endDate?->format('Y-m-d')) }}" title="Tanggal Selesai">
                </div>
                <button type="submit" class="btn btn-sm btn-primary px-3 rounded-pill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                @if (request()->hasAny(['start_date', 'end_date']))
                    <a href="{{ route('admin.revenue.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill" title="Reset Filter">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </form>

            <!-- Action Buttons -->
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="bi bi-file-earmark-arrow-down me-1"></i>Ekspor Laporan
            </a>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-success rounded-pill px-3">
                <i class="bi bi-plus-circle me-1"></i>Kelola Tagihan
            </a>
        </div>
    </div>

    <!-- 1. Stats Summary Cards -->
    @include('admin.revenue.partials.stats-cards')

    @if (isset($revenueSharing))
    <!-- 1.1 Transparansi Alokasi Finansial Al-Hikmah -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="card-header border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: transparent;">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                            <i class="bi bi-pie-chart-fill text-success me-2"></i>Transparansi Alokasi Finansial Al-Hikmah
                        </h5>
                        <small class="text-muted">Perhitungan amanah bagi hasil bimbingan: Rp 150.000 / sesi &amp; Biaya Pendaftaran santri baru.</small>
                    </div>
                    <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background: rgba(13, 122, 62, 0.12); color: var(--primary);">
                        <i class="bi bi-check2-circle me-1"></i> Standar Rp 150.000 / Sesi 90 Menit
                    </span>
                </div>
                <div class="card-body p-4 pt-3">

                    <!-- Sesi Bimbingan (Per Pertemuan Rp 150.000) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background: rgba(14, 165, 233, 0.12); color: #0284c7; font-size: 0.8rem;">
                                    <i class="bi bi-book-fill me-1"></i> Sesi Bimbingan — Per Pertemuan Rp 150.000
                                </span>
                                <small class="text-muted">Total sesi selesai: <strong>{{ number_format($revenueSharing['completed_sessions_count']) }}</strong> sesi</small>
                            </div>
                            <small class="text-muted fst-italic">* Potongan infaq 10% (Rp 5.000) otomatis diambil dari hak Owner (Rp 50.000). Hak Guru Rp 100.000 utuh.</small>
                        </div>

                        <div class="row g-3">
                            <!-- Sesi Selesai -->
                            <div class="col-xl-3 col-sm-6">
                                <div class="p-3 rounded-3 h-100 border" style="background: var(--bg-surface); border-color: var(--border-color) !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="small text-muted fw-semibold">Sesi Selesai</span>
                                        <div class="rounded-circle p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>
                                    </div>
                                    <div class="fs-4 fw-bold text-primary">{{ number_format($revenueSharing['completed_sessions_count']) }} <span class="fs-6 fw-normal text-muted">Sesi</span></div>
                                    <div class="small text-muted mt-1">Bulan ini: <strong class="text-primary">{{ number_format($revenueSharing['this_month_sessions_count']) }}</strong> sesi terlaksana</div>
                                </div>
                            </div>

                            <!-- Hak Guru (Rp 100rb) -->
                            <div class="col-xl-3 col-sm-6">
                                <div class="p-3 rounded-3 h-100 border" style="background: var(--bg-surface); border-color: var(--border-color) !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="small text-muted fw-semibold">Hak Mentor / Guru (Rp 100rb)</span>
                                        <div class="rounded-circle p-2 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-check-fill"></i>
                                        </div>
                                    </div>
                                    <div class="fs-4 fw-bold text-info">Rp {{ number_format($revenueSharing['total_mentor_honor'], 0, ',', '.') }}</div>
                                    <div class="small text-muted mt-1">Bulan ini: <strong>Rp {{ number_format($revenueSharing['this_month_mentor_honor'], 0, ',', '.') }}</strong> <span class="badge bg-info-subtle text-info ms-1">Utuh</span></div>
                                </div>
                            </div>

                            <!-- Untuk Allah SWT (10% = Rp 5rb) -->
                            <div class="col-xl-3 col-sm-6">
                                <div class="p-3 rounded-3 h-100 border" style="background: var(--bg-surface); border-color: var(--border-color) !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="small text-muted fw-semibold">Untuk Allah SWT (10% = Rp 5rb)</span>
                                        <div class="rounded-circle p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-heart-fill"></i>
                                        </div>
                                    </div>
                                    <div class="fs-4 fw-bold text-warning">Rp {{ number_format($revenueSharing['total_infaq_dakwah'], 0, ',', '.') }}</div>
                                    <div class="small text-muted mt-1">Bulan ini: <strong>Rp {{ number_format($revenueSharing['this_month_infaq_dakwah'], 0, ',', '.') }}</strong> <span class="badge bg-warning-subtle text-warning ms-1">Dari Owner</span></div>
                                </div>
                            </div>

                            <!-- Bersih Owner (Rp 45rb/sesi) -->
                            <div class="col-xl-3 col-sm-6">
                                <div class="p-3 rounded-3 h-100 border" style="background: var(--bg-surface); border-color: var(--border-color) !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="small text-muted fw-semibold">Bersih Owner / Admin (Rp 45rb/sesi)</span>
                                        <div class="rounded-circle p-2 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi bi-bank2"></i>
                                        </div>
                                    </div>
                                    <div class="fs-4 fw-bold text-success">Rp {{ number_format($revenueSharing['total_owner_net'], 0, ',', '.') }}</div>
                                    <div class="small text-muted mt-1">Bulan ini: <strong>Rp {{ number_format($revenueSharing['this_month_owner_net'], 0, ',', '.') }}</strong> <span class="badge bg-success-subtle text-success ms-1">Bersih</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biaya Pendaftaran -->
                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-3"
                         style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.03) 100%); border-color: rgba(16, 185, 129, 0.25) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0"
                                 style="width: 44px; height: 44px; background: linear-gradient(135deg, #059669, #047857); font-size: 1.2rem;">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-6 mb-0" style="color: var(--text-primary);">Biaya Pendaftaran</span>
                                    <span class="badge bg-success-subtle text-success small">Rp 150.000 / Santri Baru</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    Total <strong>{{ number_format($revenueSharing['total_registrations']) }}</strong> santri terdaftar &bull; Bulan ini: <strong>{{ number_format($revenueSharing['this_month_registrations']) }}</strong> pendaftar baru
                                </div>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="fs-4 fw-bold text-success">Rp {{ number_format($revenueSharing['total_registration_amount'], 0, ',', '.') }}</div>
                            <div class="small text-muted">Bulan ini: <strong>Rp {{ number_format($revenueSharing['this_month_registration_amount'], 0, ',', '.') }}</strong></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 2. Interactive Charts (ApexCharts) -->
    @include('admin.revenue.partials.chart')

    <!-- 3. Program Breakdown Table & Financial Audit Trail -->
    <div class="row g-4 mb-4">
        <!-- Rincian Pendapatan Per Program -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-table text-primary me-2"></i>Rincian Pendapatan Program
                    </h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ count($programBreakdown['details'] ?? []) }} Program</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th>Nama Program</th>
                                <th class="text-center">Santri Aktif</th>
                                <th class="text-end">Total Nominal</th>
                                <th class="text-end">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($programBreakdown['details'] ?? [] as $detail)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-truncate" style="max-width: 220px;">{{ $detail['name'] }}</div>
                                        <span class="badge bg-light text-secondary small border">{{ $detail['category'] }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($detail['active_students']) }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($detail['revenue'], 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-success bg-opacity-10 text-success">{{ $detail['percentage'] }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data pendapatan program.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Jejak Audit Keuangan (Financial Audit Trail) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-shield-check text-success me-2"></i>Audit Log Finansial
                    </h5>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Append-Only</span>
                </div>

                <div class="d-flex flex-column gap-3" style="max-height: 380px; overflow-y: auto;">
                    @forelse ($auditLogs as $log)
                        <div class="d-flex gap-3 align-items-start p-2 rounded-3 border" style="border-color: var(--border-color) !important; background: var(--bg-secondary);">
                            <div class="badge rounded-circle p-2 bg-primary bg-opacity-10 text-primary mt-1">
                                <i class="bi bi-activity"></i>
                            </div>
                            <div class="flex-grow-1 small">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at?->diffForHumans() }}</span>
                                </div>
                                <div class="text-muted">
                                    Oleh: <span class="fw-medium text-dark">{{ $log->user?->name ?? 'Sistem' }}</span> | Entitas: <span class="badge bg-light text-secondary border">{{ $log->entity_type }} #{{ $log->entity_id }}</span>
                                </div>
                                @if ($log->new_values)
                                    <div class="mt-1 text-secondary" style="font-size: 0.75rem; font-family: monospace;">
                                        IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-5">
                            <i class="bi bi-clipboard-check fs-2 text-muted d-block mb-2"></i>
                            Belum ada riwayat perubahan data finansial.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
