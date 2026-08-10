<div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg); border: 1px solid var(--border-color) !important;">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Aktivitas & Progres Santri Terbaru</h5>
                <p class="text-muted small mb-0">Catatan capaian hafalan, nilai tajwid, dan adab harian santri</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">Santri</th>
                        <th class="border-0">Program</th>
                        <th class="border-0">Capaian / Surah</th>
                        <th class="border-0">Ayat / Juz</th>
                        <th class="border-0">Nilai Tajwid</th>
                        <th class="border-0">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($progressList as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark">{{ $item->student?->user?->name ?? 'Santri' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success rounded-pill">{{ $item->kategori ?? 'Bimbingan' }}</span>
                            </td>
                            <td>
                                <div class="fw-medium text-primary">{{ $item->surah_start ?? $item->activity_title ?? 'Surah Al-Fatihah' }}</div>
                            </td>
                            <td>
                                <span class="small text-secondary">Ayat {{ $item->ayat_start ?? 1 }} - {{ $item->ayat_end ?? 7 }} (Juz {{ $item->juz ?? 1 }})</span>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning fw-bold rounded-pill px-3">{{ $item->nilai_tajwid ?? 85 }}/100</span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ $item->created_at ? $item->created_at->format('d M Y') : date('d M Y') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted opacity-50"></i>
                                Belum ada catatan aktivitas progres santri terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
