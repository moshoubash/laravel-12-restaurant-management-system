<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold">Floor Plan Manager</h1>
        <p class="mt-2 text-sm text-secondary">Manage restaurant sections and table layout on the floor plan.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-4">
        <!-- Sections Panel -->
        <div class="rounded-3xl border border-surface-container-high bg-surface-container p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Sections</h2>
                <button wire:click="createSection" class="rounded-full bg-primary px-3 py-1 text-sm text-on-primary hover:bg-primary/90">+ Add</button>
            </div>
            <div class="space-y-2">
                @foreach($sections as $section)
                    <div class="flex items-center justify-between rounded-lg border border-surface-container-high bg-surface-container-lowest p-3">
                        <div>
                            <div class="text-sm font-medium">{{ $section->name }}</div>
                            <div class="inline-flex mt-1 h-3 w-3 rounded-full" style="background-color: {{ $section->color }}"></div>
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="editSection({{ $section->id }})" class="text-xs text-primary hover:underline">Edit</button>
                            <button wire:click="deleteSection({{ $section->id }})" class="text-xs text-error hover:underline" onclick="return confirm('Delete section?');">Delete</button>
                        </div>
                    </div>
                @endforeach
                @if($sections->isEmpty())
                    <p class="text-sm text-secondary">No sections yet</p>
                @endif
            </div>
        </div>

        <!-- Floor Plan Canvas -->
        <div class="col-span-3 rounded-3xl border border-surface-container-high bg-surface-container p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Floor Plan Layout</h2>
                <button wire:click="createTable" class="rounded-full bg-primary px-3 py-1 text-sm text-on-primary hover:bg-primary/90">+ Add Table</button>
            </div>

            <div class="overflow-x-auto">
                <svg width="100%" height="600" viewBox="0 0 800 600" class="border border-surface-container-high rounded-lg bg-surface-container-lowest">
                    <!-- Background grid -->
                    <defs>
                        <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                            <path d="M 20 0 L 0 0 0 20" fill="none" stroke="#e5e7eb" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="800" height="600" fill="url(#grid)" />

                    <!-- Section backgrounds -->
                    @foreach($tablesBySection as $section => $tables)
                        @php
                            $sectionModel = $sections->where('name', $section)->first();
                            $bgColor = $sectionModel ? $sectionModel->color : '#f3f4f6';
                        @endphp
                        <g opacity="0.1">
                            @foreach($tables as $table)
                                @if($loop->first)
                                    <text x="10" y="30" font-size="12" fill="#6b7280">{{ $section ?? 'No Section' }}</text>
                                @endif
                            @endforeach
                        </g>
                    @endforeach

                    <!-- Tables -->
                    @foreach($tables as $table)
                        @php
                            $x = $table->x_position ?? 100;
                            $y = $table->y_position ?? 100;
                            $w = $table->width ?? 60;
                            $h = $table->height ?? 60;
                            $active = $table->status === 'occupied' ? '#dc2626' : ($table->status === 'reserved' ? '#f59e0b' : '#10b981');
                        @endphp
                        <g wire:click="editTable({{ $table->id }})" style="cursor: pointer;">
                            @if($table->shape === 'circle')
                                <circle cx="{{ $x + $w/2 }}" cy="{{ $y + $h/2 }}" r="{{ $w/2 }}" fill="{{ $active }}" opacity="0.8" stroke="#1f2937" stroke-width="2" />
                            @else
                                <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="{{ $h }}" fill="{{ $active }}" opacity="0.8" stroke="#1f2937" stroke-width="2" rx="4" />
                            @endif
                            <text x="{{ $x + $w/2 }}" y="{{ $y + $h/2 + 5 }}" text-anchor="middle" font-size="10" font-weight="bold" fill="white">T{{ $table->table_number }}</text>
                        </g>
                    @endforeach
                </svg>
            </div>

            <div class="mt-4 flex gap-4">
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
    </div>

    <!-- Section Form Modal -->
    @if($showSectionForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-lg">
                <h2 class="text-lg font-semibold">{{ $editingSection ? 'Edit Section' : 'Add Section' }}</h2>
                <form wire:submit="saveSection" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Section Name</label>
                        <input type="text" wire:model="sectionName" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                        @error('sectionName') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Description</label>
                        <textarea wire:model="sectionDescription" rows="3" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Color</label>
                        <input type="color" wire:model="sectionColor" class="mt-1 h-10 w-full rounded border border-surface-container-high">
                        @error('sectionColor') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary/90">Save</button>
                        <button type="button" wire:click="closeSectionForm" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Table Form Modal -->
    @if($showTableForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-lg">
                <h2 class="text-lg font-semibold">{{ $editingTable ? 'Edit Table' : 'Add Table' }}</h2>
                <form wire:submit="saveTable" class="mt-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Capacity</label>
                            <input type="number" wire:model="tableCapacity" min="1" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                            @error('tableCapacity') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Section</label>
                            <select wire:model="tableSection" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->name }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                            @error('tableSection') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium">X Position</label>
                            <input type="number" wire:model="tableX" step="0.1" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Y Position</label>
                            <input type="number" wire:model="tableY" step="0.1" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Shape</label>
                            <select wire:model="tableShape" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                                <option value="rectangle">Rectangle</option>
                                <option value="circle">Circle</option>
                                <option value="square">Square</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Width</label>
                            <input type="number" wire:model="tableWidth" min="20" step="1" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                            @error('tableWidth') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Height</label>
                            <input type="number" wire:model="tableHeight" min="20" step="1" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                            @error('tableHeight') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary/90">Save Table</button>
                        <button type="button" wire:click="closeTableForm" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
