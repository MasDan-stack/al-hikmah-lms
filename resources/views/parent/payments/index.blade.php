@extends('layouts.parent')

@section('title', 'Tagihan & SPP Aktif | AL-HIKMAH')
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
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 text-center mb-4">
            <i class="bi bi-check-circle-fill fs-1 text-success mb-2"></i>
            <h5 class="fw-bold text-dark mb-1">Tidak Ada Tagihan Pending!</h5>
            <p class="text-muted small mb-0">Seluruh tagihan pendaftaran dan SPP anak Anda telah diselesaikan.</p>
        </div>
    @else
        <div class="row g-4 mb-4">
            @foreach($pendingPayments as $p)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <span class="fw-bold text-secondary small">#{{ $p->invoice_number ?? ('INV-' . $p->id) }}</span>
                            <span class="badge bg-warning-subtle text-warning rounded-pill px-3">PENDING</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $p->student?->getDisplayName() }}</h5>
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

    <!-- Informasi SPP Siklus Berikutnya -->
    @if(isset($activeEnrollments) && $activeEnrollments->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-check me-2 text-info"></i>Informasi Tagihan SPP Siklus Berikutnya</h5>
            <div class="row g-3">
                @foreach($activeEnrollments as $enr)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border rounded-3 p-3 bg-light h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info-subtle text-info rounded-pill px-3">Bulan Depan</span>
                                <span class="badge bg-success-subtle text-success rounded-pill">Aktif</span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $enr->student?->getDisplayName() }}</h6>
                            <p class="small text-muted mb-2">Program: {{ $enr->program?->name }}</p>
                            <div class="p-2 bg-white rounded border mb-2">
                                <span class="small d-block text-muted">Estimasi Biaya SPP Bulanan:</span>
                                <strong class="text-success fs-5">{{ $enr->formatted_price }}</strong>
                                <span class="small d-block text-muted opacity-75">(Tanpa Biaya Pendaftaran 1x)</span>
                            </div>
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Invoice SPP rutin akan diterbitkan oleh sistem 7 hari sebelum awal bulan berikutnya.</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
