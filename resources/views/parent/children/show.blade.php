@extends('layouts.parent')

@section('title', 'Detail Progres - ' . ($child->user?->name ?? $child->full_name))
@section('header', 'Detail Progres Santri')
@section('subheader', 'Informasi perkembangan bimbingan Al-Qur\'an Ananda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-person-circle text-primary me-2"></i>{{ $child->user?->name ?? $child->full_name }}
            </h4>
            <p class="text-muted small mb-0">Usia: {{ $child->age }} Thn | Gender: {{ $child->gender === 'L' ? 'Laki-Laki' : 'Perempuan' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('parent.children.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('parent.children.report', $child->id) }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download Laporan PDF
            </a>
        </div>
    </div>

    <!-- 📊 Chart Grafik Perkembangan Bulanan -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-graph-up-arrow me-2 text-success"></i>Grafik Perkembangan Bimbingan (6 Bulan Terakhir)
            </h5>
        </div>
        <div class="card-body p-4">
            <canvas id="childProgressChart" height="120"></canvas>
        </div>
    </div>

    <!-- 📋 Tabel Riwayat Progres & Catatan Evaluasi Mentor -->
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Catatan Bimbingan & Evaluasi Mentor</h5>
        </div>
        <div class="card-body p-4">
            @if($progresses->isEmpty())
                <div class="text-center py-4 text-muted">
                    Belum ada catatan progres bimbingan tersimpan.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover datatable" id="tableChildProgressHistory">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Kategori</th>
                                <th>Surah / Juz</th>
                                <th>Nilai (Fluent / Tajwid / Adab)</th>
                                <th>Catatan Evaluasi & PR</th>
                                <th>Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($progresses as $prog)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $prog->created_at->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge bg-secondary-subtle text-dark">{{ $prog->kategori }}</span></td>
                                    <td>{{ $prog->surah_start ?? '-' }} (Juz {{ $prog->juz ?? 1 }})</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <span class="badge bg-success-subtle text-success">Tajwid: {{ $prog->nilai_tajwid ?? '-' }}</span>
                                            <span class="badge bg-primary-subtle text-primary">Fluent: {{ $prog->nilai_fluent ?? '-' }}</span>
                                            <span class="badge bg-info-subtle text-info">Adab: {{ $prog->nilai_adab ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $prog->catatan_evaluasi ?? '-' }}</div>
                                        @if($prog->homework)
                                            <small class="text-muted"><i class="bi bi-journal-bookmark me-1"></i>PR: {{ $prog->homework }}</small>
                                        @endif
                                    </td>
                                    <td class="small fw-semibold">{{ $prog->mentor?->user?->name ?? 'Ustaz' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('childProgressChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Jumlah Catatan Progres',
                            data: {!! json_encode($chartProgressCounts) !!},
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Rata-rata Nilai Tajwid',
                            data: {!! json_encode($chartAvgTajwid) !!},
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            fill: true,
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: { beginAtZero: true, max: 100 }
                    }
                }
            });
        }
    });
</script>
@endpush
