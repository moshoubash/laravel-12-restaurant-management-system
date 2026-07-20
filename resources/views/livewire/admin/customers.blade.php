<div class="space-y-4">
    {{-- Toolbar / Filters --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <h2 class="text-lg font-bold text-on-surface">Customers</h2>
            <input wire:model.live.debounce="search" type="text" placeholder="Search name, email, phone..." class="w-64 rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface placeholder-secondary focus:border-primary focus:ring-primary focus:outline-none">
            
            <select wire:model.live="filterBranch" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none">
                <option value="">All Branches</option>
                @foreach($this->branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <button wire:click="openForm()" class="rounded bg-primary px-3 py-1.5 text-sm font-bold text-on-primary hover:bg-primary-container">+ Add Customer</button>
    </div>

    {{-- Session success message --}}
    @if(session('success'))
        <div class="p-3 bg-success/10 border border-success/20 rounded-lg text-sm text-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Customers Table --}}
    <div class="overflow-x-auto rounded-lg border border-surface-container-high bg-surface-container-lowest">
        <table class="w-full border-collapse text-left text-sm text-on-surface">
            <thead class="bg-surface-container border-b border-surface-container-high">
                <tr>
                    <th class="px-6 py-3 font-semibold text-secondary">Customer</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Branch</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Stats (Visits / Spent)</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Loyalty</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Allergies & Prefs</th>
                    <th class="px-6 py-3 font-semibold text-secondary">Status</th>
                    <th class="px-6 py-3 font-semibold text-secondary text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high">
                @forelse($this->customers as $cust)
                    <tr wire:key="customer-{{ $cust->id }}" class="hover:bg-surface-container/50 {{ !$cust->is_active ? 'opacity-50' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $cust->name }}</div>
                            <div class="text-xs text-secondary">{{ $cust->phone ?? 'No Phone' }} @if($cust->email) · {{ $cust->email }} @endif</div>
                        </td>
                        <td class="px-6 py-4 text-secondary">
                            {{ $cust->branch->name ?? 'Global / None' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $cust->total_visits }} visits</div>
                            <div class="text-xs text-secondary">${{ number_format($cust->total_spent, 2) }} spent</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded bg-warning/10 border border-warning/20 px-2 py-0.5 text-xs font-semibold text-warning-variant">{{ $cust->loyalty_points }} pts</span>
                        </td>
                        <td class="px-6 py-4 space-y-1 max-w-xs">
                            {{-- Allergies --}}
                            @if(!empty($cust->allergies))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($cust->allergies as $allergy)
                                        <span class="rounded bg-error/10 border border-error/20 px-1.5 py-0.5 text-[10px] font-semibold text-error">{{ $allergy }}</span>
                                    @endforeach
                                </div>
                            @endif
                            {{-- Preferences --}}
                            @if(!empty($cust->preferences))
                                <div class="flex flex-wrap gap-1">
                                    @foreach($cust->preferences as $pref)
                                        <span class="rounded bg-surface-container-high px-1.5 py-0.5 text-[10px] font-semibold text-secondary">{{ $pref }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if(empty($cust->allergies) && empty($cust->preferences))
                                <span class="text-xs text-secondary/60 italic">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full border px-2 py-0.5 text-xs font-bold {{ $cust->is_active ? 'bg-success/10 text-success border-success/20' : 'bg-surface-container-high text-secondary border-surface-container-high' }}">
                                {{ $cust->is_active ? 'Active' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="openHistory({{ $cust->id }})" class="rounded p-1 hover:bg-surface-container text-primary" title="View History">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                </button>
                                <button wire:click="openForm({{ $cust->id }})" class="rounded p-1 hover:bg-surface-container text-secondary" title="Edit">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="deleteCustomer({{ $cust->id }})" wire:confirm="Delete this customer profile?" class="rounded p-1 hover:bg-surface-container text-error" title="Delete">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-secondary">No customer profiles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="showForm = false">
            <div class="w-full max-w-lg rounded-xl bg-surface-container p-6 shadow-xl max-h-[90vh] overflow-y-auto border border-surface-container-high">
                <h3 class="text-lg font-bold text-on-surface mb-4">{{ $editingCustomer ? 'Edit' : 'Add' }} Customer Profile</h3>
                
                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Name --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-on-surface">Full Name</label>
                            <input type="text" wire:model="name" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('name') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Email</label>
                            <input type="email" wire:model="email" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('email') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Phone Number</label>
                            <input type="tel" wire:model="phone" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('phone') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Branch --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Home Branch</label>
                            <select wire:model="branchId" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                                <option value="">None / Global</option>
                                @foreach($this->branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branchId') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Active Status --}}
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 text-sm font-medium text-on-surface cursor-pointer">
                                <input type="checkbox" wire:model="isActive" class="rounded border-surface-container-high text-primary focus:ring-primary">
                                Active Customer
                            </label>
                        </div>

                        {{-- Birthday --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Birthday</label>
                            <input type="date" wire:model="birthday" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('birthday') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Anniversary --}}
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Anniversary</label>
                            <input type="date" wire:model="anniversary" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('anniversary') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Tags section --}}
                    <div class="border-t border-surface-container-high pt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Allergies <span class="text-xs text-secondary font-normal">(Comma-separated list)</span></label>
                            <input type="text" wire:model="preferencesInput" placeholder="Nuts, Gluten, Dairy" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('preferencesInput') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-on-surface">Preferences <span class="text-xs text-secondary font-normal">(Comma-separated list)</span></label>
                            <input type="text" wire:model="allergiesInput" placeholder="Window table, Vegan, Extra spicy" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none">
                            @error('allergiesInput') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="border-t border-surface-container-high pt-4">
                        <label class="block text-sm font-semibold text-on-surface">Staff Notes</label>
                        <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none"></textarea>
                        @error('notes') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
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

    {{-- History drawer --}}
    @if($showHistory)
        <div class="fixed inset-0 z-50 flex items-center justify-end bg-black/40 backdrop-blur-sm" wire:click.self="closeHistory">
            <div class="w-full max-w-xl h-full bg-surface-container p-6 shadow-2xl overflow-y-auto border-l border-surface-container-high flex flex-col justify-between">
                <div>
                    {{-- Drawer Header --}}
                    <div class="flex items-center justify-between border-b border-surface-container-high pb-4 mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-on-surface">{{ $this->customerHistory->name }}</h3>
                            <p class="text-xs text-secondary">{{ $this->customerHistory->phone }} · {{ $this->customerHistory->email }}</p>
                        </div>
                        <button wire:click="closeHistory" class="text-secondary hover:text-on-surface">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Customer Summary stats --}}
                    <div class="grid grid-cols-3 gap-4 mb-6 text-center">
                        <div class="bg-surface-container-lowest border border-surface-container-high p-3 rounded-lg">
                            <span class="block text-[10px] uppercase font-bold text-secondary tracking-wider">Visits</span>
                            <span class="text-lg font-bold text-on-surface">{{ $this->customerHistory->total_visits }}</span>
                        </div>
                        <div class="bg-surface-container-lowest border border-surface-container-high p-3 rounded-lg">
                            <span class="block text-[10px] uppercase font-bold text-secondary tracking-wider">Spent</span>
                            <span class="text-lg font-bold text-primary">${{ number_format($this->customerHistory->total_spent, 2) }}</span>
                        </div>
                        <div class="bg-surface-container-lowest border border-surface-container-high p-3 rounded-lg">
                            <span class="block text-[10px] uppercase font-bold text-secondary tracking-wider">Loyalty</span>
                            <span class="text-lg font-bold text-warning-variant">{{ $this->customerHistory->loyalty_points }} pts</span>
                        </div>
                    </div>

                    {{-- Sections: Reservations & Orders --}}
                    <div class="space-y-6">
                        {{-- Reservations section --}}
                        <div class="space-y-3">
                            <h4 class="font-bold text-sm text-on-surface border-b border-surface-container-high pb-1.5 flex items-center justify-between">
                                <span>Recent Reservations</span>
                                <span class="text-xs font-normal text-secondary">({{ $this->customerHistory->reservations->count() }})</span>
                            </h4>
                            <div class="space-y-2 max-h-56 overflow-y-auto">
                                @forelse($this->customerHistory->reservations as $res)
                                    <div class="bg-surface-container-lowest border border-surface-container-high p-3 rounded-lg flex items-center justify-between text-xs">
                                        <div>
                                            <div class="font-semibold text-on-surface">{{ $res->reservation_date->format('Y-m-d') }} @ {{ $res->reservation_time->format('H:i') }}</div>
                                            <div class="text-secondary">{{ $res->guest_count }} people @if($res->table_id) · Table {{ $res->table_id }} @endif</div>
                                        </div>
                                        @php
                                            $resBadge = match($res->status) {
                                                'confirmed' => 'bg-success/10 text-success border-success/20',
                                                'seated' => 'bg-primary/10 text-primary border-primary/20',
                                                'cancelled' => 'bg-error/10 text-error border-error/20',
                                                default => 'bg-warning/10 text-warning border-warning/20',
                                            };
                                        @endphp
                                        <span class="rounded border px-2 py-0.5 font-bold uppercase text-[9px] {{ $resBadge }}">
                                            {{ $res->status }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-xs text-secondary italic py-2">No reservation history available.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Orders section --}}
                        <div class="space-y-3">
                            <h4 class="font-bold text-sm text-on-surface border-b border-surface-container-high pb-1.5 flex items-center justify-between">
                                <span>Recent Orders</span>
                                <span class="text-xs font-normal text-secondary">({{ $this->customerHistory->orders->count() }})</span>
                            </h4>
                            <div class="space-y-2 max-h-56 overflow-y-auto">
                                @forelse($this->customerHistory->orders as $ord)
                                    <div class="bg-surface-container-lowest border border-surface-container-high p-3 rounded-lg flex items-center justify-between text-xs">
                                        <div>
                                            <div class="font-semibold text-on-surface">{{ $ord->order_number }}</div>
                                            <div class="text-secondary">{{ $ord->ordered_at ? \Carbon\Carbon::parse($ord->ordered_at)->format('Y-m-d H:i') : '' }} · {{ ucfirst($ord->order_type) }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-on-surface">${{ number_format($ord->total, 2) }}</div>
                                            <span class="text-[9px] font-bold uppercase rounded border px-1.5 py-0.2 {{ $ord->status === 'completed' ? 'bg-success/10 text-success border-success/20' : 'bg-warning/10 text-warning border-warning/20' }}">
                                                {{ $ord->status }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-secondary italic py-2">No order history available.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-surface-container-high flex justify-end">
                    <button wire:click="closeHistory" class="rounded-lg bg-primary px-5 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                        Close Drawer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
