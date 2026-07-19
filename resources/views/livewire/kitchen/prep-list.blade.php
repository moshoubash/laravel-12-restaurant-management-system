<div class="p-4">
    <h2 class="text-lg font-bold text-on-surface mb-4">Prep List</h2>
    <div class="grid grid-cols-2 gap-4">
        @forelse($this->orders as $order)
            <div wire:key="order-{{ $order->id }}" class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-4 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="font-bold text-on-surface">#{{ $order->order_number }}</span>
                        <span class="ml-2 text-xs text-secondary">{{ ucfirst($order->order_type) }}</span>
                    </div>
                    <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $order->status === 'confirmed' ? 'bg-warning/10 text-warning' : 'bg-primary/10 text-primary' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="mt-3 space-y-1">
                    @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span>{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-secondary">
                    <span>Table {{ $order->table?->table_number ?? '—' }} · {{ $order->ordered_at?->diffForHumans() }}</span>
                </div>
                <div class="mt-3 flex gap-2">
                    @if($order->status === 'confirmed')
                        <button wire:click="markPreparing({{ $order->id }})" class="flex-1 rounded bg-primary px-3 py-1.5 text-xs font-bold text-on-primary hover:bg-primary-container">Start Preparing</button>
                    @endif
                    @if($order->status === 'preparing')
                        <button wire:click="markReady({{ $order->id }})" class="flex-1 rounded bg-success px-3 py-1.5 text-xs font-bold text-white hover:bg-success/80">Mark Ready</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-2 py-12 text-center text-secondary">No pending orders.</div>
        @endforelse
    </div>
</div>
