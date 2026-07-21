<div class="flex flex-col gap-4">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <h2 class="text-lg font-bold text-on-surface">Orders</h2>
        <div class="flex items-center gap-2">
            <select wire:model.live="filterType" class="rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Types</option>
                <option value="dine_in">Dine-in</option>
                <option value="takeaway">Takeaway</option>
                <option value="online">Online</option>
            </select>
            <select wire:model.live="filterStatus" class="rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Status</option>
                <option value="confirmed">Confirmed</option>
                <option value="preparing">Preparing</option>
                <option value="ready">Ready</option>
                <option value="served">Served</option>
            </select>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 gap-4">
        <div class="rounded-xl bg-warning/10 border border-warning/20 p-4">
            <p class="text-xs text-warning uppercase tracking-wide font-bold">Pending / Confirmed</p>
            <p class="text-2xl font-bold text-on-surface mt-1">{{ $this->pendingOrders }}</p>
        </div>
        <div class="rounded-xl bg-success/10 border border-success/20 p-4">
            <p class="text-xs text-success uppercase tracking-wide font-bold">Ready to Serve</p>
            <p class="text-2xl font-bold text-on-surface mt-1">{{ $this->readyOrders }}</p>
        </div>
    </div>

    {{-- Orders list --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->orders as $order)
            <div class="rounded-xl border border-surface-container-high bg-surface-container p-4 hover:shadow-md transition cursor-pointer"
                 wire:click="viewOrder({{ $order->id }})">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-on-surface">#{{ $order->order_number }}</p>
                        <p class="text-xs text-secondary mt-0.5">
                            @if($order->table)
                                Table T{{ $order->table->table_number }} · 
                            @endif
                            {{ ucfirst($order->order_type) }}
                        </p>
                    </div>
                    <span @class([
                        'px-2 py-0.5 rounded-full text-xs font-bold',
                        'bg-warning/20 text-warning' => $order->status === 'confirmed' || $order->status === 'pending',
                        'bg-primary/20 text-primary' => $order->status === 'preparing',
                        'bg-success/20 text-success' => $order->status === 'ready',
                        'bg-tertiary/20 text-tertiary' => $order->status === 'served',
                    ])>{{ ucfirst($order->status) }}</span>
                </div>
                <div class="mt-3 text-xs text-secondary">
                    <div class="flex justify-between">
                        <span>{{ $order->ordered_at?->diffForHumans() }}</span>
                        <span>{{ $order->items->sum('quantity') }} items</span>
                    </div>
                </div>
                <div class="mt-2 border-t border-surface-container-high pt-2 flex justify-between text-sm">
                    <span class="text-secondary">{{ $order->customer_name ?? 'Guest' }}</span>
                    <span class="font-bold text-on-surface">${{ number_format($order->total, 2) }}</span>
                </div>
                @if($order->status === 'ready')
                    <button wire:click.stop="markServed({{ $order->id }})" class="mt-3 w-full py-2 bg-success text-on-success rounded-lg text-sm font-bold hover:bg-success/90 transition">
                        Mark Served
                    </button>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-secondary">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="font-medium">No active orders</p>
                <p class="text-sm mt-1">Orders will appear here once they are placed.</p>
            </div>
        @endforelse
    </div>

    {{-- Order detail modal --}}
    @if($showDetail && $selectedOrderId)
        @php $order = \App\Models\Tenant\Order::with(['items', 'table', 'user'])->find($selectedOrderId); @endphp
        @if($order)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeDetail">
                <div class="w-full max-w-lg rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-on-surface">Order #{{ $order->order_number }}</h3>
                            <p class="text-sm text-secondary mt-0.5">
                                {{ $order->ordered_at?->format('M j, Y g:i A') }}
                                @if($order->table) · Table T{{ $order->table->table_number }} @endif
                            </p>
                        </div>
                        <span @class([
                            'px-2 py-0.5 rounded-full text-xs font-bold',
                            'bg-warning/20 text-warning' => in_array($order->status, ['pending', 'confirmed']),
                            'bg-primary/20 text-primary' => $order->status === 'preparing',
                            'bg-success/20 text-success' => $order->status === 'ready',
                            'bg-tertiary/20 text-tertiary' => $order->status === 'served',
                            'bg-surface-container-high text-secondary' => $order->status === 'completed',
                        ])>{{ ucfirst($order->status) }}</span>
                    </div>

                    <div class="mt-4 space-y-1">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between py-2 border-b border-surface-container-high last:border-0">
                                <div class="flex items-center gap-2">
                                    @if($item->status === 'served')
                                        <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <button wire:click="itemServed({{ $order->id }}, {{ $item->id }})" class="w-4 h-4 rounded border border-surface-container-high hover:border-primary" title="Mark as served"></button>
                                    @endif
                                    <span class="text-sm">{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                                </div>
                                <span class="text-sm text-secondary">${{ number_format($item->total_price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-3 border-t border-surface-container-high space-y-1 text-sm">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span class="text-primary">${{ number_format($order->total, 2) }}</span>
                        </div>
                        @if($order->customer_name)
                            <div class="flex justify-between text-secondary">
                                <span>Customer</span>
                                <span>{{ $order->customer_name }}</span>
                            </div>
                        @endif
                        @if($order->notes)
                            <div class="mt-2 p-2 bg-surface-container rounded text-xs text-secondary">
                                <p class="font-medium">Notes:</p>
                                <p>{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button wire:click="closeDetail" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Close</button>
                        <a href="{{ route('tenant.receipt', ['orderId' => $order->id]) }}" target="_blank" class="rounded-lg bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary/90">🖨️ Print Receipt</a>
                        @if($order->status === 'ready')
                            <button wire:click="markServed({{ $order->id }})" class="rounded-lg bg-success px-4 py-2 text-sm font-bold text-on-success hover:bg-success/90">Mark Served</button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
