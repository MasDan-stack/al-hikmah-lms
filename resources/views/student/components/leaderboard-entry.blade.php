@props(['entry' => null])

@if($entry)
<div class="d-flex align-items-center justify-content-between p-3 mb-2 rounded-3 border {{ $entry->is_current_user ? 'border-success bg-success-subtle' : 'bg-light-subtle' }} transition-hover">
    <div class="d-flex align-items-center gap-3">
        <div class="text-center" style="width: 32px;">
            @if($entry->rank === 1)
                <span class="badge bg-warning text-dark rounded-circle p-2 fs-6" title="Juara 1 🥇">🥇</span>
            @elseif($entry->rank === 2)
                <span class="badge bg-secondary text-white rounded-circle p-2 fs-6" title="Juara 2 🥈">🥈</span>
            @elseif($entry->rank === 3)
                <span class="badge bg-warning-subtle text-dark rounded-circle p-2 fs-6" title="Juara 3 🥉">🥉</span>
            @else
                <span class="fw-bold text-muted fs-6">#{{ $entry->rank }}</span>
            @endif
        </div>

        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
            {{ substr($entry->student_name, 0, 1) }}
        </div>

        <div>
            <div class="fw-bold text-truncate" style="max-width: 180px;">
                {{ $entry->student_name }}
                @if($entry->is_current_user)
                    <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Anda</span>
                @endif
            </div>
            <small class="text-muted d-block" style="font-size: 0.75rem;">
                <i class="bi bi-fire text-danger"></i> Streak {{ $entry->current_streak }} Hari &bull; {{ $entry->total_juz_mutqin }} Juz Mutqin
            </small>
        </div>
    </div>

    <div class="text-end">
        <div class="fw-bold text-warning fs-6">{{ number_format($entry->total_points) }} <span style="font-size: 0.75rem;">pts</span></div>
        <small class="text-muted" style="font-size: 0.75rem;">{{ $entry->total_ayat }} Ayat</small>
    </div>
</div>
@endif
