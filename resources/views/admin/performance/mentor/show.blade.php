@extends('layouts.admin')

@section('title', 'Scorecard 360°: ' . $mentor->getDisplayName())

@section('content')
<div class="container-fluid py-2">
    <!-- Breadcrumb & Navigation -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.performance.mentors.index') }}" class="text-decoration-none">Performa Mentor</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $mentor->getDisplayName() }}</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-primary-emphasis d-flex align-items-center gap-2">
                🎯 Scorecard 360°: {{ $mentor->getDisplayName() }}
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#recalculateModal">
                <i class="bi bi-arrow-repeat me-1"></i> Recalculate Snapshot
            </button>
            <form method="POST" action="{{ route('admin.performance.mentors.send-wa', ['id' => $mentor->id, 'month' => $selectedMonth]) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" onclick="return confirm('Kirim laporan evaluasi performa bulan {{ $selectedMonth }} ke WhatsApp guru ini?')">
                    <i class="bi bi-whatsapp me-1"></i> Kirim Rapor WhatsApp
                </button>
            </form>
            <a href="{{ route('admin.performance.mentors.index', ['month' => $selectedMonth]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Profile Header Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-card-custom">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-circle-lg bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 64px; height: 64px; font-size: 1.5rem;">
                    {{ strtoupper(substr($mentor->getDisplayName(), 0, 2)) }}
                </div>
                <div>
                    <h5 class="fw-bold mb-1">{{ $mentor->getDisplayName() }}</h5>
                    <div class="d-flex flex-wrap align-items-center gap-2 text-muted small">
                        <span><i class="bi bi-envelope me-1"></i> {{ $mentor->user->email ?? '-' }}</span>
                        <span>•</span>
                        <span><i class="bi bi-telephone me-1"></i> {{ $mentor->user->phone ?? $mentor->phone ?? '-' }}</span>
                        <span>•</span>
                        <span class="badge bg-light text-dark border rounded-pill">{{ $mentor->specialization ?? 'Tahsin' }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 text-end">
                <div class="p-3 bg-light rounded-4 text-center">
                    <small class="text-muted d-block">Peringkat Lembaga</small>
                    <h4 class="fw-bold mb-0 text-primary">#{{ $snapshot->rank_position ?? '-' }}</h4>
                </div>
                <div class="p-3 bg-primary-subtle rounded-4 text-center">
                    <small class="text-primary-emphasis d-block fw-semibold">Skor Komposit</small>
                    <h3 class="fw-bold mb-0 text-primary">{{ $metrics['composite_score'] }} <small class="fs-6">/100</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid: Radar Chart & AI Insights -->
    <div class="row g-4 mb-4">
        <!-- 5-Axis Radar Chart Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-0">🕸️ Radar Kinerja 5 Dimensi</h6>
                        <small class="text-muted">Perbandingan nilai 5 pilar utama performa</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                        Periode: {{ $selectedMonth }}
                    </span>
                </div>
                <div id="radarScoreChart" style="min-height: 320px;"></div>
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

        <!-- AI Prescriptive Insights Card -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 rounded-3 bg-primary text-white">
                            <i class="bi bi-robot"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-0">AI Performance Insights &amp; Coaching</h6>
                            <small class="text-muted">Model: {{ $insight->ai_model_used ?? 'Gemini 2.5 Flash' }}</small>
                        </div>
                    </div>
                    @php
                        $riskBadge = match($insight->risk_level ?? 'low') {
                            'high' => 'bg-danger text-white',
                            'medium' => 'bg-warning text-dark',
                            default => 'bg-success text-white'
                        };
                    @endphp
                    <span class="badge {{ $riskBadge }} rounded-pill px-3 py-1 text-uppercase">
                        Risk: {{ $insight->risk_level ?? 'low' }}
                    </span>
                </div>

                <div class="bg-white p-3 rounded-4 shadow-sm mb-3 border">
                    <p class="mb-0 text-dark" style="line-height: 1.6;">
                        {{ $insight->ai_summary ?? 'Ringkasan performa otomatis AI sedang diproses...' }}
                    </p>
                </div>

                <h6 class="fw-bold text-primary-emphasis mb-2 small text-uppercase">
                    <i class="bi bi-lightbulb-fill text-warning me-1"></i> Rekomendasi Coaching &amp; Action Plan:
                </h6>
                <div class="d-flex flex-column gap-2 mb-3">
                    @forelse($insight->coaching_recommendations ?? [] as $rec)
                        <div class="d-flex align-items-start gap-2 bg-white p-2 rounded-3 border">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <span class="small text-secondary">{{ $rec }}</span>
                        </div>
                    @empty
                        <div class="text-muted small">Belum ada rekomendasi coaching khusus.</div>
                    @endforelse
                </div>

                <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="small text-muted">Prediksi Skor Bulan Depan:</span>
                    <span class="badge bg-info text-white rounded-pill px-3 py-1 font-monospace fs-6">
                        {{ $insight->predicted_score_next_month ?? $metrics['composite_score'] }} / 100
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Student List & Feedback History Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-transparent border-0 p-4 pb-0">
            <ul class="nav nav-pills" id="mentorDetailsTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="students-tab" data-bs-toggle="pill" data-bs-target="#students" type="button" role="tab">
                        <i class="bi bi-people-fill me-1"></i> Santri Binaan Aktif ({{ $mentor->students->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="feedback-tab" data-bs-toggle="pill" data-bs-target="#feedback" type="button" role="tab">
                        <i class="bi bi-chat-square-quote-fill me-1"></i> Ulasan &amp; Rating Wali ({{ $mentor->feedbacks->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4" id="audit-tab" data-bs-toggle="pill" data-bs-target="#audit" type="button" role="tab">
                        <i class="bi bi-clock-history me-1"></i> Riwayat Audit
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="mentorDetailsTabContent">
                <!-- Tab 1: Santri Binaan -->
                <div class="tab-pane fade show active" id="students" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama Santri</th>
                                    <th>Program Belajar</th>
                                    <th>Jadwal Hari</th>
                                    <th>Status Bimbingan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mentor->students as $st)
                                    <tr>
                                        <td class="fw-bold">{{ $st->name }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $st->program->name ?? '-' }}</span></td>
                                        <td>{{ $st->pivot->day_assigned ?? '-' }}</td>
                                        <td><span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Aktif</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada santri binaan aktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Feedback & Rating Wali -->
                <div class="tab-pane fade" id="feedback" role="tabpanel">
                    <div class="row g-3">
                        @forelse($mentor->feedbacks as $fb)
                            <div class="col-12 col-md-6">
                                <div class="card border rounded-4 p-3 h-100 shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="fw-bold text-dark">
                                            @if($fb->is_anonymous)
                                                <i class="bi bi-incognito me-1 text-muted"></i> Wali Santri (Anonim)
                                            @else
                                                <i class="bi bi-person-fill me-1 text-primary"></i> {{ $fb->parent->name ?? 'Wali Santri' }}
                                            @endif
                                        </div>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                            ⭐ {{ $fb->overall_rating }}.0 / 5
                                        </span>
                                    </div>
                                    <p class="text-secondary small mb-2 fst-italic">
                                        "{{ $fb->comment ?: 'Tidak ada catatan tertulis.' }}"
                                    </p>
                                    @if(!empty($fb->quick_tags))
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            @foreach($fb->quick_tags as $tag)
                                                <span class="badge bg-success-subtle text-success rounded-pill font-monospace" style="font-size: 0.7rem;">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <small class="text-muted mt-auto" style="font-size: 0.75rem;">
                                        {{ $fb->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">
                                Belum ada ulasan yang diterima dari orang tua santri.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab 3: Riwayat Audit -->
                <div class="tab-pane fade" id="audit" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Tipe Aksi</th>
                                    <th>Rincian Catatan Perubahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditLogs as $log)
                                    <tr>
                                        <td class="small text-muted">{{ $log->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                                        <td><span class="badge bg-secondary rounded-pill">{{ $log->action }}</span></td>
                                        <td class="small">
                                            {{ $log->new_values['reason'] ?? 'Recalculate skor performa' }}
                                            @if(isset($log->old_values['composite_score']) && isset($log->new_values['composite_score']))
                                                <span class="badge bg-light text-dark border ms-1">
                                                    {{ $log->old_values['composite_score'] }} &rarr; {{ $log->new_values['composite_score'] }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada catatan koreksi/recalculate untuk guru ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Recalculate Snapshot -->
<div class="modal fade" id="recalculateModal" tabindex="-1" aria-labelledby="recalculateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.performance.mentors.recalculate', ['id' => $mentor->id]) }}" class="modal-content border-0 shadow rounded-4">
            @csrf
            <input type="hidden" name="month" value="{{ $selectedMonth }}">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="recalculateModalLabel">
                    <i class="bi bi-arrow-repeat text-warning me-1"></i> Hitung Ulang Snapshot Performa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted small">
                    Fitur ini akan mengkalkulasi ulang seluruh agregasi presensi, mutaba'ah, dan rating wali untuk guru <strong>{{ $mentor->getDisplayName() }}</strong> pada periode <strong>{{ $selectedMonth }}</strong>.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Alasan Perubahan / Koreksi Data <span class="text-danger">*</span></label>
                    <textarea name="reason" rows="3" class="form-control rounded-3" placeholder="Contoh: Koreksi data presensi sesi pekan ke-2 yang terlambat diinput..." required minlength="5"></textarea>
                    <small class="text-muted" style="font-size: 0.75rem;">Catatan ini akan direkam secara permanen dalam log audit finansial lembaga.</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Eksekusi Recalculate</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const radarOptions = {
        series: [{
            name: 'Pencapaian',
            data: [
                {{ $metrics['retention_rate'] }},
                {{ $metrics['academic_quality_score'] }},
                {{ $metrics['attendance_rate'] }},
                {{ round(($metrics['avg_rating_bayesian'] / 5.0) * 100, 1) }},
                {{ $metrics['target_achievement_rate'] }}
            ]
        }],
        chart: { height: 320, type: 'radar', toolbar: { show: false } },
        colors: ['#0d7a3e'],
        stroke: { width: 2 },
        fill: { opacity: 0.25 },
        markers: { size: 4 },
        xaxis: {
            categories: ['Retensi Santri', 'Mutu Akademik', 'Kehadiran Sesi', 'Kepuasan Wali', 'Target Kurikulum']
        },
        yaxis: { min: 0, max: 100 }
    };
    new ApexCharts(document.querySelector("#radarScoreChart"), radarOptions).render();
});
</script>
@endpush
@endsection
