<div class="space-y-4">
    {{-- Toolbar / Filters --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <h2 class="text-lg font-bold text-on-surface">Reservations</h2>
            <input wire:model.live.debounce="search" type="text" placeholder="Search customer..." class="w-48 rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface placeholder-secondary focus:border-primary focus:ring-primary focus:outline-none">
            
            <select wire:model.live="filterStatus" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="seated">Seated</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <select wire:model.live="filterBranch" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none">
                <option value="">All Branches</option>
                @foreach($this->branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>

            <input type="date" wire:model.live="filterDate" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none">
        </div>
        <button wire:click="openForm()" class="rounded bg-primary px-3 py-1.5 text-sm font-bold text-on-primary hover:bg-primary-container">+ Add Reservation</button>
    </div>

    {{-- Session success message --}}
    @if(session('success'))
        <div class="p-3 bg-success/10 border border-success/20 rounded-lg text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Reservations List Table --}}
    <div class="overflow-x-auto rounded-lg border border-surface-container-high bg-surface-container-lowest">
        <table class="w-full border-collapse text-left text-sm text-on-surface">
            <thead class="bg-surface-container border-b border-surface-container-high">
                <tr>
                    <th class="px-6 py-3 font-semibold text-secondary">Customer</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Date & Time</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Branch & Guests</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Table</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Status</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Source</th>
                    <th class="px-6 py-3 font-semibold text-secondary text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high">
                @forelse($this->reservations as $res)
                    <tr wire:key="reservation-{{ $res->id }}" class="hover:bg-surface-container/50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $res->customer_name }}</div>
                            <div class="text-xs text-secondary">{{ $res->customer_phone }} @if($res->customer_email) · {{ $res->customer_email }} @endif</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $res->reservation_date->format('Y-m-d') }}</div>
                            <div class="text-xs text-secondary">{{ $res->reservation_time->format('H:i') }} ({{ $res->duration }} min)</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $res->branch->name }}</div>
                            <div class="text-xs text-secondary">{{ $res->guest_count }} guests</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($res->table)
                                <span class="rounded bg-primary/10 border border-primary/20 px-2 py-0.5 text-xs font-semibold text-primary">Table {{ $res->table->table_number }}</span>
                            @else
                                <span class="rounded bg-error/10 border border-error/20 px-2 py-0.5 text-xs font-semibold text-error">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeColor = match($res->status) {
                                    'confirmed' => 'bg-success/10 text-success border-success/20',
                                    'seated' => 'bg-primary/10 text-primary border-primary/20',
                                    'cancelled' => 'bg-error/10 text-error border-error/20',
                                    default => 'bg-warning/10 text-warning border-warning/20',
                                };
                            @endphp
                            <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $badgeColor }}">
                                {{ ucfirst($res->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs capitalize font-medium text-secondary">
                                {{ $res->source }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($res->status === 'pending')
                                    <button wire:click="confirmReservation({{ $res->id }})" class="rounded p-1 hover:bg-surface-container text-success" title="Confirm">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                @endif
                                @if($res->status === 'confirmed')
                                    <button wire:click="seatReservation({{ $res->id }})" class="rounded p-1 hover:bg-surface-container text-primary" title="Seat / Check-in">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </button>
                                @endif
                                @if(in_array($res->status, ['pending', 'confirmed']))
                                    <button wire:click="openCancelModal({{ $res->id }})" class="rounded p-1 hover:bg-surface-container text-error" title="Cancel">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif
                                <button wire:click="openForm({{ $res->id }})" class="rounded p-1 hover:bg-surface-container text-secondary" title="Edit">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="deleteReservation({{ $res->id }})" wire:confirm="Delete this reservation?" class="rounded p-1 hover:bg-surface-container text-error" title="Delete">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-secondary">No reservations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" wire:click.self="showForm = false">
            <div class="w-full max-w-2xl rounded-xl bg-surface-container p-6 shadow-xl max-h-[90vh] overflow-y-auto border border-surface-container-high">
                <h3 class="text-lg font-bold text-on-surface mb-4">{{ $editingReservation ? 'Edit' : 'Add' }} Reservation</h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Branch --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Branch</label>
                            <select wire:model.live="branchId" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                @foreach($this->branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branchId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Guests --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Number of Guests</label>
                            <input type="number" min="1" max="30" wire:model.live="guestCount" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('guestCount') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Date</label>
                            <input type="date" wire:model.live="reservationDate" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('reservationDate') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Time --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Time</label>
                            <select wire:model.live="reservationTime" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                @foreach($this->availableTimes as $time)
                                    <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            </select>
                            @error('reservationTime') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Duration --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Duration (Minutes)</label>
                            <input type="number" step="15" min="15" wire:model.live="duration" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('duration') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Table Assignment --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Assign Table</label>
                            <select wire:model="tableId" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                <option value="">Select conflict-free table...</option>
                                @foreach($this->availableTablesList as $tbl)
                                    <option value="{{ $tbl->id }}">Table {{ $tbl->table_number }} (Cap: {{ $tbl->capacity }})</option>
                                @endforeach
                            </select>
                            @error('tableId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            @if($this->availableTablesList->isEmpty())
                                <p class="mt-1 text-xs text-error">Warning: No tables available for this date/time and capacity.</p>
                            @endif
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Status</label>
                            <select wire:model="status" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="seated">Seated</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Source --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Source</label>
                            <select wire:model="source" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                <option value="manual">Manual</option>
                                <option value="online">Online</option>
                            </select>
                            @error('source') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Customer Details section --}}
                    <div class="border-t border-surface-container-high pt-4 space-y-4">
                        <h4 class="font-bold text-sm text-on-surface">Customer Contact Information</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-on-surface">Customer Name</label>
                                <input type="text" wire:model="customerName" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                @error('customerName') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-on-surface">Customer Phone</label>
                                <input type="tel" wire:model="customerPhone" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                @error('customerPhone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-on-surface">Customer Email (Optional)</label>
                                <input type="email" wire:model="customerEmail" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                @error('customerEmail') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Requests and Notes --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-surface-container-high">
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Special Requests</label>
                            <textarea wire:model="specialRequests" rows="3" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none"></textarea>
                            @error('specialRequests') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Internal Notes</label>
                            <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none"></textarea>
                            @error('notes') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-surface-container-high">
                        <button type="button" wire:click="showForm = false" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-lg bg-primary px-5 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Cancellation modal --}}
    @if($showCancelModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30" wire:click.self="showCancelModal = false">
            <div class="w-full max-w-md rounded-xl bg-surface-container p-6 shadow-xl border border-surface-container-high">
                <h3 class="text-lg font-bold text-on-surface mb-3">Cancel Reservation</h3>
                
                <form wire:submit.prevent="cancelReservation" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface mb-2">Reason for Cancellation</label>
                        <input type="text" wire:model="cancellationReason" placeholder="Customer called, fully booked overlap, etc." class="block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-primary focus:border-primary">
                        @error('cancellationReason') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-surface-container-high">
                        <button type="button" wire:click="showCancelModal = false" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">
                            Close
                        </button>
                        <button type="submit" class="rounded-lg bg-error px-5 py-2 text-sm font-bold text-white hover:bg-error/95">
                            Cancel Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
