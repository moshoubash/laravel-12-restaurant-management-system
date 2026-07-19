<div>
    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <div class="flex gap-1 rounded-lg bg-surface-container-high p-1">
            @foreach(['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
                <button wire:click="$set('activeTab', '{{ $key }}')" class="rounded-md px-3 py-1.5 text-sm font-medium transition-colors {{ $activeTab === $key ? 'bg-primary text-on-primary' : 'text-on-surface hover:bg-surface-container' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <button wire:click="openCreateForm" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">+ New Order</button>
    </div>

    {{-- Orders list or create form --}}
    @if($showCreateForm)
        <div class="flex gap-6">
            {{-- Menu sidebar --}}
            <div class="w-72 shrink-0 space-y-3">
                <h3 class="text-sm font-bold text-on-surface">MENU</h3>
                <div class="space-y-1">
                    @foreach($this->categories as $cat)
                        <button wire:click="selectCategory({{ $cat->id }})"
                                class="block w-full rounded-lg px-3 py-2 text-left text-sm transition-colors {{ $selectedCategoryId === $cat->id ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface hover:bg-surface-container-high' }}">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Items grid --}}
            <div class="flex-1">
                <h3 class="mb-3 text-sm font-bold text-on-surface">
                    {{ $this->categories->firstWhere('id', $selectedCategoryId)?->name ?? 'Select a category' }}
                </h3>
                <div class="grid grid-cols-3 gap-3">
                    @php $category = $this->categories->firstWhere('id', $selectedCategoryId); @endphp
                    @if($category)
                        @foreach($category->items as $item)
                            <button wire:click="addToCart({{ $item->id }})"
                                    class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-3 text-left transition-shadow hover:shadow-md">
                                <div class="flex h-20 items-center justify-center overflow-hidden rounded-lg bg-surface-container-high mb-2">
                                    @if($item->image)
                                        <img src="{{ $item->image }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-2xl text-secondary">🍽️</span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-on-surface truncate">{{ $item->name }}</p>
                                <p class="text-sm font-bold text-primary">${{ number_format($item->price, 2) }}</p>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Cart --}}
            <div class="w-80 shrink-0 space-y-3">
                <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-4">
                    <h3 class="text-sm font-bold text-on-surface">Order</h3>

                    <div class="mt-3 space-y-2">
                        <select wire:model="orderType" class="block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                            <option value="dine_in">Dine In</option>
                            <option value="takeaway">Takeaway</option>
                            <option value="delivery">Delivery</option>
                        </select>
                        <select wire:model="orderTableId" class="block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                            <option value="">No table</option>
                            @foreach($this->tables as $table)
                                <option value="{{ $table->id }}">T{{ $table->table_number }} {{ $table->section ? "($table->section)" : '' }}</option>
                            @endforeach
                        </select>
                        <input wire:model="customerName" type="text" placeholder="Customer name" class="block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface placeholder-secondary">
                    </div>

                    <div class="mt-3 max-h-64 space-y-2 overflow-y-auto">
                        @forelse($cart as $key => $item)
                            <div wire:key="cart-{{ $key }}" class="flex items-center gap-2 rounded-lg bg-surface-container p-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-on-surface truncate">{{ $item['name'] }}</p>
                                    <p class="text-xs text-secondary">${{ number_format($item['price'], 2) }} each</p>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click="updateCartQty('{{ $key }}', {{ $item['quantity'] - 1 }})" class="rounded p-1 hover:bg-surface-container-high">−</button>
                                    <span class="w-6 text-center text-sm font-bold">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateCartQty('{{ $key }}', {{ $item['quantity'] + 1 }})" class="rounded p-1 hover:bg-surface-container-high">+</button>
                                </div>
                                <p class="w-16 text-right text-sm font-bold">${{ number_format($item['total'], 2) }}</p>
                                <button wire:click="removeFromCart('{{ $key }}')" class="rounded p-1 text-error hover:bg-surface-container-high">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @empty
                            <p class="py-4 text-center text-sm text-secondary">Click menu items to add</p>
                        @endforelse
                    </div>

                    <div class="mt-3 border-t border-surface-container-high pt-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary">Subtotal</span>
                            <span class="font-bold">${{ number_format($this->cartTotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary">Tax (8%)</span>
                            <span class="font-bold">${{ number_format($this->cartTotal * 0.08, 2) }}</span>
                        </div>
                        <div class="mt-1 flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span>${{ number_format($this->cartTotal * 1.08, 2) }}</span>
                        </div>
                    </div>

                    <textarea wire:model="orderNotes" placeholder="Order notes..." rows="2" class="mt-3 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface placeholder-secondary"></textarea>

                    <button wire:click="createOrder" class="mt-3 w-full rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                        Place Order
                    </button>
                    @error('cart') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    @elseif($showDetail)
        {{-- Order detail --}}
        @php $order = $this->selectedOrder; @endphp
        @if($order)
            <div class="mx-auto max-w-2xl">
                <button wire:click="closeDetail" class="mb-4 flex items-center gap-1 text-sm text-secondary hover:text-on-surface">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>
                <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-on-surface">Order #{{ $order->order_number }}</h2>
                            <p class="mt-1 text-sm text-secondary">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ match($order->status) { 'pending' => 'bg-warning/10 text-warning', 'confirmed' => 'bg-info/10 text-info', 'preparing' => 'bg-primary/10 text-primary', 'ready' => 'bg-success/10 text-success', 'served' => 'bg-success/10 text-success', 'completed' => 'bg-surface-container-high text-secondary', 'cancelled' => 'bg-error/10 text-error' } }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                        <div><span class="text-secondary">Type</span><p class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</p></div>
                        <div><span class="text-secondary">Table</span><p class="font-medium">{{ $order->table ? 'T' . $order->table->table_number : '—' }}</p></div>
                        <div><span class="text-secondary">Customer</span><p class="font-medium">{{ $order->customer_name ?? '—' }}</p></div>
                    </div>

                    <div class="mt-4 space-y-2">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between rounded-lg bg-surface-container p-3">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-secondary">{{ $item->quantity }}×</span>
                                    <div>
                                        <p class="text-sm font-medium text-on-surface">{{ $item->menu_item_name }}</p>
                                        @if($item->modifiers)
                                            <p class="text-xs text-secondary">{{ collect($item->modifiers)->pluck('name')->implode(', ') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm font-bold">${{ number_format($item->total_price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 border-t border-surface-container-high pt-4 space-y-1 text-sm">
                        <div class="flex justify-between"><span class="text-secondary">Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-secondary">Tax</span><span>${{ number_format($order->tax, 2) }}</span></div>
                        <div class="flex justify-between text-lg font-bold"><span>Total</span><span>${{ number_format($order->total, 2) }}</span></div>
                    </div>

                    @if($order->notes)
                        <div class="mt-4 rounded-lg bg-surface-container p-3 text-sm text-secondary">
                            <span class="font-medium text-on-surface">Notes:</span> {{ $order->notes }}
                        </div>
                    @endif

                    {{-- Status workflow --}}
                    <div class="mt-6 flex flex-wrap gap-2">
                        @php $statusFlow = ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed']; @endphp
                        @php $currentIdx = array_search($order->status, $statusFlow); @endphp
                        @foreach($statusFlow as $idx => $status)
                            @if($idx === $currentIdx)
                                <button wire:click="updateStatus({{ $order->id }}, '{{ $statusFlow[$idx + 1] ?? $status }}')"
                                        class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                                    Mark {{ ucfirst($statusFlow[$idx + 1] ?? '') }}
                                </button>
                                @break
                            @endif
                        @endforeach
                        @if($order->status !== 'cancelled' && $order->status !== 'completed')
                            <button wire:click="updateStatus({{ $order->id }}, 'cancelled')" class="rounded border border-error px-4 py-2 text-sm font-bold text-error hover:bg-error/10">Cancel Order</button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Orders list --}}
        <div class="space-y-3">
            @forelse($this->orders as $order)
                <div wire:key="order-{{ $order->id }}" wire:click="viewOrder({{ $order->id }})"
                     class="flex cursor-pointer items-center gap-4 rounded-xl border border-surface-container-high bg-surface-container-lowest p-4 transition-shadow hover:shadow-md">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-on-surface">#{{ $order->order_number }}</span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ match($order->status) { 'pending' => 'bg-warning/10 text-warning', 'confirmed' => 'bg-info/10 text-info', 'preparing' => 'bg-primary/10 text-primary', 'ready' => 'bg-success/10 text-success', 'served' => 'bg-success/10 text-success', 'completed' => 'bg-surface-container-high text-secondary', 'cancelled' => 'bg-error/10 text-error' } }}">
                                {{ ucfirst($order->status) }}
                            </span>
                            <span class="text-xs text-secondary">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</span>
                        </div>
                        <div class="mt-1 flex gap-4 text-sm text-secondary">
                            <span>{{ $order->items->count() }} items</span>
                            @if($order->table)<span>Table {{ $order->table->table_number }}</span>@endif
                            @if($order->customer_name)<span>{{ $order->customer_name }}</span>@endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-primary">${{ number_format($order->total, 2) }}</p>
                        <p class="text-xs text-secondary">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-secondary">No orders found.</div>
            @endforelse
        </div>
    @endif
</div>
