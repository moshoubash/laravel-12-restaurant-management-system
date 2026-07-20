<div class="space-y-6" wire:poll.30s>
    <div class="section-header">
        <div>
            <h1 class="section-title">Notifications</h1>
            <p class="section-description">Stay updated with what's happening in your restaurant</p>
        </div>
        <div class="section-actions">
            @if(count($notifications) > 0)
                <button wire:click="markAllAsRead" class="btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mark All Read
                </button>
                <button wire:click="clearAll" class="btn-ghost btn-sm text-surface-500" onclick="return confirm('Clear all notifications?')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clear All
                </button>
            @endif
        </div>
    </div>

    {{-- Filter tabs --}}
    <div class="tabs-pills">
        <button wire:click="setFilter('all')" class="tab-pill {{ $filter === 'all' ? 'tab-pill-active' : '' }}">
            All
            @if($unreadCount > 0)
                <span class="ml-1.5 text-xs font-bold">({{ $unreadCount }})</span>
            @endif
        </button>
        <button wire:click="setFilter('unread')" class="tab-pill {{ $filter === 'unread' ? 'tab-pill-active' : '' }}">
            Unread
            @if($unreadCount > 0)
                <span class="ml-1.5 text-xs font-bold">({{ $unreadCount }})</span>
            @endif
        </button>
    </div>

    {{-- Notification list --}}
    <div class="card p-0 overflow-hidden">
        @forelse($notifications as $notification)
            <div class="flex items-start gap-4 px-5 py-4 border-b border-surface-100 last:border-0 hover:bg-surface-50/50 transition-colors {{ !$notification['read'] ? 'bg-primary-50/20' : '' }}">
                {{-- Icon --}}
                <div class="flex-shrink-0 mt-0.5">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ !$notification['read'] ? 'bg-primary-100' : 'bg-surface-100' }}">
                        @switch($notification['type'])
                            @case('order')
                                <svg class="w-5 h-5 {{ !$notification['read'] ? 'text-primary' : 'text-surface-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                @break
                            @case('reservation')
                                <svg class="w-5 h-5 {{ !$notification['read'] ? 'text-primary' : 'text-surface-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @break
                            @case('alert')
                                <svg class="w-5 h-5 {{ !$notification['read'] ? 'text-error' : 'text-surface-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                @break
                            @default
                                <svg class="w-5 h-5 {{ !$notification['read'] ? 'text-primary' : 'text-surface-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @endswitch
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium {{ !$notification['read'] ? 'text-surface-900' : 'text-surface-600' }}">
                                {{ $notification['title'] }}
                            </p>
                            @if($notification['message'])
                                <p class="text-xs text-surface-500 mt-0.5">{{ $notification['message'] }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-xs text-surface-400 whitespace-nowrap">{{ $notification['time'] }}</span>
                            @if(!$notification['read'])
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        @if(!$notification['read'])
                            <button wire:click="markAsRead('{{ $notification['id'] }}')" class="text-xs font-medium text-primary hover:text-primary-700 transition-colors">
                                Mark read
                            </button>
                        @endif
                        <button wire:click="deleteNotification('{{ $notification['id'] }}')" class="text-xs font-medium text-surface-400 hover:text-error transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state py-12">
                <svg class="w-12 h-12 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <h3 class="empty-state-title">All clear</h3>
                <p class="empty-state-description">No notifications {{ $filter === 'unread' ? 'to read' : 'yet' }}. We'll let you know when something needs your attention.</p>
            </div>
        @endforelse
    </div>
</div>
