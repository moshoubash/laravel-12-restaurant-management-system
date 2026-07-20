<div class="space-y-6">
    {{-- Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-lg font-bold text-on-surface">Branch Reports</h2>
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="dateRange" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_year">This Year</option>
                <option value="custom">Custom Range</option>
            </select>

            @if($dateRange === 'custom')
                <input type="date" wire:model.live="dateFrom" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none">
                <span class="text-xs text-secondary">to</span>
                <input type="date" wire:model.live="dateTo" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none">
            @endif

            <div class="flex gap-2 border-l border-surface-container-high pl-3">
                <button wire:click="exportPdf" class="inline-flex items-center gap-1.5 rounded-lg border border-surface-container-high px-3 py-1.5 text-sm font-medium text-on-surface hover:bg-surface-container transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path d="M12 3v6a1 1 0 001 1h6"/></svg>
                    PDF
                </button>
                <button wire:click="exportExcel" class="inline-flex items-center gap-1.5 rounded-lg border border-surface-container-high px-3 py-1.5 text-sm font-medium text-on-surface hover:bg-surface-container transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </button>
            </div>
        </div>
    </div>

    {{-- Date context --}}
    <div class="text-xs text-secondary">
        Showing data from <span class="font-semibold text-on-surface">{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}</span>
        to <span class="font-semibold text-on-surface">{{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</span>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Revenue --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary">Total Revenue</p>
                    <p class="text-2xl font-bold text-on-surface">${{ $kpis['totalRevenue'] }}</p>
                </div>
            </div>
        </div>

        {{-- Orders --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary">Total Orders</p>
                    <p class="text-2xl font-bold text-on-surface">{{ number_format($kpis['totalOrders']) }}</p>
                </div>
            </div>
        </div>

        {{-- Avg Order --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary">Avg. Order Value</p>
                    <p class="text-2xl font-bold text-on-surface">${{ $kpis['avgOrderValue'] }}</p>
                </div>
            </div>
        </div>

        {{-- Customers --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-5">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary">Unique Customers</p>
                    <p class="text-2xl font-bold text-on-surface">{{ number_format($kpis['totalCustomers']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Chart --}}
    <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6">
        <h3 class="text-sm font-bold text-on-surface mb-4">Revenue Trend</h3>
        @if(count($revenueChart['labels']) > 0)
            <div class="relative" style="height: 220px;">
                <canvas id="revenueChart" class="w-full h-full"></canvas>
            </div>
        @else
            <p class="text-sm text-secondary text-center py-8">No revenue data for this period.</p>
        @endif
    </div>

    {{-- Two-column: Top Items + Order Breakdowns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Selling Items --}}
        <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest">
            <div class="px-6 py-4 border-b border-surface-container-high">
                <h3 class="text-sm font-bold text-on-surface">Top Selling Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container border-b border-surface-container-high">
                        <tr>
                            <th class="px-4 py-2.5 font-semibold text-secondary text-xs">#</th>
                            <th class="px-4 py-2.5 font-semibold text-secondary text-xs">Item</th>
                            <th class="px-4 py-2.5 font-semibold text-secondary text-xs text-right">Qty</th>
                            <th class="px-4 py-2.5 font-semibold text-secondary text-xs text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-high">
                        @forelse($topItems as $i => $item)
                            <tr class="hover:bg-surface-container/50">
                                <td class="px-4 py-3 text-secondary text-xs">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-on-surface">{{ $item['menu_item_name'] }}</td>
                                <td class="px-4 py-3 text-right text-on-surface">{{ number_format($item['total_qty']) }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-on-surface">${{ number_format($item['total_revenue'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-secondary text-sm">No items sold in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Order Breakdowns --}}
        <div class="space-y-4">
            {{-- By Type --}}
            <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-5">
                <h3 class="text-sm font-bold text-on-surface mb-3">Orders by Type</h3>
                @php $totalByType = array_sum($ordersByType); @endphp
                @if($totalByType > 0)
                    <div class="space-y-2">
                        @foreach($ordersByType as $type => $count)
                            @php
                                $pct = round($count / $totalByType * 100, 1);
                                $color = match($type) {
                                    'dine_in' => 'bg-primary',
                                    'takeaway' => 'bg-warning',
                                    'delivery' => 'bg-success',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-medium text-on-surface capitalize">{{ str_replace('_', ' ', $type) }}</span>
                                    <span class="text-secondary">{{ $count }} ({{ $pct }}%)</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-surface-container">
                                    <div class="h-2 rounded-full {{ $color }}" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-secondary">No orders in this period.</p>
                @endif
            </div>

            {{-- By Status --}}
            <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-5">
                <h3 class="text-sm font-bold text-on-surface mb-3">Orders by Status</h3>
                @php $totalByStatus = array_sum($ordersByStatus); @endphp
                @if($totalByStatus > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($ordersByStatus as $status => $count)
                            @php
                                $badge = match($status) {
                                    'completed' => 'bg-success/10 text-success border-success/20',
                                    'cancelled' => 'bg-error/10 text-error border-error/20',
                                    'preparing' => 'bg-warning/10 text-warning border-warning/20',
                                    'ready' => 'bg-primary/10 text-primary border-primary/20',
                                    default => 'bg-surface-container text-secondary border-surface-container-high',
                                };
                            @endphp
                            <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $badge }}">
                                {{ ucfirst($status) }}: {{ $count }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-secondary">No orders in this period.</p>
                @endif
            </div>

            {{-- Payment Methods --}}
            <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-5">
                <h3 class="text-sm font-bold text-on-surface mb-3">Payment Methods</h3>
                @if(count($paymentMethods) > 0)
                    <div class="space-y-2">
                        @foreach($paymentMethods as $pm)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-on-surface capitalize">{{ str_replace('_', ' ', $pm['payment_method'] ?? 'unknown') }}</span>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-on-surface">${{ number_format($pm['revenue'], 2) }}</span>
                                    <span class="text-xs text-secondary ml-1">({{ $pm['count'] }} orders)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-secondary">No paid orders in this period.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Hourly Distribution --}}
    <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest p-6">
        <h3 class="text-sm font-bold text-on-surface mb-4">Hourly Order Distribution</h3>
        @if(count($hourlyDistribution) > 0)
            @php $maxHourly = max($hourlyDistribution); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1">
                @for($h = 0; $h < 24; $h++)
                    @php
                        $count = $hourlyDistribution[$h] ?? 0;
                        $pct = $maxHourly > 0 ? round($count / $maxHourly * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="w-12 text-right text-xs font-mono text-secondary">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00</span>
                        <div class="flex-1 h-4 rounded bg-surface-container overflow-hidden">
                            <div class="h-full rounded bg-primary/70 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="w-8 text-xs text-secondary text-right">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        @else
            <p class="text-sm text-secondary text-center py-4">No order data available.</p>
        @endif
    </div>

    {{-- Recent Orders --}}
    <div class="rounded-xl border border-surface-container-high bg-surface-container-lowest">
        <div class="px-6 py-4 border-b border-surface-container-high">
            <h3 class="text-sm font-bold text-on-surface">Recent Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container border-b border-surface-container-high">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-secondary">Order #</th>
                        <th class="px-6 py-3 font-semibold text-secondary">Customer</th>
                        <th class="px-6 py-3 font-semibold text-secondary">Type</th>
                        <th class="px-6 py-3 font-semibold text-secondary text-right">Total</th>
                        <th class="px-6 py-3 font-semibold text-secondary">Status</th>
                        <th class="px-6 py-3 font-semibold text-secondary">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-high">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-surface-container/50">
                            <td class="px-6 py-3 font-mono text-sm text-on-surface">{{ $order->order_number }}</td>
                            <td class="px-6 py-3 text-on-surface">{{ $order->customer?->name ?? $order->customer_name ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="rounded bg-surface-container px-2 py-0.5 text-xs font-medium text-secondary capitalize">{{ str_replace('_', ' ', $order->order_type) }}</span>
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-on-surface">${{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $statusBadge = match($order->status) {
                                        'completed' => 'bg-success/10 text-success border-success/20',
                                        'cancelled' => 'bg-error/10 text-error border-error/20',
                                        'preparing' => 'bg-warning/10 text-warning border-warning/20',
                                        'ready' => 'bg-primary/10 text-primary border-primary/20',
                                        default => 'bg-surface-container text-secondary border-surface-container-high',
                                    };
                                @endphp
                                <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $statusBadge }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-secondary text-sm">{{ $order->created_at->format('M d, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-secondary">No orders found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:navigated', initRevenueChart);
document.addEventListener('DOMContentLoaded', initRevenueChart);

function initRevenueChart() {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const labels = @json($revenueChart['labels']);
    const data = @json($revenueChart['data']);

    if (!labels.length) return;

    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.parentElement.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const w = rect.width;
    const h = rect.height;
    const padding = { top: 10, right: 10, bottom: 30, left: 60 };
    const chartW = w - padding.left - padding.right;
    const chartH = h - padding.top - padding.bottom;

    const maxVal = Math.max(...data, 1);
    const barWidth = Math.max(4, (chartW / labels.length) - 4);

    ctx.clearRect(0, 0, w, h);

    const gridLines = 4;
    ctx.strokeStyle = getComputedStyle(document.body).getPropertyValue('--md-sys-color-outline-variant') || '#e0e0e0';
    ctx.lineWidth = 0.5;
    ctx.font = '10px system-ui';
    ctx.fillStyle = getComputedStyle(document.body).getPropertyValue('--md-sys-color-secondary') || '#888';
    ctx.textAlign = 'right';
    for (let i = 0; i <= gridLines; i++) {
        const y = padding.top + chartH - (chartH / gridLines) * i;
        const val = (maxVal / gridLines) * i;
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(w - padding.right, y);
        ctx.stroke();
        ctx.fillText('$' + val.toFixed(0), padding.left - 5, y + 3);
    }

    const primaryColor = getComputedStyle(document.body).getPropertyValue('--md-sys-color-primary') || '#6750A4';
    data.forEach((val, i) => {
        const x = padding.left + (chartW / labels.length) * i + (chartW / labels.length - barWidth) / 2;
        const barH = (val / maxVal) * chartH;
        const y = padding.top + chartH - barH;

        ctx.fillStyle = primaryColor;
        ctx.beginPath();
        const radius = Math.min(3, barWidth / 2);
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + barWidth - radius, y);
        ctx.quadraticCurveTo(x + barWidth, y, x + barWidth, y + radius);
        ctx.lineTo(x + barWidth, padding.top + chartH);
        ctx.lineTo(x, padding.top + chartH);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.fill();
    });

    ctx.fillStyle = getComputedStyle(document.body).getPropertyValue('--md-sys-color-secondary') || '#888';
    ctx.textAlign = 'center';
    ctx.font = '9px system-ui';
    const step = Math.max(1, Math.floor(labels.length / 12));
    labels.forEach((label, i) => {
        if (i % step === 0) {
            const x = padding.left + (chartW / labels.length) * i + (chartW / labels.length) / 2;
            ctx.fillText(label, x, h - 5);
        }
    });
}
</script>
@endpush
