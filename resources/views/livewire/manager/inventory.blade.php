<div class="space-y-6">
    {{-- Header --}}
    <div class="section-header">
        <div>
            <h2 class="section-title">Inventory</h2>
            <p class="section-description">View and adjust stock levels</p>
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

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="kpi-card">
            <p class="kpi-label">Total Items</p>
            <p class="kpi-value">{{ $this->items->count() }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Low Stock</p>
            <p class="kpi-value text-warning">{{ $this->items->filter(fn($i) => $i->stock_quantity > 0 && $i->stock_quantity <= $i->reorder_point)->count() }}</p>
        </div>
        <div class="kpi-card">
            <p class="kpi-label">Out of Stock</p>
            <p class="kpi-value text-error">{{ $this->items->filter(fn($i) => $i->stock_quantity <= 0)->count() }}</p>
        </div>
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
                        <td class="table-td">
                            @if($item->stock_quantity <= 0)
                                <span class="badge bg-error/10 text-error">Out of Stock</span>
                            @elseif($item->stock_quantity <= $item->reorder_point)
                                <span class="badge bg-warning/10 text-warning">Low Stock</span>
                            @else
                                <span class="badge bg-success/10 text-success">In Stock</span>
                            @endif
                        </td>
                        <td class="table-td text-right">
                            <button wire:click="openAdjust({{ $item->id }})" class="btn-icon" title="Adjust Stock">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="table-td text-center text-secondary py-12">
                            <p class="font-medium">No inventory items found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Stock adjustment modal --}}
    @if($showAdjust)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelAdjust">
            <div class="w-full max-w-sm rounded-xl bg-surface-container p-6 shadow-xl border border-surface-container-high">
                <h3 class="text-lg font-bold text-on-surface">Adjust Stock</h3>
                <p class="text-sm text-secondary mt-1">{{ InventoryItem::find($adjustingItem)?->name }}</p>
                <form wire:submit.prevent="saveAdjust" class="mt-4 space-y-4">
                    <div>
                        <label class="input-label">Quantity Change</label>
                        <input wire:model="adjustQuantity" type="number" step="0.01" class="input" placeholder="Positive to add, negative to subtract">
                        @error('adjustQuantity') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label">Reason</label>
                        <input wire:model="adjustReason" type="text" class="input" placeholder="e.g. Physical count, spoilage">
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
