<div class="flex gap-6">
    {{-- Floor plan --}}
    <div class="flex-1">
        <h2 class="mb-4 text-lg font-bold text-on-surface">Tables</h2>

        @php $grouped = $this->tables->groupBy('section'); @endphp
        @if($grouped->isEmpty())
            <div class="py-12 text-center text-secondary">No tables available.</div>
        @else
            @foreach($grouped as $section => $tables)
                <div class="mb-6">
                    @if($section)
                        <h3 class="mb-2 text-sm font-bold uppercase tracking-wider text-secondary">{{ $section }}</h3>
                    @endif
                    <div class="flex flex-wrap gap-3">
                        @foreach($tables as $table)
                            <button wire:click="selectTable({{ $table->id }})"
                                    class="flex h-24 w-24 flex-col items-center justify-center rounded-xl border-2 text-center text-sm font-bold transition-shadow hover:shadow-lg {{ $selectedTableId === $table->id ? 'ring-2 ring-primary ring-offset-2' : '' }}"
                                    style="border-color: {{ match($table->status) { 'free' => 'var(--color-success, #22c55e)', 'occupied' => 'var(--color-error, #ef4444)', 'reserved' => 'var(--color-warning, #f59e0b)', default => 'var(--color-surface-container-high, #e7e5e4)' } }}; background-color: {{ match($table->status) { 'free' => 'color-mix(in srgb, var(--color-success, #22c55e) 10%, transparent)', 'occupied' => 'color-mix(in srgb, var(--color-error, #ef4444) 10%, transparent)', 'reserved' => 'color-mix(in srgb, var(--color-warning, #f59e0b) 10%, transparent)', default => 'transparent' } }};">
                                <span class="text-lg text-on-surface">T{{ $table->table_number }}</span>
                                <span class="text-xs text-secondary">{{ $table->capacity }} seats</span>
                                <span class="mt-1 text-[10px] font-bold uppercase {{ match($table->status) { 'free' => 'text-success', 'occupied' => 'text-error', 'reserved' => 'text-warning', default => 'text-secondary' } }}">
                                    {{ $table->status }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Table detail sidebar --}}
    @if($selectedTableId)
        @php $table = \App\Models\Tenant\Table::find($selectedTableId); @endphp
        @if($table)
            <div class="w-80 shrink-0 rounded-xl border border-surface-container-high bg-surface-container-lowest p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-on-surface">Table T{{ $table->table_number }}</h3>
                    <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ match($table->status) { 'free' => 'bg-success/10 text-success', 'occupied' => 'bg-error/10 text-error', 'reserved' => 'bg-warning/10 text-warning', default => 'bg-surface-container-high text-secondary' } }}">
                        {{ ucfirst($table->status) }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-secondary">{{ $table->capacity }} seats{{ $table->section ? " · $table->section" : '' }}</p>

                <div class="mt-4 space-y-3">
                    <h4 class="text-sm font-bold text-on-surface">Active Orders</h4>
                    @forelse($selectedTableOrders as $order)
                        <div class="rounded-lg border border-surface-container-high p-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-on-surface">#{{ $order['order_number'] }}</span>
                                <span class="text-xs text-secondary">{{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</span>
                            </div>
                            <div class="mt-2 space-y-1">
                                @foreach($order['items'] as $item)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-secondary">{{ $item['quantity'] }}× {{ $item['menu_item_name'] }}</span>
                                        <span class="font-medium">${{ number_format($item['total_price'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ match($order['status']) { 'pending' => 'bg-warning/10 text-warning', 'preparing' => 'bg-primary/10 text-primary', 'ready' => 'bg-success/10 text-success', default => 'bg-surface-container-high text-secondary' } }}">
                                    {{ ucfirst($order['status']) }}
                                </span>
                                <span class="text-sm font-bold text-primary">${{ number_format($order['total'], 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-secondary">No active orders for this table.</p>
                    @endforelse
                </div>
            </div>
        @endif
    @endif
</div>
