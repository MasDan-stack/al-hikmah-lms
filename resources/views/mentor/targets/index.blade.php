@extends('layouts.mentor')

@section('title', 'Manajemen Target Hafalan Santri')
@section('header', 'Target Hafalan Santri')
@section('subheader', 'Pantau, tetapkan, dan evaluasi target setoran hafalan santri binaan Anda')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Target Hafalan Santri</h5>
            <small class="text-muted">Kelola target hafalan harian dan penugasan per santri</small>
        </div>
        <a href="{{ route('mentor.targets.create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Tetapkan Target Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Santri</th>
                        <th>Tanggal Target</th>
                        <th>Surat & Target Ayat</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th class="text-end">Aksi Evaluasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($targets as $target)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $target->student?->getDisplayName() ?? 'Santri' }}</div>
                                <small class="text-muted">{{ $target->student?->user?->email }}</small>
                            </td>
                            <td>{{ $target->target_date->translatedFormat('d M Y') }}</td>
                            <td>
                                <span class="fw-semibold text-success">{{ $target->surah_name }}</span>
                                <small class="text-muted d-block">Ayat {{ $target->start_ayat }} - {{ $target->end_ayat }} ({{ $target->total_ayat }} Ayat)</small>
                            </td>
                            <td>
                                @if($target->status === 'completed')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                    </span>
                                @elseif($target->status === 'missed')
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-x-circle-fill me-1"></i> Terlewat
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-hourglass-split me-1"></i> Berjalan
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted text-truncate" style="max-width: 150px;">
                                {{ $target->notes ?: '-' }}
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    @if($target->status !== 'completed')
                                        <form action="{{ route('mentor.targets.evaluate', $target->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 me-1" title="Tandai Tercapai">
                                                <i class="bi bi-check-lg"></i> Lulus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-check" style="font-size: 2.5rem;"></i>
                                <p class="mt-2 mb-0">Belum ada target hafalan yang ditetapkan untuk santri.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $targets->links() }}
        </div>
    </div>
</div>
@endsection
