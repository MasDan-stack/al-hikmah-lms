@extends('layouts.student')

@section('title', 'Statistik & Riwayat Poin')
@section('header', 'Statistik & Riwayat Poin')
@section('subheader', 'Transparansi perolehan poin dari setiap aktivitas setoran dan muroja\'ah Anda.')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat Overview -->
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light-subtle">
            <div class="rounded-circle mx-auto my-2 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-star-fill fs-2"></i>
            </div>
            <h2 class="fw-bold text-warning mb-0">{{ number_format($student->total_points ?: 0) }}</h2>
            <small class="text-muted">Total Poin Terkumpul</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light-subtle">
            <div class="rounded-circle mx-auto my-2 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-fire fs-2"></i>
            </div>
            <h2 class="fw-bold text-danger mb-0">{{ $student->current_streak ?: 0 }} <span class="fs-6">Hari</span></h2>
            <small class="text-muted">Streak Saat Ini (Terpanjang: {{ $student->longest_streak ?: 0 }} Hari)</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light-subtle">
            <div class="rounded-circle mx-auto my-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-award fs-2"></i>
            </div>
            <h2 class="fw-bold text-success mb-0">{{ $student->earnedBadges->count() }}</h2>
            <small class="text-muted">Total Lencana Diraih</small>
        </div>
    </div>
</div>

<!-- Ledger Log Table -->
<div class="card border-0 shadow-sm rounded-4 p-4">
    <h6 class="fw-bold mb-3"><i class="bi bi-journal-text text-primary me-2"></i>Buku Besar Riwayat Poin (Ledger)</h6>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>Poin</th>
                    <th>Tipe Aktivitas</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pointLogs as $log)
                    <tr>
                        <td class="small text-muted">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">
                                +{{ $log->points }} Pts
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
                                {{ strtoupper(str_replace('_', ' ', $log->activity_type)) }}
                            </span>
                        </td>
                        <td class="small">{{ $log->description ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada catatan perolehan poin.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $pointLogs->links() }}
    </div>
</div>
@endsection
