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
                @php
                    $activeOrder = $table->orders->first();
                    $bgColor = match($table->status) {
                        'free' => '#16a34a',
                        'occupied' => '#dc2626',
                        'reserved' => '#d97706',
                        'maintenance' => '#78716c',
                        default => '#a8a29e',
                    };
                    $bgClass = match($table->status) {
                        'free' => 'bg-success/15 border-success',
                        'occupied' => 'bg-error/15 border-error',
                        'reserved' => 'bg-warning/15 border-warning',
                        'maintenance' => 'bg-secondary/15 border-secondary',
                        default => 'bg-surface-container-high border-surface-container-high',
                    };
                @endphp
                <div wire:key="table-{{ $table->id }}"
                     class="absolute flex flex-col items-center justify-center rounded-lg border-2 text-center text-xs font-bold transition-shadow hover:shadow-lg cursor-pointer {{ $bgClass }}"
                     style="left: {{ $table->x_position ?? 0 }}px; top: {{ $table->y_position ?? 0 }}px; width: {{ $table->width }}px; height: {{ $table->height }}px; border-radius: {{ $table->shape === 'circle' ? '50%' : '0.5rem' }};"
                     x-data="{ dragging: false, moved: false, startX: 0, startY: 0, origX: {{ $table->x_position ?? 0 }}, origY: {{ $table->y_position ?? 0 }} }"
                     x-on:mousedown="dragging = true; moved = false; startX = $event.clientX; startY = $event.clientY; origX = {{ $table->x_position ?? 0 }}; origY = {{ $table->y_position ?? 0 }}"
                     x-on:mousemove.window="if (!dragging) return; const dx = $event.clientX - startX; const dy = $event.clientY - startY; if (Math.abs(dx) > 3 || Math.abs(dy) > 3) moved = true; $el.style.left = (origX + dx) + 'px'; $el.style.top = (origY + dy) + 'px'"
                     x-on:mouseup.window="if (!dragging) return; dragging = false; const dx = $event.clientX - startX; const dy = $event.clientY - startY; if (moved) { $wire.updatePosition({{ $table->id }}, Math.max(0, origX + dx), Math.max(0, origY + dy)); }"
                     x-on:mouseleave.window="if (!dragging) return; dragging = false; const dx2 = $event.clientX - startX; const dy2 = $event.clientY - startY; if (moved) { $wire.updatePosition({{ $table->id }}, Math.max(0, origX + dx2), Math.max(0, origY + dy2)); }"
                     x-on:click="if (!moved) { $wire.viewTable({{ $table->id }}); }">

                    <div class="flex flex-col items-center leading-tight pointer-events-none">
                        <span class="text-sm font-bold text-on-surface">T{{ $table->table_number }}</span>
                        <span class="text-[10px] text-secondary">{{ $table->capacity }}p</span>
                        @if($activeOrder)
                            <span class="mt-1 text-[9px] font-bold {{ $table->status === 'occupied' ? 'text-error' : ($table->status === 'reserved' ? 'text-warning' : 'text-secondary') }}">
                                {{ ucfirst($activeOrder->order_type) }}
                            </span>
                            <span class="text-[9px] text-secondary">${{ number_format($activeOrder->total, 0) }}</span>
                        @endif
                    </div>

                    {{-- Status badge on top-right --}}
                    <div class="absolute -top-2.5 left-1/2 -translate-x-1/2 flex items-center gap-0.5 pointer-events-none" x-data x-on:click.stop>
                        <span class="rounded-full px-1.5 py-0.5 text-[8px] font-bold text-white uppercase"
                              style="background-color: {{ $bgColor }}">
                            {{ $table->status }}
                        </span>
                    </div>

                    {{-- Quick actions on hover --}}
                    <div class="absolute -top-2 -right-2 hidden gap-0.5 group-hover:flex" x-data x-on:click.stop>
                        @foreach(['free', 'occupied', 'reserved', 'maintenance'] as $s)
                            <button wire:click="setStatus({{ $table->id }}, '{{ $s }}')"
                                    class="h-3 w-3 rounded-full border border-white shadow-sm"
                                    style="background-color: {{ match($s) { 'free' => '#16a34a', 'occupied' => '#dc2626', 'reserved' => '#d97706', 'maintenance' => '#78716c' } }}"
                                    title="{{ ucfirst($s) }}"></button>
                        @endforeach
                        <button wire:click="openForm({{ $table->id }})" class="rounded p-0.5 hover:bg-surface-container bg-white/80">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button wire:click="deleteTable({{ $table->id }})" wire:confirm="Delete table T{{ $table->table_number }}?" class="rounded p-0.5 hover:bg-surface-container bg-white/80">
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
        <span class="ml-auto">Click a table for details · Drag to reposition</span>
    </div>

    {{-- Table form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelForm">
            <div class="w-full max-w-md rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl">
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
                    <button wire:click="cancelForm" class="rounded border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                    <button wire:click="save" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Save</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Table detail modal --}}
    @if($showDetail && $selectedTable = $this->selectedTable)
        @php $activeOrder = $selectedTable->orders->first(); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="w-full max-w-lg rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-on-surface">Table T{{ $selectedTable->table_number }}</h3>
                        <p class="text-sm text-secondary">
                            {{ $selectedTable->section ?? 'No section' }} · {{ $selectedTable->capacity }} seats · {{ ucfirst($selectedTable->shape) }}
                        </p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold text-white"
                          style="background-color: {{ match($selectedTable->status) { 'free' => '#16a34a', 'occupied' => '#dc2626', 'reserved' => '#d97706', 'maintenance' => '#78716c', default => '#a8a29e' } }}; text-transform: uppercase;">
                        {{ $selectedTable->status }}
                    </span>
                </div>

                @if($activeOrder)
                    <div class="mt-5 rounded-lg border border-surface-container-high bg-surface-container p-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-on-surface">Active Order</h4>
                            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-bold text-primary">{{ ucfirst($activeOrder->order_type) }}</span>
                        </div>
                        <div class="mt-2 space-y-1 text-sm">
                            <div class="flex justify-between text-secondary">
                                <span>Order #{{ $activeOrder->order_number }}</span>
                                <span>{{ $activeOrder->ordered_at?->format('g:i A') }}</span>
                            </div>
                            <div class="flex justify-between text-secondary">
                                <span>Status</span>
                                <span class="font-bold text-on-surface">{{ ucfirst($activeOrder->status) }}</span>
                            </div>
                            @if($activeOrder->customer_name)
                                <div class="flex justify-between text-secondary">
                                    <span>Customer</span>
                                    <span>{{ $activeOrder->customer_name }}</span>
                                </div>
                            @endif
                            @if($activeOrder->user)
                                <div class="flex justify-between text-secondary">
                                    <span>Served by</span>
                                    <span>{{ $activeOrder->user->name }}</span>
                                </div>
                            @endif
                            @if($activeOrder->notes)
                                <div class="mt-2 text-secondary">
                                    <span>Notes:</span>
                                    <p class="mt-0.5 text-on-surface italic">{{ $activeOrder->notes }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Order items --}}
                        <div class="mt-3 space-y-1">
                            <div class="text-xs font-bold text-secondary uppercase">Items</div>
                            @foreach($activeOrder->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                                    <span class="text-secondary">${{ number_format($item->total_price, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="border-t border-surface-container-high pt-1 mt-1">
                                <div class="flex justify-between text-sm font-bold">
                                    <span>Total</span>
                                    <span>${{ number_format($activeOrder->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-5 rounded-lg border border-dashed border-surface-container-high bg-surface-container-lowest p-6 text-center text-sm text-secondary">
                        No active order on this table.
                    </div>
                @endif

                @php $activeReservation = $selectedTable->reservations->first(); @endphp
                @if($activeReservation)
                    <div class="mt-3 rounded-lg border border-warning/30 bg-warning/5 p-4">
                        <h4 class="font-bold text-on-surface">Upcoming Reservation</h4>
                        <div class="mt-2 space-y-1 text-sm">
                            @if($activeReservation->customer_name)
                                <div class="flex justify-between text-secondary">
                                    <span>Guest</span>
                                    <span>{{ $activeReservation->customer_name }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-secondary">
                                <span>Date</span>
                                <span>{{ \Carbon\Carbon::parse($activeReservation->reservation_date)->format('M j, Y') }} {{ \Carbon\Carbon::parse($activeReservation->reservation_time)->format('g:i A') }}</span>
                            </div>
                            <div class="flex justify-between text-secondary">
                                <span>Party size</span>
                                <span>{{ $activeReservation->guest_count ?? '—' }}</span>
                            </div>
                            @if($activeReservation->status)
                                <div class="flex justify-between text-secondary">
                                    <span>Status</span>
                                    <span class="font-bold text-warning">{{ ucfirst($activeReservation->status) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- QR Code --}}
                <div class="mt-4 rounded-lg border border-surface-container-high bg-surface-container p-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-on-surface text-sm">QR Code — Self Ordering</h4>
                        <div class="flex gap-2">
                            <button wire:click="regenerateQr({{ $selectedTable->id }})" class="text-xs text-primary hover:underline">Regenerate</button>
                        </div>
                    </div>
                    <div class="mt-2 flex items-start gap-3">
                        <div class="flex-shrink-0 bg-white rounded-lg p-1">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($selectedTable->qr_code ?? $this->generateQrUrl($selectedTable)) }}"
                                 alt="QR Code" class="w-24 h-24">
                        </div>
                        <div class="text-xs text-secondary space-y-2">
                            <p><strong class="text-on-surface">How it works:</strong> Place this QR code on the table. Customers scan it with their phone to open the online menu, add items to cart, and checkout — no app or account needed.</p>
                            <p>The order appears in <strong class="text-on-surface">Kitchen</strong> (prep list) and <strong class="text-on-surface">Waiter</strong> dashboard with the table number assigned automatically.</p>
                            <div class="flex gap-2 pt-1">
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode($selectedTable->qr_code ?? $this->generateQrUrl($selectedTable)) }}"
                                   target="_blank" download="table-{{ $selectedTable->table_number }}-qr.png"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-primary hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download QR
                                </a>
                                <button onclick="window.print()" class="inline-flex items-center gap-1 text-xs font-medium text-primary hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeDetail" class="rounded border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Close</button>
                    <button wire:click="openForm({{ $selectedTable->id }})" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Edit Table</button>
                </div>
            </div>
        </div>
    @endif
</div>
