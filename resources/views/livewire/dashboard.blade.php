<div class="space-y-8">
    @php $user = auth('tenant')->user(); @endphp

    {{-- ==================== ADMIN / OWNER / MANAGER ==================== --}}
    @if ($user->hasAnyRole(['owner', 'admin', 'manager']))
        {{-- Page Header --}}
        <div class="section-header">
            <div>
                <h1 class="section-title">Dashboard</h1>
                <p class="section-description">Welcome back, {{ $user->name }} · {{ now()->format('l, F j') }}</p>
            </div>
            <div class="section-actions">
                <div class="items-center hidden gap-2 px-3 py-2 text-sm rounded-lg md:flex text-surface-500 bg-surface-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>▲ 12% vs last week</span>
                </div>
            </div>
        </div>

        {{-- Executive KPI Cards --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="kpi-card card-accent-primary animate-fade-in">
                <div class="kpi-header">
                    <span class="kpi-label">Today's Revenue</span>
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="kpi-value">${{ number_format($todayRevenue, 2) }}</p>
                @if(isset($revenueTrend))
                    <div class="kpi-trend {{ $revenueTrend >= 0 ? 'kpi-trend-up' : 'kpi-trend-down' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $revenueTrend >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/></svg>
                        <span>{{ abs($revenueTrend) }}% vs yesterday</span>
                    </div>
                @endif
            </div>

            <div class="kpi-card card-accent-primary">
                <div class="kpi-header">
                    <span class="kpi-label">Orders Today</span>
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <p class="kpi-value">{{ $ordersToday }}</p>
                @if(isset($ordersTrend))
                    <div class="kpi-trend {{ $ordersTrend >= 0 ? 'kpi-trend-up' : 'kpi-trend-down' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ordersTrend >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/></svg>
                        <span>{{ abs($ordersTrend) }}% vs yesterday</span>
                    </div>
                @endif
            </div>

            <div class="kpi-card card-accent-success">
                <div class="kpi-header">
                    <span class="kpi-label">Avg Order Value</span>
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <p class="kpi-value">${{ $ordersToday > 0 ? number_format($todayRevenue / $ordersToday, 2) : '0.00' }}</p>
            </div>

            <div class="kpi-card {{ $occupancyRate > 80 ? 'card-accent-warning' : 'card-accent-success' }}">
                <div class="kpi-header">
                    <span class="kpi-label">Occupancy</span>
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="kpi-value">{{ $occupancyRate ?? 0 }}%</p>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Revenue Trend Chart --}}
            <div class="card lg:col-span-2">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Revenue Trend</h3>
                        <p class="text-xs text-surface-500 mt-0.5">Last 7 days compared to previous period</p>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-surface-500">
                        <span class="w-3 h-0.5 rounded bg-primary"></span>
                        <span>This week</span>
                        <span class="w-3 h-0.5 rounded bg-surface-300 ml-2"></span>
                        <span>Last week</span>
                    </div>
                </div>
                <div class="flex items-end h-40 gap-2">
                    @php $maxRevenue = max(max(array_column($weeklyRevenue ?? [], 'amount')), 1); @endphp
                    @foreach ($weeklyRevenue ?? [] as $day)
                        <div class="flex flex-col items-center justify-end flex-1 h-full">
                            <span class="text-[10px] font-medium text-surface-500 mb-1.5">${{ number_format($day['amount'] / 1000, 1) }}k</span>
                            <div class="w-full transition-all duration-500 ease-out bg-primary/80 rounded-t-md hover:bg-primary"
                                 style="height: {{ ($day['amount'] / $maxRevenue) * 100 }}%"></div>
                            <span class="text-[10px] text-surface-500 mt-1.5 font-medium">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Peak Hours/Status --}}
            <div class="card">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Orders by Status</h3>
                        <p class="text-xs text-surface-500 mt-0.5">Current distribution</p>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    @forelse (['pending' => 'Pending', 'confirmed' => 'Confirmed', 'preparing' => 'Preparing', 'ready' => 'Ready', 'served' => 'Served', 'completed' => 'Completed'] as $status => $label)
                        @php $count = $ordersByStatus[$status] ?? 0; @endphp
                        @if ($count > 0)
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="status-dot {{ match($status) { 'pending' => 'bg-amber-500', 'confirmed' => 'bg-blue-500', 'preparing' => 'bg-orange-500', 'ready' => 'bg-green-500', 'served' => 'bg-purple-500', default => 'bg-gray-400' } }}"></span>
                                        <span class="text-sm text-surface-600">{{ $label }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-surface-900">{{ $count }}</span>
                                </div>
                                <div class="progress-track">
                                    @php $total = max(array_sum($ordersByStatus ?? []), 1); @endphp
                                    <div class="progress-bar {{ match($status) { 'pending' => 'bg-amber-500', 'confirmed' => 'bg-blue-500', 'preparing' => 'bg-orange-500', 'ready' => 'bg-green-500', 'served' => 'bg-purple-500', default => 'bg-gray-400' } }}"
                                         style="width: {{ ($count / $total) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="py-4 text-sm text-center text-surface-500">No orders today</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Data Row --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Top Selling Items --}}
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Top Selling Items</h3>
                        <p class="text-xs text-surface-500 mt-0.5">Most ordered today</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse ($topItems ?? [] as $index => $item)
                        <div class="flex items-center gap-4 p-2.5 rounded-lg hover:bg-surface-100 transition-colors">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ $index === 0 ? 'bg-primary-100 text-primary-700' : ($index === 1 ? 'bg-surface-100 text-surface-600' : ($index === 2 ? 'bg-amber-50 text-amber-700' : 'bg-surface-100 text-surface-500')) }} text-xs font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate text-surface-800">{{ $item->menu_item_name ?? $item['name'] ?? 'Item' }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-primary">{{ $item->total_qty ?? $item['quantity'] ?? 0 }}×</span>
                                <div class="w-16 progress-track">
                                    @php $maxQty = max($topItems[0]->total_qty ?? $topItems[0]['quantity'] ?? 1, 1); @endphp
                                    <div class="progress-bar progress-bar-primary" style="width: {{ (($item->total_qty ?? $item['quantity'] ?? 0) / $maxQty) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-center text-surface-500">No items sold today</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Recent Orders</h3>
                        <p class="text-xs text-surface-500 mt-0.5">Latest transactions</p>
                    </div>
                    <a href="{{ route($user->hasAnyRole(['owner', 'admin']) ? 'tenant.manager.orders' : 'tenant.manager.orders') }}" class="text-sm font-medium transition-colors text-primary hover:text-primary-700">View all</a>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead class="table-head">
                            <tr>
                                <th class="table-th">Order</th>
                                <th class="table-th">Table</th>
                                <th class="table-th">Amount</th>
                                <th class="table-th">Status</th>
                                <th class="table-th">Time</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            @forelse ($recentOrders ?? [] as $order)
                                <tr class="table-tr">
                                    <td class="font-mono text-xs table-td text-surface-500">#{{ $order->order_number }}</td>
                                    <td class="table-td">{{ $order->table?->table_number ?? '—' }}</td>
                                    <td class="font-semibold table-td text-surface-900">${{ number_format($order->total, 2) }}</td>
                                    <td class="table-td">
                                        <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td class="table-td text-surface-500">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center table-td text-surface-400">No orders yet today</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Alerts Row --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Low Stock Alerts --}}
            <div class="card card-accent-error">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center rounded-lg w-9 h-9 bg-error/10">
                        <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Low Stock Alerts</h3>
                        <p class="text-xs text-surface-500 mt-0.5">Items below reorder point</p>
                    </div>
                </div>
                @forelse ($lowStockItems ?? [] as $item)
                    <div class="flex items-center justify-between py-2.5 border-b border-surface-100 last:border-0">
                        <span class="text-sm font-medium text-surface-800">{{ $item->name }}</span>
                        <div class="text-right">
                            <span class="text-sm font-semibold text-error">{{ $item->stock_quantity }} {{ $item->unit ?? '' }}</span>
                            <span class="ml-1 text-xs text-surface-400">/ reorder at {{ $item->reorder_point }}</span>
                        </div>
                    </div>
                @empty
                    <div class="flex items-center gap-2 py-4 text-sm text-surface-500">
                        <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        All items well stocked
                    </div>
                @endforelse
            </div>

            {{-- Today's Reservations --}}
            <div class="card card-accent-primary">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center rounded-lg w-9 h-9 bg-primary-100">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Today's Reservations</h3>
                        <p class="text-xs text-surface-500 mt-0.5">{{ count($todayReservations ?? []) }} upcoming bookings</p>
                    </div>
                </div>
                @forelse ($todayReservations ?? [] as $res)
                    <div class="flex items-center justify-between py-2.5 border-b border-surface-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-8 h-8 text-xs font-bold rounded-full bg-primary-100 text-primary-700">
                                {{ substr($res->customer_name ?? 'G', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-surface-800">{{ $res->customer_name ?? 'Guest' }}</p>
                                <p class="text-xs text-surface-500">{{ $res->guest_count ?? 0 }} guests</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-surface-900">{{ $res->reservation_time?->format('g:i A') ?? '—' }}</p>
                            @if ($res->table ?? false)
                                <p class="text-xs text-surface-500">Table {{ $res->table->table_number }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex items-center gap-2 py-4 text-sm text-surface-500">
                        <svg class="w-5 h-5 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        No reservations today
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ==================== CHEF ==================== --}}
    @if ($user->hasRole('chef'))
        <div class="section-header">
            <div>
                <h1 class="section-title">Kitchen Dashboard</h1>
                <p class="section-description">{{ now()->format('g:i A') }} · Stay sharp back there!</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="kpi-card card-accent-warning">
                <div class="kpi-header">
                    <span class="kpi-label">Pending</span>
                </div>
                <p class="kpi-value text-warning">{{ $pendingOrders ?? 0 }}</p>
            </div>
            <div class="kpi-card card-accent-primary">
                <div class="kpi-header">
                    <span class="kpi-label">Preparing</span>
                </div>
                <p class="kpi-value text-primary">{{ $preparingOrders ?? 0 }}</p>
            </div>
            <div class="kpi-card card-accent-success">
                <div class="kpi-header">
                    <span class="kpi-label">Ready</span>
                </div>
                <p class="kpi-value text-success">{{ $readyOrders ?? 0 }}</p>
            </div>
            <div class="kpi-card card-accent-error">
                <div class="kpi-header">
                    <span class="kpi-label">Low Stock Items</span>
                </div>
                <p class="kpi-value text-error">{{ $lowStockItems?->count() ?? 0 }}</p>
            </div>
        </div>

        {{-- Active Orders Grid --}}
        <div class="card">
            <h3 class="mb-4 text-base font-semibold text-surface-900">Active Orders</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($activeOrders ?? [] as $order)
                    <div class="kds-card {{ $order->elapsed_minutes > 10 ? 'kds-card-urgent' : ($order->elapsed_minutes > 5 ? 'kds-card-warning' : '') }}">
                        <div class="kds-card-header">
                            <div class="kds-order-info">
                                <span class="kds-order-number">#{{ $order->order_number }}</span>
                                @if($order->table)
                                    <span class="kds-table-badge">T{{ $order->table->table_number }}</span>
                                @endif
                            </div>
                            <div class="kds-timer {{ $order->elapsed_minutes > 10 ? 'text-error' : ($order->elapsed_minutes > 5 ? 'text-warning' : 'text-success') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $order->elapsed_minutes }}m</span>
                            </div>
                        </div>
                        <p class="mb-3 text-xs text-surface-500">{{ $order->items->count() }} items</p>
                        <div class="kds-card-items">
                            @foreach($order->items as $item)
                                <div class="kds-item">
                                    <span class="kds-item-qty">{{ $item->quantity }}×</span>
                                    <span class="kds-item-name">{{ $item->menu_item_name }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if ($order->elapsed_minutes > 10)
                            <div class="mt-2 flex items-center gap-1.5 text-xs font-bold text-error">
                                <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                                URGENT
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center py-12 col-span-full text-surface-400">
                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm">No active orders — kitchen is clear</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if ($lowStockItems?->isNotEmpty())
            <div class="card card-accent-error">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center rounded-lg w-9 h-9 bg-error/10">
                        <svg class="w-5 h-5 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-surface-900">Low Stock Alerts</h3>
                    </div>
                </div>
                @foreach ($lowStockItems as $item)
                    <div class="flex items-center justify-between py-2.5 border-b border-surface-100 last:border-0">
                        <span class="text-sm font-medium text-surface-800">{{ $item->name }}</span>
                        <span class="text-sm font-semibold text-error">{{ $item->stock_quantity }} {{ $item->unit ?? '' }} (reorder at {{ $item->reorder_point }})</span>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- ==================== WAITER ==================== --}}
    @if ($user->hasRole('waiter'))
        <div class="section-header">
            <div>
                <h1 class="section-title">My Dashboard</h1>
                <p class="section-description">Welcome back, {{ $user->name }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="kpi-card card-accent-primary">
                <div class="kpi-header">
                    <span class="kpi-label">My Active Tables</span>
                </div>
                <p class="kpi-value">{{ $myActiveTables ?? 0 }}</p>
            </div>
            <div class="kpi-card card-accent-primary">
                <div class="kpi-header">
                    <span class="kpi-label">Orders Today</span>
                </div>
                <p class="kpi-value">{{ $myOrdersToday ?? 0 }}</p>
            </div>
            <div class="kpi-card card-accent-warning">
                <div class="kpi-header">
                    <span class="kpi-label">Pending Orders</span>
                </div>
                <p class="kpi-value text-warning">{{ $pendingOrders ?? 0 }}</p>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-surface-900">My Active Orders</h3>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead class="table-head">
                        <tr>
                            <th class="table-th">Order</th>
                            <th class="table-th">Table</th>
                            <th class="table-th">Amount</th>
                            <th class="table-th">Status</th>
                            <th class="table-th">Elapsed</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @forelse ($activeOrders ?? [] as $order)
                            <tr class="table-tr">
                                <td class="font-mono text-xs table-td text-surface-500">#{{ $order->order_number }}</td>
                                <td class="table-td">{{ $order->table?->table_number ?? '—' }}</td>
                                <td class="font-semibold table-td">${{ number_format($order->total, 2) }}</td>
                                <td class="table-td">
                                    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="table-td">
                                    <span class="text-sm font-medium {{ $order->elapsed_minutes > 10 ? 'text-error' : ($order->elapsed_minutes > 5 ? 'text-warning' : 'text-surface-500') }}">
                                        {{ $order->elapsed_minutes }} min
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center table-td text-surface-400">No active orders</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ==================== CASHIER ==================== --}}
    @if ($user->hasRole('cashier'))
        <div class="section-header">
            <div>
                <h1 class="section-title">Cashier Dashboard</h1>
                <p class="section-description">Welcome back, {{ $user->name }} · {{ now()->format('l, F j') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="kpi-card card-accent-primary">
                <div class="kpi-header">
                    <span class="kpi-label">Today's Sales</span>
                </div>
                <p class="kpi-value text-primary">${{ number_format($todaySales ?? 0, 2) }}</p>
            </div>
            <div class="kpi-card card-accent-primary">
                <div class="kpi-header">
                    <span class="kpi-label">Orders Today</span>
                </div>
                <p class="kpi-value">{{ $ordersToday ?? 0 }}</p>
            </div>
            <div class="kpi-card {{ $activeShift ? 'card-accent-success' : 'card-accent-warning' }}">
                <div class="kpi-header">
                    <span class="kpi-label">Shift</span>
                </div>
                @if ($activeShift)
                    <p class="text-sm kpi-value">{{ $activeShift->name }}</p>
                    <p class="mt-1 text-xs text-surface-500">Opened {{ $activeShift->opened_at?->diffForHumans() }}</p>
                @else
                    <p class="text-sm kpi-value text-surface-400">No open shift</p>
                @endif
            </div>
            <div class="kpi-card card-accent-primary">
                <div class="kpi-header">
                    <span class="kpi-label">Avg per Order</span>
                </div>
                <p class="kpi-value">${{ ($ordersToday ?? 0) > 0 ? number_format(($todaySales ?? 0) / $ordersToday, 2) : '0.00' }}</p>
            </div>
        </div>

        @if ($activeShift)
            <div class="card">
                <h3 class="mb-4 text-base font-semibold text-surface-900">Shift Summary — {{ $activeShift->name }}</h3>
                <div class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                    <div>
                        <p class="text-xs font-medium tracking-wider uppercase text-surface-500">Expected Cash</p>
                        <p class="mt-1 text-xl font-bold text-surface-900">${{ number_format($activeShift->expected_cash ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium tracking-wider uppercase text-surface-500">Card Total</p>
                        <p class="mt-1 text-xl font-bold text-surface-900">${{ number_format($activeShift->card_total ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium tracking-wider uppercase text-surface-500">Other Payments</p>
                        <p class="mt-1 text-xl font-bold text-surface-900">${{ number_format($activeShift->other_total ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium tracking-wider uppercase text-surface-500">Difference</p>
                        <p class="text-xl font-bold mt-1 {{ ($activeShift->difference ?? 0) != 0 ? 'text-error' : 'text-success' }}">
                            {{ ($activeShift->difference ?? 0) >= 0 ? '+' : '' }}${{ number_format($activeShift->difference ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="card">
                <h3 class="mb-4 text-base font-semibold text-surface-900">Recent Transactions</h3>
                <div class="table-wrapper">
                    <table class="table">
                        <thead class="table-head">
                            <tr>
                                <th class="table-th">Order</th>
                                <th class="table-th">Table</th>
                                <th class="table-th">Amount</th>
                                <th class="table-th">Method</th>
                                <th class="table-th">Time</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            @forelse ($recentTransactions ?? [] as $order)
                                <tr class="table-tr">
                                    <td class="font-mono text-xs table-td">#{{ $order->order_number }}</td>
                                    <td class="table-td">{{ $order->table?->table_number ?? '—' }}</td>
                                    <td class="font-semibold table-td">${{ number_format($order->total, 2) }}</td>
                                    <td class="capitalize table-td">{{ $order->payment_method ?? '—' }}</td>
                                    <td class="table-td text-surface-500">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center table-td text-surface-400">No transactions today</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3 class="mb-4 text-base font-semibold text-surface-900">Payment Methods</h3>
                <div class="flex flex-col gap-4">
                    @forelse ($paymentMethodBreakdown ?? [] as $pm)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-surface-100/50">
                            <span class="text-sm font-medium capitalize text-surface-700">{{ $pm->method ?? 'Unknown' }}</span>
                            <div class="text-right">
                                <span class="text-sm font-bold text-surface-900">${{ number_format($pm->total_amount ?? 0, 2) }}</span>
                                <span class="ml-2 text-xs text-surface-400">({{ $pm->total ?? 0 }} txns)</span>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-center text-surface-500">No payments today</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== CUSTOMER ==================== --}}
    @if ($user->hasRole('customer'))
        <div class="section-header">
            <div>
                <h1 class="section-title">My Dashboard</h1>
                <p class="section-description">Welcome back, {{ $user->name }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="kpi-card">
                <span class="kpi-label">My Orders</span>
                <p class="kpi-value">{{ $myOrdersCount ?? 0 }}</p>
            </div>
            <div class="kpi-card">
                <span class="kpi-label">Loyalty Points</span>
                <p class="kpi-value text-primary">{{ $loyaltyPoints ?? 0 }}</p>
            </div>
            <div class="kpi-card">
                <span class="kpi-label">Upcoming Reservations</span>
                <p class="kpi-value">{{ $upcomingReservations?->count() ?? 0 }}</p>
            </div>
            <div class="kpi-card">
                <span class="kpi-label">Orders Today</span>
                <p class="kpi-value">{{ $ordersToday ?? 0 }}</p>
            </div>
        </div>

        <div class="card">
            <h3 class="mb-4 text-base font-semibold text-surface-900">My Recent Orders</h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead class="table-head">
                        <tr>
                            <th class="table-th">Order</th>
                            <th class="table-th">Items</th>
                            <th class="table-th">Total</th>
                            <th class="table-th">Status</th>
                            <th class="table-th">Date</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @forelse ($recentOrders ?? [] as $order)
                            <tr class="table-tr">
                                <td class="font-mono text-xs table-td">#{{ $order->order_number }}</td>
                                <td class="table-td">{{ $order->items->count() }}</td>
                                <td class="font-semibold table-td">${{ number_format($order->total, 2) }}</td>
                                <td class="table-td">
                                    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="table-td text-surface-500">{{ $order->created_at->format('M d, g:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center table-td text-surface-400">No orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($upcomingReservations?->isNotEmpty())
            <div class="card">
                <h3 class="mb-4 text-base font-semibold text-surface-900">Upcoming Reservations</h3>
                <div class="space-y-3">
                    @foreach ($upcomingReservations as $res)
                        <div class="flex items-center justify-between py-3 border-b border-surface-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center rounded-lg w-9 h-9 bg-primary-100">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-surface-800">{{ $res->reservation_date->format('D, M d') }} · {{ $res->reservation_time?->format('g:i A') }}</p>
                                    <p class="text-xs text-surface-500">{{ $res->guest_count }} guests</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
