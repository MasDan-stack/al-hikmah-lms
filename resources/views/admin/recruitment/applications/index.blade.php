@extends('layouts.admin')

@section('title', 'Manajemen Lamaran Guru')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i>Daftar Lamaran Guru Pembimbing</h1>
            <p class="text-muted small mb-0">Kelola dan seleksi calon Guru Pembimbing Al-Qur'an secara bertahap melalui sistem ATS Al-Hikmah.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.recruitment.applications.export') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Data CSV
            </a>
            <a href="{{ route('bergabung') }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Buka Form Publik
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6 mb-3 mb-xl-0">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header py-3 bg-light border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-funnel me-1"></i> Funnel Rekrutmen Guru</h6>
                </div>
                <div class="card-body">
                    <div id="recruitmentFunnelChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header py-3 bg-light border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-graph-up me-1"></i> Tren Pendaftar Harian</h6>
                </div>
                <div class="card-body">
                    <div id="dailyTrendChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 7 Tab Filter Pipeline Rekrutmen -->
    <ul class="nav nav-pills mb-3 gap-2 flex-wrap" id="recruitmentPipelineTabs">
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-1.5 {{ empty($currentStatus) || $currentStatus === 'all' ? 'active bg-primary' : 'bg-white border text-dark' }}" href="{{ route('admin.recruitment.applications.index') }}">
                <i class="bi bi-grid-fill me-1"></i> Semua Lamaran
                <span class="badge {{ empty($currentStatus) || $currentStatus === 'all' ? 'bg-white text-primary' : 'bg-secondary' }} rounded-pill ms-1">{{ $statusCounts['all'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-1.5 {{ ($currentStatus ?? '') === 'submitted' ? 'active bg-success' : 'bg-white border text-dark' }}" href="{{ route('admin.recruitment.applications.index', ['status' => 'submitted']) }}">
                <i class="bi bi-folder-check me-1 text-success"></i> Perlu Verifikasi
                <span class="badge {{ ($currentStatus ?? '') === 'submitted' ? 'bg-white text-success' : 'bg-success-subtle text-success' }} rounded-pill ms-1">{{ $statusCounts['submitted'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-1.5 {{ ($currentStatus ?? '') === 'test_scheduled' ? 'active bg-info text-white' : 'bg-white border text-dark' }}" href="{{ route('admin.recruitment.applications.index', ['status' => 'test_scheduled']) }}">
                <i class="bi bi-pencil-square me-1 text-info"></i> Sedang Ujian
                <span class="badge {{ ($currentStatus ?? '') === 'test_scheduled' ? 'bg-white text-info' : 'bg-info-subtle text-info' }} rounded-pill ms-1">{{ $statusCounts['test_scheduled'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-1.5 {{ ($currentStatus ?? '') === 'test_completed' ? 'active bg-warning text-dark' : 'bg-white border text-dark' }}" href="{{ route('admin.recruitment.applications.index', ['status' => 'test_completed']) }}">
                <i class="bi bi-clipboard-data me-1 text-warning"></i> Evaluasi Hasil Tes
                <span class="badge {{ ($currentStatus ?? '') === 'test_completed' ? 'bg-dark text-warning' : 'bg-warning-subtle text-dark' }} rounded-pill ms-1">{{ $statusCounts['test_completed'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-1.5 {{ ($currentStatus ?? '') === 'interview_scheduled' ? 'active bg-primary' : 'bg-white border text-dark' }}" href="{{ route('admin.recruitment.applications.index', ['status' => 'interview_scheduled']) }}">
                <i class="bi bi-camera-video me-1 text-primary"></i> Jadwal Wawancara
                <span class="badge {{ ($currentStatus ?? '') === 'interview_scheduled' ? 'bg-white text-primary' : 'bg-primary-subtle text-primary' }} rounded-pill ms-1">{{ $statusCounts['interview_scheduled'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-1.5 {{ ($currentStatus ?? '') === 'approved' ? 'active bg-success' : 'bg-white border text-dark' }}" href="{{ route('admin.recruitment.applications.index', ['status' => 'approved']) }}">
                <i class="bi bi-check-circle-fill me-1 text-success"></i> Lulus / Probation
                <span class="badge {{ ($currentStatus ?? '') === 'approved' ? 'bg-white text-success' : 'bg-success-subtle text-success' }} rounded-pill ms-1">{{ $statusCounts['approved'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link rounded-pill px-3 py-1.5 {{ ($currentStatus ?? '') === 'rejected' ? 'active bg-danger' : 'bg-white border text-dark' }}" href="{{ route('admin.recruitment.applications.index', ['status' => 'rejected']) }}">
                <i class="bi bi-x-circle me-1 text-danger"></i> Tidak Lolos
                <span class="badge {{ ($currentStatus ?? '') === 'rejected' ? 'bg-white text-danger' : 'bg-danger-subtle text-danger' }} rounded-pill ms-1">{{ $statusCounts['rejected'] ?? 0 }}</span>
            </a>
        </li>
    </ul>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header py-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-table me-1"></i> Database Calon Guru Terdaftar</h6>
                <small class="text-muted">Gunakan tab di atas, pencarian, dan tombol aksi cerdas di bawah ini</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0 datatable" id="tableMentorApplications" data-export="true" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Lengkap</th>
                            <th>Kontak</th>
                            <th>Spesialisasi & Hafalan</th>
                            <th>Tahap</th>
                            <th>Status Seleksi</th>
                            <th>Tanggal Masuk</th>
                            <th class="text-end no-sort">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                        <tr>
                            <td class="fw-bold text-primary font-monospace">{{ $app->application_code }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $app->full_name }}</div>
                                <small class="text-muted">{{ $app->education }}</small>
                            </td>
                            <td>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $app->phone) }}" target="_blank" class="text-success text-decoration-none fw-semibold">
                                    <i class="bi bi-whatsapp me-1"></i>{{ $app->phone }}
                                </a>
                                <small class="d-block text-muted">{{ $app->email }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $app->specialization }}</span>
                                <small class="d-block text-muted">{{ $app->hifz_total_juz }} Juz | {{ $app->experience_years }} thn</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border">Tahap {{ $app->current_stage }}/5</span>
                            </td>
                            <td>{!! $app->status_badge !!}</td>
                            <td class="small">{{ $app->submitted_at ? $app->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-end text-nowrap">
                                @if($app->status === 'submitted')
                                    <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold shadow-xs">
                                        <i class="bi bi-check-circle me-1"></i> Verifikasi Berkas
                                    </a>
                                @elseif($app->status === 'test_completed')
                                    <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 py-1 fw-bold shadow-xs text-dark">
                                        <i class="bi bi-calendar-event me-1"></i> Jadwalkan Wawancara
                                    </a>
                                @elseif($app->status === 'interview_scheduled')
                                    <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold shadow-xs">
                                        <i class="bi bi-person-check me-1"></i> Putuskan Penerimaan
                                    </a>
                                @elseif($app->status === 'approved' && $app->mentor)
                                    <a href="{{ route('admin.mentors.probation.show', $app->mentor->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-bold shadow-xs">
                                        <i class="bi bi-speedometer2 me-1"></i> Monitoring Probation
                                    </a>
                                @else
                                    <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                        <i class="bi bi-eye me-1"></i> Review
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada lamaran masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi DataTables eksplisit
    if (typeof DataTable !== 'undefined') {
        const appTable = document.getElementById('tableMentorApplications');
        if (appTable && !DataTable.isDataTable(appTable)) {
            if (typeof window.initDataTable === 'function') {
                window.initDataTable(appTable);
            } else {
                new DataTable(appTable, {
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari data calon guru...",
                        lengthMenu: "Tampilkan _MENU_ baris",
                        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ pelamar",
                        zeroRecords: "Tidak ada data pelamar yang cocok",
                    }
                });
            }
        }
    }

    // Funnel Chart
    fetch('{{ route("admin.api.recruitment.funnel") }}')
        .then(response => response.json())
        .then(data => {
            var options = {
                series: [{
                    name: "Jumlah Pelamar",
                    data: data.series || []
                }],
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: { show: false }
                },
                colors: ['#0d6efd', '#0dcaf0', '#ffc107', '#198754', '#dc3545'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: true,
                        distributed: true
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: { colors: ['#fff'] }
                },
                xaxis: {
                    categories: data.labels || [],
                },
                legend: {
                    show: false
                }
            };

            var chart = new ApexCharts(document.querySelector("#recruitmentFunnelChart"), options);
            chart.render();
        })
        .catch(err => console.log('Chart load notice:', err));

    // Daily Trend Chart
    fetch('{{ route("admin.api.recruitment.daily-trend") }}')
        .then(response => response.json())
        .then(data => {
            var options = {
                series: data.series || [],
                chart: {
                    height: 320,
                    type: 'area',
                    toolbar: { show: false }
                },
                colors: ['#0d6efd'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                    }
                },
                xaxis: {
                    categories: data.labels || [],
                }
            };

            var chart = new ApexCharts(document.querySelector("#dailyTrendChart"), options);
            chart.render();
        })
        .catch(err => console.log('Trend chart load notice:', err));
});
</script>
@endpush
