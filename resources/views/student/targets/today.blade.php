@extends('layouts.student')

@section('title', 'Target Hafalan Hari Ini')
@section('header', 'Target Hafalan & Setoran')
@section('subheader', 'Pantau dan kelola target setoran harian Anda.')

@section('content')
<div class="row g-4">
    <!-- Form Tambah Target Mandiri & Card Hari Ini -->
    <div class="col-12 col-lg-5">
        <div class="mb-4">
            @include('student.components.target-card', ['target' => $todayTarget])
        </div>

        <!-- Form Tambah Target Mandiri -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle text-success me-2"></i>Buat Target Hafalan Mandiri</h6>
            <form action="{{ route('student.targets.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tanggal Target</label>
                    <input type="date" name="target_date" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Surat</label>
                    <input type="text" name="surah_name" class="form-control" placeholder="Contoh: QS. Al-Mulk" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Mulai Ayat</label>
                        <input type="number" name="start_ayat" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Sampai Ayat</label>
                        <input type="number" name="end_ayat" class="form-control" value="10" min="1" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Jam Target (Opsional)</label>
                    <input type="time" name="scheduled_time" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Catatan / Doa Pribadi</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Bismillah target lancar..."></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100 rounded-pill fw-semibold">
                    <i class="bi bi-check2 me-1"></i> Simpan Target Mandiri
                </button>
            </form>
        </div>
    </div>

    <!-- Riwayat Target Hafalan -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Target Hafalan Sebelumnya</h6>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Surat & Ayat</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyTargets as $history)
                            <tr>
                                <td class="small">{{ $history->target_date->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $history->surah_name ?: 'Al-Qur\'an' }}</span>
                                    <small class="text-muted d-block">Ayat {{ $history->start_ayat }} - {{ $history->end_ayat }} ({{ $history->total_ayat }} Ayat)</small>
                                </td>
                                <td>
                                    @if($history->status === 'completed')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Selesai</span>
                                    @elseif($history->status === 'missed')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2">Terlewat</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2">Pending</span>
                                    @endif
                                </td>
                                <td class="small text-muted text-truncate" style="max-width: 150px;">
                                    {{ $history->notes ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat target sebelumnya.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $historyTargets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
