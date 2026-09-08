{{-- resources/views/components/stat-card.blade.php --}}
@props([
    'title' => '',
    'value' => '0',
    'icon' => 'bi-bar-chart-fill',
    'color' => 'primary',
    'subtitle' => null,
    'trend' => null,
    'trendUp' => true
])

<div class="stat-card">
    <div class="stat-icon bg-{{ $color }}-subtle text-{{ $color }}">
        <i class="bi {{ $icon }}"></i>
    </div>
    <div class="flex-grow-1">
        <h6 class="text-muted fw-semibold mb-1 small text-uppercase letter-spacing-1">{{ $title }}</h6>
        <div class="d-flex align-items-baseline gap-2">
            <h3 class="fw-bold text-dark mb-0">{{ $value }}</h3>
            @if($trend)
                <span class="small fw-semibold text-{{ $trendUp ? 'success' : 'danger' }}">
                    <i class="bi bi-arrow-{{ $trendUp ? 'up' : 'down' }}-short"></i>{{ $trend }}
                </span>
            @endif
        </div>
        @if($subtitle)
            <small class="text-muted">{{ $subtitle }}</small>
        @endif
    </div>
</div>
