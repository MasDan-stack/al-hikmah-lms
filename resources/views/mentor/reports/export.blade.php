<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja & Progres Mentor - AL-HIKMAH LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; color: #333; }
        .report-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; }
        .stat-badge { background-color: #eef2ff; color: #3730a3; border-radius: 8px; padding: 12px 20px; text-align: center; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .report-card { border: none; }
        }
    </style>
</head>
<body class="py-4">
    <div class="container my-3">
        <!-- Top Controls -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('mentor.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-printer-fill me-2"></i> Cetak / Download PDF
            </button>
        </div>

        <div class="report-card p-4 p-md-5 shadow-sm">
            <!-- Header Report -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4">
                <div>
                    <h2 class="fw-bold text-success mb-1">AL-HIKMAH LMS</h2>
                    <h5 class="fw-semibold text-secondary mb-0">Laporan Kinerja Mentor & Bimbingan Santri</h5>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success fs-6 rounded-pill px-3 py-2">
                        Periode: {{ now()->translatedFormat('F Y') }}
                    </span>
                    <div class="text-muted small mt-1">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</div>
                </div>
            </div>

            <!-- Mentor Identity -->
            <div class="row g-3 mb-4 bg-light p-3 rounded-3">
                <div class="col-md-6">
                    <div class="small text-muted fw-bold text-uppercase">Nama Mentor / Pendamping:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $user->name }}</div>
                    <div class="small text-secondary">{{ $mentor->specialization ?? 'Guru Al-Qur\'an' }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="small text-muted fw-bold text-uppercase">Email / Kontak:</div>
                    <div class="fw-semibold text-dark">{{ $user->email }}</div>
                    <div class="small text-secondary">Rating Bimbingan: ⭐ {{ number_format($mentor->rating ?? 5.0, 1) }} / 5.0</div>
                </div>
            </div>

            <!-- Summary Stat Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-badge">
                        <div class="small text-muted fw-bold">TOTAL SESI</div>
                        <div class="fs-3 fw-bold text-primary">{{ $totalSessions }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-badge">
                        <div class="small text-muted fw-bold">SESI SELESAI</div>
                        <div class="fs-3 fw-bold text-success">{{ $completedSessions }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-badge">
                        <div class="small text-muted fw-bold">SANTRI BINAAN</div>
                        <div class="fs-3 fw-bold text-dark">{{ $activeStudentsCount }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-badge">
                        <div class="small text-muted fw-bold">RATA-RATA TAJWID</div>
                        <div class="fs-3 fw-bold text-warning">{{ $avgTajwid }}</div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Santri Binaan -->
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Santri Binaan & Capaian Rata-Rata</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Santri</th>
                            <th>Total Catatan</th>
                            <th>Rata-rata Tajwid</th>
                            <th>Capaian Hafalan Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $st)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $st->user?->name ?? $st->full_name }}</td>
                                <td>{{ $st->total_records }} Kali</td>
                                <td>
                                    <span class="badge {{ $st->avg_tajwid >= 75 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill">
                                        {{ $st->avg_tajwid }}
                                    </span>
                                </td>
                                <td class="small">
                                    @if($st->latest_progress)
                                        {{ $st->latest_progress->surah_start ?? 'Surah' }} (Juz {{ $st->latest_progress->juz ?? 1 }}) - {{ $st->latest_progress->created_at->format('d M Y') }}
                                    @else
                                        <span class="text-muted">Belum ada catatan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada santri binaan yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Detail Riwayat Progres Terakhir -->
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-check text-success me-2"></i>Riwayat Catatan Progres Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Santri</th>
                            <th>Kategori</th>
                            <th>Surah / Juz</th>
                            <th>Nilai (Fluent / Tajwid)</th>
                            <th>Adab</th>
                            <th>Evaluasi & PR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProgresses as $p)
                            <tr>
                                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-semibold">{{ $p->student?->user?->name ?? 'Santri' }}</td>
                                <td><span class="badge bg-secondary-subtle text-dark">{{ $p->kategori }}</span></td>
                                <td>{{ $p->surah_start ?? '-' }} (Juz {{ $p->juz ?? '-' }})</td>
                                <td>Fluent: {{ $p->nilai_fluent ?? '-' }} | Tajwid: {{ $p->nilai_tajwid ?? '-' }}</td>
                                <td>{{ $p->nilai_adab ?? '-' }}</td>
                                <td>{{ $p->catatan_evaluasi ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada catatan progres tersimpan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Signatures -->
            <div class="row mt-5 pt-4 border-top text-center">
                <div class="col-6">
                    <div class="small text-muted">Mengetahui,</div>
                    <div class="fw-bold mt-1 mb-5">Kepala LMS AL-HIKMAH</div>
                    <div>( __________________________ )</div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Pendamping / Mentor,</div>
                    <div class="fw-bold mt-1 mb-5">{{ $user->name }}</div>
                    <div>( __________________________ )</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
