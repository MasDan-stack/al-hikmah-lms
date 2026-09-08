@extends('layouts.mentor')

@section('title', 'Tong Sampah Bank Soal')

@section('content')
<div class="container-fluid">
    <!-- Header Page -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="section-badge mb-2 text-danger"><i class="bi bi-trash3"></i> Tong Sampah Bank Soal</div>
            <h1 class="h3 fw-bold mb-1">Riwayat Soal <span class="text-gradient">Terhapus</span></h1>
            <p class="text-muted small mb-0">Daftar butir soal yang dipindahkan ke Tong Sampah. Anda dapat memulihkan (*restore*) atau menghapus secara permanen.</p>
        </div>
        <div>
            <a href="{{ route('mentor.questions.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Bank Soal
            </a>
        </div>
    </div>

    <!-- Main Data Table Card -->
    <div class="card border-0 shadow-sm rounded-4" style="background: var(--card-bg);">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableTrashBankSoal">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Program</th>
                            <th>Topik Materi</th>
                            <th>Tingkat</th>
                            <th style="min-width: 250px;">Pertanyaan Soal</th>
                            <th>Tanggal Dihapus</th>
                            <th class="text-center no-sort" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashedQuestions as $index => $q)
                            <tr>
                                <td>{{ $trashedQuestions->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1 fw-semibold">
                                        {{ $q->program->name ?? '-' }}
                                    </span>
                                </td>
                                <td><span class="fw-bold text-dark">{{ $q->topic }}</span></td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $q->difficulty }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $q->question }}">
                                        {{ $q->question }}
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $q->deleted_at->format('d M Y H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <form method="POST" action="{{ route('mentor.questions.restore', $q->id) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-3 px-2 py-1" title="Pulihkan Soal">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('mentor.questions.force-delete', $q->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini secara PERMANEN? Data tidak dapat dikembalikan lagi.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Hapus Permanen">
                                                <i class="bi bi-x-circle me-1"></i> Hapus Permanen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-check-circle fs-1 text-success d-block mb-3"></i>
                                    <h6 class="fw-bold text-secondary mb-1">Tong Sampah Kosong</h6>
                                    <p class="text-muted small mb-0">Tidak ada butir soal terhapus saat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4 d-flex justify-content-end">
                {{ $trashedQuestions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
