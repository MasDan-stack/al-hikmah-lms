<div class="dropdown notification-dropdown" wire:poll.15s="loadNotifications">
    <button class="btn btn-icon position-relative rounded-circle p-2" 
            type="button" 
            id="notificationBellBtn" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            title="Notifikasi"
            style="background: rgba(13, 122, 62, 0.08); color: #0d7a3e; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
        <i class="bi bi-bell-fill fs-5"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" 
         aria-labelledby="notificationBellBtn" 
         style="width: 340px; max-width: 90vw; z-index: 1050;">
        
        <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light rounded-top-4">
            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bell-fill text-success"></i> Notifikasi
                @if($unreadCount > 0)
                    <span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 0.75rem;">{{ $unreadCount }} baru</span>
                @endif
            </div>
            @if($unreadCount > 0)
                <button type="button" wire:click="markAllAsRead" class="btn btn-link p-0 text-decoration-none text-success small" style="font-size: 0.8rem;">
                    Tandai semua dibaca
                </button>
            @endif
        </div>

        <div class="notification-list" style="max-height: 360px; overflow-y: auto;">
            @forelse($notifications as $notif)
                @php
                    $typeEnum = $notif->getTypeEnum();
                @endphp
                <div wire:click="markAsRead({{ $notif->id }})" 
                     class="p-3 border-bottom d-flex gap-3 align-items-start {{ $notif->is_read ? 'bg-white' : 'bg-success-subtle bg-opacity-25' }} text-decoration-none"
                     style="cursor: pointer; transition: background 0.2s ease;">
                    
                    <div class="flex-shrink-0 mt-1">
                        <span class="badge rounded-circle p-2 {{ $typeEnum->badgeClass() }}">
                            <i class="bi {{ $typeEnum->icon() }}"></i>
                        </span>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <span class="fw-semibold text-dark small">{{ $notif->title }}</span>
                            <span class="text-muted ms-2" style="font-size: 0.7rem; min-width: 50px; text-align: right;">{{ $notif->created_at->diffForHumans(null, true, true) }}</span>
                        </div>
                        <p class="text-secondary small mb-0 lh-sm" style="font-size: 0.8rem;">
                            {{ Str::limit($notif->message, 85) }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-bell-slash fs-2 d-block mb-2 text-secondary opacity-50"></i>
                    <span class="small">Belum ada notifikasi baru</span>
                </div>
            @endforelse
        </div>

        @if(count($notifications) > 0)
            <div class="p-2 text-center border-top bg-light rounded-bottom-4">
                @if(auth()->user()->isParent())
                    <a href="{{ route('parent.dashboard') }}" class="small text-decoration-none text-muted fw-medium">
                        Lihat Portal Orang Tua
                    </a>
                @elseif(auth()->user()->isMentor())
                    <a href="{{ route('mentor.dashboard') }}" class="small text-decoration-none text-muted fw-medium">
                        Lihat Portal Guru
                    </a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="small text-decoration-none text-muted fw-medium">
                        Lihat Dashboard Admin
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
