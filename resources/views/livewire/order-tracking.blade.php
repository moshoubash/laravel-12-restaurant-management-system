<div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-on-surface">Track Your Order</h1>
            <p class="text-secondary mt-1">Enter your order number to check the status</p>
        </div>

        <form wire:submit="track" class="flex gap-2">
            <input wire:model="orderNumber" placeholder="e.g. ONL-20260719-ABC123"
                   class="flex-1 px-4 py-3 rounded-xl border border-surface-container-high bg-surface-container text-on-surface placeholder-secondary focus:outline-none focus:ring-2 focus:ring-primary">
            <button type="submit" class="px-6 py-3 bg-primary text-on-primary rounded-xl font-bold hover:bg-primary/90 transition">Track</button>
        </form>
        @error('orderNumber') <p class="text-sm text-error mt-1">{{ $message }}</p> @enderror

        @if($searched)
            <div class="mt-8">
                @if($order)
                    <div class="rounded-xl border border-surface-container-high bg-surface-container overflow-hidden">
                        <div class="p-5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-secondary">Order #</p>
                                    <p class="text-lg font-bold text-on-surface">{{ $order->order_number }}</p>
                                </div>
                                <span @class([
                                    'px-3 py-1 rounded-full text-xs font-bold',
                                    'bg-warning/20 text-warning' => $order->status === 'pending',
                                    'bg-primary/20 text-primary' => $order->status === 'confirmed',
                                    'bg-info/20 text-info' => $order->status === 'preparing',
                                    'bg-success/20 text-success' => in_array($order->status, ['ready', 'served']),
                                    'bg-tertiary/20 text-tertiary' => $order->status === 'completed',
                                    'bg-error/20 text-error' => $order->status === 'cancelled',
                                ])>{{ ucfirst($order->status) }}</span>
                            </div>

                            <div class="mt-4 space-y-2">
                                @foreach($order->items as $item)
                                    <div class="flex justify-between text-sm">
                                        <span>{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                                        <span class="text-secondary">${{ number_format($item->total_price, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t border-surface-container-high mt-4 pt-3">
                                <div class="flex justify-between font-bold">
                                    <span>Total</span>
                                    <span>${{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>

                            <div class="mt-4 text-xs text-secondary space-y-1">
                                <p>Ordered: {{ $order->ordered_at?->format('M j, Y g:i A') }}</p>
                                @if($order->order_type === 'dine_in')
                                    <p>Table: #{{ $order->table?->table_number ?? '—' }}</p>
                                @else
                                    <p>Takeaway</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 rounded-xl border border-dashed border-surface-container-high">
                        <svg class="w-12 h-12 mx-auto mb-3 text-secondary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-secondary font-medium">Order not found</p>
                        <p class="text-sm text-secondary mt-1">Check the order number and try again.</p>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('tenant.menu') }}" class="text-sm text-primary hover:underline">Back to Menu</a>
        </div>
    </div>
</div>
