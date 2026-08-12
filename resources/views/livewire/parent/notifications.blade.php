<div class="dropdown">
    <button class="btn btn-light rounded-circle position-relative me-2 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell-fill text-secondary fs-5"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
                <span class="visually-hidden">notifikasi belum dibaca</span>
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 style-container" style="width: 340px; max-height: 420px; overflow-y: auto;">
        <div class="p-3 bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-bell me-2"></i>Notifikasi ({{ $unreadCount }} Baru)</h6>
            <button wire:click="loadNotifications" class="btn btn-sm btn-link text-white p-0 text-decoration-none" title="Refresh">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>

        <div class="list-group list-group-flush">
            @forelse($notifications as $notif)
                <div class="list-group-item p-3 border-bottom {{ $notif->is_read ? 'bg-light text-muted' : 'bg-white fw-bold' }}">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <span class="badge {{ $notif->type === 'payment_reminder' ? 'bg-danger' : 'bg-info' }} rounded-pill small">
                            {{ $notif->type === 'payment_reminder' ? '💰 SPP' : '📝 Catatan' }}
                        </span>
                        <small class="text-muted" style="font-size: 11px;">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="text-dark small fw-bold mb-1">{{ $notif->title }}</div>
                    <div class="small text-secondary mb-2" style="font-size: 12px; line-height: 1.3;">{{ $notif->message }}</div>

                    @if(!$notif->is_read)
                        <button wire:click="markAsRead({{ $notif->id }})" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2 small style-btn" style="font-size: 11px;">
                            <i class="bi bi-check2 me-1"></i>Tandai Dibaca
                        </button>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-bell-slash fs-4 d-block mb-1 text-secondary"></i>
                    Belum ada notifikasi baru.
                </div>
            @endforelse
        </div>
    </div>
</div>
