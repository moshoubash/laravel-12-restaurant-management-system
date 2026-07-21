<div class="max-w-4xl mx-auto px-4 py-8 space-y-8">
    <h1 class="text-2xl font-bold text-on-surface">My Profile</h1>

    {{-- Profile Info --}}
    <div class="bg-surface-container rounded-xl border border-surface-container-high p-6">
        @if(!$showEdit)
            <div class="flex items-start justify-between">
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-secondary uppercase tracking-wide">Name</p>
                        <p class="font-medium text-on-surface">{{ $name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-secondary uppercase tracking-wide">Email</p>
                        <p class="text-on-surface">{{ $email ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-secondary uppercase tracking-wide">Phone</p>
                        <p class="text-on-surface">{{ $phone ?: '—' }}</p>
                    </div>
                </div>
                <button wire:click="openEdit" class="px-4 py-2 text-sm font-medium text-primary border border-primary rounded-lg hover:bg-primary/5 transition">Edit</button>
            </div>
        @else
            <form wire:submit="save" class="flex flex-col gap-4 max-w-md">
                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Name</label>
                    <input wire:model="name" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:ring-2 focus:ring-primary focus:outline-none">
                    @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Email</label>
                    <input wire:model="email" type="email" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:ring-2 focus:ring-primary focus:outline-none">
                    @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-on-surface mb-1">Phone</label>
                    <input wire:model="phone" type="tel" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:ring-2 focus:ring-primary focus:outline-none">
                    @error('phone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg font-medium text-sm hover:bg-primary/90 transition">Save</button>
                    <button type="button" wire:click="cancelEdit" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                </div>
            </form>
        @endif
    </div>

    {{-- Recent Orders --}}
    <div>
        <h2 class="text-lg font-bold text-on-surface mb-4">Recent Orders</h2>
        <div class="space-y-3">
            @forelse($this->recentOrders as $order)
                <div class="bg-surface-container rounded-xl border border-surface-container-high p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-on-surface">#{{ $order->order_number }}</p>
                        <p class="text-xs text-secondary">{{ $order->ordered_at?->format('M j, Y g:i A') }} · {{ ucfirst($order->order_type) }} · {{ $order->items->count() }} items</p>
                    </div>
                    <div class="text-right">
                        <span @class([
                            'text-xs font-bold px-2 py-0.5 rounded-full',
                            'bg-warning/20 text-warning' => $order->status === 'pending',
                            'bg-primary/20 text-primary' => $order->status === 'confirmed',
                            'bg-info/20 text-info' => $order->status === 'preparing',
                            'bg-success/20 text-success' => in_array($order->status, ['ready', 'served']),
                            'bg-tertiary/20 text-tertiary' => $order->status === 'completed',
                            'bg-error/20 text-error' => $order->status === 'cancelled',
                        ])>{{ ucfirst($order->status) }}</span>
                        <p class="text-sm font-bold text-on-surface mt-1">${{ number_format($order->total, 2) }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-secondary text-sm border border-dashed border-surface-container-high rounded-xl">
                    No orders yet. <a href="{{ route('tenant.menu') }}" class="text-primary hover:underline">Browse the menu</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
