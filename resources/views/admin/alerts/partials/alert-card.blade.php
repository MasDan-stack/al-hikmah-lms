<div class="card border-0 shadow-sm rounded-4 p-4 border-start border-4 {{ $borderClass ?? 'border-primary' }}" style="background: var(--card-bg);">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div class="d-flex align-items-start gap-3">
            <div class="badge rounded-circle p-3 {{ $badgeClass ?? 'bg-primary text-white' }} fs-5 mt-1">
                <i class="bi {{ $alert['icon'] ?? 'bi-bell-fill' }}"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="fw-bold mb-0" style="color: var(--text-primary);">{{ $alert['title'] }}</h5>
                    <span class="badge {{ $badgeClass ?? 'bg-primary text-white' }} rounded-pill px-2 py-1 small">
                        {{ $alert['count'] }} item
                    </span>
                </div>
                <p class="text-muted small mb-2">{{ $alert['description'] }}</p>

                <!-- Preview Items List if Available -->
                @if (!empty($alert['items']))
                    <div class="d-flex flex-column gap-1 mt-2 p-2 rounded-3" style="background: var(--bg-secondary); border: 1px dashed var(--border-color);">
                        @foreach ($alert['items'] as $item)
                            <div class="d-flex justify-content-between align-items-center small py-1 px-2">
                                <span class="fw-medium text-truncate" style="max-width: 320px;">
                                    <i class="bi bi-dot text-primary"></i> {{ $item['title'] }}
                                </span>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $item['subtitle'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if (!empty($alert['action_url']))
            <a href="{{ $alert['action_url'] }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1 flex-shrink-0">
                <span>{{ $alert['action_label'] ?? 'Tindak Lanjuti' }}</span>
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        @endif
    </div>
</div>
