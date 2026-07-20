<?php

namespace App\Livewire;

use Livewire\Component;

class Notifications extends Component
{
    public int $unreadCount = 0;
    public array $recentNotifications = [];
    public string $filter = 'all';

    protected $listeners = ['refreshNotifications' => '$refresh'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth('tenant')->user();
        if (! $user) return;

        $query = $user->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->take(50)->get();

        $this->unreadCount = $user->unreadNotifications()->count();
        $this->recentNotifications = $notifications->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->data['title'] ?? 'Notification',
            'message' => $n->data['message'] ?? '',
            'type' => $n->data['type'] ?? 'info',
            'read' => $n->read_at !== null,
            'created_at' => $n->created_at->diffForHumans(),
            'time' => $n->created_at->format('M d, g:i A'),
            'url' => $n->data['url'] ?? null,
        ])->toArray();
    }

    public function markAsRead($notificationId)
    {
        $user = auth('tenant')->user();
        if (! $user) return;

        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }

        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        $user = auth('tenant')->user();
        if (! $user) return;

        $user->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function deleteNotification($notificationId)
    {
        $user = auth('tenant')->user();
        if (! $user) return;

        $user->notifications()->find($notificationId)?->delete();
        $this->loadNotifications();
    }

    public function clearAll()
    {
        $user = auth('tenant')->user();
        if (! $user) return;

        $user->notifications()->delete();
        $this->loadNotifications();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->loadNotifications();
    }

    public function render()
    {
        $user = auth('tenant')->user();
        $layout = 'layouts.admin';

        if ($user) {
            if ($user->hasRole('chef')) $layout = 'layouts.kitchen';
            elseif ($user->hasRole('waiter')) $layout = 'layouts.waiter';
            elseif ($user->hasRole('cashier')) $layout = 'layouts.cashier';
            elseif ($user->hasRole('manager')) $layout = 'layouts.manager';
        }

        return view('livewire.notifications', [
            'notifications' => $this->recentNotifications,
        ])->layout($layout);
    }
}
