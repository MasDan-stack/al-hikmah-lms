@extends('layouts.admin')

@section('title', 'Dashboard Performa Mentor & AI Coaching')

@section('content')
<div class="container-fluid py-2">
    <!-- Header Page & Controls -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary-emphasis d-flex align-items-center gap-2">
                <i class="bi bi-award-fill text-warning"></i> Dashboard Performa Mentor & AI Coaching
            </h4>
            <p class="text-muted small mb-0">
                Pemantauan analitik kinerja pengajar objektif, Bayesian rating smoothing, alokasi insentif, dan rekomendasi preskriptif AI.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('admin.performance.mentors.index') }}" class="d-flex align-items-center gap-2">
                <input type="month" name="month" value="{{ $selectedMonth }}" class="form-control form-control-sm rounded-pill px-3" onchange="this.form.submit()">
            </form>
            <a href="{{ route('admin.performance.mentors.export-excel', ['month' => $selectedMonth]) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Ekspor Excel
            </a>
        </div>
    </div>

    <!-- Alert / Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 4 KPI Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Guru Aktif</span>
                    <span class="p-2 rounded-3 bg-primary-subtle text-primary">
                        <i class="bi bi-people-fill"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-1">{{ $summary['total_mentors'] }} <small class="text-muted fs-6 fw-normal">Guru</small></h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill align-self-start font-monospace">
                    {{ $summary['total_active_students'] }} Santri Binaan
                </span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Rata-rata Rating</span>
                    <span class="p-2 rounded-3 bg-warning-subtle text-warning">
                        <i class="bi bi-star-fill"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-warning-emphasis">{{ $summary['avg_rating'] }} <small class="text-muted fs-6 fw-normal">/ 5.0</small></h3>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill align-self-start">
                    <i class="bi bi-shield-check me-1"></i>Bayesian Smoothed
                </span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Tingkat Retensi</span>
                    <span class="p-2 rounded-3 bg-success-subtle text-success">
                        <i class="bi bi-graph-up-arrow"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-success">{{ $summary['avg_retention'] }}%</h3>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill align-self-start">
                    Benchmark Lembaga &ge; 95%
                </span>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Rata-rata Skor Komposit</span>
                    <span class="p-2 rounded-3 bg-info-subtle text-info">
                        <i class="bi bi-trophy-fill"></i>
                    </span>
                </div>
                <h3 class="fw-bold mb-1 text-info">{{ $summary['avg_composite'] }} <small class="text-muted fs-6 fw-normal">/ 100</small></h3>
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill align-self-start">
                    Kategori Mumtaz &amp; Baik
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Row: Top 10 Leaderboard & 6-Month Quality Trend -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">🏆 Top 10 Guru Pembimbing Berprestasi</h6>
                        <small class="text-muted">Peringkat berdasarkan Skor Komposit Kinerja (Periode {{ $selectedMonth }})</small>
                    </div>
                </div>
                <div id="leaderboardBarChart" style="min-height: 280px;"></div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">📈 Tren Rata-rata Skor Komposit</h6>
                        <small class="text-muted">Pergerakan kualitas bimbingan 6 bulan terakhir</small>
                    </div>
                </div>
                <div id="trendLineChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- DataTables Table of All Mentors -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="fw-bold mb-1">📋 Rekapitulasi Performa Seluruh Guru</h6>
                <small class="text-muted">Data snapshot lengkap beserta bobot dinamis program &amp; bonus kesabaran</small>
            </div>
            <form method="GET" action="{{ route('admin.performance.mentors.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                <select name="status" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                    <option value="">Semua Kategori Skor</option>
                    <option value="excellent" {{ request('status') === 'excellent' ? 'selected' : '' }}>Sangat Memuaskan (&ge; 90)</option>
                    <option value="good" {{ request('status') === 'good' ? 'selected' : '' }}>Baik (80 - 89.9)</option>
                    <option value="needs_improvement" {{ request('status') === 'needs_improvement' ? 'selected' : '' }}>Perlu Bimbingan (&lt; 80)</option>
                </select>
                <input type="text" name="specialization" value="{{ request('specialization') }}" placeholder="Cari Spesialisasi..." class="form-control form-control-sm rounded-pill px-3">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">Filter</button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">Rank</th>
                            <th>Nama Guru Pembimbing</th>
                            <th>Spesialisasi</th>
                            <th class="text-center">Santri Aktif</th>
                            <th class="text-center">Retensi</th>
                            <th class="text-center">Nilai Akademik</th>
                            <th class="text-center">Rating Wali</th>
                            <th class="text-center">Kehadiran</th>
                            <th class="text-center">Handicap Bonus</th>
                            <th class="text-center">Skor Komposit</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($snapshots as $snap)
                            @php
                                $mentorUser = $snap->mentor?->user;
                                $scoreBadge = $snap->composite_score >= 90 ? 'bg-success' : ($snap->composite_score >= 80 ? 'bg-primary' : 'bg-warning text-dark');
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-muted">
                                    @if($snap->rank_position === 1)
                                        <span class="badge bg-warning text-dark rounded-circle p-2">🥇 1</span>
                                    @elseif($snap->rank_position === 2)
                                        <span class="badge bg-secondary text-white rounded-circle p-2">🥈 2</span>
                                    @elseif($snap->rank_position === 3)
                                        <span class="badge bg-danger-subtle text-danger rounded-circle p-2">🥉 3</span>
                                    @else
                                        #{{ $snap->rank_position ?? '-' }}
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle-sm bg-primary-subtle text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                            {{ strtoupper(substr($mentorUser?->name ?? 'G', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $mentorUser?->name ?? $snap->mentor?->name }}</div>
                                            <small class="text-muted">{{ $mentorUser?->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border rounded-pill">
                                        {{ $snap->mentor?->specialization ?? 'Tahsin Al-Qur\'an' }}
                                    </span>
                                </td>
                                <td class="text-center fw-semibold">
                                    {{ $snap->active_students }} <span class="text-muted small">/ {{ $snap->total_students }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold {{ $snap->retention_rate >= 95 ? 'text-success' : 'text-danger' }}">
                                        {{ $snap->retention_rate }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $snap->academic_quality_score }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                        ⭐ {{ $snap->avg_rating_bayesian }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $snap->attendance_rate }}%</span>
                                </td>
                                <td class="text-center">
                                    @if($snap->handicap_bonus_points > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-monospace">
                                            +{{ $snap->handicap_bonus_points }} Pts
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $scoreBadge }} rounded-pill px-3 py-2 fs-6">
                                        {{ $snap->composite_score }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.performance.mentors.show', ['id' => $snap->mentor_id, 'month' => $selectedMonth]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-eye-fill me-1"></i> Scorecard 360°
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data snapshot performa untuk periode {{ $selectedMonth }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($snapshots->hasPages())
                <div class="p-4 border-top">
                    {{ $snapshots->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Leaderboard Horizontal Bar Chart
    const leaderboardNames = @json($leaderboard->map(fn($s) => $s->mentor?->user?->name ?? 'Mentor')->toArray());
    const leaderboardScores = @json($leaderboard->map(fn($s) => (float)$s->composite_score)->toArray());

    const barOptions = {
        series: [{ name: 'Skor Komposit', data: leaderboardScores }],
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        plotOptions: { bar: { horizontal: true, borderRadius: 6, dataLabels: { position: 'top' } } },
        colors: ['#0d7a3e'],
        dataLabels: { enabled: true, offsetX: -6, style: { fontSize: '12px', colors: ['#fff'] } },
        xaxis: { categories: leaderboardNames, max: 100 },
        tooltip: { y: { formatter: val => val + ' / 100' } }
    };
    new ApexCharts(document.querySelector("#leaderboardBarChart"), barOptions).render();

    // 2. 6-Month Quality Trend Line Chart
    const trendMonths = @json(array_column($sixMonthTrends, 'month'));
    const trendScores = @json(array_column($sixMonthTrends, 'avg_score'));

    const lineOptions = {
        series: [{ name: 'Rata-rata Skor', data: trendScores }],
        chart: { type: 'area', height: 280, toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#0284c7'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05 } },
        xaxis: { categories: trendMonths },
        yaxis: { min: 60, max: 100 },
        tooltip: { y: { formatter: val => val + ' / 100' } }
    };
    new ApexCharts(document.querySelector("#trendLineChart"), lineOptions).render();
});
</script>
@endpush
@endsection
