@extends('layouts.parent')

@section('title', 'Detail Invoice #' . ($payment->invoice_number ?? 'INV-' . $payment->id))
@section('header', 'Detail Invoice Tagihan')
@section('subheader', 'Informasi rincian tagihan dan metode pembayaran online otomatis')

@section('content')
    <div class="container-fluid p-0">
        <!-- Header Navigation -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="bi bi-receipt-cutoff text-success me-2"></i>Invoice
                        #{{ $payment->invoice_number ?? 'INV-' . $payment->id }}
                    </h4>
                    @if ($payment->status === 'paid')
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> LUNAS
                        </span>
                    @elseif(!empty($payment->pakasir_order_id))
                        <span
                            class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-1">
                            <span class="spinner-grow spinner-grow-sm text-warning" style="width: 0.6rem; height: 0.6rem;"
                                role="status"></span>
                            MENUNGGU PEMBAYARAN
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 py-1 fw-bold">
                            <i class="bi bi-clock-history me-1"></i> BELUM DIBAYAR
                        </span>
                    @endif
                </div>
                <small class="text-muted">Diterbitkan pada
                    {{ $payment->created_at ? $payment->created_at->format('d M Y, H:i') : '-' }} WIB</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('parent.payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Tagihan
                </a>
                @if ($payment->status === 'paid')
                    <a href="{{ route('parent.payments.download', $payment->id) }}"
                        class="btn btn-dark rounded-pill px-3 fw-bold" target="_blank">
                        <i class="bi bi-download me-1"></i> Unduh PDF
                    </a>
                @endif
            </div>
        </div>

        <!-- Alert Status -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4"
                role="alert">
                <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- ==========================================
                             KOLOM KIRI: RINCIAN TAGIHAN & SANTRI
                             ========================================== -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">
                        <i class="bi bi-person-vcard text-primary me-2"></i>Informasi Santri & Program
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">Nama Santri:</small>
                                <div class="fw-bold text-dark fs-6">{{ $payment->student?->user?->name ?? 'Santri' }}</div>
                                <small class="text-secondary">NIS: {{ $payment->student?->nis ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted d-block mb-1">Program Belajar:</small>
                                <div class="fw-bold text-dark fs-6">{{ $payment->program?->name ?? 'Program Bimbingan' }}
                                </div>
                                <small class="text-success fw-semibold"><i class="bi bi-journal-check me-1"></i>4
                                    Sesi/Bulan</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-card-checklist text-success me-2"></i>Rincian
                        Komponen Biaya</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 px-3">Item Pembayaran</th>
                                    <th class="text-end py-2 px-3" style="width: 160px;">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 1. Paket Program Belajar -->
                                <tr>
                                    <td class="py-3 px-3">
                                        <div class="fw-bold text-dark">Paket Belajar:
                                            {{ $payment->program?->name ?? 'Program Bimbingan' }}</div>
                                        <small class="text-muted">Investasi kurikulum, modul pembelajaran, dan bimbingan
                                            mentor 1-on-1</small>
                                    </td>
                                    <td class="text-end fw-bold text-dark px-3">
                                        Rp
                                        {{ number_format($payment->program_fee > 0 ? $payment->program_fee : $payment->amount, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <!-- 2. Biaya Pendaftaran 1x (Jika Ada) -->
                                @if ($payment->registration_fee > 0)
                                    <tr class="table-warning-subtle">
                                        <td class="py-3 px-3">
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <span>Biaya Pendaftaran Santri Baru</span>
                                                <span class="badge bg-primary text-white rounded-pill px-2 py-0 small"
                                                    style="font-size: 0.7rem;">1x Awal Pendaftaran</span>
                                            </div>
                                            <small class="text-secondary">Administrasi berkas santri, akun portal
                                                pembelajaran & assessment awal</small>
                                        </td>
                                        <td class="text-end fw-bold text-dark px-3">
                                            Rp {{ number_format($payment->registration_fee, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif

                                <!-- 3. Biaya Layanan Gateway (Jika ada transaksi aktif) -->
                                @if (($payment->admin_fee ?? 0) > 0)
                                    <tr class="table-light">
                                        <td class="py-2 px-3">
                                            <div class="fw-semibold text-secondary">Biaya Layanan Gateway
                                                ({{ $payment->payment_method ?? 'Payment Gateway' }})</div>
                                            <small class="text-muted">Biaya administrasi pemrosesan otomatis payment
                                                gateway</small>
                                        </td>
                                        <td class="text-end fw-semibold text-secondary px-3">
                                            Rp {{ number_format($payment->admin_fee, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="text-end fw-bold fs-6 py-3 px-3">TOTAL PEMBAYARAN:</td>
                                    <td class="text-end fw-bold fs-5 text-success py-3 px-3">
                                        Rp {{ number_format($payment->total_amount ?? $payment->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($payment->registration_fee > 0)
                        <div class="alert alert-info border-0 rounded-3 d-flex align-items-start gap-2 mb-0 py-2 px-3">
                            <i class="bi bi-info-circle-fill text-info fs-5 mt-1"></i>
                            <div class="small text-dark">
                                <strong>Informasi Biaya Pendaftaran:</strong> Biaya pendaftaran Rp
                                {{ number_format($payment->registration_fee, 0, ',', '.') }} hanya dibebankan <strong>1
                                    kali</strong> saat santri pertama kali terdaftar. Pembayaran SPP bulan-bulan berikutnya
                                tidak akan dikenakan biaya registrasi lagi.
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Petunjuk Bantuan -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success-subtle text-success rounded-circle p-3 d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-headset fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Butuh Bantuan Pembayaran?</h6>
                            <p class="text-muted small mb-0">Jika mengalami kendala transaksi atau konfirmasi, hubungi
                                Layanan Administrasi Al-Hikmah.</p>
                        </div>
                        <div class="ms-auto">
                            <a href="https://wa.me/{{ env('WHATSAPP_NUMBER', '6285786689008') }}?text={{ urlencode('Halo Admin Al-Hikmah, saya ingin bertanya mengenai tagihan invoice #' . ($payment->invoice_number ?? $payment->id)) }}"
                                target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">
                                <i class="bi bi-whatsapp me-1"></i> Chat Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                             KOLOM KANAN: PANEL PEMBAYARAN INTERAKTIF
                             ========================================== -->
            <div class="col-lg-5">

                <!-- ----------------------------------------------------
                                 KONDISI 1: SUDAH LUNAS (PAID)
                                 ---------------------------------------------------- -->
                @if ($payment->status === 'paid')
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 text-center">
                        <div class="my-auto py-4">
                            <div class="bg-success-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-success mb-2">Alhamdulillah, Pembayaran Lunas!</h4>
                            <p class="text-muted small mb-4">
                                Pembayaran tagihan telah diverifikasi oleh sistem pada:<br>
                                <strong
                                    class="text-dark">{{ $payment->payment_date ? $payment->payment_date->format('d F Y - H:i') : '-' }}
                                    WIB</strong>
                            </p>

                            <div class="bg-light rounded-4 p-3 mb-4 text-start">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Metode Pembayaran:</span>
                                    <span
                                        class="fw-bold text-dark small">{{ $payment->payment_method ?? 'Pembayaran Digital / QRIS' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">No. Referensi / Order ID:</span>
                                    <span
                                        class="font-monospace fw-bold text-primary small">{{ $payment->pakasir_order_id ?? $payment->invoice_number }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Total Terbayar:</span>
                                    <span class="fw-bold text-success fs-6">Rp
                                        {{ number_format($payment->total_amount ?? $payment->amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('parent.schedules.list') }}"
                                    class="btn btn-success rounded-pill fw-bold py-2 shadow-sm">
                                    <i class="bi bi-calendar2-check-fill me-1"></i> Buka Jadwal & Sesi Belajar Ananda
                                </a>
                                <a href="{{ route('parent.payments.download', $payment->id) }}"
                                    class="btn btn-outline-dark rounded-pill fw-semibold py-2" target="_blank">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Unduh Kuitansi Resmi PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ----------------------------------------------------
                                 KONDISI 2: PEMBAYARAN SEDANG BERLANGSUNG (IN PROGRESS)
                                 ---------------------------------------------------- -->
                @elseif(!empty($payment->pakasir_order_id))
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100" id="inProgressCard">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-qr-code-scan text-success me-2"></i>Selesaikan Pembayaran
                            </h5>
                            <span
                                class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1 small fw-bold">
                                <i class="bi bi-clock-history me-1"></i> Menunggu Transfer
                            </span>
                        </div>

                        <!-- Live Auto-Detection Radar Box -->
                        <div
                            class="alert alert-success-subtle border border-success-subtle rounded-3 p-3 mb-4 d-flex align-items-center gap-3">
                            <div class="spinner-border spinner-border-sm text-success flex-shrink-0" role="status"></div>
                            <div class="small text-dark">
                                <strong>Mendeteksi Pembayaran Otomatis:</strong> Halaman akan otomatis memperbarui status
                                segera setelah Anda menyelesaikan transfer. Tidak perlu kirim bukti struk!
                            </div>
                        </div>

                        <!-- Total Amount Box -->
                        <div class="text-center bg-light rounded-4 p-3 mb-4">
                            <small class="text-muted text-uppercase fw-semibold d-block mb-1">Total Yang Harus
                                Dibayar:</small>
                            <div class="fw-bold text-success fs-3 mb-2" id="displayAmount">
                                Rp {{ number_format($payment->total_amount ?? $payment->amount, 0, ',', '.') }}
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 copy-btn"
                                data-clipboard-text="{{ (int) ($payment->total_amount ?? $payment->amount) }}">
                                <i class="bi bi-copy me-1"></i> Salin Nominal
                            </button>
                        </div>

                        @php
                            $isQris =
                                str_contains(strtolower($payment->payment_method ?? ''), 'qris') ||
                                !empty($payment->qr_content);
                        @endphp

                        <!-- SUB-TAMPILAN 2A: METODE QRIS -->
                        @if ($isQris)
                            <div class="text-center mb-4">
                                <div
                                    class="p-3 bg-white border border-2 border-success-subtle rounded-4 d-inline-block shadow-sm mb-3">
                                    @php
                                        $qrData =
                                            $payment->qr_content ??
                                            ($payment->checkout_url ?? $payment->pakasir_order_id);
                                        $qrApiUrl =
                                            'https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=10&data=' .
                                            urlencode($qrData);
                                    @endphp
                                    <img src="{{ $qrApiUrl }}" alt="QRIS Al-Hikmah" class="img-fluid rounded-3"
                                        style="max-width: 220px; height: auto;" id="qrisImage">
                                </div>
                                <div class="d-flex justify-content-center gap-2 mb-3">
                                    <span class="badge bg-light text-dark border px-2 py-1"><i
                                            class="bi bi-wallet2 text-primary me-1"></i>GoPay</span>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i
                                            class="bi bi-wallet2 text-success me-1"></i>OVO</span>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i
                                            class="bi bi-wallet2 text-info me-1"></i>Dana</span>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i
                                            class="bi bi-wallet2 text-danger me-1"></i>ShopeePay</span>
                                    <span class="badge bg-light text-dark border px-2 py-1"><i
                                            class="bi bi-bank text-dark me-1"></i>BCA/Livin/BRImo</span>
                                </div>

                                <div class="accordion accordion-flush text-start border rounded-3 mb-3"
                                    id="accordionQrisGuide">
                                    <div class="accordion-item rounded-3">
                                        <h2 class="accordion-header" id="headingQris">
                                            <button class="accordion-button collapsed py-2 small fw-semibold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseQris">
                                                <i class="bi bi-info-circle text-success me-2"></i> Cara Pembayaran via
                                                QRIS
                                            </button>
                                        </h2>
                                        <div id="collapseQris" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionQrisGuide">
                                            <div class="accordion-body small text-muted pt-0">
                                                <ol class="ps-3 mb-0">
                                                    <li>Buka aplikasi Mobile Banking atau E-Wallet pilihan Anda.</li>
                                                    <li>Pilih menu <strong>Scan / Bayar QRIS</strong>.</li>
                                                    <li>Arahkan kamera ke kode QR di atas (atau screenshot lalu upload).
                                                    </li>
                                                    <li>Pastikan nominal transfer sesuai: <strong>Rp
                                                            {{ number_format($payment->total_amount ?? $payment->amount, 0, ',', '.') }}</strong>.
                                                    </li>
                                                    <li>Masukkan PIN dan konfirmasi pembayaran.</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-TAMPILAN 2B: METODE VIRTUAL ACCOUNT -->
                        @else
                            @php
                                $vaNumber =
                                    $payment->qr_content ??
                                    ($payment->gateway_response['payment']['payment_number'] ??
                                        ($payment->gateway_response['payment_number'] ?? $payment->pakasir_order_id));
                                $bankName = $payment->payment_method ?? 'Virtual Account';
                            @endphp
                            <div class="mb-4">
                                <div class="p-3 bg-light rounded-4 border mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted d-block">Metode Transfer:</small>
                                        <span
                                            class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-bank me-1"></i> {{ $bankName }}
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mb-1">Nomor Rekening / Virtual Account:</small>
                                    <div
                                        class="d-flex align-items-center justify-content-between bg-white p-3 rounded-3 border mb-2">
                                        <span class="font-monospace fw-bold fs-4 text-primary"
                                            id="vaNumberText">{{ $vaNumber }}</span>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-pill copy-btn px-3"
                                            data-clipboard-text="{{ $vaNumber }}">
                                            <i class="bi bi-copy me-1"></i> Salin No. VA
                                        </button>
                                    </div>
                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Silakan transfer tepat
                                        sejumlah <strong>Rp
                                            {{ number_format($payment->total_amount ?? $payment->amount, 0, ',', '.') }}</strong>
                                        ke nomor Virtual Account di atas.</small>
                                </div>

                                <div class="accordion accordion-flush text-start border rounded-3 mb-3"
                                    id="accordionVaGuide">
                                    <div class="accordion-item rounded-3">
                                        <h2 class="accordion-header" id="headingVa">
                                            <button class="accordion-button collapsed py-2 small fw-semibold"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseVa">
                                                <i class="bi bi-question-circle text-primary me-2"></i> Panduan Cara Bayar
                                                Virtual Account
                                            </button>
                                        </h2>
                                        <div id="collapseVa" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionVaGuide">
                                            <div class="accordion-body small text-muted pt-0">
                                                <ol class="ps-3 mb-0">
                                                    <li>Buka aplikasi Mobile Banking, Internet Banking, atau ATM bank
                                                        pilihan Anda.</li>
                                                    <li>Pilih menu <strong>Transfer / Bayar</strong> &rarr; <strong>Virtual
                                                            Account</strong>.</li>
                                                    <li>Masukkan nomor VA: <strong>{{ $vaNumber }}</strong>.</li>
                                                    <li>Pastikan detail tagihan & nominal transfer sesuai: <strong>Rp
                                                            {{ number_format($payment->total_amount ?? $payment->amount, 0, ',', '.') }}</strong>.
                                                    </li>
                                                    <li>Konfirmasi transfer dengan memasukkan PIN. Tagihan akan otomatis
                                                        terverifikasi <strong>LUNAS</strong> seketika!</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (!empty($payment->checkout_url))
                            <div class="mb-3">
                                <a href="{{ $payment->checkout_url }}" target="_blank"
                                    class="btn btn-outline-success rounded-pill w-100 fw-semibold py-2">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Pembayaran Online
                                </a>
                            </div>
                        @endif

                        <!-- Action: Ganti Metode Pembayaran -->
                        <form action="{{ route('parent.payments.cancel', $payment->id) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin membatalkan transaksi ini dan memilih metode pembayaran lain?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill w-100 py-2">
                                <i class="bi bi-arrow-repeat me-1"></i> Ganti Metode Pembayaran Lain
                            </button>
                        </form>
                    </div>

                    <!-- ----------------------------------------------------
                             KONDISI 3: BELUM MEMILIH METODE (FORM PEMILIHAN METODE)
                             ---------------------------------------------------- -->
                @else
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                        <h5 class="fw-bold text-dark border-bottom pb-3 mb-3">
                            <i class="bi bi-credit-card-2-front-fill text-success me-2"></i>Pilih Metode Pembayaran
                        </h5>

                        <p class="text-muted small mb-3">
                            Pilih saluran pembayaran otomatis online favorit Anda. Transaksi diproses secara instan & aman
                            melalui <strong>Payment Gateway Resmi</strong>.
                        </p>

                        <form action="{{ route('parent.payments.pay', $payment->id) }}" method="POST" id="formPayment">
                            @csrf

                            <div class="d-flex flex-column gap-3 mb-4">
                                <!-- 1. OPSI QRIS (REKOMENDASI) -->
                                <label
                                    class="form-check-label border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option selected-option"
                                    for="methodQris" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 payment-radio" type="radio"
                                            name="payment_method" id="methodQris" value="qris" checked
                                            data-fee-type="percent" data-fee-val="0.7">
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <span>QRIS Instant</span>
                                                <span class="badge bg-success text-white rounded-pill px-2 py-0 small"
                                                    style="font-size: 0.65rem;">Paling Praktis ⭐</span>
                                            </div>
                                            <small class="text-muted">BCA, Mandiri Livin, BRImo, BNI, GoPay, OVO, Dana,
                                                ShopeePay</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-secondary border">Fee ~0.7%</span>
                                    </div>
                                </label>

                                <!-- 2. BRI VIRTUAL ACCOUNT -->
                                <label
                                    class="form-check-label border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option"
                                    for="methodVaBri" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 payment-radio" type="radio"
                                            name="payment_method" id="methodVaBri" value="bri_va" data-fee-type="flat"
                                            data-fee-val="3500">
                                        <div>
                                            <div class="fw-bold text-dark">BRI Virtual Account (BRIVA)</div>
                                            <small class="text-muted">Transfer via BRImo, Internet Banking, atau ATM
                                                BRI</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-secondary border">+Rp 3.500</span>
                                    </div>
                                </label>

                                <!-- 3. BNI VIRTUAL ACCOUNT -->
                                <label
                                    class="form-check-label border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option"
                                    for="methodVaBni" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 payment-radio" type="radio"
                                            name="payment_method" id="methodVaBni" value="bni_va" data-fee-type="flat"
                                            data-fee-val="3500">
                                        <div>
                                            <div class="fw-bold text-dark">BNI Virtual Account</div>
                                            <small class="text-muted">Transfer via BNI Mobile Banking, Internet Banking,
                                                atau ATM BNI</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-secondary border">+Rp 3.500</span>
                                    </div>
                                </label>

                                <!-- 4. PERMATA VIRTUAL ACCOUNT -->
                                <label
                                    class="form-check-label border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option"
                                    for="methodVaPermata" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 payment-radio" type="radio"
                                            name="payment_method" id="methodVaPermata" value="permata_va"
                                            data-fee-type="flat" data-fee-val="3500">
                                        <div>
                                            <div class="fw-bold text-dark">Permata Virtual Account</div>
                                            <small class="text-muted">Transfer via PermataMobile X, ATM Permata, atau ATM
                                                Bersama</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-secondary border">+Rp 3.500</span>
                                    </div>
                                </label>

                                <!-- 5. BCA VIRTUAL ACCOUNT -->
                                <label
                                    class="form-check-label border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option"
                                    for="methodVaBca" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 payment-radio" type="radio"
                                            name="payment_method" id="methodVaBca" value="va_bca" data-fee-type="flat"
                                            data-fee-val="3500">
                                        <div>
                                            <div class="fw-bold text-dark">BCA Virtual Account</div>
                                            <small class="text-muted">Transfer via BCA Mobile, myBCA, KlikBCA, atau ATM
                                                BCA</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-secondary border">+Rp 3.500</span>
                                    </div>
                                </label>

                                <!-- 6. MANDIRI VIRTUAL ACCOUNT -->
                                <label
                                    class="form-check-label border rounded-4 p-3 d-flex align-items-center justify-content-between cursor-pointer payment-option"
                                    for="methodVaMandiri" style="cursor: pointer;">
                                    <div class="d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 payment-radio" type="radio"
                                            name="payment_method" id="methodVaMandiri" value="va_mandiri"
                                            data-fee-type="flat" data-fee-val="3500">
                                        <div>
                                            <div class="fw-bold text-dark">Mandiri Virtual Account</div>
                                            <small class="text-muted">Transfer via Livin' by Mandiri atau ATM
                                                Mandiri</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-secondary border">+Rp 3.500</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Estimasi Total Kalkulator Sederhana -->
                            <div class="bg-light rounded-4 p-3 mb-4">
                                <div class="d-flex justify-content-between text-muted small mb-1">
                                    <span>Tagihan Pokok:</span>
                                    <span class="fw-bold text-dark">Rp
                                        {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-2">
                                    <span>Estimasi Biaya Admin:</span>
                                    <span class="fw-bold text-dark" id="calcAdminFee">Rp
                                        {{ number_format(ceil($payment->amount * 0.007), 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2">
                                    <span class="fw-bold text-dark fs-6">Estimasi Total Bayar:</span>
                                    <span class="fw-bold text-success fs-5" id="calcTotalAmount">Rp
                                        {{ number_format($payment->amount + ceil($payment->amount * 0.007), 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold shadow-sm py-2"
                                id="btnSubmitPayment">
                                <i class="bi bi-shield-lock-fill me-1"></i> Selesaikan Pembayaran Sekarang
                            </button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- ==========================================
                     CELEBRATION SUCCESS MODAL (REAL-TIME SUCCESS)
                     ========================================== -->
    <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg p-4 text-center">
                <div class="modal-body p-0">
                    <div class="bg-success-subtle text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 84px; height: 84px;">
                        <i class="bi bi-check2-circle" style="font-size: 3.5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-success mb-2">Alhamdulillah! Pembayaran Berhasil</h4>
                    <p class="text-muted small mb-4">
                        Sistem telah berhasil mengonfirmasi pembayaran Anda.<br>
                        <strong>4 Sesi Kelas Belajar Ananda kini telah aktif!</strong>
                    </p>
                    <div class="spinner-border spinner-border-sm text-success mb-2" role="status"></div>
                    <p class="text-secondary small">Mengalihkan ke halaman jadwal santri dalam <span
                            id="countdownRedirect" class="fw-bold text-success">3</span> detik...</p>

                    <div class="d-grid mt-3">
                        <a href="{{ route('parent.schedules.list') }}" class="btn btn-success rounded-pill fw-bold py-2">
                            Buka Jadwal Belajar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .payment-option {
                transition: all 0.2s ease-in-out;
                border-color: #e9ecef !important;
            }

            .payment-option:hover {
                border-color: #198754 !important;
                background-color: rgba(25, 135, 84, 0.03);
            }

            .payment-option.selected-option {
                border-color: #198754 !important;
                background-color: rgba(25, 135, 84, 0.05);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const baseAmount = {{ (float) $payment->amount }};

                // 1. Radio method selection effect & calculator
                const paymentRadios = document.querySelectorAll('.payment-radio');
                const calcAdminFee = document.getElementById('calcAdminFee');
                const calcTotalAmount = document.getElementById('calcTotalAmount');

                paymentRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        document.querySelectorAll('.payment-option').forEach(opt => opt.classList
                            .remove('selected-option'));
                        this.closest('.payment-option').classList.add('selected-option');

                        const feeType = this.getAttribute('data-fee-type');
                        const feeVal = parseFloat(this.getAttribute('data-fee-val') || 0);

                        let fee = 0;
                        if (feeType === 'percent') {
                            fee = Math.ceil((baseAmount * feeVal) / 100);
                        } else {
                            fee = feeVal;
                        }

                        const total = baseAmount + fee;

                        if (calcAdminFee) {
                            calcAdminFee.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                                fee);
                        }
                        if (calcTotalAmount) {
                            calcTotalAmount.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                                total);
                        }
                    });
                });

                // 2. Clipboard Copy Buttons
                document.querySelectorAll('.copy-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const textToCopy = this.getAttribute('data-clipboard-text');
                        if (!textToCopy) return;

                        navigator.clipboard.writeText(textToCopy).then(() => {
                            const originalHtml = this.innerHTML;
                            this.innerHTML = '<i class="bi bi-check2"></i> Tersalin!';
                            this.classList.remove('btn-outline-secondary',
                                'btn-outline-primary');
                            this.classList.add('btn-success');

                            setTimeout(() => {
                                this.innerHTML = originalHtml;
                                this.classList.remove('btn-success');
                                this.classList.add('btn-outline-secondary');
                            }, 2000);
                        }).catch(err => {
                            console.error('Gagal menyalin:', err);
                        });
                    });
                });

                // 3. Real-time Status Polling (Hanya aktif jika status masih pending dan ada transaksi berjalan)
                const isPendingTransaction = @json(!empty($payment->pakasir_order_id) && $payment->status === 'pending');
                const statusUrl = @json(route('parent.payments.status', $payment->id));

                if (isPendingTransaction) {
                    let pollInterval = setInterval(() => {
                        fetch(statusUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.is_paid || data.status === 'paid') {
                                    clearInterval(pollInterval);

                                    // Show celebration modal
                                    const successModalEl = document.getElementById('paymentSuccessModal');
                                    if (successModalEl && typeof bootstrap !== 'undefined') {
                                        const modal = new bootstrap.Modal(successModalEl);
                                        modal.show();

                                        let seconds = 3;
                                        const timerSpan = document.getElementById('countdownRedirect');
                                        const countdownInterval = setInterval(() => {
                                            seconds--;
                                            if (timerSpan) timerSpan.textContent = seconds;
                                            if (seconds <= 0) {
                                                clearInterval(countdownInterval);
                                                window.location.href = data.redirect_url || window
                                                    .location.href;
                                            }
                                        }, 1000);
                                    } else {
                                        // Fallback reload
                                        window.location.reload();
                                    }
                                }
                            })
                            .catch(err => console.log('Polling status error:', err));
                    }, 3000); // Polling setiap 3 detik
                }
            });
        </script>
    @endpush
@endsection
