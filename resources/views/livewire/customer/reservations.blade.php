<div class="max-w-7xl mx-auto px-4 py-8 flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-surface-container-high pb-4">
        <div>
            <h1 class="text-2xl font-bold text-on-surface">My Reservations</h1>
            <p class="text-sm text-secondary">View and manage your table bookings.</p>
        </div>
        <button wire:click="openBookModal" class="rounded-lg bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
            + Book a Table
        </button>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="p-4 bg-success/10 border border-success/20 rounded-lg text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Reservations List --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($this->reservations as $res)
            <div wire:key="res-{{ $res->id }}" class="border border-surface-container-high bg-surface-container p-6 rounded-xl flex flex-col gap-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-on-surface">{{ $res->branch->name }}</h3>
                        <p class="text-xs text-secondary">{{ $res->branch->address }}</p>
                    </div>

                    @php
                        $badgeClasses = match($res->status) {
                            'confirmed' => 'bg-success/10 text-success border-success/20',
                            'seated' => 'bg-primary/10 text-primary border-primary/20',
                            'cancelled' => 'bg-error/10 text-error border-error/20',
                            default => 'bg-warning/10 text-warning border-warning/20',
                        };
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-bold border rounded-full {{ $badgeClasses }}">
                        {{ ucfirst($res->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm py-2 border-t border-b border-surface-container-high">
                    <div>
                        <span class="block text-xs text-secondary">Date & Time</span>
                        <span class="font-semibold text-on-surface">
                            {{ $res->reservation_date->format('M d, Y') }} @ {{ $res->reservation_time->format('H:i') }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-secondary">Table & Guests</span>
                        <span class="font-semibold text-on-surface">
                            {{ $res->table ? 'Table ' . $res->table->table_number : 'Assigning Table...' }} ({{ $res->guest_count }} guests)
                        </span>
                    </div>
                </div>

                @if($res->special_requests)
                    <div class="text-xs text-secondary bg-surface-container-lowest p-2.5 rounded border border-surface-container-high">
                        <span class="font-bold">Requests:</span> {{ $res->special_requests }}
                    </div>
                @endif

                @if($res->status === 'cancelled')
                    @if($res->cancellation_reason)
                        <div class="text-xs text-error bg-error/5 p-2.5 rounded border border-error/10">
                            <span class="font-bold">Cancellation Reason:</span> {{ $res->cancellation_reason }}
                        </div>
                    @endif
                @endif

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-2 pt-2">
                    @if(in_array($res->status, ['pending', 'confirmed']) && $res->reservation_date->isAfter(now()->subDay()))
                        <button wire:click="cancelReservation({{ $res->id }})" wire:confirm="Are you sure you want to cancel this reservation?" class="px-4 py-2 border border-error/20 rounded-lg text-xs font-bold text-error hover:bg-error/5">
                            Cancel Booking
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-2 py-16 text-center text-secondary border border-dashed border-surface-container-high rounded-xl">
                <svg class="mx-auto h-12 w-12 text-secondary opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 font-semibold text-on-surface">No reservations yet</h3>
                <p class="mt-1 text-sm">Create your first restaurant table booking today.</p>
                <button wire:click="openBookModal" class="mt-4 rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                    Book a Table
                </button>
            </div>
        @endforelse
    </div>

    {{-- Book Modal --}}
    @if($showBookModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelBooking">
            <div class="w-full max-w-lg rounded-xl bg-surface-container p-6 shadow-xl max-h-[90vh] overflow-y-auto border border-surface-container-high">
                <div class="flex items-center justify-between border-b border-surface-container-high pb-3 mb-4">
                    <h3 class="text-lg font-bold text-on-surface">Book a Table</h3>
                    <button wire:click="cancelBooking" class="text-secondary hover:text-on-surface">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="book" class="flex flex-col gap-4">
                    @if (session()->has('error'))
                        <div class="p-3 bg-error/10 border border-error/20 rounded-lg text-xs text-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Branch --}}
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Branch</label>
                        <select wire:model.live="branchId" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-primary focus:border-primary">
                            @foreach($this->branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branchId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Guests --}}
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Number of Guests</label>
                        <input type="number" min="1" max="30" wire:model.live="guestCount" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-primary focus:border-primary">
                        @error('guestCount') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Date</label>
                        <input type="date" min="{{ date('Y-m-d') }}" wire:model.live="reservationDate" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-primary focus:border-primary">
                        @error('reservationDate') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Time --}}
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Time</label>
                        <select wire:model.live="reservationTime" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-primary focus:border-primary">
                            @foreach($this->availableTimes as $time)
                                <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                        @error('reservationTime') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Live Indicator --}}
                    <div class="p-3.5 rounded-lg flex items-center gap-2 border {{ $isAvailable ? 'bg-success/10 border-success/20 text-success' : 'bg-error/10 border-error/20 text-error' }}">
                        <div class="w-2 h-2 rounded-full {{ $isAvailable ? 'bg-success' : 'bg-error' }}"></div>
                        <span class="text-xs font-semibold">
                            {{ $isAvailable ? 'Tables are available for this slot!' : 'No tables available. Please change selection.' }}
                        </span>
                    </div>

                    {{-- Special Requests --}}
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Special Requests <span class="text-secondary font-normal">(Optional)</span></label>
                        <textarea wire:model="specialRequests" rows="3" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                        @error('specialRequests') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-3 pt-3 border-t border-surface-container-high">
                        <button type="button" wire:click="cancelBooking" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">
                            Cancel
                        </button>
                        <button type="submit" @if(!$isAvailable) disabled @endif class="rounded-lg bg-primary px-5 py-2 text-sm font-bold text-on-primary hover:bg-primary-container disabled:opacity-50 disabled:cursor-not-allowed">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
