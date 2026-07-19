<div class="space-y-4">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-bold text-on-surface">Staff Shifts</h2>
            <input wire:model.live="filterDate" type="date" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
            <select wire:model.live="filterStatus" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="on_break">On Break</option>
            </select>
        </div>
    </div>

    <div class="rounded-lg border border-surface-container-high overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-surface-container-high text-left">
                    <th class="px-4 py-2 font-medium text-on-surface">Staff</th>
                    <th class="px-4 py-2 font-medium text-on-surface">Clock In</th>
                    <th class="px-4 py-2 font-medium text-on-surface">Clock Out</th>
                    <th class="px-4 py-2 font-medium text-on-surface">Hours</th>
                    <th class="px-4 py-2 font-medium text-on-surface">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high">
                @forelse($this->shifts as $shift)
                    <tr class="hover:bg-surface-container-low">
                        <td class="px-4 py-2">
                            <span class="font-medium text-on-surface">{{ $shift->user?->name ?? 'Deleted' }}</span>
                            @if($shift->branch)
                                <span class="text-secondary">· {{ $shift->branch->name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-secondary">{{ $shift->clock_in?->format('M j, g:i A') ?? '—' }}</td>
                        <td class="px-4 py-2 text-secondary">{{ $shift->clock_out?->format('M j, g:i A') ?? '—' }}</td>
                        <td class="px-4 py-2 text-secondary">{{ $shift->total_hours ? $shift->total_hours . 'h' : '—' }}</td>
                        <td class="px-4 py-2">
                            <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $shift->status === 'active' ? 'bg-success/10 text-success' : ($shift->status === 'on_break' ? 'bg-warning/10 text-warning' : 'bg-secondary/10 text-secondary') }}">
                                {{ ucfirst($shift->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-secondary">No shifts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Today's on-duty summary --}}
    <div class="rounded-lg border border-surface-container-high bg-surface-container-lowest p-4">
        <h3 class="text-sm font-bold text-on-surface mb-2">On Duty Today</h3>
        <div class="flex flex-wrap gap-2">
            @php $active = $this->shifts->where('status', 'active'); @endphp
            @forelse($active as $shift)
                <span class="rounded-full bg-success/10 px-3 py-1 text-xs font-bold text-success">{{ $shift->user?->name }}</span>
            @empty
                <span class="text-sm text-secondary">No staff currently clocked in.</span>
            @endforelse
        </div>
    </div>
</div>
