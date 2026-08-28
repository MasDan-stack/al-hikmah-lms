<div class="row g-3 mb-4">
    <!-- Total Pendapatan -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(30, 144, 255, 0.08) 0%, rgba(30, 144, 255, 0.02) 100%); border: 1px solid rgba(30, 144, 255, 0.15) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Pendapatan</span>
                <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary p-2">
                    <i class="bi bi-wallet2 fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-primary">Rp {{ number_format($metrics['total_revenue'] ?? 0, 0, ',', '.') }}</h3>
            <div class="d-flex align-items-center gap-1 small text-muted">
                <i class="bi bi-check-circle-fill text-success"></i>
                <span>{{ number_format($metrics['total_paid_invoices'] ?? 0) }} transaksi lunas</span>
            </div>
        </div>
    </div>

    <!-- Pendapatan Bulan Ini & MoM Growth -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%); border: 1px solid rgba(16, 185, 129, 0.15) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Bulan Ini ({{ now()->translatedFormat('F') }})</span>
                <div class="badge rounded-pill bg-success bg-opacity-10 text-success p-2">
                    <i class="bi bi-graph-up-arrow fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-success">Rp {{ number_format($metrics['this_month_revenue'] ?? 0, 0, ',', '.') }}</h3>
            <div class="d-flex align-items-center gap-2 small">
                @if (($metrics['mom_growth_percent'] ?? 0) >= 0)
                    <span class="badge bg-success text-white px-2 py-1 rounded-pill">
                        <i class="bi bi-arrow-up-right"></i> +{{ $metrics['mom_growth_percent'] }}% MoM
                    </span>
                @else
                    <span class="badge bg-danger text-white px-2 py-1 rounded-pill">
                        <i class="bi bi-arrow-down-right"></i> {{ $metrics['mom_growth_percent'] }}% MoM
                    </span>
                @endif
                <span class="text-muted">vs lalu (Rp {{ number_format($metrics['last_month_revenue'] ?? 0, 0, ',', '.') }})</span>
            </div>
        </div>
    </div>

    <!-- ARPU (Average Revenue Per User) -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(139, 92, 246, 0.02) 100%); border: 1px solid rgba(139, 92, 246, 0.15) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Rata-rata / Santri (ARPU)</span>
                <div class="badge rounded-pill bg-purple bg-opacity-10 text-purple p-2" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1" style="color: #8b5cf6;">Rp {{ number_format($metrics['arpu'] ?? 0, 0, ',', '.') }}</h3>
            <div class="d-flex align-items-center gap-1 small text-muted">
                <i class="bi bi-person-check"></i>
                <span>Dari {{ number_format($metrics['active_students_count'] ?? 0) }} santri binaan aktif</span>
            </div>
        </div>
    </div>

    <!-- Tagihan Tertunda & Overdue -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.08) 0%, rgba(239, 68, 68, 0.02) 100%); border: 1px solid rgba(239, 68, 68, 0.15) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Tagihan Overdue</span>
                <div class="badge rounded-pill bg-danger bg-opacity-10 text-danger p-2">
                    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-danger">Rp {{ number_format($metrics['overdue_invoices_amount'] ?? 0, 0, ',', '.') }}</h3>
            <div class="d-flex align-items-center gap-2 small text-muted">
                <span class="badge bg-danger bg-opacity-10 text-danger">{{ $metrics['overdue_invoices_count'] ?? 0 }} invoice lewat tempo</span>
                <span>({{ $metrics['pending_invoices_count'] ?? 0 }} pending)</span>
            </div>
        </div>
    </div>
</div>
