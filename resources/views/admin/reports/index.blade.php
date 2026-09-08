@extends('layouts.admin')

@section('title', 'Generator & Ekspor Laporan Keuangan')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Ekspor & Rekapitulasi Laporan Keuangan
            </h3>
            <p class="text-muted small mb-0">Generator laporan transaksi akuntansi resmi dalam format Excel (.xlsx / CSV) dan PDF siap cetak.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.revenue.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Pendapatan
            </a>
        </div>
    </div>

    <!-- Filter & Generator Form Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
        <h5 class="fw-bold mb-3" style="color: var(--text-primary);">
            <i class="bi bi-sliders2 text-primary me-2"></i>Kriteria & Filter Laporan
        </h5>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
            <!-- Rentang Tanggal -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $startDate?->format('Y-m-d') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $endDate?->format('Y-m-d') }}">
            </div>

            <!-- Filter Program -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Program Belajar</label>
                <select name="program_id" class="form-select form-select-sm rounded-3">
                    <option value="">Semua Program</option>
                    @foreach ($programs as $prog)
                        <option value="{{ $prog->id }}" {{ (string) $programId === (string) $prog->id ? 'selected' : '' }}>
                            {{ $prog->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Status Pembayaran</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Hanya Lunas (Paid)</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Hanya Pending</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top" style="border-color: var(--border-color) !important;">
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                        <i class="bi bi-search me-1"></i>Pratinjau Data
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                    </a>
                </div>

                <!-- Export Download Buttons -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Excel / CSV Export -->
                    <a href="{{ route('admin.reports.export-excel', request()->all()) }}" class="btn btn-sm btn-success rounded-pill px-3">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i>Ekspor Excel / CSV
                    </a>

                    <!-- PDF Official Export -->
                    <a href="{{ route('admin.reports.export-pdf', request()->all()) }}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>Cetak / Unduh PDF Resmi
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Box & Results Table -->
    <div class="card border-0 shadow-sm rounded-4 p-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-0" style="color: var(--text-primary);">
                    <i class="bi bi-list-check text-primary me-2"></i>Hasil Pratinjau Transaksi
                </h5>
                <span class="text-muted small">Menampilkan {{ $payments->total() }} data transaksi yang cocok dengan kriteria filter.</span>
            </div>

            <div class="badge bg-success bg-opacity-10 text-success p-2 fs-6 rounded-pill px-3">
                Total Nominal: <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th>Invoice</th>
                        <th>Santri & Wali</th>
                        <th>Program</th>
                        <th>Tipe Tagihan</th>
                        <th>Metode</th>
                        <th>Tanggal Bayar</th>
                        <th>Status</th>
                        <th class="text-end">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>
                                <span class="fw-bold font-monospace text-primary">#{{ $payment->invoice_number }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $payment->student?->getDisplayName() ?? '-' }}</div>
                                <div class="text-muted small">Wali: {{ $payment->student?->parent_name ?? '-' }} ({{ $payment->student?->getParentPhone() ?? '-' }})</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">{{ $payment->program?->name ?? 'Pendaftaran' }}</span>
                            </td>
                            <td>
                                <span class="small">{{ ucfirst($payment->payment_purpose ?? 'SPP Bulanan') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ strtoupper($payment->payment_method ?? 'Online') }}</span>
                            </td>
                            <td class="small text-muted">
                                {{ $payment->payment_date ? $payment->payment_date->translatedFormat('d/m/Y H:i') : '-' }}
                            </td>
                            <td>
                                @if ($payment->status === 'paid')
                                    <span class="badge bg-success bg-opacity-10 text-success">LUNAS</span>
                                @elseif ($payment->status === 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning">PENDING</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">{{ strtoupper($payment->status) }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Tidak ditemukan data transaksi yang sesuai dengan filter tanggal atau program di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
