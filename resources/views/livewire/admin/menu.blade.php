<div class="flex h-full gap-6">
    {{-- Categories sidebar --}}
    <div class="w-72 shrink-0 space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-on-surface">Categories</h2>
            <button wire:click="openCategoryForm" class="rounded bg-primary px-3 py-1.5 text-sm font-bold text-on-primary hover:bg-primary-container">+ Add</button>
        </div>
        <div class="space-y-1">
            @foreach($this->categories as $cat)
                <div wire:key="cat-{{ $cat->id }}"
                     wire:click="selectCategory({{ $cat->id }})"
                     class="group flex cursor-pointer items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors {{ $selectedCategoryId === $cat->id ? 'bg-primary-container text-on-primary-container' : 'hover:bg-surface-container-high text-on-surface' }} {{ !$cat->is_active ? 'opacity-40' : '' }}">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="truncate">{{ $cat->name }}</span>
                        <span class="shrink-0 text-xs text-secondary">({{ $cat->items_count }})</span>
                    </div>
                    <div class="hidden shrink-0 items-center gap-1 group-hover:flex">
                        <button wire:click.stop="openCategoryForm({{ $cat->id }})" class="rounded p-1 hover:bg-surface-container" title="Edit">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click.stop="toggleCategory({{ $cat->id }})" class="rounded p-1 hover:bg-surface-container" title="{{ $cat->is_active ? 'Disable' : 'Enable' }}">
                            @if($cat->is_active)
                                <svg class="h-3.5 w-3.5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            @else
                                <svg class="h-3.5 w-3.5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                            @endif
                        </button>
                        <button wire:click.stop="deleteCategory({{ $cat->id }})" wire:confirm="Delete this category?" class="rounded p-1 hover:bg-surface-container" title="Delete">
                            <svg class="h-3.5 w-3.5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        @if(session('error'))
            <p class="text-sm text-error">{{ session('error') }}</p>
        @endif
    </div>

    {{-- Items area --}}
    <div class="flex-1 min-w-0">
        @if($selectedCategoryId)
            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-on-surface">{{ $this->categories->firstWhere('id', $selectedCategoryId)?->name }} Items</h2>
                <div class="flex items-center gap-3">
                    <input wire:model.live="search" type="text" placeholder="Search items..." class="w-48 rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface placeholder-secondary focus:border-primary focus:ring-primary">
                    <button wire:click="openItemForm" class="rounded bg-primary px-3 py-1.5 text-sm font-bold text-on-primary hover:bg-primary-container">+ Add Item</button>
                </div>
            </div>

            <div class="space-y-2">
                @forelse($this->items as $item)
                    <div wire:key="item-{{ $item->id }}" class="flex items-center gap-4 rounded-lg border border-surface-container-high bg-surface-container-lowest p-4 {{ !$item->is_active ? 'opacity-50' : '' }}">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-surface-container-high text-secondary">
                            @if($item->image)
                                <img src="{{ $item->image }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                            @else
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-on-surface">{{ $item->name }}</span>
                                @if($item->is_featured)
                                    <span class="rounded bg-primary-container px-1.5 py-0.5 text-[10px] font-bold text-on-primary-container">FEATURED</span>
                                @endif
                            </div>
                            <div class="mt-0.5 text-sm text-secondary">
                                {{ $item->description ? Str::limit($item->description, 60) : '' }}
                            </div>
                            <div class="mt-1 flex items-center gap-3 text-sm">
                                <span class="font-bold text-primary">${{ number_format($item->price, 2) }}</span>
                                @if($item->compare_price)
                                    <span class="text-secondary line-through">${{ number_format($item->compare_price, 2) }}</span>
                                @endif
                                @if($item->preparation_time)
                                    <span class="text-secondary">{{ $item->preparation_time }}min</span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <button wire:click="toggleAvailable({{ $item->id }})" class="rounded-full px-2 py-0.5 text-xs font-bold {{ $item->is_available ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                                        {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                    </button>
                                </span>
                                @if($item->modifiers->count())
                                    <span class="text-secondary">{{ $item->modifiers->count() }} modifier groups</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <button wire:click="moveItemUp({{ $item->id }})" class="rounded p-1.5 hover:bg-surface-container" title="Move up">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button wire:click="moveItemDown({{ $item->id }})" class="rounded p-1.5 hover:bg-surface-container" title="Move down">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <button wire:click="openModifierForm({{ $item->id }})" class="rounded p-1.5 hover:bg-surface-container" title="Modifiers">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </button>
                            <button wire:click="openItemForm({{ $item->id }})" class="rounded p-1.5 hover:bg-surface-container" title="Edit">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="toggleItem({{ $item->id }})" class="rounded p-1.5 hover:bg-surface-container" title="{{ $item->is_active ? 'Disable' : 'Enable' }}">
                                @if($item->is_active)
                                    <svg class="h-4 w-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @else
                                    <svg class="h-4 w-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                                @endif
                            </button>
                            <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this item?" class="rounded p-1.5 hover:bg-surface-container" title="Delete">
                                <svg class="h-4 w-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-secondary">No items in this category. Click "+ Add Item" to create one.</div>
                @endforelse
            </div>
        @else
            <div class="py-12 text-center text-secondary">Select a category to view items, or create one.</div>
        @endif
    </div>

    {{-- Category modal --}}
    @if($showCategoryForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" wire:click.self="showCategoryForm = false">
            <div class="w-full max-w-md rounded-xl bg-surface-container-lowest p-6 shadow-xl">
                <h3 class="text-lg font-bold text-on-surface">{{ $editingCategory ? 'Edit' : 'Add' }} Category</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Name</label>
                        <input wire:model="categoryName" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('categoryName') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Description</label>
                        <textarea wire:model="categoryDescription" rows="2" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Sort Order</label>
                        <input wire:model="categorySortOrder" type="number" min="0" class="mt-1 block w-24 rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="cancelCategoryForm" class="rounded border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                    <button wire:click="saveCategory" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Save</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Item modal --}}
    @if($showItemForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" wire:click.self="showItemForm = false">
            <div class="w-full max-w-lg rounded-xl bg-surface-container-lowest p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-on-surface">{{ $editingItem ? 'Edit' : 'Add' }} Menu Item</h3>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-on-surface">Category</label>
                        <select wire:model="itemCategoryId" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                            @foreach($this->categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('itemCategoryId') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-on-surface">Name</label>
                        <input wire:model="itemName" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('itemName') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-on-surface">Description</label>
                        <textarea wire:model="itemDescription" rows="2" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Price ($)</label>
                        <input wire:model="itemPrice" type="number" step="0.01" min="0" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('itemPrice') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Compare Price ($)</label>
                        <input wire:model="itemComparePrice" type="number" step="0.01" min="0" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Cost ($)</label>
                        <input wire:model="itemCost" type="number" step="0.01" min="0" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Prep Time (min)</label>
                        <input wire:model="itemPreparationTime" type="number" min="0" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                    <div class="col-span-2 flex gap-6">
                        <label class="flex items-center gap-2 text-sm">
                            <input wire:model="itemIsAvailable" type="checkbox" class="rounded border-surface-container-high text-primary focus:ring-primary">
                            Available
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input wire:model="itemIsFeatured" type="checkbox" class="rounded border-surface-container-high text-primary focus:ring-primary">
                            Featured
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="cancelItemForm" class="rounded border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                    <button wire:click="saveItem" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Save</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modifier modal --}}
    @if($showModifierForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" wire:click.self="showModifierForm = false">
            <div class="w-full max-w-md rounded-xl bg-surface-container-lowest p-6 shadow-xl">
                <h3 class="text-lg font-bold text-on-surface">{{ $editingModifier ? 'Edit' : 'Add' }} Modifier Group</h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Group Name</label>
                        <input wire:model="modifierName" placeholder="e.g. Size, Extra Toppings" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('modifierName') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Selection Type</label>
                        <select wire:model="modifierType" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                            <option value="single">Single (radio)</option>
                            <option value="multiple">Multiple (checkbox)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Options <span class="text-secondary font-normal">(one per line)</span></label>
                        <textarea wire:model="modifierOptions" rows="4" placeholder="Small&#10;Medium&#10;Large" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary"></textarea>
                        @error('modifierOptions') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 text-sm">
                            <input wire:model="modifierIsRequired" type="checkbox" class="rounded border-surface-container-high text-primary focus:ring-primary">
                            Required
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-on-surface">Max Selections</label>
                            <input wire:model="modifierMaxSelections" type="number" min="1" class="mt-1 block w-20 rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="cancelModifierForm" class="rounded border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                    <button wire:click="saveModifier" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>
