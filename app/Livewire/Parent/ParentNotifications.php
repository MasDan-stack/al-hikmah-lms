<?php

namespace App\Livewire\Parent;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ParentNotifications extends Component
{
    public $notifications;

    public $unreadCount;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->limit(5)
            ->get();

        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->update([
                'is_read' => true,
            ]);
            $this->loadNotifications();
        }
    }

    public function render()
    {
        return view('livewire.parent.notifications');
    }
}
