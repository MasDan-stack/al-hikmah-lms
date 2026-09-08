@extends('layouts.mentor')

@section('title', 'Portal Kinerja Saya & Goals')

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary-emphasis d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-warning"></i> Portal Kinerja Saya &amp; Goals
            </h4>
            <p class="text-muted small mb-0">
                Pantau capaian mutu bimbingan pribadi, target bulanan, rekomendasi coaching AI, dan koleksi lencana prestasi.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('mentor.performance.index') }}" class="d-flex align-items-center gap-2">
                <input type="month" name="month" value="{{ $selectedMonth }}" class="form-control form-control-sm rounded-pill px-3" onchange="this.form.submit()">
            </form>
            <a href="{{ route('mentor.performance.goals') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-bullseye me-1"></i> Target Capaian
            </a>
            <a href="{{ route('mentor.performance.self-assessment', ['month' => $selectedMonth]) }}" class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Evaluasi Diri
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

    <!-- 3 Highlight Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-primary text-white">
                <small class="text-white-50 fw-semibold text-uppercase">Skor Komposit Bulan Ini</small>
                <div class="d-flex align-items-baseline gap-2 my-2">
                    <h1 class="fw-bold mb-0">{{ $snapshot->composite_score }}</h1>
                    <span class="fs-5 text-white-50">/ 100</span>
                </div>
                <span class="badge bg-white text-primary rounded-pill align-self-start px-3 py-1 font-monospace">
                    {{ $snapshot->composite_score >= 90 ? '🌟 Sangat Memuaskan (Mumtaz)' : ($snapshot->composite_score >= 80 ? '👍 Baik (Jayyid Jiddan)' : '📈 Terus Berkembang') }}
                </span>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-semibold text-uppercase">Posisi di Lembaga</small>
                    <span class="p-2 rounded-3 bg-warning-subtle text-warning">
                        <i class="bi bi-trophy-fill"></i>
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-primary">Top {{ 100 - $percentile }}%</h2>
                <p class="text-muted small mb-0">
                    Posisi antum lebih tinggi dari <strong>{{ $percentile }}%</strong> guru bimbingan di AL-HIKMAH.
                </p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-card-custom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted fw-semibold text-uppercase">Kepuasan Wali Santri</small>
                    <span class="p-2 rounded-3 bg-warning-subtle text-warning">
                        <i class="bi bi-star-fill"></i>
                    </span>
                </div>
                <h2 class="fw-bold mb-1 text-warning-emphasis">⭐ {{ $snapshot->avg_rating_bayesian }} <small class="fs-6 text-muted">/ 5.0</small></h2>
                <p class="text-muted small mb-0">
                    Berdasarkan <strong>{{ $snapshot->total_feedback_count }}</strong> ulasan wali santri (Bayesian Adjusted).
                </p>
            </div>
        </div>
    </div>

    <!-- Main Grid: Radar Chart & AI Coaching with 1-Click Adopt -->
    <div class="row g-4 mb-4">
        <!-- 5-Axis Radar Chart Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">🕸️ Diagram Keseimbangan 5 Pilar</h6>
                        <small class="text-muted">Analisis capaian kompetensi bimbingan</small>
                    </div>
                </div>
                <div id="mentorRadarChart" style="min-height: 280px;"></div>
                <div class="row text-center g-2 mt-2 pt-2 border-top">
                    <div class="col">
                        <small class="text-muted d-block">Retensi</small>
                        <strong class="text-dark">{{ $metrics['retention_rate'] }}%</strong>
                    </div>
                    <div class="col">
                        <small class="text-muted d-block">Akademik</small>
                        <strong class="text-dark">{{ $metrics['academic_quality_score'] }}</strong>
                    </div>
                    <div class="col">
                        <small class="text-muted d-block">Kehadiran</small>
                        <strong class="text-dark">{{ $metrics['attendance_rate'] }}%</strong>
                    </div>
                    <div class="col">
                        <small class="text-muted d-block">Kepuasan</small>
                        <strong class="text-dark">{{ $metrics['avg_rating_bayesian'] }}/5</strong>
                    </div>
                    <div class="col">
                        <small class="text-muted d-block">Target</small>
                        <strong class="text-dark">{{ $metrics['target_achievement_rate'] }}%</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Prescriptive Insights & 1-Click Adopt Goal -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 rounded-3 bg-primary text-white">
                            <i class="bi bi-robot"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-0">Rekomendasi Pembinaan AI</h6>
                            <small class="text-muted">Asisten Pembinaan Kualitas Mengajar</small>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-4 shadow-sm mb-3 border">
                    <p class="mb-0 text-dark small" style="line-height: 1.6;">
                        {{ $insight->ai_summary ?? 'Ringkasan performa bimbingan sedang diproses...' }}
                    </p>
                </div>

                <h6 class="fw-bold text-primary-emphasis mb-2 small text-uppercase">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Rencana Aksi yang Disarankan:
                </h6>
                <div class="d-flex flex-column gap-2 mb-3">
                    @forelse($insight->coaching_recommendations ?? [] as $rec)
                        <div class="d-flex align-items-center justify-content-between bg-white p-2 rounded-3 border">
                            <div class="d-flex align-items-start gap-2 me-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                <span class="small text-secondary">{{ $rec }}</span>
                            </div>
                            <form method="POST" action="{{ route('mentor.performance.goals.store') }}" class="flex-shrink-0">
                                @csrf
                                <input type="hidden" name="title" value="{{ $rec }}">
                                <input type="hidden" name="goal_type" value="rating">
                                <input type="hidden" name="target_value" value="95">
                                <input type="hidden" name="period" value="{{ $selectedMonth }}">
                                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-2 py-0" style="font-size: 0.75rem;" title="Adopsi menjadi target aktif bulan ini">
                                    + Target
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-muted small">Belum ada rekomendasi pembinaan khusus.</div>
                    @endforelse
                </div>

                <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="small text-muted">Refleksi Diri Bulanan:</span>
                    @if($selfAssessment)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                            <i class="bi bi-check2-circle me-1"></i> Sudah Diisi
                        </span>
                    @else
                        <a href="{{ route('mentor.performance.self-assessment', ['month' => $selectedMonth]) }}" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm fw-bold">
                            <i class="bi bi-pencil me-1"></i> Isi Sekarang
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Active Goals & Badges Showcase -->
    <div class="row g-4 mb-4">
        <!-- Target Capaian (Goals) List -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">🎯 Target Capaian Aktif (Bulan Ini)</h6>
                        <small class="text-muted">Progres pencapaian target bulanan mandiri</small>
                    </div>
                    <a href="{{ route('mentor.performance.goals') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        + Tambah Target
                    </a>
                </div>
                <div class="d-flex flex-column gap-3">
                    @forelse($goals as $goal)
                        @php
                            $pct = $goal->target_value > 0 ? min(100, round(($goal->current_value / $goal->target_value) * 100)) : 0;
                            $barColor = $pct >= 100 ? 'bg-success' : ($pct >= 75 ? 'bg-primary' : 'bg-warning');
                        @endphp
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-dark small">{{ $goal->title }}</span>
                                <span class="badge {{ $pct >= 100 ? 'bg-success' : 'bg-primary' }} rounded-pill">
                                    {{ $pct }}%
                                </span>
                            </div>
                            <div class="progress rounded-pill mb-2" style="height: 8px;">
                                <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                <span>Saat ini: <strong>{{ $goal->current_value }}</strong></span>
                                <span>Target: <strong>{{ $goal->target_value }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bullseye fs-2 d-block mb-1"></i>
                            Belum ada target mandiri untuk periode ini. Klik "+ Tambah Target" untuk memulai.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Badges Showcase -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">🏅 Koleksi Lencana Prestasi</h6>
                        <small class="text-muted">Apresiasi &amp; penghargaan kinerja bimbingan</small>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($badges as $badge)
                        <div class="p-2 px-3 bg-light rounded-pill border d-flex align-items-center gap-2 shadow-sm">
                            <i class="{{ $badge->icon ?? 'fas fa-award' }} text-warning"></i>
                            <div>
                                <strong class="small d-block text-dark">{{ $badge->name }}</strong>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted w-100">
                            <i class="bi bi-award fs-2 d-block mb-1"></i>
                            Terus tingkatkan mutu bimbingan untuk membuka lencana prestasi (M01–M07).
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const radarOptions = {
        series: [{
            name: 'Pencapaian Saya',
            data: [
                {{ $metrics['retention_rate'] }},
                {{ $metrics['academic_quality_score'] }},
                {{ $metrics['attendance_rate'] }},
                {{ round(($metrics['avg_rating_bayesian'] / 5.0) * 100, 1) }},
                {{ $metrics['target_achievement_rate'] }}
            ]
        }],
        chart: { height: 280, type: 'radar', toolbar: { show: false } },
        colors: ['#0d7a3e'],
        stroke: { width: 2 },
        fill: { opacity: 0.25 },
        markers: { size: 4 },
        xaxis: {
            categories: ['Retensi Santri', 'Mutu Akademik', 'Kehadiran Sesi', 'Kepuasan Wali', 'Target Kurikulum']
        },
        yaxis: { min: 0, max: 100 }
    };
    new ApexCharts(document.querySelector("#mentorRadarChart"), radarOptions).render();
});
</script>
@endpush
@endsection
