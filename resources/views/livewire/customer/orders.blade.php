<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-on-surface mb-6">My Orders</h1>

    <div class="space-y-3">
        @forelse($this->orders as $order)
            <div class="bg-surface-container rounded-xl border border-surface-container-high p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-on-surface">#{{ $order->order_number }}</p>
                        <p class="text-xs text-secondary mt-0.5">{{ $order->ordered_at?->format('M j, Y g:i A') }}</p>
                    </div>
                    <span @class([
                        'text-xs font-bold px-2 py-0.5 rounded-full',
                        'bg-warning/20 text-warning' => $order->status === 'pending',
                        'bg-primary/20 text-primary' => $order->status === 'confirmed',
                        'bg-info/20 text-info' => $order->status === 'preparing',
                        'bg-success/20 text-success' => in_array($order->status, ['ready', 'served']),
                        'bg-tertiary/20 text-tertiary' => $order->status === 'completed',
                        'bg-error/20 text-error' => $order->status === 'cancelled',
                    ])>{{ ucfirst($order->status) }}</span>
                </div>
                <div class="mt-3 space-y-1">
                    @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-on-surface">{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                            <span class="text-secondary">${{ number_format($item->total_price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-surface-container-high mt-3 pt-3 flex justify-between">
                    <span class="text-xs text-secondary">{{ ucfirst($order->order_type) }}</span>
                    <span class="font-bold text-on-surface">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-secondary">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="font-medium">No orders yet</p>
                <p class="text-sm mt-1">Once you place an order, it will appear here.</p>
            </div>
        @endforelse
    </div>
</div>
