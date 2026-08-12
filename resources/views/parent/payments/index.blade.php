@extends('layouts.parent')

@section('title', 'Tagihan Aktif')
@section('header', 'Tagihan & SPP Aktif')
@section('subheader', 'Daftar tagihan pendaftaran dan SPP yang perlu diselesaikan')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-wallet2 text-danger me-2"></i>Tagihan Belum Lunas</h4>
            <p class="text-muted small mb-0">Selesaikan pembayaran untuk kelancaran bimbingan Al-Qur'an ananda.</p>
        </div>
        <a href="{{ route('parent.payments.history') }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">
            <i class="bi bi-clock-history me-1"></i> Riwayat Pembayaran Lunas
        </a>
    </div>

    @if($pendingPayments->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="bi bi-check-circle-fill fs-1 text-success mb-3"></i>
            <h5 class="fw-bold text-dark">Tidak Ada Tagihan Pending!</h5>
            <p class="text-muted small mb-0">Seluruh tagihan SPP dan pendaftaran anak Anda telah diselesaikan.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($pendingPayments as $p)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <span class="fw-bold text-secondary small">#{{ $p->invoice_number ?? ('INV-' . $p->id) }}</span>
                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3">PENDING</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $p->student?->user?->name ?? $p->student?->full_name }}</h5>
                        <div class="small text-muted mb-3">Program: {{ $p->program?->name ?? 'SPP Bimbingan' }}</div>

                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-muted fw-bold">TOTAL TAGIHAN:</span>
                                <span class="badge bg-danger-subtle text-danger rounded-pill"><i class="bi bi-calendar-event me-1"></i>Jatuh Tempo: {{ $p->due_date ? $p->due_date->format('d M Y') : '15 ' . now()->translatedFormat('F Y') }}</span>
                            </div>
                            <div class="fs-4 fw-bold text-danger">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('parent.payments.show', $p->id) }}" class="btn btn-success rounded-pill fw-bold">
                                <i class="bi bi-credit-card me-1"></i> Bayar Online (Midtrans)
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
