<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBadge extends Component
{
    public int $unreadCount = 0;

    protected $listeners = ['$refresh'];

    public function mount()
    {
        $this->loadCount();
    }

    public function loadCount()
    {
        if (! tenancy()->initialized || ! auth('tenant')->check()) {
            $this->unreadCount = 0;
            return;
        }

        try {
            $this->unreadCount = auth('tenant')->user()->unreadNotifications()->count();
        } catch (\Exception) {
            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        return <<<'HTML'
        <a href="{{ route('tenant.notifications') }}" class="notification-bell" aria-label="Notifications" wire:poll.15s>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            @if($unreadCount > 0)
                <span class="notification-badge">{{ min($unreadCount, 99) }}</span>
            @endif
        </a>
        HTML;
    }
}
