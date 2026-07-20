<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-on-surface">My Shifts</h2>
        @if(!$this->activeShift)
            <button wire:click="$set('showOpenForm', true)" class="rounded-lg bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary/90 transition">Open Shift</button>
        @endif
    </div>

    {{-- Active shift banner --}}
    @if($this->activeShift)
        @php
            $shiftOrders = \App\Models\Tenant\Order::where('shift_id', $this->activeShift->id)->where('payment_status', 'paid')->get();
            $totalSales = $shiftOrders->sum('total');
            $orderCount = $shiftOrders->count();
        @endphp
        <div class="rounded-xl bg-primary/5 border border-primary/20 p-5 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                    <h3 class="font-bold text-on-surface">{{ $this->activeShift->name }}</h3>
                </div>
                <p class="text-sm text-secondary mt-1">Opened {{ $this->activeShift->opened_at?->diffForHumans() }}</p>
                <div class="flex gap-6 mt-3 text-sm">
                    <div>
                        <span class="text-secondary">Orders</span>
                        <p class="font-bold text-on-surface">{{ $orderCount }}</p>
                    </div>
                    <div>
                        <span class="text-secondary">Sales</span>
                        <p class="font-bold text-primary">${{ number_format($totalSales, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-secondary">Opening Cash</span>
                        <p class="font-bold text-on-surface">${{ number_format($this->activeShift->opening_cash, 2) }}</p>
                    </div>
                </div>
            </div>
            <button wire:click="prepareClose" class="rounded-lg bg-warning px-4 py-2 text-sm font-bold text-on-warning hover:bg-warning/90 transition">Close Shift</button>
        </div>
    @endif

    {{-- Previous shifts --}}
    <div class="space-y-2">
        <h3 class="text-sm font-bold text-secondary uppercase tracking-wide">Shift History</h3>
        @forelse($this->shifts as $shift)
            <div class="rounded-xl border border-surface-container-high bg-surface-container p-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div>
                        <p class="font-medium text-on-surface">{{ $shift->name }}</p>
                        <p class="text-xs text-secondary">{{ $shift->opened_at?->format('M j, Y g:i A') }} @if($shift->closed_at) - {{ $shift->closed_at->format('g:i A') }} @endif</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-right">
                    <div>
                        <p class="text-xs text-secondary">Sales</p>
                        <p class="font-bold text-on-surface">${{ number_format($shift->expected_cash + $shift->card_total + $shift->other_total, 2) }}</p>
                    </div>
                    <span @class([
                        'px-2 py-0.5 rounded-full text-xs font-bold',
                        'bg-success/20 text-success' => $shift->status === 'closed',
                        'bg-warning/20 text-warning' => $shift->status === 'open',
                    ])>{{ ucfirst($shift->status) }}</span>
                    <button wire:click="viewShift({{ $shift->id }})" class="text-xs font-medium text-primary hover:underline">View</button>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-secondary text-sm border border-dashed border-surface-container-high rounded-xl">
                No shifts yet.
            </div>
        @endforelse
    </div>

    {{-- Open shift form --}}
    @if($showOpenForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="$set('showOpenForm', false)">
            <div class="w-full max-w-md rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl">
                <h3 class="text-lg font-bold text-on-surface mb-4">Open New Shift</h3>
                <form wire:submit="openShift" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Shift Name</label>
                        <input wire:model="shiftName" placeholder="e.g. Morning Shift" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('shiftName') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Opening Cash ($)</label>
                        <input wire:model="openingCash" type="number" step="0.01" min="0" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('openingCash') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showOpenForm', false)" class="px-4 py-2 border border-surface-container-high rounded-lg text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-sm font-bold hover:bg-primary/90">Open Shift</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Close shift form --}}
    @if($showCloseForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="$set('showCloseForm', false)">
            <div class="w-full max-w-md rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl">
                <h3 class="text-lg font-bold text-on-surface mb-4">Close Shift</h3>
                @php $shift = \App\Models\Tenant\Shift::find($editingShiftId); @endphp
                @if($shift)
                    <div class="space-y-3 text-sm mb-4 p-3 bg-surface-container rounded-lg">
                        <div class="flex justify-between">
                            <span class="text-secondary">Expected Cash</span>
                            <span class="font-medium">${{ number_format($shift->expected_cash, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-secondary">Card Sales</span>
                            <span class="font-medium">${{ number_format($shift->card_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-secondary">Other Sales</span>
                            <span class="font-medium">${{ number_format($shift->other_total, 2) }}</span>
                        </div>
                    </div>
                    <form wire:submit="closeShift" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Actual Cash in Drawer ($)</label>
                            <input wire:model="actualCash" type="number" step="0.01" min="0" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                            @error('actualCash') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Notes (optional)</label>
                            <textarea wire:model="closeNotes" rows="2" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" wire:click="$set('showCloseForm', false)" class="px-4 py-2 border border-surface-container-high rounded-lg text-sm text-on-surface hover:bg-surface-container">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-warning text-on-warning rounded-lg text-sm font-bold hover:bg-warning/90">Close &amp; Calculate</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    {{-- Shift detail modal --}}
    @if($showDetail && $editingShiftId)
        @php $shift = \App\Models\Tenant\Shift::with('orders.items')->find($editingShiftId); @endphp
        @if($shift)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeDetail">
                <div class="w-full max-w-lg rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-on-surface">{{ $shift->name }}</h3>
                        <span @class([
                            'px-2 py-0.5 rounded-full text-xs font-bold',
                            'bg-success/20 text-success' => $shift->status === 'closed',
                            'bg-warning/20 text-warning' => $shift->status === 'open',
                        ])>{{ ucfirst($shift->status) }}</span>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-secondary">Opened</p>
                            <p class="font-medium text-on-surface">{{ $shift->opened_at?->format('M j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Closed</p>
                            <p class="font-medium text-on-surface">{{ $shift->closed_at?->format('M j, Y g:i A') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Opening Cash</p>
                            <p class="font-medium">${{ number_format($shift->opening_cash, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Actual Cash</p>
                            <p class="font-medium">${{ number_format($shift->actual_cash, 2) ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Expected Cash</p>
                            <p class="font-medium">${{ number_format($shift->expected_cash, 2) ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Card Sales</p>
                            <p class="font-medium">${{ number_format($shift->card_total, 2) ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Other Sales</p>
                            <p class="font-medium">${{ number_format($shift->other_total, 2) ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-secondary">Difference</p>
                            <p class="font-bold {{ $shift->difference >= 0 ? 'text-success' : 'text-error' }}">
                                {{ $shift->difference >= 0 ? '+' : '' }}${{ number_format($shift->difference, 2) }}
                            </p>
                        </div>
                    </div>
                    @if($shift->notes)
                        <div class="mt-4 p-3 bg-surface-container rounded-lg text-sm">
                            <p class="text-secondary text-xs">Notes</p>
                            <p class="text-on-surface">{{ $shift->notes }}</p>
                        </div>
                    @endif
                    <div class="mt-6 flex justify-end">
                        <button wire:click="closeDetail" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Close</button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
