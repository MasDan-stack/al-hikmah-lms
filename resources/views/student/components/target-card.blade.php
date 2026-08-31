@props(['target' => null])

<div class="card border-0 shadow-sm rounded-4 p-4 h-100">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-bullseye fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0">Target Hafalan Hari Ini</h6>
                <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
            </div>
        </div>
        @if($target)
            @if($target->status === 'completed')
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Selesai
                </span>
            @elseif($target->status === 'missed')
                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-x-circle-fill me-1"></i> Terlewat
                </span>
            @else
                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-hourglass-split me-1"></i> Berjalan
                </span>
            @endif
        @endif
    </div>

    @if($target)
        <div class="p-3 rounded-3 bg-light-subtle border mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fw-bold text-success fs-5">{{ $target->surah_name ?: 'Al-Qur\'an' }}</span>
                <span class="badge bg-secondary-subtle text-secondary">Ayat {{ $target->start_ayat }} - {{ $target->end_ayat }}</span>
            </div>
            <div class="text-muted small">
                <i class="bi bi-hash me-1"></i> Total Target: <strong>{{ $target->total_ayat }} Ayat</strong>
                @if($target->scheduled_time)
                    <span class="ms-2"><i class="bi bi-clock me-1"></i> {{ $target->scheduled_time }} WIB</span>
                @endif
            </div>
            @if($target->notes)
                <div class="mt-2 pt-2 border-top text-secondary small fst-italic">
                    <i class="bi bi-chat-quote-fill text-success me-1"></i> "{{ $target->notes }}"
                </div>
            @endif
        </div>

        <div class="mt-auto d-flex gap-2">
            @if($target->status !== 'completed')
                <form action="{{ route('student.targets.complete', $target->id) }}" method="POST" class="w-100">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Tandai Sudah Disetor
                    </button>
                </form>
            @else
                <button class="btn btn-outline-success w-100 rounded-pill py-2 fw-semibold" disabled>
                    <i class="bi bi-check-all me-1"></i> Target Telah Tercapai (+50 Poin)
                </button>
            @endif
        </div>
    @else
        <div class="text-center py-4 my-auto">
            <i class="bi bi-journal-plus text-muted" style="font-size: 2.5rem;"></i>
            <h6 class="fw-bold mt-2 mb-1">Belum Ada Target Dari Ustadz Hari Ini</h6>
            <p class="text-muted small mb-3">Anda bisa membuat target hafalan mandiri untuk hari ini.</p>
            <a href="{{ route('student.targets.today') }}" class="btn btn-sm btn-outline-success rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> Tambah Target Mandiri
            </a>
        </div>
    @endif
</div>
