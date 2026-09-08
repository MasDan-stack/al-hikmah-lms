@extends('layouts.parent')

@section('title', 'Riwayat Pembayaran')
@section('header', 'Riwayat Pembayaran Lunas')
@section('subheader', 'Histori transaksi pembayaran pendaftaran & SPP anak Anda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history text-success me-2"></i>Histori Pembayaran</h4>
            <p class="text-muted small mb-0">Daftar seluruh transaksi yang telah berhasil terverifikasi.</p>
        </div>
        <a href="{{ route('parent.payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Tagihan Aktif
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        @if($paidPayments->isEmpty())
            <div class="text-center py-4 text-muted">
                Belum ada riwayat transaksi pembayaran lunas.
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle table-hover datatable" id="tableParentPaymentHistory">
                    <thead class="table-light">
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal Lunas</th>
                            <th>Santri</th>
                            <th>Program / Item</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th class="no-sort">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paidPayments as $p)
                            <tr>
                                <td class="fw-bold text-primary">#{{ $p->invoice_number ?? ('INV-' . $p->id) }}</td>
                                <td>{{ $p->payment_date ? $p->payment_date->format('d/m/Y H:i') : $p->updated_at->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $p->student?->user?->name ?? $p->student?->full_name }}</td>
                                <td>{{ $p->program?->name ?? 'SPP Bimbingan' }}</td>
                                <td><span class="badge bg-success-subtle text-success rounded-pill">{{ $p->payment_method ?? 'Pembayaran Digital' }}</span></td>
                                <td class="fw-bold text-dark">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('parent.payments.download', $p->id) }}" class="btn btn-sm btn-outline-dark rounded-pill" target="_blank">
                                        <i class="bi bi-download me-1"></i> Invoice PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $paidPayments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
