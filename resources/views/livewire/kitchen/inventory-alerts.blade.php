<div class="p-4 space-y-6">
    {{-- Out of stock --}}
    <div>
        <h2 class="text-lg font-bold text-error mb-3">Out of Stock</h2>
        @forelse($this->outOfStock as $item)
            <div class="flex items-center justify-between rounded-lg border border-error/30 bg-error/5 p-3 mb-2">
                <div>
                    <span class="font-medium text-on-surface">{{ $item->name }}</span>
                    <span class="ml-2 text-sm text-secondary">{{ $item->sku }}</span>
                </div>
                <span class="text-sm font-bold text-error">0 {{ $item->unit }}</span>
            </div>
        @empty
            <p class="text-sm text-secondary">No items out of stock.</p>
        @endforelse
    </div>

    {{-- Low stock --}}
    <div>
        <h2 class="text-lg font-bold text-warning mb-3">Low Stock</h2>
        @forelse($this->lowStock as $item)
            <div class="flex items-center justify-between rounded-lg border border-warning/30 bg-warning/5 p-3 mb-2">
                <div>
                    <span class="font-medium text-on-surface">{{ $item->name }}</span>
                    <span class="ml-2 text-sm text-secondary">{{ $item->sku }}</span>
                    @if($item->branch)
                        <span class="ml-2 text-xs text-secondary">· {{ $item->branch->name }}</span>
                    @endif
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-warning">{{ $item->stock_quantity }} {{ $item->unit }}</span>
                    <span class="ml-2 text-xs text-secondary">Reorder at: {{ $item->reorder_point }}</span>
                </div>
            </div>
        @empty
            <p class="text-sm text-secondary">All items are well-stocked.</p>
        @endforelse
    </div>
</div>
