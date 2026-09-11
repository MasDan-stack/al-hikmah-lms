<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji Guru — {{ $salarySlip['period_label'] ?? '' }} — Al-Hikmah LMS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #fff;
            padding: 24px 32px;
            max-width: 820px;
            margin: 0 auto;
        }
        .slip-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2.5px solid #0d7a3e;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .slip-header .org-name { font-size: 20px; font-weight: 700; color: #0d7a3e; }
        .slip-header .org-tagline { font-size: 11px; color: #6b7280; margin-top: 2px; }
        .slip-header .doc-type { text-align: right; }
        .slip-header .doc-type .label { font-size: 15px; font-weight: 700; color: #1a1a1a; text-transform: uppercase; letter-spacing: 0.04em; }
        .slip-header .doc-type .period { font-size: 12px; color: #6b7280; margin-top: 3px; }
        .status-banner { border-radius: 8px; padding: 10px 18px; font-size: 12px; font-weight: 600; margin-bottom: 20px; }
        .status-pending { background: #fefce8; border: 1.5px solid #fde68a; color: #78350f; }
        .status-paid { background: #ecfdf5; border: 1.5px solid #6ee7b7; color: #065f46; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; margin-bottom: 20px; background: #f9fafb; border-radius: 8px; padding: 14px 16px; border: 1px solid #e5e7eb; }
        .info-grid .row-item { display: flex; gap: 8px; }
        .info-grid .row-item .key { color: #6b7280; min-width: 110px; font-size: 12px; }
        .info-grid .row-item .val { font-weight: 600; color: #111827; font-size: 12px; }
        .section-label { background: #f0fdf4; border-left: 4px solid #0d7a3e; padding: 6px 12px; font-weight: 700; font-size: 12px; color: #065f46; margin-bottom: 10px; border-radius: 0 6px 6px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
        thead th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb; }
        thead th.text-right { text-align: right; }
        thead th.text-center { text-align: center; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; color: #374151; vertical-align: top; }
        tbody td.text-right { text-align: right; }
        tbody td.text-center { text-align: center; }
        .badge-hadir { background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-terlambat { background: #fef9c3; color: #92400e; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-izin { background: #ede9fe; color: #5b21b6; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-sakit { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-other { background: #f3f4f6; color: #374151; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        tfoot td { padding: 8px 10px; font-weight: 700; }
        .tfoot-border { border-top: 2px solid #e5e7eb; }
        .total-row td { background: #f0fdf4; color: #065f46; font-size: 13px; }
        .total-row td.text-right { font-size: 15px; color: #0d7a3e; }
        .note-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px; font-size: 11.5px; color: #78350f; margin-bottom: 24px; }
        .sig-area { display: flex; justify-content: space-between; margin-top: 20px; }
        .sig-block { text-align: center; min-width: 180px; }
        .sig-block .sig-title { font-size: 11.5px; color: #6b7280; margin-bottom: 55px; }
        .sig-block .sig-line { border-top: 1.5px solid #374151; margin: 0 12px; }
        .sig-block .sig-name { font-size: 11.5px; font-weight: 700; color: #111827; margin-top: 6px; }
        .sig-block .sig-role { font-size: 11px; color: #6b7280; }
        .slip-footer { border-top: 1px solid #e5e7eb; margin-top: 24px; padding-top: 10px; text-align: center; font-size: 10.5px; color: #9ca3af; }
        @media print {
            body { padding: 12px 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 16px; display: flex; gap: 10px;">
    <button onclick="window.print()" style="background: #0d7a3e; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
        🖨️ Cetak Dokumen
    </button>
    <button onclick="window.close()" style="background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 13px;">
        ✕ Tutup
    </button>
</div>

@if(!$salarySlip)
    <p>Data slip gaji tidak tersedia.</p>
@else

<div class="slip-header">
    <div>
        <div class="org-name">🕌 Al-Hikmah LMS</div>
        <div class="org-tagline">Lembaga Bimbingan Belajar Al-Qur'an — Amanah &amp; Transparan</div>
    </div>
    <div class="doc-type">
        <div class="label">Slip Gaji / Honorarium Mengajar</div>
        <div class="period">Periode: {{ $salarySlip['period_label'] }}</div>
    </div>
</div>

@if($salarySlip['salary_status'] === 'paid')
    <div class="status-banner status-paid">
        ✅ Status Pembayaran: <strong>LUNAS</strong> — Honor periode ini telah diverifikasi dan ditandai lunas oleh Admin.
    </div>
@else
    <div class="status-banner status-pending">
        ⏳ Status Pembayaran: <strong>MENUNGGU VERIFIKASI ADMIN</strong> — Slip ini belum menandakan pembayaran. Status berubah setelah admin menandai lunas.
    </div>
@endif

<div class="info-grid">
    <div class="row-item">
        <span class="key">Nama Guru</span>
        <span class="val">{{ $salarySlip['mentor_name'] }}</span>
    </div>
    <div class="row-item">
        <span class="key">Periode</span>
        <span class="val">{{ $salarySlip['period_label'] }}</span>
    </div>
    <div class="row-item">
        <span class="key">Tarif Mengajar</span>
        <span class="val">Rp {{ number_format($salarySlip['rate_per_attendance'], 0, ',', '.') }} / kehadiran santri</span>
    </div>
    <div class="row-item">
        <span class="key">Total Sesi</span>
        <span class="val">{{ $salarySlip['total_sessions'] }} sesi dijadwalkan</span>
    </div>
    <div class="row-item">
        <span class="key">Kehadiran Valid</span>
        <span class="val">{{ $salarySlip['total_valid_attendance'] }} kali (hadir / terlambat)</span>
    </div>
    <div class="row-item">
        <span class="key">Total Honorarium</span>
        <span class="val" style="color: #0d7a3e; font-size: 14px;">Rp {{ number_format($salarySlip['total_honor'], 0, ',', '.') }}</span>
    </div>
</div>

@if(count($salarySlip['sessions_a']) === 0)
    <p style="text-align: center; color: #9ca3af; padding: 30px;">Tidak ada sesi pada periode ini.</p>
@else

<div class="section-label">A. Rincian Sesi &amp; Jadwal Mengajar</div>
<table>
    <thead>
        <tr>
            <th style="width: 2.5rem;">No</th>
            <th>Hari / Tanggal &amp; Jam</th>
            <th>Nama Santri</th>
            <th class="text-center">Status Kehadiran</th>
            <th class="text-right">Tarif</th>
            <th class="text-right">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salarySlip['sessions_a'] as $i => $sess)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>
                <strong>{{ $sess['date'] }}</strong><br>
                <span style="color: #6b7280;">{{ $sess['time'] }} WIB · {{ ucfirst($sess['method']) }}</span>
            </td>
            <td>{{ $sess['student_name'] }}</td>
            <td class="text-center">
                @if($sess['is_valid_attendance'])
                    @if($sess['confirmation_status'] === 'hadir')
                        <span class="badge-hadir">✓ Hadir</span>
                    @else
                        <span class="badge-terlambat">⏱ Terlambat</span>
                    @endif
                    <br><small style="color: #059669;">= 1 kehadiran</small>
                @else
                    @if($sess['confirmation_status'] === 'izin')
                        <span class="badge-izin">Izin</span>
                    @elseif($sess['confirmation_status'] === 'sakit')
                        <span class="badge-sakit">Sakit</span>
                    @else
                        <span class="badge-other">{{ ucfirst($sess['confirmation_status']) }}</span>
                    @endif
                    <br><small style="color: #9ca3af;">= tidak dihitung</small>
                @endif
            </td>
            <td class="text-right" style="color: #6b7280;">
                @if($sess['is_valid_attendance'])Rp {{ number_format($sess['rate'], 0, ',', '.') }}@else —@endif
            </td>
            <td class="text-right" style="font-weight: 700; color: {{ $sess['is_valid_attendance'] ? '#0d7a3e' : '#9ca3af' }};">
                @if($sess['is_valid_attendance'])Rp {{ number_format($sess['amount'], 0, ',', '.') }}@else —@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="section-label">B. Rincian Kehadiran &amp; Honor Persantri</div>
<table>
    <thead>
        <tr>
            <th style="width: 2.5rem;">No</th>
            <th>Nama Siswa</th>
            <th>Paket / Program</th>
            <th class="text-center">Total Sesi</th>
            <th class="text-center">Kehadiran Valid</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salarySlip['students_b'] as $i => $st)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td style="font-weight: 600;">{{ $st['student_name'] }}</td>
            <td style="color: #6b7280;">{{ $st['program_name'] }}</td>
            <td class="text-center" style="color: #6b7280;">{{ $st['total_sessions'] }} sesi</td>
            <td class="text-center"><span class="badge-hadir">{{ $st['valid_attendance'] }} kali hadir</span></td>
            <td class="text-right" style="font-weight: 700; color: #0d7a3e;">Rp {{ number_format($st['subtotal'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="tfoot-border">
            <td colspan="4" style="color: #6b7280; font-size: 11px; padding-top: 10px;">TOTAL KEHADIRAN VALID</td>
            <td class="text-center" style="padding-top: 10px;">
                <span class="badge-hadir" style="font-size: 12px;">{{ $salarySlip['total_valid_attendance'] }} kali</span>
            </td>
            <td></td>
        </tr>
        <tr class="total-row">
            <td colspan="5" style="font-size: 13px; padding: 10px;">TOTAL HONORARIUM MENGAJAR PERIODE {{ strtoupper($salarySlip['period_label']) }}</td>
            <td class="text-right" style="font-size: 16px; color: #0d7a3e; font-weight: 800; padding: 10px;">
                Rp {{ number_format($salarySlip['total_honor'], 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>

@endif

<div class="note-box">
    <strong>ⓘ Catatan:</strong> honor dihitung dari Daftar hadir: tiap anak yang hadir atau terlambat pada satu pertemuan dihitung satu kehadiran, slip ini belum menandakan pembayaran. Status berubah setelah admin menandai lunas
</div>

<div class="sig-area">
    <div class="sig-block">
        <div class="sig-title">Diterima oleh,</div>
        <div class="sig-line"></div>
        <div class="sig-name">{{ $salarySlip['mentor_name'] }}</div>
        <div class="sig-role">Guru / Mentor</div>
    </div>
    <div class="sig-block">
        <div class="sig-title">Disetujui oleh,</div>
        <div class="sig-line"></div>
        <div class="sig-name">Pimpinan Al-Hikmah LMS</div>
        <div class="sig-role">Admin / Owner</div>
    </div>
</div>

<div class="slip-footer">
    Slip ini digenerate otomatis oleh sistem Al-Hikmah LMS pada {{ now()->locale('id')->translatedFormat('l, d F Y, H:i') }} WIB.
    Dokumen ini sah tanpa tanda tangan basah selama status pembayaran: LUNAS.
</div>

@endif
</body>
</html>
