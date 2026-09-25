@extends('layouts.admin')

@section('title', 'Reality Check Dashboard')
@section('header', 'Reality Check Dashboard')
@section('subheader', 'Transparansi Metrik Lapangan Riil & Evaluasi Utilisasi Fitur Yayasan')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-clipboard2-pulse-fill text-success me-2"></i>Reality Check & Observabilitas Lapangan
            </h3>
            <p class="text-muted small mb-0">
                Penyajian data faktual tanpa <em>vanity metrics</em>: keaktifan berbasis sesi riil selesai dan omzet berbasis pembayaran lunas.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('admin.reality-check.export') }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>Ekspor CSV Rekapitulasi
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- 4 Core Executive Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Santri Aktif Riil -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: var(--card-bg);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Santri Aktif Riil</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(13, 122, 62, 0.12); color: var(--primary);">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="h2 fw-bold mb-1" style="color: var(--text-primary);">{{ $activeStudentsCount }}</div>
                <div class="small text-muted border-top pt-2 mt-1">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>&ge; 1 sesi bimbingan selesai (30 hari terakhir)
                </div>
            </div>
        </div>

        <!-- 2. Guru Aktif Riil -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: var(--card-bg);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Guru Aktif Riil</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(14, 165, 233, 0.12); color: #0284c7;">
                        <i class="bi bi-person-video3 fs-5"></i>
                    </div>
                </div>
                <div class="h2 fw-bold mb-1" style="color: var(--text-primary);">{{ $activeMentorsCount }}</div>
                <div class="small text-muted border-top pt-2 mt-1">
                    <i class="bi bi-check-circle-fill text-info me-1"></i>Memandu &ge; 1 sesi selesai (30 hari terakhir)
                </div>
            </div>
        </div>

        <!-- 3. Sesi Selesai / Hari -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: var(--card-bg);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Sesi Selesai / Hari</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-calendar2-check-fill fs-5"></i>
                    </div>
                </div>
                <div class="h2 fw-bold mb-1" style="color: var(--text-primary);">{{ $avgSessionsPerDay }}</div>
                <div class="small text-muted border-top pt-2 mt-1">
                    <i class="bi bi-clock-history me-1"></i>Total {{ $completedSessions30d }} sesi dalam 30 hari
                </div>
            </div>
        </div>

        <!-- 4. Gross Revenue Riil Bulan Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: var(--card-bg);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Gross Revenue Riil</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </div>
                </div>
                <div class="h3 fw-bold mb-1 text-success">Rp {{ number_format($grossRevenueMonth, 0, ',', '.') }}</div>
                <div class="small text-muted border-top pt-2 mt-1">
                    <i class="bi bi-shield-check text-success me-1"></i>Hanya dari transaksi status <strong>paid</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 10 Feature Utilization Table -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--card-bg);">
        <div class="card-header bg-transparent border-0 pt-3 pb-2 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                    <i class="bi bi-pie-chart-fill text-primary me-2"></i>Utilisasi 10 Fitur Utama (Fitur Aktif vs Menganggur)
                </h5>
                <small class="text-muted">Pelacakan hibrida: Agregasi transaksi database (Opsi C) dan hit counter middleware (Opsi B).</small>
            </div>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-2">
                <i class="bi bi-hourglass-split me-1"></i>Siklus Evaluasi 30 Hari
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-body-secondary text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Nama Fitur</th>
                            <th class="py-3">Akses / Penggunaan (30 Hari)</th>
                            <th class="py-3">Terakhir Digunakan</th>
                            <th class="py-3 pe-4 text-end">Status Adopsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($featureUsage as $feature)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark">{{ $feature['name'] }}</div>
                                <code class="small text-muted">{{ $feature['key'] }}</code>
                            </td>
                            <td class="py-3">
                                <span class="fw-semibold">{{ number_format($feature['count_30d']) }}</span> kali
                            </td>
                            <td class="py-3 text-muted small">
                                <i class="bi bi-clock me-1"></i>{{ $feature['last_used'] }}
                            </td>
                            <td class="py-3 pe-4 text-end">
                                @if($feature['status'] === 'aktif')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Sering Digunakan (&gt;20x)
                                    </span>
                                @elseif($feature['status'] === 'jarang')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>Jarang Digunakan (1-20x)
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                        <i class="bi bi-x-circle-fill me-1"></i>Menganggur (Evaluasi Pemangkasan)
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
