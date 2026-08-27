@props(['badge' => null, 'isEarned' => false, 'earnedAt' => null])

@if($badge)
<div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 position-relative overflow-hidden {{ $isEarned ? 'bg-light-subtle' : 'bg-secondary-subtle opacity-60' }}"
     style="{{ $isEarned ? 'border: 1px solid rgba(255, 193, 7, 0.3) !important;' : '' }}">
    
    @if($isEarned)
        <div class="position-absolute top-0 end-0 p-2">
            <span class="badge bg-warning text-dark rounded-pill" title="Lencana Diraih!">
                <i class="bi bi-patch-check-fill"></i>
            </span>
        </div>
    @endif

    <div class="my-2">
        <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-sm {{ $isEarned ? 'bg-warning bg-opacity-10 text-warning border border-warning' : 'bg-secondary bg-opacity-10 text-secondary' }}" 
             style="width: 60px; height: 60px;">
            <i class="bi {{ $badge->icon }} fs-2"></i>
        </div>
    </div>

    <h6 class="fw-bold mb-1 mt-2 text-truncate" title="{{ $badge->name }}">{{ $badge->name }}</h6>
    <p class="text-muted small mb-2" style="font-size: 0.75rem; min-height: 36px;">
        {{ $badge->description }}
    </p>

    <div class="mt-auto pt-2 border-top">
        @if($isEarned)
            <span class="badge bg-success-subtle text-success fw-bold px-2 py-1" style="font-size: 0.75rem;">
                +{{ $badge->points_reward }} Pts &bull; Diraih
            </span>
            @if($earnedAt)
                <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($earnedAt)->translatedFormat('d M Y') }}</small>
            @endif
        @else
            <span class="badge bg-secondary-subtle text-secondary fw-normal px-2 py-1" style="font-size: 0.75rem;">
                <i class="bi bi-lock-fill me-1"></i> Terkunci (+{{ $badge->points_reward }} Pts)
            </span>
        @endif
    </div>
</div>
@endif
