<div class="flex flex-col gap-6">
    <div>
        <h1 class="text-2xl font-bold">Floor Plan View</h1>
        <p class="mt-2 text-sm text-secondary">View and manage table status in real time. Click a table for details.</p>
    </div>

    <div class="rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-semibold">Floor Plan Layout</h2>
            <button wire:click="arrangeTables" class="rounded-full border border-surface-container-high px-3 py-1 text-sm font-medium text-on-surface hover:bg-surface-container transition">Arrange</button>
        </div>
        <div class="overflow-x-auto">
            <svg width="100%" height="600" viewBox="0 0 800 600"
                 class="border border-surface-container-high rounded-lg bg-surface-container-lowest">
                <defs>
                    <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                        <path d="M 20 0 L 0 0 0 20" fill="none" stroke="#e5e7eb" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="800" height="600" fill="url(#grid)" />

                @foreach($sectionBounds as $bg)
                    <rect x="{{ $bg['x'] }}" y="{{ $bg['y'] }}" width="{{ $bg['w'] }}" height="{{ $bg['h'] }}" fill="#e5e7eb" opacity="0.15" rx="8" />
                    <text x="{{ $bg['x'] + 8 }}" y="{{ $bg['y'] + 16 }}" font-size="11" font-weight="bold" fill="#374151">{{ $bg['name'] }}</text>
                @endforeach

                @foreach($this->tables as $table)
                    @php
                        $x = $table->x_position ?? 100;
                        $y = $table->y_position ?? 100;
                        $w = $table->width ?? 60;
                        $h = $table->height ?? 60;
                        $statusColor = match($table->status) {
                            'occupied' => '#dc2626',
                            'reserved' => '#f59e0b',
                            default => '#10b981',
                        };
                    @endphp
                    <g wire:click="selectTable({{ $table->id }})" style="cursor: pointer;">
                        @if($table->shape === 'circle')
                            <circle cx="{{ $x + $w/2 }}" cy="{{ $y + $h/2 }}" r="{{ $w/2 }}" fill="{{ $statusColor }}" opacity="0.8" stroke="#1f2937" stroke-width="2" />
                        @else
                            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="{{ $h }}" fill="{{ $statusColor }}" opacity="0.8" stroke="#1f2937" stroke-width="2" rx="4" />
                        @endif
                        <text x="{{ $x + $w/2 }}" y="{{ $y + $h/2 + 5 }}" text-anchor="middle" font-size="11" font-weight="bold" fill="white">T{{ $table->table_number }}</text>
                        <text x="{{ $x + $w/2 }}" y="{{ $y + $h + 18 }}" text-anchor="middle" font-size="9" fill="#6b7280">{{ ucfirst($table->status) }}</text>
                    </g>
                @endforeach
            </svg>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-green-500"></div>
                <span class="text-sm">Available</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-red-500"></div>
                <span class="text-sm">Occupied</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-4 w-4 rounded bg-amber-500"></div>
                <span class="text-sm">Reserved</span>
            </div>
        </div>
    </div>

    <!-- Table Details Modal -->
    @if($showTableModal && $tableData)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeTableModal">
            <div class="w-full max-w-md rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-lg">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Table {{ $tableData['table_number'] ?? 'N/A' }}</h2>
                    <button wire:click="closeTableModal" class="text-2xl leading-none text-secondary hover:text-on-surface">&times;</button>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-secondary">Capacity:</span>
                        <span class="font-medium">{{ $tableData['capacity'] ?? 'N/A' }} guests</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-secondary">Section:</span>
                        <span class="font-medium">{{ $tableData['section'] ?? 'No section' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-secondary">Status:</span>
                        <span class="inline-flex items-center gap-2 font-medium">
                            <div class="h-2 w-2 rounded-full" style="background-color: {{ match($tableData['status'] ?? 'free') { 'occupied' => '#dc2626', 'reserved' => '#f59e0b', default => '#10b981' } }}"></div>
                            {{ ucfirst($tableData['status'] ?? 'free') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-secondary">Position:</span>
                        <span class="font-medium">X: {{ $tableData['x_position'] ?? 0 }}, Y: {{ $tableData['y_position'] ?? 0 }}</span>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button wire:click="closeTableModal" class="flex-1 rounded-full border border-surface-container-high px-4 py-2 text-sm font-semibold hover:bg-surface-container-high">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
