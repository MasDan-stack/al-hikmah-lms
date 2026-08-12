<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $payment->invoice_number ?? ('INV-' . $payment->id) }} - AL-HIKMAH LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; color: #333; }
        .invoice-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .invoice-card { border: none; }
        }
    </style>
</head>
<body class="py-4">
    <div class="container my-3">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('parent.payments.show', $payment->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-printer-fill me-2"></i> Cetak / Download Invoice PDF
            </button>
        </div>

        <div class="invoice-card p-5 shadow-sm">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4">
                <div>
                    <h2 class="fw-bold text-success mb-1">AL-HIKMAH LMS</h2>
                    <h6 class="text-secondary mb-0">INVOICE BUKTI PEMBAYARAN SPP</h6>
                </div>
                <div class="text-end">
                    <div class="fs-5 fw-bold text-dark">#{{ $payment->invoice_number ?? ('INV-' . $payment->id) }}</div>
                    <small class="text-muted">Tanggal: {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : now()->format('d M Y') }}</small>
                </div>
            </div>

            <div class="row mb-4 bg-light p-3 rounded-3">
                <div class="col-6">
                    <small class="text-muted fw-bold">DITERIMA DARI / WALI SANTRI:</small>
                    <div class="fw-bold text-dark fs-6">{{ auth()->user()->name }}</div>
                    <div class="small text-secondary">{{ auth()->user()->email }}</div>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted fw-bold">SANTRI BINAAN:</small>
                    <div class="fw-bold text-dark fs-6">{{ $payment->student?->user?->name ?? $payment->student?->full_name }}</div>
                </div>
            </div>

            <table class="table table-bordered align-middle my-4">
                <thead class="table-light">
                    <tr>
                        <th>Deskripsi Pembayaran</th>
                        <th>Metode Pembayaran</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $payment->program?->name ?? 'Biaya SPP & Bimbingan Al-Qur\'an' }}</div>
                            <small class="text-muted">Status: {{ strtoupper($payment->status) }}</small>
                        </td>
                        <td>{{ $payment->payment_method ?? 'Midtrans Digital Payment' }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">TOTAL LUNAS:</th>
                        <th class="text-end fs-5 fw-bold text-success">Rp {{ number_format($payment->amount, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="row mt-5 pt-4 border-top text-center">
                <div class="col-6">
                    <small class="text-muted">Pembayar / Orang Tua,</small>
                    <div class="fw-bold mt-1 mb-5">{{ auth()->user()->name }}</div>
                    <div>( __________________________ )</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Bendahara AL-HIKMAH,</small>
                    <div class="fw-bold mt-1 mb-5">Admin Keuangan</div>
                    <div>( __________________________ )</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
