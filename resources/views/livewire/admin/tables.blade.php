<div class="space-y-4">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-bold text-on-surface">Floor Plan</h2>
            <select wire:model.live="filterSection" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="">All Sections</option>
                @foreach($this->sections as $sec)
                    <option value="{{ $sec }}">{{ $sec }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterStatus" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="">All Statuses</option>
                <option value="free">Free</option>
                <option value="occupied">Occupied</option>
                <option value="reserved">Reserved</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        <button wire:click="openForm" class="rounded bg-primary px-3 py-1.5 text-sm font-bold text-on-primary hover:bg-primary-container">+ Add Table</button>
    </div>

    {{-- Floor plan grid --}}
    <div class="relative min-h-[500px] rounded-xl border border-surface-container-high bg-surface-container-low p-4">
        @if($this->tables->isEmpty())
            <div class="flex h-[500px] items-center justify-center text-secondary">No tables yet. Click "+ Add Table" to create one.</div>
        @else
            @foreach($this->tables as $table)
                <div wire:key="table-{{ $table->id }}"
                     class="absolute flex cursor-grab items-center justify-center rounded-lg border-2 text-center text-xs font-bold transition-shadow hover:shadow-lg"
                     style="left: {{ $table->x_position ?? 0 }}px; top: {{ $table->y_position ?? 0 }}px; width: {{ $table->width }}px; height: {{ $table->height }}px; border-radius: {{ $table->shape === 'circle' ? '50%' : '0.5rem' }}; background-color: {{ match($table->status) { 'free' => 'var(--color-success, #22c55e)', 'occupied' => 'var(--color-error, #ef4444)', 'reserved' => 'var(--color-warning, #f59e0b)', 'maintenance' => 'var(--color-secondary, #78716c)', default => 'var(--color-surface-container-high, #e7e5e4)' } }}10; border-color: {{ match($table->status) { 'free' => 'var(--color-success, #22c55e)', 'occupied' => 'var(--color-error, #ef4444)', 'reserved' => 'var(--color-warning, #f59e0b)', 'maintenance' => 'var(--color-secondary, #78716c)', default => 'var(--color-surface-container-high, #e7e5e4)' } }};"
                     x-data="{ dragging: false, startX: 0, startY: 0, origX: {{ $table->x_position ?? 0 }}, origY: {{ $table->y_position ?? 0 }} }"
                     x-on:mousedown="dragging = true; startX = $event.clientX; startY = $event.clientY; origX = {{ $table->x_position ?? 0 }}; origY = {{ $table->y_position ?? 0 }}"
                     x-on:mousemove.window="if (!dragging) return; const dx = $event.clientX - startX; const dy = $event.clientY - startY; $el.style.left = (origX + dx) + 'px'; $el.style.top = (origY + dy) + 'px'"
                     x-on:mouseup.window="if (!dragging) return; dragging = false; const dx = $event.clientX - startX; const dy = $event.clientY - startY; $wire.updatePosition({{ $table->id }}, Math.max(0, origX + dx), Math.max(0, origY + dy))"
                     x-on:mouseleave.window="if (!dragging) return; dragging = false; const dx2 = $event.clientX - startX; const dy2 = $event.clientY - startY; $wire.updatePosition({{ $table->id }}, Math.max(0, origX + dx2), Math.max(0, origY + dy2))">

                    <div class="flex flex-col items-center leading-tight">
                        <span class="text-sm font-bold text-on-surface">T{{ $table->table_number }}</span>
                        <span class="text-[10px] text-secondary">{{ $table->capacity }}p</span>
                    </div>

                    {{-- Quick actions --}}
                    <div class="absolute -top-2 -right-2 hidden gap-0.5 group-hover:flex" x-data x-on:click.stop>
                        @foreach(['free', 'occupied', 'reserved', 'maintenance'] as $s)
                            <button wire:click="setStatus({{ $table->id }}, '{{ $s }}')"
                                    class="h-3 w-3 rounded-full border border-white"
                                    style="background-color: {{ match($s) { 'free' => '#22c55e', 'occupied' => '#ef4444', 'reserved' => '#f59e0b', 'maintenance' => '#78716c' } }}"
                                    title="{{ ucfirst($s) }}"></button>
                        @endforeach
                        <button wire:click="openForm({{ $table->id }})" class="rounded p-0.5 hover:bg-surface-container">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click="deleteTable({{ $table->id }})" wire:confirm="Delete table T{{ $table->table_number }}?" class="rounded p-0.5 hover:bg-surface-container">
                            <svg class="h-3 w-3 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Legend --}}
    <div class="flex items-center gap-4 text-xs text-secondary">
        <span class="flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-full bg-success/30 border border-success"></span> Free</span>
        <span class="flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-full bg-error/30 border border-error"></span> Occupied</span>
        <span class="flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-full bg-warning/30 border border-warning"></span> Reserved</span>
        <span class="flex items-center gap-1"><span class="inline-block h-3 w-3 rounded-full bg-secondary/30 border border-secondary"></span> Maintenance</span>
        <span class="ml-auto">Drag tables to reposition</span>
    </div>

    {{-- Table form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" wire:click.self="showForm = false">
            <div class="w-full max-w-md rounded-xl bg-surface-container-lowest p-6 shadow-xl">
                <h3 class="text-lg font-bold text-on-surface">{{ $editingTable ? 'Edit' : 'Add' }} Table</h3>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Table Number</label>
                        <input wire:model="tableNumber" type="number" min="1" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('tableNumber') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Capacity</label>
                        <input wire:model="capacity" type="number" min="1" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('capacity') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Section</label>
                        <input wire:model="section" placeholder="e.g. Patio, Main Hall" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Shape</label>
                        <select wire:model="shape" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                            <option value="rectangle">Rectangle</option>
                            <option value="circle">Circle</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Width (px)</label>
                        <input wire:model="width" type="number" min="30" max="200" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Height (px)</label>
                        <input wire:model="height" type="number" min="30" max="200" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="showForm = false" class="rounded border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                    <button wire:click="save" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>
