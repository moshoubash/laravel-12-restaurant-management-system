<div class="space-y-6">
    {{-- Header --}}
    <div class="section-header">
        <div>
            <h2 class="section-title">Inventory</h2>
            <p class="section-description">Manage stock items and quantities</p>
        </div>
        <div class="section-actions">
            <button wire:click="openForm" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Item
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3">
        <input wire:model.live.debounce="search" type="text" placeholder="Search by name or SKU..." class="input max-w-xs">
        <select wire:model.live="filterCategory" class="input-select max-w-[180px]">
            <option value="">All Categories</option>
            @foreach($this->categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-on-surface cursor-pointer">
            <input wire:model.live="filterLowStock" type="checkbox" class="rounded border-surface-container-high text-primary focus:ring-primary">
            Low stock only
        </label>
    </div>

    {{-- Table --}}
    <div class="table-wrapper">
        <table class="table">
            <thead class="table-head">
                <tr>
                    <th class="table-th">Item</th>
                    <th class="table-th">SKU</th>
                    <th class="table-th">Category</th>
                    <th class="table-th">Stock</th>
                    <th class="table-th">Reorder</th>
                    <th class="table-th">Cost</th>
                    <th class="table-th">Status</th>
                    <th class="table-th text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse($this->items as $item)
                    <tr class="table-tr {{ $item->stock_quantity <= $item->reorder_point ? 'bg-warning/5' : '' }}">
                        <td class="table-td font-medium">{{ $item->name }}</td>
                        <td class="table-td text-secondary">{{ $item->sku ?? '—' }}</td>
                        <td class="table-td">
                            @if($item->category)
                                <span class="badge bg-surface-container-high text-on-surface">{{ $item->category }}</span>
                            @else
                                <span class="text-secondary">—</span>
                            @endif
                        </td>
                        <td class="table-td">
                            <span @class([
                                'font-bold',
                                'text-error' => $item->stock_quantity <= 0,
                                'text-warning' => $item->stock_quantity > 0 && $item->stock_quantity <= $item->reorder_point,
                                'text-success' => $item->stock_quantity > $item->reorder_point,
                            ])>{{ number_format($item->stock_quantity, 2) }}</span>
                            <span class="text-xs text-secondary">{{ $item->unit }}</span>
                        </td>
                        <td class="table-td text-secondary">{{ number_format($item->reorder_point, 2) }}</td>
                        <td class="table-td">${{ number_format($item->unit_cost, 2) }}</td>
                        <td class="table-td">
                            @if($item->is_active)
                                <span class="badge bg-success/10 text-success">Active</span>
                            @else
                                <span class="badge bg-error/10 text-error">Inactive</span>
                            @endif
                        </td>
                        <td class="table-td text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openAdjust({{ $item->id }})" class="btn-icon" title="Adjust Stock">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                </button>
                                <button wire:click="openForm({{ $item->id }})" class="btn-icon" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="toggleActive({{ $item->id }})" class="btn-icon" title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}">
                                    @if($item->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </button>
                                <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this inventory item?" class="btn-icon text-error" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="table-td text-center text-secondary py-12">
                            <p class="font-medium">No inventory items found</p>
                            <p class="text-sm mt-1">Click "Add Item" to create your first item.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelForm">
            <div class="w-full max-w-lg rounded-xl bg-surface-container p-6 shadow-xl border border-surface-container-high max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-on-surface">{{ $editingItem ? 'Edit' : 'Add' }} Inventory Item</h3>
                <form wire:submit.prevent="save" class="mt-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="input-label">Item Name</label>
                            <input wire:model="name" type="text" class="input" required>
                            @error('name') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="input-label">SKU</label>
                            <input wire:model="sku" type="text" class="input" placeholder="e.g. TOM-001">
                            @error('sku') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="input-label">Category</label>
                            <input wire:model="category" type="text" class="input" placeholder="e.g. Produce">
                        </div>
                        <div>
                            <label class="input-label">Unit</label>
                            <select wire:model="unit" class="input-select">
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="kg">Kilograms (kg)</option>
                                <option value="g">Grams (g)</option>
                                <option value="L">Liters (L)</option>
                                <option value="ml">Milliliters (ml)</option>
                                <option value="oz">Ounces (oz)</option>
                                <option value="lb">Pounds (lb)</option>
                                <option value="box">Box</option>
                                <option value="bag">Bag</option>
                                <option value="case">Case</option>
                            </select>
                            @error('unit') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="input-label">Supplier</label>
                            <select wire:model="supplierId" class="input-select">
                                <option value="">No supplier</option>
                                @foreach($this->suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-surface-container-high pt-4">
                        <p class="text-sm font-semibold text-on-surface mb-3">Stock Levels</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">Current Stock</label>
                                <input wire:model="stockQuantity" type="number" step="0.01" min="0" class="input">
                                @error('stockQuantity') <p class="input-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="input-label">Unit Cost ($)</label>
                                <input wire:model="unitCost" type="number" step="0.01" min="0" class="input">
                                @error('unitCost') <p class="input-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="input-label">Min Stock Level</label>
                                <input wire:model="minStockLevel" type="number" step="0.01" min="0" class="input">
                            </div>
                            <div>
                                <label class="input-label">Max Stock Level</label>
                                <input wire:model="maxStockLevel" type="number" step="0.01" min="0" class="input" placeholder="Optional">
                            </div>
                            <div>
                                <label class="input-label">Reorder Point</label>
                                <input wire:model="reorderPoint" type="number" step="0.01" min="0" class="input">
                            </div>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input wire:model="isActive" type="checkbox" class="rounded border-surface-container-high text-primary focus:ring-primary">
                        Active
                    </label>

                    <div class="flex justify-end gap-3 pt-3 border-t border-surface-container-high">
                        <button type="button" wire:click="cancelForm" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                        <button type="submit" class="btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Stock adjustment modal --}}
    @if($showAdjust)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelAdjust">
            <div class="w-full max-w-sm rounded-xl bg-surface-container p-6 shadow-xl border border-surface-container-high">
                <h3 class="text-lg font-bold text-on-surface">Adjust Stock</h3>
                <p class="text-sm text-secondary mt-1">{{ InventoryItem::find($adjustingItem)?->name }}</p>
                <form wire:submit.prevent="saveAdjust" class="mt-4 space-y-4">
                    <div>
                        <label class="input-label">Quantity Change</label>
                        <input wire:model="adjustQuantity" type="number" step="0.01" class="input" placeholder="Use positive to add, negative to subtract">
                        @error('adjustQuantity') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Reason</label>
                        <input wire:model="adjustReason" type="text" class="input" placeholder="e.g. Physical count adjustment, spoilage">
                        @error('adjustReason') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-surface-container-high">
                        <button type="button" wire:click="cancelAdjust" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                        <button type="submit" class="btn-primary">Save Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
