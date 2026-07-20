<div x-data="{
    cartOpen: @entangle('showCheckout'),
    checkoutOpen: @entangle('showCheckout'),
    showAddedToast: false,
    addedToastName: '',
    showConfirmedToast: false,
 }"
     x-on:added-to-cart.window="addedToastName = $event.detail.name; showAddedToast = true; setTimeout(() => showAddedToast = false, 2000)"
     x-on:confirmed-order.window="showConfirmedToast = true; setTimeout(() => showConfirmedToast = false, 3000)"
     class="min-h-screen flex flex-col">
    {{-- "Added to cart" toast --}}
    <div x-show="showAddedToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-success text-on-success px-4 py-2 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2"
         style="display: none;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span x-text="'Added ' + addedToastName + ' to cart'"></span>
    </div>

    {{-- "Order confirmed" toast --}}
    <div x-show="showConfirmedToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed top-16 left-1/2 -translate-x-1/2 z-50 bg-primary text-on-primary px-5 py-3 rounded-xl shadow-lg text-sm font-medium"
         style="display: none;">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <div>
                <p class="font-semibold">Order confirmed!</p>
                <p class="text-xs opacity-90">Check your email for details &middot; <a href="{{ route('tenant.track-order') }}" class="font-bold underline">Track order</a></p>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-surface-container-lowest border-b border-surface-container-high shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-primary">{{ config('app.name', 'Menu') }}</h1>
                @if($this->table)
                    <p class="text-sm text-secondary">Table #{{ $this->table->table_number }} &middot; Dine-in</p>
                @endif
            </div>
            <button wire:click="openCheckout" class="relative flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary/90 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                <span>Cart</span>
                            @if($this->cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-error text-on-error text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $this->cartCount }}</span>
                            @endif
            </button>
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto px-4 py-6 w-full">
        {{-- Category Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-2 mb-6 scrollbar-none">
            @foreach($this->categories as $cat)
                <button wire:click="selectCategory({{ $cat->id }})"
                    @class([
                        'px-4 py-2 rounded-full whitespace-nowrap font-medium text-sm transition border',
                        'bg-primary text-on-primary border-primary' => $selectedCategoryId == $cat->id,
                        'bg-surface-container text-on-surface border-surface-container-high hover:bg-surface-container-high' => $selectedCategoryId != $cat->id,
                    ])>
                    {{ $cat->name }}
                    <span class="text-xs ml-1 opacity-70">({{ $cat->items_count }})</span>
                </button>
            @endforeach
        </div>

        {{-- Items Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($this->items as $item)
                <div class="bg-surface-container rounded-xl border border-surface-container-high overflow-hidden hover:shadow-md transition">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="w-full h-40 object-cover">
                    @else
                        <div class="w-full h-40 bg-surface-container-high flex items-center justify-center text-secondary">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-on-surface">{{ $item->name }}</h3>
                            <span class="font-bold text-primary whitespace-nowrap">${{ number_format($item->price, 2) }}</span>
                        </div>
                        @if($item->description)
                            <p class="text-sm text-secondary mt-1 line-clamp-2">{{ $item->description }}</p>
                        @endif
                        @if($item->dietary_labels)
                            <div class="flex gap-1 mt-2 flex-wrap">
                                @foreach($item->dietary_labels as $label)
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-tertiary-container text-on-tertiary-container">{{ $label }}</span>
                                @endforeach
                            </div>
                        @endif
                        <button wire:click="addToCart({{ $item->id }})" class="mt-3 w-full py-2 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary/90 transition text-sm">
                            Add to Cart
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-secondary">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <p class="text-lg font-medium">No items in this category</p>
                </div>
            @endforelse
        </div>
    </main>

    {{-- Footer --}}
    <footer class="text-center py-6 text-sm text-secondary border-t border-surface-container-high flex justify-center gap-6">
        <a href="{{ route('tenant.track-order') }}" class="hover:text-primary transition">Track your order</a>
        <a href="{{ route('tenant.reserve') }}" class="hover:text-primary transition">Book a Table</a>
    </footer>

    {{-- Cart Drawer / Checkout --}}
    @if($showCheckout)
        <div class="fixed inset-0 z-50 bg-black/50 flex justify-end" wire:click.self="$set('showCheckout', false)">
            <div class="w-full max-w-lg bg-surface-container-lowest h-full overflow-y-auto" wire:click.stop>
                <div class="sticky top-0 bg-surface-container-lowest border-b border-surface-container-high p-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold">
                        @if(!$orderNumber)
                            Your Cart
                        @else
                            Order Confirmed
                        @endif
                    </h2>
                    <button wire:click="$set('showCheckout', false)" class="p-1 hover:bg-surface-container rounded">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Cart Items --}}
                @if(!$orderNumber)
                    <div class="p-4 space-y-3">
                        @forelse($cart as $key => $item)
                            <div class="flex items-center gap-3 bg-surface-container rounded-lg p-3 border border-surface-container-high">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm">{{ $item['name'] }}</p>
                                    @if(!empty($item['modifiers']))
                                        <p class="text-xs text-secondary">{{ implode(', ', $item['modifiers']) }}</p>
                                    @endif
                                    <p class="text-sm font-semibold text-primary mt-0.5">${{ number_format($item['total'], 2) }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="updateQty('{{ $key }}', {{ $item['quantity'] - 1 }})" class="w-7 h-7 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-surface-container-higher transition text-sm font-medium">&minus;</button>
                                    <span class="w-6 text-center font-medium text-sm">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQty('{{ $key }}', {{ $item['quantity'] + 1 }})" class="w-7 h-7 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-surface-container-higher transition text-sm font-medium">+</button>
                                </div>
                                <button wire:click="removeItem('{{ $key }}')" class="p-1 text-secondary hover:text-error transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-center text-secondary py-8">Your cart is empty</p>
                        @endforelse
                    </div>

                    {{-- Customer Info Form --}}
                    <div class="p-4 border-t border-surface-container-high">
                        <h3 class="font-medium mb-3">Your Information</h3>
                        <div class="space-y-3">
                            <div>
                                <input wire:model="customerName" placeholder="Your Name *" class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container text-on-surface placeholder-secondary focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('customerName') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <input wire:model="customerEmail" type="email" placeholder="Email (for receipt)" class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container text-on-surface placeholder-secondary focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('customerEmail') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <input wire:model="customerPhone" type="tel" placeholder="Phone (optional)" class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container text-on-surface placeholder-secondary focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('customerPhone') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <textarea wire:model="orderNotes" placeholder="Special instructions..." rows="2" class="w-full px-3 py-2 rounded-lg border border-surface-container-high bg-surface-container text-on-surface placeholder-secondary focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Summary & Place Order --}}
                    <div class="sticky bottom-0 bg-surface-container-lowest border-t border-surface-container-high p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium">Total</span>
                            <span class="text-xl font-bold text-primary">${{ number_format($this->cartTotal, 2) }}</span>
                        </div>
                        <button wire:click="placeOrder" wire:loading.attr="disabled" class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-lg hover:bg-primary/90 transition disabled:opacity-50">
                            <span wire:loading.remove>Place Order</span>
                            <span wire:loading>Placing...</span>
                        </button>
                    </div>
                @endif

                {{-- Confirmation --}}
                @if($orderNumber)
                    <div class="p-8 text-center" x-data
                         x-on:confirmed-order-toast.window=""
                         x-init="$nextTick(() => { setTimeout(() => $dispatch('confirmed-order-toast'), 2000); })">
                        <div class="w-20 h-20 rounded-full bg-tertiary-container text-on-tertiary-container flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2">Order Placed!</h2>
                        <p class="text-secondary mb-1">Your order number is</p>
                        <p class="text-3xl font-bold text-primary mb-6">{{ $orderNumber }}</p>
                        <p class="text-sm text-secondary mb-4">We'll start preparing your order right away.</p>
                        {{-- Email & tracking info --}}
                        <div class="bg-surface-container rounded-xl p-3 text-left text-sm space-y-2 mb-6 border border-surface-container-high">
                            @if($customerEmail)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <p class="text-secondary text-xs">A confirmation email has been sent to <strong class="text-on-surface">{{ $customerEmail }}</strong> with your order details.</p>
                            </div>
                            @endif
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 mt-0.5 text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-secondary text-xs">Track your order anytime at <a href="{{ route('tenant.track-order') }}" class="text-primary font-medium hover:underline">Track Order</a> using your order number.</p>
                            </div>
                        </div>
                        <button wire:click="backToMenu" class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold hover:bg-primary/90 transition">
                            Back to Menu
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Modifier Picker Modal --}}
    @if($showModifierPicker)
        <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" wire:click.self="cancelModifiers">
            <div class="w-full max-w-md bg-surface-container-lowest rounded-xl p-6" wire:click.stop>
                <h3 class="text-lg font-bold mb-4">Customize Your Item</h3>
                @foreach($modifierGroups as $group)
                    <div class="mb-4">
                        <p class="font-medium text-sm mb-2">{{ $group->name }} @if($group->is_required)<span class="text-error">*</span>@endif</p>
                        <div class="space-y-2">
                            @foreach($group->options as $idx => $option)
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-surface-container cursor-pointer">
                                    @if($group->type === 'radio' || $group->max_selections === 1)
                                        <input type="radio" name="modifier_{{ $group->id }}" value="{{ $idx }}"
                                            wire:model="selectedModifiers.{{ $group->id }}"
                                            class="text-primary focus:ring-primary">
                                    @else
                                        <input type="checkbox" value="{{ $idx }}"
                                            wire:model="selectedModifiers.{{ $group->id }}"
                                            class="text-primary focus:ring-primary rounded">
                                    @endif
                                    <span class="flex-1 text-sm">{{ $option['name'] }}</span>
                                    @if(($option['price'] ?? 0) > 0)
                                        <span class="text-sm text-secondary">+${{ number_format($option['price'], 2) }}</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <div class="flex gap-3 mt-6">
                    <button wire:click="cancelModifiers" class="flex-1 py-2.5 border border-surface-container-high rounded-lg font-medium hover:bg-surface-container transition">Cancel</button>
                    <button wire:click="confirmModifiers" class="flex-1 py-2.5 bg-primary text-on-primary rounded-lg font-medium hover:bg-primary/90 transition">Add to Cart</button>
                </div>
            </div>
        </div>
    @endif
</div>
