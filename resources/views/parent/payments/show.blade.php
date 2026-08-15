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
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2">Item Pembayaran</th>
                                <th class="text-end py-2" style="width: 160px;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- 1. Paket Program Belajar -->
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">Paket Belajar: {{ $payment->program?->name ?? 'Program Bimbingan' }}</div>
                                    <small class="text-muted">Investasi kurikulum dan bimbingan mentor terstruktur</small>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp {{ number_format($payment->program_fee > 0 ? $payment->program_fee : $payment->amount, 0, ',', '.') }}
                                </td>
                            </tr>

                            <!-- 2. Biaya Pendaftaran 1x (Jika Ada) -->
                            @if($payment->registration_fee > 0)
                            <tr class="table-warning-subtle">
                                <td class="py-3">
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <span>Biaya Pendaftaran Santri Baru</span>
                                        <span class="badge bg-primary text-white rounded-pill px-2 py-1 small">1x Sekali Bayar</span>
                                    </div>
                                    <small class="text-secondary">Administrasi pendaftaran awal, pembuatan akun & assessment santri</small>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rp {{ number_format($payment->registration_fee, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td class="text-end fw-bold fs-6">TOTAL PEMBAYARAN:</td>
                                <td class="text-end fw-bold fs-5 text-success">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($payment->registration_fee > 0)
                <div class="alert alert-info border-0 rounded-3 d-flex align-items-start gap-2 mb-0 py-2 px-3">
                    <i class="bi bi-info-circle-fill text-info fs-5 mt-1"></i>
                    <div class="small text-dark">
                        <strong>Informasi Biaya Pendaftaran:</strong> Biaya pendaftaran Rp {{ number_format($payment->registration_fee, 0, ',', '.') }} hanya dibebankan <strong>1 kali</strong> saat pertama kali santri mendaftar di AL-HIKMAH LMS. Pendaftaran program selanjutnya untuk santri ini tidak akan dikenakan biaya registrasi lagi.
                    </div>
                </div>
                @endif

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
