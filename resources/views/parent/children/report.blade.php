<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perkembangan Santri - {{ $child->user?->name ?? $child->full_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; color: #333; }
        .report-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .report-card { border: none; }
        }
    </style>
</head>
<body class="py-4">
    <div class="container my-3">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('parent.children.show', $child->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-printer-fill me-2"></i> Cetak / Download PDF
            </button>
        </div>

        <div class="report-card p-4 p-md-5 shadow-sm">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4">
                <div>
                    <h2 class="fw-bold text-success mb-1">AL-HIKMAH LMS</h2>
                    <h5 class="fw-semibold text-secondary mb-0">Laporan Perkembangan Bimbingan Al-Qur'an Santri</h5>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success fs-6 rounded-pill px-3 py-2">
                        Tanggal: {{ now()->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>

            <div class="row g-3 mb-4 bg-light p-3 rounded-3">
                <div class="col-md-6">
                    <div class="small text-muted fw-bold text-uppercase">Nama Santri:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $child->user?->name ?? $child->full_name }}</div>
                    <div class="small text-secondary">Usia: {{ $child->age }} Tahun | Gender: {{ $child->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="small text-muted fw-bold text-uppercase">Wali Santri:</div>
                    <div class="fw-semibold text-dark">{{ auth()->user()->name }}</div>
                    <div class="small text-secondary">Kontak: {{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-success-subtle text-success rounded-3 text-center">
                        <div class="small fw-bold">RATA-RATA TAJWID</div>
                        <div class="fs-2 fw-bold">{{ $avgTajwid }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-primary-subtle text-primary rounded-3 text-center">
                        <div class="small fw-bold">RATA-RATA KELANCARAN</div>
                        <div class="fs-2 fw-bold">{{ $avgFluent }}</div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-text text-primary me-2"></i>Riwayat Catatan Capaian Hafalan</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle small">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Surah / Juz</th>
                            <th>Tajwid / Fluent</th>
                            <th>Evaluasi & PR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($progresses as $p)
                            <tr>
                                <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                <td>{{ $p->kategori }}</td>
                                <td>{{ $p->surah_start ?? '-' }} (Juz {{ $p->juz ?? 1 }})</td>
                                <td>Tajwid: {{ $p->nilai_tajwid ?? '-' }} | Fluent: {{ $p->nilai_fluent ?? '-' }}</td>
                                <td>{{ $p->catatan_evaluasi ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data catatan progres.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row mt-5 pt-4 border-top text-center">
                <div class="col-6">
                    <div class="small text-muted">Mengetahui,</div>
                    <div class="fw-bold mt-1 mb-5">Kepala LMS AL-HIKMAH</div>
                    <div>( __________________________ )</div>
                </div>
                <div class="col-6">
                    <div class="small text-muted">Orang Tua / Wali,</div>
                    <div class="fw-bold mt-1 mb-5">{{ auth()->user()->name }}</div>
                    <div>( __________________________ )</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
