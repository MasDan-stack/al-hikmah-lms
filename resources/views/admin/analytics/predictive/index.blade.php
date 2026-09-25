@extends('layouts.admin')

@section('title', 'Predictive Analytics & Early Warning System')
@section('header', 'Predictive Analytics & Early Warning System')
@section('subheader', 'Deteksi dini risiko dropout santri, laju hafalan, proyeksi arus kas 6 bulan, dan peringatan pembinaan mentor.')

@section('content')
<div class="container-fluid px-0">
    <!-- Top Action Bar & Program Filter -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-shield-exclamation text-danger me-2"></i>Executive Predictive Hub (v8.6)
            </h3>
            <p class="text-muted small mb-0">Transformasi pemantauan operasional dari model reaktif menjadi proaktif preskriptif.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Filter Program -->
            <select class="form-select form-select-sm border-0 shadow-sm rounded-pill px-3" id="filterProgram" onchange="window.location.href='?program_id=' + this.value;" style="min-width: 180px;">
                <option value="">Semua Program</option>
                @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ (isset($selectedProgramId) && $selectedProgramId == $prog->id) ? 'selected' : '' }}>
                        {{ $prog->name }}
                    </option>
                @endforeach
            </select>

            <!-- Export Buttons -->
            <div class="btn-group shadow-sm rounded-pill">
                <a href="{{ route('admin.analytics.predictive.export', ['format' => 'excel', 'program_id' => $selectedProgramId ?? '']) }}" class="btn btn-sm btn-outline-success rounded-start-pill px-3">
                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </a>
                <a href="{{ route('admin.analytics.predictive.export', ['format' => 'csv', 'program_id' => $selectedProgramId ?? '']) }}" class="btn btn-sm btn-outline-secondary rounded-end-pill px-3">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
                </a>
            </div>

            <!-- Recalculate Form -->
            <form action="{{ route('admin.analytics.predictive.recalculate') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-clockwise me-1"></i>Recalculate All
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 1. 4 Executive KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Santri Kritis Dropout -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(239, 68, 68, 0.02) 100%); border: 1px solid rgba(239, 68, 68, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Kritis Dropout</span>
                    <div class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-danger">{{ number_format($criticalCount) }} <span class="fs-6 fw-normal text-muted">Santri</span></h3>
                <span class="small text-muted">Perlu intervensi komunikasi segera</span>
            </div>
        </div>

        <!-- Card 2: Santri Velocity Lambat -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(245, 158, 11, 0.02) 100%); border: 1px solid rgba(245, 158, 11, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Velocity Lambat</span>
                    <div class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-2">
                        <i class="bi bi-speedometer2 fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-warning">{{ number_format($slowVelocityCount) }} <span class="fs-6 fw-normal text-muted">Santri</span></h3>
                <span class="small text-muted">Capaian setoran &lt; 50% target</span>
            </div>
        </div>

        <!-- Card 3: Prediksi Revenue Bulan Depan -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%); border: 1px solid rgba(16, 185, 129, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Prediksi Revenue M+1</span>
                    <div class="badge rounded-pill bg-success bg-opacity-10 text-success p-2">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($nextMonthRevenue, 0, ',', '.') }}</h3>
                <span class="small text-muted">Regresi OLS + Musim & Churn</span>
            </div>
        </div>

        <!-- Card 4: Guru Butuh Coaching -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(139, 92, 246, 0.02) 100%); border: 1px solid rgba(139, 92, 246, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Guru Butuh Coaching</span>
                    <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2">
                        <i class="bi bi-person-workspace fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-primary">{{ number_format($coachingCount) }} <span class="fs-6 fw-normal text-muted">Guru</span></h3>
                <span class="small text-muted">Slope tren kinerja &lt; threshold</span>
            </div>
        </div>
    </div>

    <!-- 2. Nav Pills Tabs -->
    <ul class="nav nav-pills nav-fill gap-2 p-1 bg-white rounded-pill shadow-sm mb-4" id="predictive-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill fw-semibold py-2" id="dropout-tab" data-bs-toggle="pill" data-bs-target="#dropout" type="button" role="tab">
                <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i> 1. Dropout Risk Monitor
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold py-2" id="velocity-tab" data-bs-toggle="pill" data-bs-target="#velocity" type="button" role="tab">
                <i class="bi bi-speedometer2 me-1 text-warning"></i> 2. Learning Velocity Tracker
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold py-2" id="revenue-tab" data-bs-toggle="pill" data-bs-target="#revenue" type="button" role="tab">
                <i class="bi bi-graph-up-arrow me-1 text-success"></i> 3. Revenue Forecast (6 Bulan)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill fw-semibold py-2" id="teacher-tab" data-bs-toggle="pill" data-bs-target="#teacher" type="button" role="tab">
                <i class="bi bi-person-lines-fill me-1 text-primary"></i> 4. Teacher Coaching Alerts
            </button>
        </li>
    </ul>

    <!-- 3. Tab Contents -->
    <div class="tab-content" id="predictive-tabs-content">
        <!-- TAB 1: DROPOUT RISK -->
        <div class="tab-pane fade show active" id="dropout" role="tabpanel" tabindex="0">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Deteksi Dini Risiko Dropout (4-Factor Ensemble)</h5>
                        <p class="text-muted small mb-0">Presensi (35%), Riwayat Bayar (30%), Progres (20%), Keterlibatan Wali (15%).</p>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">
                        Total Terdata: {{ $dropoutRisks->total() }} Santri
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Santri</th>
                                    <th>Program</th>
                                    <th>Tingkat Risiko</th>
                                    <th>Skor Risiko</th>
                                    <th>Faktor Pemicu Utama</th>
                                    <th>Rekomendasi Tindakan AI</th>
                                    <th class="text-center">Aksi Intervensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dropoutRisks as $risk)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $risk->student->full_name ?? 'Santri' }}</div>
                                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $risk->student->parent?->emergency_phone ?? $risk->student->parent?->user?->phone ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                                {{ $risk->student->enrollments->first()->program->name ?? 'Tahfidz' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ riskLevelColor($risk->risk_level) }} rounded-pill px-2 py-1">
                                                {{ riskLevelLabel($risk->risk_level) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 8px; width: 70px;">
                                                    <div class="progress-bar bg-{{ riskLevelColor($risk->risk_level) }}" role="progressbar" style="width: {{ $risk->risk_score }}%;"></div>
                                                </div>
                                                <span class="small fw-bold">{{ $risk->risk_score }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="list-unstyled mb-0 small">
                                                @foreach((array) $risk->risk_factors as $factor)
                                                    <li class="text-danger"><i class="bi bi-dash-circle me-1"></i>{{ $factor }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            <ul class="list-unstyled mb-0 small text-muted">
                                                @foreach(array_slice((array) $risk->recommendations, 0, 2) as $rec)
                                                    <li><i class="bi bi-lightbulb-fill text-warning me-1"></i>{{ $rec }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="text-center">
                                            @if(!$risk->is_alerted)
                                                <form action="{{ route('admin.analytics.predictive.intervention.wa') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="prediction_id" value="{{ $risk->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" title="Kirim Pesan WhatsApp Silaturahmi & Solusi">
                                                        <i class="bi bi-whatsapp me-1"></i>Hubungi via WA
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                                    <i class="bi bi-check-all me-1"></i>Sudah Diintervensi
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-shield-check text-success fs-1 d-block mb-2"></i>
                                            Alhamdulillah, tidak ada santri yang teridentifikasi dalam risiko dropout hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $dropoutRisks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: LEARNING VELOCITY -->
        <div class="tab-pane fade" id="velocity" role="tabpanel" tabindex="0">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Kecepatan Menghafal & Estimasi Tanggal Khatam (ETA)</h5>
                    <p class="text-muted small">Target standar: 5.0 ayat/hari aktif. Memproyeksikan sisa waktu menuju khatam 30 Juz.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Santri</th>
                                    <th>Hari Aktif (30H)</th>
                                    <th>Total Ayat (30H)</th>
                                    <th>Kecepatan Aktual</th>
                                    <th>Status Laju</th>
                                    <th>Estimasi Sisa Hari</th>
                                    <th>Proyeksi Khatam (ETA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($velocities as $vel)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $vel->student->full_name ?? 'Santri' }}</div>
                                            <span class="small text-muted">{{ $vel->student->enrollments->first()->program->name ?? 'Tahfidz' }}</span>
                                        </td>
                                        <td>{{ $vel->days_active }} Hari</td>
                                        <td>{{ $vel->total_ayat_30d }} Ayat</td>
                                        <td>
                                            <span class="fw-bold {{ $vel->velocity_ayat_per_day >= $vel->target_velocity ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($vel->velocity_ayat_per_day, 1) }} <small class="text-muted">ayat/hari</small>
                                            </span>
                                        </td>
                                        <td>
                                            @if($vel->velocity_status === 'excellent')
                                                <span class="badge bg-success rounded-pill px-2 py-1">🚀 Sangat Cepat</span>
                                            @elseif($vel->velocity_status === 'good')
                                                <span class="badge bg-primary rounded-pill px-2 py-1">⚡ Sesuai Target</span>
                                            @elseif($vel->velocity_status === 'moderate')
                                                <span class="badge bg-warning rounded-pill px-2 py-1">⚠️ Cukup</span>
                                            @elseif($vel->velocity_status === 'slow')
                                                <span class="badge bg-danger rounded-pill px-2 py-1">🔴 Melambat</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill px-2 py-1">⚪ Belum Ada Data</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $vel->estimated_days_remaining ? number_format($vel->estimated_days_remaining) . ' Hari' : '-' }}
                                        </td>
                                        <td>
                                            {{ $vel->projected_completion_date ? \Carbon\Carbon::parse($vel->projected_completion_date)->translatedFormat('d F Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data analitik kecepatan hafalan hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $velocities->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: REVENUE FORECAST -->
        <div class="tab-pane fade" id="revenue" role="tabpanel" tabindex="0">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                            <h5 class="fw-bold mb-0">Visualisasi Proyeksi Pendapatan 6 Bulan Ke Depan</h5>
                            <p class="text-muted small">Model Regresi Linier OLS + Pengali Musiman Islami + Diskon Churn (5%).</p>
                        </div>
                        <div class="card-body">
                            <div id="revenueForecastChart" style="min-height: 330px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                            <h5 class="fw-bold mb-0">Rincian Proyeksi Bulanan</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Proyeksi Bersih</th>
                                            <th>Faktor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($revenueForecasts as $rf)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $rf->forecast_month_label }}</div>
                                                    <span class="text-muted" style="font-size: 0.75rem;">Akurasi: {{ $rf->confidence_level }}%</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success">Rp {{ number_format($rf->predicted_amount, 0, ',', '.') }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $rf->seasonal_factor }}x
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Belum ada proyeksi.</td>
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

        <!-- TAB 4: TEACHER COACHING ALERTS -->
        <div class="tab-pane fade" id="teacher" role="tabpanel" tabindex="0">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0">Deteksi Dini Penurunan Kinerja Guru (Teacher Coaching Alerts)</h5>
                    <p class="text-muted small">Menganalisis kemiringan tren (slope) kinerja 6 bulan terakhir untuk memicu pembinaan 1-on-1.</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Guru Pembimbing</th>
                                    <th>Spesialisasi</th>
                                    <th>Rating Saat Ini</th>
                                    <th>Santri Binaan</th>
                                    <th>Status Pembinaan</th>
                                    <th>Urgensi</th>
                                    <th class="text-center">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teacherPredictions as $mentor)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $mentor->getDisplayName() }}</div>
                                            <div class="small text-muted">{{ $mentor->user?->email ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $mentor->specialization ?? 'Al-Qur\'an' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($mentor->rating ?? 5.0, 1) }}</span>
                                        </td>
                                        <td>{{ $mentor->students->count() }} Santri</td>
                                        <td>
                                            @if($mentor->coaching_needed)
                                                <span class="badge bg-danger rounded-pill px-2 py-1">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Wajib Coaching
                                                </span>
                                            @else
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                                                    <i class="bi bi-check-circle me-1"></i>Performa Stabil
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($mentor->coaching_urgency === 'critical')
                                                <span class="badge bg-danger">Kritis</span>
                                            @elseif($mentor->coaching_urgency === 'high')
                                                <span class="badge bg-warning text-dark">Tinggi</span>
                                            @elseif($mentor->coaching_urgency === 'medium')
                                                <span class="badge bg-info text-dark">Sedang</span>
                                            @else
                                                <span class="badge bg-secondary">Rendah / Normal</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $mentorPhone = $mentor->user?->phone ?? $mentor->emergency_contact;
                                            @endphp
                                            @if($mentorPhone)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mentorPhone) }}?text={{ urlencode('Assalamu\'alaikum Ustadz/Ustdzah ' . $mentor->getDisplayName() . ', kami dari manajemen ingin mengundang sesi sharing 1-on-1 evaluasi bimbingan.') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="bi bi-whatsapp me-1"></i>Jadwalkan 1-on-1
                                                </a>
                                            @else
                                                <span class="text-muted small">No HP tidak ada</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada data guru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $teacherPredictions->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rawData = @json($revenueForecasts);
    
    if (rawData && rawData.length > 0) {
        const categories = rawData.map(d => d.forecast_month_label);
        const amounts = rawData.map(d => d.predicted_amount);
        const trends = rawData.map(d => d.trend_value);

        const options = {
            series: [{
                name: 'Proyeksi Bersih (Net of Churn)',
                data: amounts
            }, {
                name: 'Nilai Tren Dasar (Baseline)',
                data: trends
            }],
            chart: {
                height: 330,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            colors: ['#10B981', '#3B82F6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: [3, 2],
                dashArray: [0, 5]
            },
            xaxis: {
                categories: categories,
                labels: {
                    style: { fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return 'Rp ' + (value/1000000).toFixed(1) + ' Jt';
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'Rp ' + Math.round(val).toLocaleString('id-ID');
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        const chart = new ApexCharts(document.querySelector("#revenueForecastChart"), options);
        chart.render();
    }
});
</script>
@endpush
