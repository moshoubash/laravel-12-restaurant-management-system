<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-on-surface">Purchase Orders</h2>
        <button wire:click="openForm" class="rounded-lg bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary/90">+ New Order</button>
    </div>

    <div class="flex gap-2">
        <input wire:model.live.debounce="search" placeholder="Search by order #..." class="rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface w-56 focus:outline-none focus:ring-2 focus:ring-primary">
        <select wire:model.live="filterStatus" class="rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="ordered">Ordered</option>
            <option value="received">Received</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <div class="overflow-x-auto rounded-xl border border-surface-container-high">
        <table class="w-full text-sm">
            <thead class="bg-surface-container">
                <tr class="text-left text-secondary">
                    <th class="px-4 py-3 font-medium">Order #</th>
                    <th class="px-4 py-3 font-medium">Supplier</th>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium text-right">Total</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high">
                @forelse($this->orders as $po)
                    <tr class="hover:bg-surface-container/50">
                        <td class="px-4 py-3 font-medium text-on-surface">{{ $po->order_number }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $po->supplier?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $po->ordered_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right font-medium text-on-surface">${{ number_format($po->total, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-block px-2 py-0.5 rounded-full text-xs font-bold',
                                'bg-surface-container-high text-secondary' => $po->status === 'draft',
                                'bg-warning/20 text-warning' => $po->status === 'ordered',
                                'bg-success/20 text-success' => $po->status === 'received',
                                'bg-error/20 text-error' => $po->status === 'cancelled',
                            ])>{{ ucfirst($po->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($po->status === 'draft')
                                <button wire:click="openForm({{ $po->id }})" class="text-xs text-primary hover:underline mr-2">Edit</button>
                                <button wire:click="markOrdered({{ $po->id }})" class="text-xs text-warning hover:underline mr-2">Order</button>
                                <button wire:click="markCancelled({{ $po->id }})" wire:confirm="Cancel this PO?" class="text-xs text-error hover:underline">Cancel</button>
                            @elseif($po->status === 'ordered')
                                <button wire:click="markReceived({{ $po->id }})" wire:confirm="Mark as received? Stock will be updated." class="text-xs text-success hover:underline mr-2">Receive</button>
                                <button wire:click="markCancelled({{ $po->id }})" wire:confirm="Cancel this PO?" class="text-xs text-error hover:underline">Cancel</button>
                            @else
                                <span class="text-xs text-secondary">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-secondary">No purchase orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelForm">
            <div class="w-full max-w-2xl rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-on-surface mb-4">{{ $editingOrder ? 'Edit' : 'New' }} Purchase Order</h3>
                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Supplier</label>
                            <select wire:model="supplierId" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Select supplier</option>
                                @foreach($this->suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('supplierId') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Notes</label>
                            <input wire:model="notes" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-on-surface">Items</label>
                            <button type="button" wire:click="addItem" class="text-xs text-primary hover:underline">+ Add Item</button>
                        </div>
                        @error('items') <p class="text-xs text-error mb-2">{{ $message }}</p> @enderror
                        <div class="space-y-2">
                            @foreach($items as $index => $item)
                                <div class="flex items-center gap-2" wire:key="item-{{ $index }}">
                                    <select wire:model="items.{{ $index }}.inventory_item_id" class="flex-1 rounded-lg border border-surface-container-high bg-surface-container-lowest px-2 py-1.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="">Select item</option>
                                        @foreach($this->inventoryItems as $inv)
                                            <option value="{{ $inv->id }}">{{ $inv->name }} ({{ $inv->unit }})</option>
                                        @endforeach
                                    </select>
                                    <input wire:model="items.{{ $index }}.quantity" type="number" step="0.01" min="0.01" class="w-20 rounded-lg border border-surface-container-high bg-surface-container-lowest px-2 py-1.5 text-sm text-on-surface text-center focus:outline-none focus:ring-2 focus:ring-primary">
                                    <input wire:model="items.{{ $index }}.unit_cost" type="number" step="0.01" min="0" class="w-20 rounded-lg border border-surface-container-high bg-surface-container-lowest px-2 py-1.5 text-sm text-on-surface text-center focus:outline-none focus:ring-2 focus:ring-primary">
                                    <span class="text-sm text-secondary w-16 text-right">${{ number_format($item['total_cost'] ?? 0, 2) }}</span>
                                    <button type="button" wire:click="removeItem({{ $index }})" class="text-error hover:text-error/80">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        @if(count($items) > 0)
                            <div class="text-right text-sm font-bold text-on-surface mt-2">Total: ${{ number_format($this->total, 2) }}</div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="cancelForm" class="px-4 py-2 border border-surface-container-high rounded-lg text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-sm font-bold hover:bg-primary/90">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
