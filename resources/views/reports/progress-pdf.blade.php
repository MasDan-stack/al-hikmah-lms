<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Perkembangan Santri - AL-HIKMAH LMS</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 24px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f5132;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .header h2 {
            color: #0f5132;
            margin: 0 0 4px 0;
            letter-spacing: 1px;
        }
        .header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        .meta-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 6px 12px;
            font-size: 14px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .data-table th, .data-table td {
            border: 1px solid #dee2e6;
            padding: 10px 12px;
            font-size: 13px;
            text-align: left;
        }
        .data-table th {
            background-color: #0f5132;
            color: #ffffff;
            font-weight: 600;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            background: #e2e3e5;
            color: #383d41;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="background: #0f5132; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="header">
        <h2>AL-HIKMAH LEARNING MANAGEMENT SYSTEM</h2>
        <p>Laporan Resmi Capaian Pembelajaran & Muroja'ah Al-Qur'an Santri</p>
    </div>

    <div class="meta-card">
        <table class="meta-table">
            <tr>
                <td><strong>Nama Santri:</strong> {{ $studentUser->name ?? 'Santri' }}</td>
                <td><strong>Email Registered:</strong> {{ $studentUser->email ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak:</strong> {{ $generatedAt }}</td>
                <td><strong>Status Bimbingan:</strong> <span class="badge">Aktif</span></td>
            </tr>
        </table>
    </div>

    <h3>Aktivitas Capaian Terbaru</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Program</th>
                <th>Capaian / Surah</th>
                <th>Ayat / Juz</th>
                <th>Nilai Tajwid</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($progressList as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kategori ?? 'Bimbingan Al-Qur\'an' }}</td>
                    <td>{{ $item->surah_start ?? $item->activity_title ?? 'Surah Al-Fatihah' }}</td>
                    <td>Ayat {{ $item->ayat_start ?? 1 }} - {{ $item->ayat_end ?? 7 }} (Juz {{ $item->juz ?? 1 }})</td>
                    <td><strong>{{ $item->nilai_tajwid ?? 85 }}/100</strong></td>
                    <td>{{ $item->created_at ? $item->created_at->format('d M Y') : date('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #888;">Belum ada data catatan progres tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak secara otomatis oleh Sistem AL-HIKMAH LMS &bull; {{ date('Y') }}</p>
    </div>
</body>
</html>
