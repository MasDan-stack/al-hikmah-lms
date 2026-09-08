<div class="row g-4 mb-4">
    <!-- Chart 1: Tren Pendapatan 12 Bulan (Area & Line) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                        <i class="bi bi-graph-up text-primary me-2"></i>Tren Pendapatan 12 Bulan Terakhir
                    </h5>
                    <p class="text-muted small mb-0">Visualisasi arus kas bulanan beserta perbandingan tahun sebelumnya.</p>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Chart Period Filters">
                    <button type="button" class="btn btn-outline-primary active" id="btnPeriod12M" onclick="filterChartPeriod(12)">12 Bulan</button>
                    <button type="button" class="btn btn-outline-primary" id="btnPeriod6M" onclick="filterChartPeriod(6)">6 Bulan</button>
                </div>
            </div>

            <!-- Chart Container with Loading Skeleton -->
            <div id="revenueChartSkeleton" class="text-center py-5">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Memuat data grafik...</span>
                </div>
                <div class="text-muted small">Mengambil analitik data dari server...</div>
            </div>
            <div id="revenue12MonthsChart" style="min-height: 350px;"></div>
        </div>
    </div>

    <!-- Chart 2: Breakdown Pendapatan Per Program (Donut Chart) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
            <div class="mb-3">
                <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                    <i class="bi bi-pie-chart-fill text-success me-2"></i>Komposisi Program
                </h5>
                <p class="text-muted small mb-0">Distribusi pendapatan berdasarkan program bimbingan.</p>
            </div>

            <!-- Donut Chart Container -->
            <div id="programChartSkeleton" class="text-center py-5">
                <div class="spinner-border text-success mb-2" role="status">
                    <span class="visually-hidden">Memuat diagram...</span>
                </div>
            </div>
            <div id="programDonutChart" style="min-height: 280px;"></div>

            <!-- List Subtotal Program -->
            <div class="mt-3 pt-3 border-top" style="border-color: var(--border-color) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold">Program</span>
                    <span class="text-muted small fw-semibold">Pendapatan</span>
                </div>
                <div class="d-flex flex-column gap-2" style="max-height: 180px; overflow-y: auto;">
                    @forelse ($programBreakdown['details'] ?? [] as $prog)
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="d-flex align-items-center gap-2 text-truncate" style="max-width: 160px;">
                                <span class="badge rounded-circle p-1" style="background-color: var(--primary);"></span>
                                <span class="fw-medium text-truncate">{{ $prog['name'] }}</span>
                            </span>
                            <span class="fw-bold text-end">Rp {{ number_format($prog['revenue'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-muted small text-center py-2">Belum ada transaksi program.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let revenueChartInstance = null;
    let originalChartData = null;

    document.addEventListener('DOMContentLoaded', function () {
        const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        // 1. Fetch & Initialize 12 Months Revenue Trend
        fetch("{{ route('api.analytics.revenue-trend') }}")
            .then(res => res.json())
            .then(response => {
                const skeleton = document.getElementById('revenueChartSkeleton');
                if (skeleton) skeleton.style.display = 'none';

                if (response.status === 'success' && response.data) {
                    originalChartData = response.data;
                    renderRevenueTrendChart(response.data, isDarkMode);
                }
            })
            .catch(err => {
                console.error('Failed to load revenue trend chart:', err);
                const skeleton = document.getElementById('revenueChartSkeleton');
                if (skeleton) {
                    skeleton.innerHTML = '<div class="alert alert-warning py-2 mb-0 small">Gagal memuat grafik analitik. Silakan refresh halaman.</div>';
                }
            });

        // 2. Fetch & Initialize Program Donut Chart
        fetch("{{ route('api.analytics.program-breakdown') }}")
            .then(res => res.json())
            .then(response => {
                const skeleton = document.getElementById('programChartSkeleton');
                if (skeleton) skeleton.style.display = 'none';

                if (response.status === 'success' && response.data) {
                    renderProgramDonutChart(response.data, isDarkMode);
                }
            })
            .catch(err => {
                console.error('Failed to load program donut chart:', err);
            });
    });

    function renderRevenueTrendChart(data, isDarkMode) {
        const options = {
            series: data.series,
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: false,
                        reset: true
                    }
                },
                fontFamily: 'Poppins, sans-serif',
                background: 'transparent'
            },
            theme: {
                mode: isDarkMode ? 'dark' : 'light'
            },
            colors: ['#0d6efd', '#94a3b8'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: [3, 2],
                dashArray: [0, 5]
            },
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: data.categories,
                labels: {
                    style: {
                        colors: isDarkMode ? '#94a3b8' : '#64748b',
                        fontSize: '12px'
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        if (val >= 1000000) {
                            return 'Rp ' + (val / 1000000).toFixed(1) + ' Jt';
                        } else if (val >= 1000) {
                            return 'Rp ' + (val / 1000).toFixed(0) + ' Rb';
                        }
                        return 'Rp ' + val;
                    },
                    style: {
                        colors: isDarkMode ? '#94a3b8' : '#64748b'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            grid: {
                borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                strokeDashArray: 4
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        const chartElement = document.querySelector("#revenue12MonthsChart");
        if (chartElement) {
            revenueChartInstance = new ApexCharts(chartElement, options);
            revenueChartInstance.render();
        }
    }

    function renderProgramDonutChart(data, isDarkMode) {
        const hasData = data.series && data.series.length > 0 && data.series.some(s => s > 0);
        
        const options = {
            series: hasData ? data.series : [1],
            labels: hasData ? data.labels : ['Belum Ada Data'],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Poppins, sans-serif',
                background: 'transparent'
            },
            theme: {
                mode: isDarkMode ? 'dark' : 'light'
            },
            colors: ['#0d6efd', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'],
            dataLabels: {
                enabled: hasData
            },
            legend: {
                position: 'bottom',
                fontSize: '12px'
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return hasData ? 'Rp ' + new Intl.NumberFormat('id-ID').format(val) : '-';
                    }
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    if (total >= 1000000) {
                                        return 'Rp ' + (total / 1000000).toFixed(1) + ' Jt';
                                    }
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                                }
                            }
                        }
                    }
                }
            }
        };

        const chartElement = document.querySelector("#programDonutChart");
        if (chartElement) {
            const chart = new ApexCharts(chartElement, options);
            chart.render();
        }
    }

    function filterChartPeriod(months) {
        document.getElementById('btnPeriod12M').classList.toggle('active', months === 12);
        document.getElementById('btnPeriod6M').classList.toggle('active', months === 6);

        if (!revenueChartInstance || !originalChartData) return;

        if (months === 6) {
            const slicedCategories = originalChartData.categories.slice(-6);
            const slicedSeries = originalChartData.series.map(s => ({
                name: s.name,
                type: s.type,
                data: s.data.slice(-6)
            }));
            revenueChartInstance.updateOptions({
                xaxis: { categories: slicedCategories }
            });
            revenueChartInstance.updateSeries(slicedSeries);
        } else {
            revenueChartInstance.updateOptions({
                xaxis: { categories: originalChartData.categories }
            });
            revenueChartInstance.updateSeries(originalChartData.series);
        }
    }
</script>
@endpush
