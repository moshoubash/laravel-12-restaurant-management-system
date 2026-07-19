<div class="p-4">
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold text-on-surface">Kitchen Display</h1>
        <div class="flex gap-2 text-sm">
            <span class="rounded bg-warning/10 px-2 py-1 font-bold text-warning">New: {{ $this->newOrders->count() }}</span>
            <span class="rounded bg-primary/10 px-2 py-1 font-bold text-primary">Preparing: {{ $this->preparingOrders->count() }}</span>
            <span class="rounded bg-success/10 px-2 py-1 font-bold text-success">Ready: {{ $this->readyOrders->count() }}</span>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        {{-- New orders column --}}
        <div class="space-y-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-warning">New</h2>
            @forelse($this->newOrders as $order)
                <div wire:key="new-{{ $order->id }}" class="rounded-xl border-2 border-warning/30 bg-surface-container-lowest p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-lg font-bold text-on-surface">#{{ $order->order_number }}</span>
                            @if($order->table)<span class="ml-2 text-sm text-secondary">T{{ $order->table->table_number }}</span>@endif
                        </div>
                        <span class="text-xs text-secondary">{{ $order->confirmed_at?->format('h:i A') }}</span>
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach($order->items as $item)
                            <div class="rounded-lg bg-surface-container p-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="font-bold text-on-surface">{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                                        @if($item->modifiers)
                                            <p class="text-xs text-secondary">{{ collect($item->modifiers)->pluck('name')->implode(', ') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($item->notes)
                                    <p class="mt-1 text-xs text-warning">📝 {{ $item->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="startPreparing({{ $order->id }})" class="mt-3 w-full rounded bg-warning px-3 py-2 text-sm font-bold text-white hover:bg-warning/80">
                        Start Preparing
                    </button>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-secondary">No new orders</div>
            @endforelse
        </div>

        {{-- Preparing column --}}
        <div class="space-y-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-primary">Preparing</h2>
            @forelse($this->preparingOrders as $order)
                <div wire:key="prep-{{ $order->id }}" class="rounded-xl border-2 border-primary/30 bg-surface-container-lowest p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-lg font-bold text-on-surface">#{{ $order->order_number }}</span>
                            @if($order->table)<span class="ml-2 text-sm text-secondary">T{{ $order->table->table_number }}</span>@endif
                        </div>
                        <span class="text-xs text-secondary">{{ $order->preparing_at?->format('h:i A') }}</span>
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach($order->items as $item)
                            <div class="rounded-lg bg-surface-container p-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="font-bold text-on-surface">{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                                        @if($item->modifiers)
                                            <p class="text-xs text-secondary">{{ collect($item->modifiers)->pluck('name')->implode(', ') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($item->notes)
                                    <p class="mt-1 text-xs text-warning">📝 {{ $item->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="markReady({{ $order->id }})" class="mt-3 w-full rounded bg-primary px-3 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                        Mark Ready
                    </button>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-secondary">No orders preparing</div>
            @endforelse
        </div>

        {{-- Ready column --}}
        <div class="space-y-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-success">Ready</h2>
            @forelse($this->readyOrders as $order)
                <div wire:key="ready-{{ $order->id }}" class="rounded-xl border-2 border-success/30 bg-surface-container-lowest p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-lg font-bold text-on-surface">#{{ $order->order_number }}</span>
                            @if($order->table)<span class="ml-2 text-sm text-secondary">T{{ $order->table->table_number }}</span>@endif
                        </div>
                        <span class="text-xs text-secondary">{{ $order->ready_at?->format('h:i A') }}</span>
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach($order->items as $item)
                            <div class="rounded-lg bg-surface-container p-2">
                                <span class="font-bold text-on-surface">{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 rounded-lg bg-success/10 p-2 text-center text-sm font-bold text-success">Ready to serve!</div>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-secondary">No ready orders</div>
            @endforelse
        </div>
    </div>
</div>
