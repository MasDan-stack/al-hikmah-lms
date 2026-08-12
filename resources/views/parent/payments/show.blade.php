@extends('layouts.parent')

@section('title', 'Detail Invoice Tagihan')
@section('header', 'Detail Invoice Tagihan')
@section('subheader', 'Informasi rincian tagihan dan pembayaran online Midtrans')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Invoice #{{ $payment->invoice_number ?? ('INV-' . $payment->id) }}</h4>
        <a href="{{ route('parent.payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">Rincian Tagihan</h5>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">SANTRI:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $payment->student?->user?->name ?? $payment->student?->full_name }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">PROGRAM / KELAS:</div>
                    <div class="fw-semibold text-dark">{{ $payment->program?->name ?? 'SPP Bimbingan Al-Qur\'an' }}</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small fw-bold">STATUS PEMBAYARAN:</div>
                    @if($payment->status === 'paid')
                        <span class="badge bg-success fs-6 rounded-pill px-3 py-1">LUNAS</span>
                    @else
                        <span class="badge bg-warning fs-6 rounded-pill px-3 py-1 text-dark">PENDING / BELUM DIBAYAR</span>
                    @endif
                </div>

                <div class="p-4 bg-light rounded-4 mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-secondary">TOTAL PEMBAYARAN:</span>
                        <span class="fs-3 fw-bold text-success">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark border-bottom pb-3 mb-3"><i class="bi bi-credit-card-fill text-success me-2"></i>Bayar Online</h5>

                @if($payment->status === 'paid')
                    <div class="alert alert-success rounded-3 mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i>Tagihan ini sudah lunas pada {{ $payment->payment_date ? $payment->payment_date->format('d M Y H:i') : '' }}.
                    </div>
                    <a href="{{ route('parent.payments.download', $payment->id) }}" class="btn btn-dark rounded-pill w-100 fw-bold" target="_blank">
                        <i class="bi bi-download me-1"></i> Unduh Invoice PDF
                    </a>
                @else
                    <p class="text-muted small mb-3">Pilih metode pembayaran online yang Anda inginkan (Midtrans Payment Gateway):</p>

                    <form action="{{ route('parent.payments.pay', $payment->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Metode Pembayaran *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Midtrans QRIS (Gopay/OVO/ShopeePay)">QRIS (Gopay / OVO / ShopeePay / Dana)</option>
                                <option value="Midtrans Bank Transfer (BCA/Mandiri/BRI)">Bank Transfer / Virtual Account</option>
                                <option value="Midtrans Credit Card">Kartu Kredit / Debit Online</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold shadow-sm py-2">
                            <i class="bi bi-shield-check me-1"></i> Selesaikan Pembayaran Sekarang
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
