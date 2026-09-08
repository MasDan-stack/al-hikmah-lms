<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public $notifications = [];

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        if (! Auth::check()) {
            return;
        }

        $userId = Auth::id();
        $this->unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        $this->notifications = Notification::where('user_id', $userId)
            ->latest()
            ->take(8)
            ->get();
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();

            if ($notification->action_url) {
                $this->redirect($notification->action_url);
            }
        }
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
