@extends('layouts.admin')

@section('title', 'Pusat Peringatan Operasional (Alerts Center)')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-bell-fill text-warning me-2"></i>Pusat Peringatan Operasional (Operational Alerts Center)
            </h3>
            <p class="text-muted small mb-0">Deteksi dini kendala operasional santri, mentor, piutang, dan sistem dengan prioritas penanganan.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Alert Counters Strip -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-danger" style="background: var(--card-bg);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">🔴 Kritis (Segera Tangani)</div>
                        <h3 class="fw-bold text-danger mb-0">{{ $allAlerts['critical_count'] }} <span class="fs-6 fw-normal text-muted">isu</span></h3>
                    </div>
                    <div class="badge rounded-circle p-3 bg-danger bg-opacity-10 text-danger fs-4">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-warning" style="background: var(--card-bg);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">🟡 Perhatian (Monitoring)</div>
                        <h3 class="fw-bold text-warning mb-0">{{ $allAlerts['warning_count'] }} <span class="fs-6 fw-normal text-muted">isu</span></h3>
                    </div>
                    <div class="badge rounded-circle p-3 bg-warning bg-opacity-10 text-warning fs-4">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-success" style="background: var(--card-bg);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">🟢 Info (Pemberitahuan)</div>
                        <h3 class="fw-bold text-success mb-0">{{ $allAlerts['info_count'] }} <span class="fs-6 fw-normal text-muted">aktivitas</span></h3>
                    </div>
                    <div class="badge rounded-circle p-3 bg-success bg-opacity-10 text-success fs-4">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <ul class="nav nav-pills mb-4 gap-2" id="alertTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill active px-3" id="tab-all-btn" data-bs-toggle="pill" data-bs-target="#tab-all" type="button" role="tab">
                Semua Alert ({{ $allAlerts['total_count'] }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill text-danger px-3" id="tab-critical-btn" data-bs-toggle="pill" data-bs-target="#tab-critical" type="button" role="tab">
                🔴 Kritis ({{ $allAlerts['critical_count'] }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill text-warning px-3" id="tab-warning-btn" data-bs-toggle="pill" data-bs-target="#tab-warning" type="button" role="tab">
                🟡 Perhatian ({{ $allAlerts['warning_count'] }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill text-success px-3" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#tab-info" type="button" role="tab">
                🟢 Info ({{ $allAlerts['info_count'] }})
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="alertTabsContent">
        <!-- 1. TAB SEMUA -->
        <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
            <div class="d-flex flex-column gap-3">
                @if ($allAlerts['total_count'] === 0)
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center" style="background: var(--card-bg);">
                        <i class="bi bi-shield-check text-success display-3 mb-3"></i>
                        <h4 class="fw-bold text-success">Kondisi Operasional Prima!</h4>
                        <p class="text-muted mb-0">Tidak ada isu kritis atau perhatian yang memerlukan tindakan mendesak.</p>
                    </div>
                @else
                    <!-- Render Critical Items -->
                    @foreach ($allAlerts['critical'] as $alert)
                        @include('admin.alerts.partials.alert-card', ['alert' => $alert, 'badgeClass' => 'bg-danger text-white', 'borderClass' => 'border-danger'])
                    @endforeach

                    <!-- Render Warning Items -->
                    @foreach ($allAlerts['warning'] as $alert)
                        @include('admin.alerts.partials.alert-card', ['alert' => $alert, 'badgeClass' => 'bg-warning text-dark', 'borderClass' => 'border-warning'])
                    @endforeach

                    <!-- Render Info Items -->
                    @foreach ($allAlerts['info'] as $alert)
                        @include('admin.alerts.partials.alert-card', ['alert' => $alert, 'badgeClass' => 'bg-success text-white', 'borderClass' => 'border-success'])
                    @endforeach
                @endif
            </div>
        </div>

        <!-- 2. TAB KRITIS -->
        <div class="tab-pane fade" id="tab-critical" role="tabpanel">
            <div class="d-flex flex-column gap-3">
                @forelse ($allAlerts['critical'] as $alert)
                    @include('admin.alerts.partials.alert-card', ['alert' => $alert, 'badgeClass' => 'bg-danger text-white', 'borderClass' => 'border-danger'])
                @empty
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center" style="background: var(--card-bg);">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                        <h5 class="fw-bold mb-1">Nol Isu Kritis</h5>
                        <p class="text-muted mb-0">Tidak ada kendala kritis pada sistem saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. TAB PERHATIAN -->
        <div class="tab-pane fade" id="tab-warning" role="tabpanel">
            <div class="d-flex flex-column gap-3">
                @forelse ($allAlerts['warning'] as $alert)
                    @include('admin.alerts.partials.alert-card', ['alert' => $alert, 'badgeClass' => 'bg-warning text-dark', 'borderClass' => 'border-warning'])
                @empty
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center" style="background: var(--card-bg);">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                        <h5 class="fw-bold mb-1">Semua Terkendali</h5>
                        <p class="text-muted mb-0">Tidak ada hal khusus yang memerlukan monitoring berkala.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 4. TAB INFO -->
        <div class="tab-pane fade" id="tab-info" role="tabpanel">
            <div class="d-flex flex-column gap-3">
                @forelse ($allAlerts['info'] as $alert)
                    @include('admin.alerts.partials.alert-card', ['alert' => $alert, 'badgeClass' => 'bg-success text-white', 'borderClass' => 'border-success'])
                @empty
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center" style="background: var(--card-bg);">
                        <i class="bi bi-info-circle text-muted fs-1 mb-2"></i>
                        <h5 class="fw-bold mb-1">Belum Ada Aktivitas Baru</h5>
                        <p class="text-muted mb-0">Tidak ada notifikasi rutin dalam 7 hari terakhir.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
