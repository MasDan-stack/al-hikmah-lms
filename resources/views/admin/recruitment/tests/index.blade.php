@extends('layouts.admin')

@section('title', 'Manajemen Tes Evaluasi AI')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Daftar Sesi Tes Kompetensi Calon Guru</h1>
            <p class="text-muted small mb-0">Pantau sesi tes kompetensi keguruan, tajwid, makharijul huruf, dan tahsin calon guru pembimbing.</p>
        </div>
        <a href="{{ route('admin.recruitment.applications.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Ke Daftar Pelamar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header py-3 bg-light border-bottom">
            <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-table me-1"></i> Tabel Riwayat Sesi Tes Calon Guru</h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0 datatable" id="tableMentorTestSessions" data-export="true" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Pelamar</th>
                            <th>Spesialisasi</th>
                            <th>Waktu Pelaksanaan</th>
                            <th>Status Sesi</th>
                            <th>Nilai Skor</th>
                            <th>Predikat</th>
                            <th class="text-end no-sort">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td class="fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $session->application?->full_name ?? '-' }}</div>
                                <small class="text-muted">{{ $session->application?->application_code ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $session->application?->specialization ?? '-' }}</span>
                            </td>
                            <td class="small">{{ $session->scheduled_at ? Carbon\Carbon::parse($session->scheduled_at)->format('d/m/Y H:i') : $session->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($session->status === 'in_progress' || $session->status === 'scheduled')
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Sedang Berlangsung</span>
                                @elseif($session->status === 'completed')
                                    <span class="badge bg-success px-3 py-1 rounded-pill">Selesai Dinilai</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-1 rounded-pill">{{ $session->status }}</span>
                                @endif
                            </td>
                            <td>
                                <strong class="fs-6 {{ ($session->score ?? 0) >= 75 ? 'text-success' : 'text-danger' }}">
                                    {{ $session->score !== null ? $session->score : '-' }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-info text-capitalize">{{ $session->grade ?: '-' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.recruitment.tests.show', $session->id) }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> Detail Soal & Nilai
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada sesi tes yang dibuat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof DataTable !== 'undefined') {
        const testTable = document.getElementById('tableMentorTestSessions');
        if (testTable && !DataTable.isDataTable(testTable)) {
            if (typeof window.initDataTable === 'function') {
                window.initDataTable(testTable);
            } else {
                new DataTable(testTable, { responsive: true, autoWidth: false, pageLength: 10 });
            }
        }
    }
});
</script>
@endpush
