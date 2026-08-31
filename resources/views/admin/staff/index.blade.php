@extends('layouts.admin')

@section('title', 'Manajemen SDM & Beban Kerja Guru')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-person-badge-fill text-primary me-2"></i>Manajemen SDM & Beban Kerja Guru
            </h3>
            <p class="text-muted small mb-0">Monitoring kapasitas mengajar mentor, rasio guru:santri, cuti harian, dan evaluasi performa bimbingan.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.mentors.availability') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bi bi-calendar-range me-1"></i>Ketersediaan & Alokasi
            </a>
            <a href="{{ route('admin.mentors.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-gear me-1"></i>Master Guru
            </a>
        </div>
    </div>

    <!-- Overload Red Alert Banner if exists -->
    @if (($summary['overload_mentors_count'] ?? 0) > 0)
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="badge bg-danger text-white rounded-circle p-2 fs-5">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-danger">Peringatan Overload: {{ $summary['overload_mentors_count'] }} Guru Membina &gt; 40 Santri</h6>
                    <span class="small text-muted">Beban mengajar yang terlalu tinggi berisiko menurunkan efektivitas halaqah dan mutu talaqqi santri.</span>
                </div>
            </div>
            <a href="{{ route('admin.mentors.availability') }}" class="btn btn-sm btn-danger rounded-pill px-3 flex-shrink-0">
                Atur Ulang Alokasi
            </a>
        </div>
    @endif

    <!-- 1. HR KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Mentor -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(30, 144, 255, 0.08) 0%, rgba(30, 144, 255, 0.02) 100%); border: 1px solid rgba(30, 144, 255, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Guru</span>
                    <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2">
                        <i class="bi bi-person-video3 fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-primary">{{ $summary['total_mentors'] }} <span class="fs-6 fw-normal text-muted">Guru</span></h3>
                <span class="small text-muted">{{ $summary['active_mentors'] }} aktif bertugas</span>
            </div>
        </div>

        <!-- Rasio Guru : Santri -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%); border: 1px solid rgba(16, 185, 129, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Rasio Guru : Santri</span>
                    <div class="badge rounded-pill bg-success bg-opacity-10 text-success p-2">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success">{{ $summary['mentor_student_ratio'] }}</h3>
                <span class="small text-muted">Rata-rata {{ $summary['ratio_value'] }} santri / mentor</span>
            </div>
        </div>

        <!-- Guru Cuti Hari Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(245, 158, 11, 0.02) 100%); border: 1px solid rgba(245, 158, 11, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Guru Cuti Hari Ini</span>
                    <div class="badge rounded-pill bg-warning bg-opacity-10 text-warning p-2">
                        <i class="bi bi-calendar-x fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-warning">{{ $summary['mentors_on_leave_today'] }} <span class="fs-6 fw-normal text-muted">Guru</span></h3>
                <span class="small text-muted">{{ $summary['mentors_on_leave_today'] > 0 ? 'Perlu konfirmasi guru pengganti' : 'Seluruh guru siap mengajar' }}</span>
            </div>
        </div>

        <!-- Santri Binaan Aktif Total -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(139, 92, 246, 0.02) 100%); border: 1px solid rgba(139, 92, 246, 0.15) !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Santri Aktif</span>
                    <div class="badge rounded-pill bg-purple bg-opacity-10 text-purple p-2" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <i class="bi bi-mortarboard-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #8b5cf6;">{{ $summary['total_active_students'] }} <span class="fs-6 fw-normal text-muted">Santri</span></h3>
                <span class="small text-muted">Terdaftar di seluruh program</span>
            </div>
        </div>
    </div>

    <!-- 2. Top Performing Mentors & Workload by Program -->
    <div class="row g-4 mb-4">
        <!-- Top Performing Mentors Showcase -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                            <i class="bi bi-trophy-fill text-warning me-2"></i>Top Performing Mentors
                        </h5>
                        <p class="text-muted small mb-0">Peringkat guru berdasarkan kehadiran sesi & ketercapaian target santri.</p>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill">Juara Kinerja</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse ($topMentors as $index => $top)
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border" style="border-color: var(--border-color) !important; background: var(--bg-secondary);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="badge rounded-circle p-2 fw-bold fs-6 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; {{ $index === 0 ? 'background: #f59e0b; color: white;' : ($index === 1 ? 'background: #94a3b8; color: white;' : ($index === 2 ? 'background: #b45309; color: white;' : 'background: rgba(13, 110, 253, 0.1); color: #0d6efd;')) }}">
                                    #{{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="fw-bold" style="color: var(--text-primary);">{{ $top['name'] }}</div>
                                    <div class="text-muted small">
                                        <span class="badge bg-light text-secondary border me-1">{{ $top['specialization'] }}</span>
                                        <i class="bi bi-star-fill text-warning"></i> {{ number_format($top['rating'], 1) }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-4 text-end">
                                <div>
                                    <div class="fw-bold text-success">{{ $top['attendance_rate'] }}%</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">Presensi Sesi</div>
                                </div>
                                <div>
                                    <div class="fw-bold text-primary">{{ $top['completed_targets_count'] }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">Target Santri</div>
                                </div>
                                <div>
                                    <div class="fw-bold" style="color: var(--text-primary);">{{ $top['active_students_count'] }}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">Santri Binaan</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">Belum ada data evaluasi performa guru.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Workload Distribution Bar Chart -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                <div class="mb-3">
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                        <i class="bi bi-bar-chart-fill text-primary me-2"></i>Beban Santri Per Program
                    </h5>
                    <p class="text-muted small mb-0">Distribusi jumlah santri binaan aktif pada masing-masing paket bimbingan.</p>
                </div>

                <div id="workloadProgramChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- 3. Workload Table (DataTables) -->
    @include('admin.staff.partials.mentor-table')
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        const chartOptions = {
            series: [{
                name: 'Santri Aktif',
                data: {!! json_encode($workloadByProgram['series']) !!}
            }],
            chart: {
                type: 'bar',
                height: 280,
                fontFamily: 'Poppins, sans-serif',
                toolbar: { show: false },
                background: 'transparent'
            },
            theme: {
                mode: isDarkMode ? 'dark' : 'light'
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    horizontal: true,
                    distributed: true,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            colors: ['#0d6efd', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
            dataLabels: {
                enabled: true,
                offsetX: -6,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            xaxis: {
                categories: {!! json_encode($workloadByProgram['labels']) !!},
                labels: {
                    style: {
                        colors: isDarkMode ? '#94a3b8' : '#64748b'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: isDarkMode ? '#94a3b8' : '#64748b'
                    }
                }
            },
            legend: { show: false },
            grid: {
                borderColor: isDarkMode ? '#334155' : '#e2e8f0'
            }
        };

        const chartElem = document.querySelector("#workloadProgramChart");
        if (chartElem) {
            const chart = new ApexCharts(chartElem, chartOptions);
            chart.render();
        }

        // Initialize DataTables if available
        if (typeof $ !== 'undefined' && $.fn.DataTable) {
            $('#staffWorkloadTable').DataTable({
                language: {
                    search: "Cari Guru:",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ guru",
                    paginate: {
                        first: "«",
                        previous: "‹",
                        next: "›",
                        last: "»"
                    }
                }
            });
        }
    });
</script>
@endpush
