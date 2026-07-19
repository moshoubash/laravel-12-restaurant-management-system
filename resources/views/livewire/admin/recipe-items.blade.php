<div class="space-y-4">
    <h2 class="text-lg font-bold text-on-surface">Recipes</h2>

    <div class="flex gap-3">
        <div class="w-72 shrink-0">
            <label class="text-xs font-medium text-secondary uppercase tracking-wide mb-1 block">Menu Item</label>
            <select wire:model.live="selectedMenuItemId" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select a menu item...</option>
                @foreach($this->menuItems as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($this->selectedItem)
        <div class="rounded-xl border border-surface-container-high bg-surface-container p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-on-surface text-lg">{{ $this->selectedItem->name }}</h3>
                    <p class="text-sm text-secondary">Selling price: <strong>${{ number_format($this->selectedItem->price, 2) }}</strong></p>
                </div>
                <button wire:click="addIngredient" class="rounded-lg bg-primary px-3 py-1.5 text-sm font-bold text-on-primary hover:bg-primary/90">+ Add Ingredient</button>
            </div>

            <div class="space-y-2">
                @forelse($items as $index => $item)
                    <div class="flex items-center gap-3 bg-surface-container-lowest rounded-lg p-3 border border-surface-container-high" wire:key="recipe-{{ $index }}">
                        <div class="flex-1">
                            <select wire:model="items.{{ $index }}.inventory_item_id" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-2 py-1.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">Select ingredient...</option>
                                @foreach($this->inventoryItems as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->name }} (stock: {{ $inv->stock_quantity }} {{ $inv->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input wire:model="items.{{ $index }}.quantity" type="number" step="0.01" min="0.01" class="w-24 rounded-lg border border-surface-container-high bg-surface-container-lowest px-2 py-1.5 text-sm text-on-surface text-center focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <input wire:model="items.{{ $index }}.unit" class="w-16 rounded-lg border border-surface-container-high bg-surface-container-lowest px-2 py-1.5 text-sm text-on-surface text-center focus:outline-none focus:ring-2 focus:ring-primary" readonly>
                        </div>
                        <button wire:click="removeIngredient({{ $index }})" class="text-error hover:text-error/80">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8 text-secondary text-sm border border-dashed border-surface-container-high rounded-lg">
                        No ingredients added yet. Click "Add Ingredient" to define the recipe.
                    </div>
                @endforelse
            </div>

            @if(count($items) > 0)
                <div class="mt-4 flex justify-end">
                    <button wire:click="save" wire:loading.attr="disabled" class="rounded-lg bg-primary px-6 py-2 text-sm font-bold text-on-primary hover:bg-primary/90 transition disabled:opacity-50">
                        <span wire:loading.remove>Save Recipe</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-16 text-secondary border border-dashed border-surface-container-high rounded-xl">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="font-medium">Select a menu item to configure its recipe</p>
            <p class="text-sm mt-1">Define which inventory ingredients are used and in what quantity.</p>
        </div>
    @endif
</div>
