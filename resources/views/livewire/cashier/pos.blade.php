<div class="flex h-full gap-4">
    {{-- Left: Menu --}}
    <div class="flex w-96 shrink-0 flex-col gap-3">
        <input wire:model.live="search" type="text" placeholder="Search menu..." class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2 text-sm text-on-surface placeholder-secondary focus:border-primary focus:ring-primary">

        <div class="flex gap-1 overflow-x-auto pb-1">
            @foreach($this->categories as $cat)
                <button wire:click="selectCategory({{ $cat->id }})"
                        class="shrink-0 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap transition-colors {{ $selectedCategoryId === $cat->id ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface hover:bg-surface-container' }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <div class="flex-1 space-y-2 overflow-y-auto">
            @forelse($this->items as $item)
                <button wire:click="addItem({{ $item->id }})"
                        wire:key="item-{{ $item->id }}"
                        class="flex w-full items-center gap-3 rounded-xl border border-surface-container-high bg-surface-container-lowest p-3 text-left transition-shadow hover:shadow-md">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-surface-container-high text-lg">
                        @if($item->image)
                            <img src="{{ $item->image }}" alt="" class="h-full w-full rounded-lg object-cover">
                        @else
                            🍽️
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-on-surface truncate">{{ $item->name }}</p>
                        <p class="text-xs text-secondary truncate">{{ $item->description }}</p>
                        <p class="mt-0.5 text-sm font-bold text-primary">${{ number_format($item->price, 2) }}</p>
                    </div>
                </button>
            @empty
                <div class="py-8 text-center text-sm text-secondary">No items found</div>
            @endforelse
        </div>
    </div>

    {{-- Right: Cart & Checkout --}}
    <div class="flex flex-1 flex-col rounded-xl border border-surface-container-high bg-surface-container-lowest">
        {{-- Order header --}}
        <div class="flex items-center gap-3 border-b border-surface-container-high p-4">
            <select wire:model="orderType" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="dine_in">Dine In</option>
                <option value="takeaway">Takeaway</option>
                <option value="delivery">Delivery</option>
            </select>
            <select wire:model="orderTableId" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="">No table</option>
                @foreach($this->tables as $table)
                    <option value="{{ $table->id }}">T{{ $table->table_number }} {{ $table->section ? "($table->section)" : '' }}</option>
                @endforeach
            </select>
            <select wire:model="customerId" class="flex-1 rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="">Walk-in customer</option>
                @foreach($this->customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Cart items --}}
        <div class="flex-1 space-y-2 overflow-y-auto p-4">
            @forelse($cart as $key => $item)
                <div wire:key="cart-{{ $key }}" class="flex items-center gap-3 rounded-lg border border-surface-container-high bg-surface-container p-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-on-surface">{{ $item['name'] }}</p>
                        <p class="text-xs text-secondary">${{ number_format($item['price'], 2) }} each</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="updateQty('{{ $key }}', {{ $item['quantity'] - 1 }})" class="flex h-7 w-7 items-center justify-center rounded bg-surface-container-high text-sm font-bold hover:bg-surface-container">−</button>
                        <span class="flex h-7 w-8 items-center justify-center text-sm font-bold">{{ $item['quantity'] }}</span>
                        <button wire:click="updateQty('{{ $key }}', {{ $item['quantity'] + 1 }})" class="flex h-7 w-7 items-center justify-center rounded bg-surface-container-high text-sm font-bold hover:bg-surface-container">+</button>
                    </div>
                    <p class="w-16 text-right text-sm font-bold">${{ number_format($item['total'], 2) }}</p>
                    <button wire:click="removeItem('{{ $key }}')" class="rounded p-1 text-error hover:bg-surface-container-high">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @empty
                <div class="flex h-full items-center justify-center text-sm text-secondary">
                    <div class="text-center">
                        <p class="text-4xl mb-2">🛒</p>
                        <p>Select items from the menu</p>
                    </div>
                </div>
            @endforelse
            @error('cart') <p class="text-sm text-error">{{ $message }}</p> @enderror
        </div>

        {{-- Totals & actions --}}
        <div class="border-t border-surface-container-high p-4 space-y-3">
            {{-- Discount --}}
            <div class="flex items-center gap-2">
                <select wire:model="discountType" class="rounded border border-surface-container-high bg-surface-container-lowest px-2 py-1 text-sm text-on-surface">
                    <option value="">No discount</option>
                    <option value="percentage">% Percentage</option>
                    <option value="fixed">$ Fixed</option>
                </select>
                @if($discountType)
                    <input wire:model="discountValue" type="number" step="0.01" min="0" placeholder="Value" class="w-24 rounded border border-surface-container-high bg-surface-container-lowest px-2 py-1 text-sm text-on-surface">
                @endif
            </div>

            {{-- Totals --}}
            <div class="space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-secondary">Subtotal</span><span>${{ number_format($this->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-secondary">Tax (8%)</span><span>${{ number_format($this->tax, 2) }}</span></div>
                @if($this->discountAmount > 0)
                    <div class="flex justify-between text-success"><span>Discount</span><span>−${{ number_format($this->discountAmount, 2) }}</span></div>
                @endif
                <div class="flex justify-between text-lg font-bold border-t border-surface-container-high pt-1">
                    <span>Total</span>
                    <span>${{ number_format($this->total, 2) }}</span>
                </div>
            </div>

            <textarea wire:model="orderNotes" placeholder="Notes..." rows="1" class="w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface placeholder-secondary"></textarea>

            <div class="flex gap-2">
                <button wire:click="clearCart" class="flex-1 rounded border border-surface-container-high px-4 py-2 text-sm font-bold text-on-surface hover:bg-surface-container">Clear</button>
                <button wire:click="openCheckout" class="flex-1 rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Charge ${{ number_format($this->total, 2) }}</button>
            </div>
        </div>
    </div>

    {{-- Checkout modal --}}
    @if($showCheckout)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelCheckout">
            <div class="w-full max-w-md rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl">
                <h3 class="text-lg font-bold text-on-surface">Complete Payment</h3>
                <div class="mt-4 space-y-4">
                    <div class="rounded-lg bg-surface-container p-4 space-y-1 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>${{ number_format($this->subtotal, 2) }}</span></div>
                        <div class="flex justify-between"><span>Tax</span><span>${{ number_format($this->tax, 2) }}</span></div>
                        @if($this->discountAmount > 0)
                            <div class="flex justify-between text-success"><span>Discount</span><span>−${{ number_format($this->discountAmount, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between text-lg font-bold border-t border-surface-container-high pt-1">
                            <span>Total Due</span>
                            <span>${{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface">Payment Method</label>
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            @foreach(['cash' => 'Cash', 'card' => 'Card', 'mobile' => 'Mobile'] as $val => $label)
                                <button wire:click="$set('paymentMethod', '{{ $val }}')"
                                        class="rounded-lg border-2 px-3 py-3 text-center text-sm font-bold transition-colors {{ $paymentMethod === $val ? 'border-primary bg-primary-container text-on-primary-container' : 'border-surface-container-high text-on-surface hover:bg-surface-container' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface">Amount Received</label>
                        <input wire:model="paymentAmount" type="number" step="0.01" min="{{ $this->total }}" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-lg font-bold text-on-surface focus:border-primary focus:ring-primary">
                        @error('paymentAmount') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface">Reference (optional)</label>
                        <input wire:model="paymentReference" type="text" placeholder="Transaction ID" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:border-primary focus:ring-primary">
                    </div>

                    @php $change = max(0, (float) $this->paymentAmount - $this->total); @endphp
                    @if($change > 0)
                        <div class="rounded-lg bg-surface-container p-3 text-center">
                            <span class="text-sm text-secondary">Change due:</span>
                            <span class="ml-2 text-xl font-bold text-success">${{ number_format($change, 2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="cancelCheckout" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                    <button wire:click="processPayment" class="rounded bg-primary px-6 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                        Complete Payment
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Success modal --}}
    @if($showSuccess)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-xl bg-surface-container border border-surface-container-high p-8 text-center shadow-xl">
                <div class="text-5xl mb-4">✅</div>
                <h3 class="text-xl font-bold text-on-surface">Payment Complete!</h3>
                <p class="mt-2 text-secondary">Order #{{ $lastOrderNumber }}</p>
                @if($lastOrderId)
                    <a href="{{ route('tenant.receipt', ['orderId' => $lastOrderId]) }}" target="_blank" class="mt-4 block w-full rounded border border-primary px-4 py-2 text-sm font-bold text-primary hover:bg-primary/5">🖨️ Print Receipt</a>
                @endif
                <button wire:click="newOrder" class="mt-2 w-full rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">New Order</button>
            </div>
        </div>
    @endif
</div>
