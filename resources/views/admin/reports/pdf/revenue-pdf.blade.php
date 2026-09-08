<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Resmi AL-HIKMAH LMS - {{ $startDate?->format('d/m/Y') }} s/d {{ $endDate?->format('d/m/Y') }}</title>
    
    <!-- Google Font Poppins & Serif untuk Dokumen Resmi -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #1e293b;
            background: #f8fafc;
            padding: 24px;
            font-size: 11pt;
            line-height: 1.5;
        }

        .paper {
            background: #ffffff;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 48px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }

        /* Kop Surat Resmi AL-HIKMAH */
        .kop-surat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .kop-logo {
            width: 80px;
            height: auto;
        }

        .kop-header {
            text-align: center;
            flex-grow: 1;
            padding: 0 16px;
        }

        .kop-title-ar {
            font-family: 'Amiri', 'Traditional Arabic', serif;
            font-size: 16pt;
            color: #047857;
            margin-bottom: 2px;
        }

        .kop-title {
            font-family: 'Cinzel', serif;
            font-size: 16pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .kop-subtitle {
            font-size: 10pt;
            font-weight: 600;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-address {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 4px;
            line-height: 1.3;
        }

        /* Document Metadata */
        .doc-title-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
            color: #0f172a;
        }

        .doc-number {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 9.5pt;
        }

        .meta-item {
            display: flex;
            gap: 8px;
        }

        .meta-label {
            font-weight: 600;
            color: #475569;
            width: 130px;
        }

        .meta-value {
            color: #0f172a;
            font-weight: 500;
        }

        /* Summary Cards Box */
        .summary-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .summary-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            background: #ffffff;
        }

        .summary-card-title {
            font-size: 8pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }

        .summary-card-value {
            font-size: 12pt;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Detail Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 32px;
        }

        .report-table th, .report-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }

        .report-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-end { text-align: right !important; }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: 700; }

        /* Signatures Section */
        .signatures-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .sig-block {
            width: 220px;
            text-align: center;
            font-size: 9.5pt;
        }

        .sig-space {
            height: 70px;
        }

        .sig-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .sig-title {
            font-size: 8.5pt;
            color: #64748b;
        }

        /* Print Controls */
        .print-controls {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            gap: 12px;
            z-index: 1000;
        }

        .btn-print {
            background: #0d6efd;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(13, 110, 253, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .btn-close-print {
            background: #64748b;
            color: white;
            padding: 10px 18px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-weight: 500;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .paper {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
            .print-controls {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Controls for Web View -->
    <div class="print-controls">
        <button class="btn-print" onclick="window.print()">
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak / Simpan PDF
        </button>
        <button class="btn-close-print" onclick="window.close()">Tutup</button>
    </div>

    <div class="paper">
        <!-- 1. KOP SURAT RESMI -->
        <div class="kop-surat">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="AL-HIKMAH" class="kop-logo" onerror="this.style.display='none'">
            <div class="kop-header">
                <div class="kop-title-ar">مَعْهَدُ الحِكْمَةِ لِتَعْلِيْمِ القُرْآنِ</div>
                <div class="kop-title">LEMBAGA PENDIDIKAN AL-QUR'AN AL-HIKMAH</div>
                <div class="kop-subtitle">Pusat Bimbingan Tahsin, Tahfidz, & Studi Islam Terpadu</div>
                <div class="kop-address">
                    Jl. Al-Hikmah No. 45, Jakarta Selatan | Telp/WA: +62 812-3456-7890 | Web: www.alhikmahlms.sch.id
                </div>
            </div>
            <div style="width: 80px;"></div> <!-- Spacer balancing logo -->
        </div>

        <!-- 2. JUDUL DOKUMEN & METADATA -->
        <div class="doc-title-container">
            <div class="doc-title">REKAPITULASI LAPORAN KEUANGAN & TRANSAKSI</div>
            <div class="doc-number">Nomor: {{ 'REP-'.date('Ym').'-'.str_pad((string) rand(100, 999), 4, '0', STR_PAD_LEFT) }}</div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <span class="meta-label">Periode Laporan:</span>
                <span class="meta-value">{{ $startDate?->translatedFormat('d F Y') ?? 'Semua' }} s/d {{ $endDate?->translatedFormat('d F Y') ?? 'Semua' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Tanggal Cetak:</span>
                <span class="meta-value">{{ $generatedAt }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Program Filter:</span>
                <span class="meta-value">{{ $selectedProgram ? $selectedProgram->name : 'Semua Program' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Petugas Verifikasi:</span>
                <span class="meta-value">{{ $adminUser->name ?? 'Administrator Keuangan' }}</span>
            </div>
        </div>

        <!-- 3. RINGKASAN REKAPITULASI FINANSIAL -->
        <div class="summary-box">
            <div class="summary-card">
                <div class="summary-card-title">Total Transaksi</div>
                <div class="summary-card-value">{{ count($payments) }} Transaksi</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-title">Total Uang Masuk (Lunas)</div>
                <div class="summary-card-value" style="color: #047857;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-card-title">Status Filter</div>
                <div class="summary-card-value">{{ strtoupper($status) }}</div>
            </div>
        </div>

        <!-- 4. TABEL RINCIAN TRANSAKSI -->
        <table class="report-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">No</th>
                    <th>No. Invoice</th>
                    <th>Tanggal</th>
                    <th>Nama Santri</th>
                    <th>Program Belajar</th>
                    <th>Jenis Tagihan</th>
                    <th>Metode</th>
                    <th class="text-end">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold font-monospace">#{{ $item->invoice_number }}</td>
                        <td>{{ $item->payment_date ? $item->payment_date->format('d/m/Y') : '-' }}</td>
                        <td>
                            <strong>{{ $item->student?->getDisplayName() ?? 'Santri' }}</strong>
                            <div style="font-size: 7.5pt; color: #64748b;">Wali: {{ $item->student?->parent_name ?? '-' }}</div>
                        </td>
                        <td>{{ $item->program?->name ?? 'Pendaftaran' }}</td>
                        <td>{{ ucfirst($item->payment_purpose ?? 'SPP') }}</td>
                        <td>{{ strtoupper($item->payment_method ?? 'Online') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">
                            Tidak ada transaksi pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: 700;">
                    <td colspan="7" class="text-end">TOTAL AKUMULASI PENDAPATAN:</td>
                    <td class="text-end" style="color: #047857; font-size: 10pt;">
                        Rp {{ number_format($totalAmount, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- 5. BLOK TANDA TANGAN RESMI -->
        <div class="signatures-container">
            <div class="sig-block">
                <div>Mengetahui,</div>
                <div class="sig-title">Direktur / Pimpinan Lembaga</div>
                <div class="sig-space"></div>
                <div class="sig-name">Ustadz Dr. H. Ahmad Fauzi, M.Pd.I</div>
                <div class="sig-title">NIP. 19820415 200801 1 002</div>
            </div>

            <div class="sig-block">
                <div>Jakarta, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="sig-title">Bendahara & Bag. Keuangan</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $adminUser->name ?? 'Dan Hermawan' }}</div>
                <div class="sig-title">Koordinator Keuangan & Sistem</div>
            </div>
        </div>
    </div>

</body>
</html>
