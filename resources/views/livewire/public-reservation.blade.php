<div class="min-h-screen px-4 py-12 bg-surface-container-lowest sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto overflow-hidden border shadow-xl bg-surface-container rounded-2xl border-surface-container-high">
        {{-- Header --}}
        <div class="px-8 py-6 bg-primary text-on-primary">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Book a Table</h2>
                    <p class="mt-1 text-sm opacity-90">Secure your table at ReSaaS restaurant in seconds.</p>
                </div>
                <a href="{{ route('tenant.landing') }}" class="text-sm font-semibold hover:underline">Back Home</a>
            </div>
        </div>

        <div class="p-8">
            @if($isBooked)
                {{-- Success Screen --}}
                <div class="py-8 space-y-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success/20 text-success">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-bold text-on-surface">Reservation Confirmed!</h3>
                        <p class="text-sm text-secondary">Thank you, {{ $successDetails['customer_name'] }}. Your table reservation has been recorded.</p>
                    </div>

                    <div class="max-w-md p-6 mx-auto space-y-3 text-left border bg-surface-container-lowest rounded-xl border-surface-container-high">
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary">Branch:</span>
                            <span class="font-bold text-on-surface">{{ $successDetails['branch_name'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary">Date:</span>
                            <span class="font-bold text-on-surface">{{ $successDetails['date'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary">Time:</span>
                            <span class="font-bold text-on-surface">{{ $successDetails['time'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary">Guests:</span>
                            <span class="font-bold text-on-surface">{{ $successDetails['guest_count'] }} people</span>
                        </div>
                        @if($successDetails['table_number'])
                            <div class="flex justify-between pt-2 text-sm border-t border-surface-container-high">
                                <span class="text-secondary">Assigned Table:</span>
                                <span class="font-bold text-primary">Table {{ $successDetails['table_number'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-center gap-4 pt-4">
                        <a href="{{ route('tenant.menu') }}" class="px-6 py-2.5 bg-primary text-on-primary font-bold rounded-lg hover:bg-primary-container">Order Food Menu</a>
                        <button wire:click="resetBooking" class="px-6 py-2.5 border border-surface-container-high text-on-surface rounded-lg hover:bg-surface-container">Book Another Table</button>
                    </div>
                </div>
            @else
                {{-- Form Screen --}}
                <form wire:submit.prevent="book" class="space-y-6">
                    @if (session()->has('error'))
                        <div class="p-4 text-sm border rounded-lg bg-error/10 border-error/20 text-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Branch Selection --}}
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-on-surface">Select Branch</label>
                            <select wire:model.live="branchId" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none">
                                <option value="">Select branch...</option>
                                @foreach($this->branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branchId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Guest Count --}}
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-on-surface">Number of Guests</label>
                            <input type="number" min="1" max="30" wire:model.live="guestCount" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none">
                            @error('guestCount') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Date Selection --}}
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-on-surface">Reservation Date</label>
                            <input type="date" min="{{ date('Y-m-d') }}" wire:model.live="reservationDate" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none">
                            @error('reservationDate') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Time Selection --}}
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-on-surface">Preferred Time</label>
                            <select wire:model.live="reservationTime" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none">
                                @foreach($this->availableTimes as $time)
                                    <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            </select>
                            @error('reservationTime') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Live Availability Indicator --}}
                    <div class="p-4 rounded-lg flex items-center gap-3 border {{ $isAvailable ? 'bg-success/10 border-success/20 text-success' : 'bg-error/10 border-error/20 text-error' }}">
                        <div class="w-2.5 h-2.5 rounded-full {{ $isAvailable ? 'bg-success' : 'bg-error' }}"></div>
                        <span class="text-sm font-medium">
                            @if($isAvailable)
                                Tables are available for this size and time slot!
                            @else
                                No tables are available for the selected slot. Please adjust date, time, or guest count.
                            @endif
                        </span>
                    </div>

                    <div class="pt-6 space-y-6 border-t border-surface-container-high">
                        <h3 class="text-base font-bold text-on-surface">Contact Information</h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- Customer Name --}}
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-semibold text-on-surface">Full Name</label>
                                <input type="text" wire:model="customerName" placeholder="John Doe" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none">
                                @error('customerName') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>

                            {{-- Customer Phone --}}
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-on-surface">Phone Number</label>
                                <input type="tel" wire:model="customerPhone" placeholder="+1 (555) 000-0000" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none">
                                @error('customerPhone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>

                            {{-- Customer Email --}}
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-on-surface">Email Address (Optional)</label>
                                <input type="email" wire:model="customerEmail" placeholder="john@example.com" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none">
                                @error('customerEmail') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Special Requests --}}
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-on-surface">Special Requests (Optional)</label>
                            <textarea wire:model="specialRequests" rows="3" placeholder="Allergies, high chair, window seat, anniversary..." class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-4 py-2.5 text-on-surface focus:border-primary focus:ring-primary focus:outline-none"></textarea>
                            @error('specialRequests') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit" @if(!$isAvailable) disabled @endif class="w-full px-8 py-3 font-bold rounded-lg md:w-auto bg-primary text-on-primary hover:bg-primary-container disabled:opacity-50 disabled:cursor-not-allowed">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
