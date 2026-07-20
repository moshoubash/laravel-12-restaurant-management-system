<div class="space-y-6">
    @php $user = auth('tenant')->user(); @endphp

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-secondary mt-1">Welcome back, {{ $user->name }}</p>
    </div>

    {{-- ==================== ADMIN / OWNER / MANAGER ==================== --}}
    @if ($user->hasAnyRole(['owner', 'admin', 'manager']))
        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card">
                <p class="text-sm text-secondary font-medium">Today's Revenue</p>
                <p class="text-2xl font-bold text-primary mt-1">${{ number_format($todayRevenue, 2) }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Orders Today</p>
                <p class="text-2xl font-bold mt-1">{{ $ordersToday }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Active Tables</p>
                <p class="text-2xl font-bold mt-1">{{ $activeTables }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Staff on Duty</p>
                <p class="text-2xl font-bold mt-1">{{ $staffOnDuty }}</p>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Revenue Trend --}}
            <div class="card lg:col-span-2">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Revenue Trend (7 days)</h3>
                <div class="flex items-end gap-2 h-32">
                    @foreach ($weeklyRevenue as $day)
                        <div class="flex flex-col items-center flex-1 h-full justify-end">
                            <span class="text-xs font-medium text-on-surface mb-1">${{ number_format($day['amount']) }}</span>
                            <div class="w-full bg-primary rounded-t transition-all"
                                 style="height: {{ ($day['amount'] / $maxRevenue) * 100 }}%"></div>
                            <span class="text-xs text-secondary mt-1">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Orders by Status --}}
            <div class="card">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Orders by Status</h3>
                <div class="space-y-3">
                    @forelse (['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed'] as $status)
                        @php $count = $ordersByStatus[$status] ?? 0; @endphp
                        @if ($count > 0)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full"
                                          style="background: {{ match($status) { 'pending' => '#f59e0b', 'confirmed' => '#3b82f6', 'preparing' => '#f97316', 'ready' => '#22c55e', 'served' => '#8b5cf6', default => '#6b7280' } }}"></span>
                                    <span class="text-sm capitalize">{{ $status }}</span>
                                </div>
                                <span class="text-sm font-semibold">{{ $count }}</span>
                            </div>
                        @endif
                    @empty
                        <p class="text-sm text-secondary">No orders today</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tables Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Recent Orders --}}
            <div class="card">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Recent Orders</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-secondary border-b border-surface-container-high">
                                <th class="pb-2 font-medium">#</th>
                                <th class="pb-2 font-medium">Table</th>
                                <th class="pb-2 font-medium">Amount</th>
                                <th class="pb-2 font-medium">Status</th>
                                <th class="pb-2 font-medium">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr class="border-b border-surface-container-high">
                                    <td class="py-2 font-mono text-xs">{{ $order->order_number }}</td>
                                    <td class="py-2">{{ $order->table?->table_number ?? '—' }}</td>
                                    <td class="py-2 font-medium">${{ number_format($order->total, 2) }}</td>
                                    <td class="py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                              style="background: {{ match($order->status) { 'pending' => '#fef3c7', 'confirmed' => '#dbeafe', 'preparing' => '#ffedd5', 'ready' => '#dcfce7', 'served' => '#ede9fe', 'completed' => '#f3f4f6', default => '#f3f4f6' } }};
                                              color: {{ match($order->status) { 'pending' => '#92400e', 'confirmed' => '#1e40af', 'preparing' => '#9a3412', 'ready' => '#166534', 'served' => '#5b21b6', 'completed' => '#374151', default => '#374151' } }};">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-secondary">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 text-center text-secondary">No orders yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Top Selling Items --}}
            <div class="card">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Top Selling Items Today</h3>
                <div class="space-y-3">
                    @forelse ($topItems as $index => $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-primary-container text-on-primary-container text-xs font-bold flex items-center justify-center">{{ $index + 1 }}</span>
                                <span class="text-sm">{{ $item->menu_item_name }}</span>
                            </div>
                            <span class="text-sm font-semibold text-primary">{{ $item->total_qty }} ordered</span>
                        </div>
                    @empty
                        <p class="text-sm text-secondary">No items sold today</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Alerts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Low Stock --}}
            <div class="card border-l-4 border-l-error">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Low Stock Alerts</h3>
                @forelse ($lowStockItems as $item)
                    <div class="flex items-center justify-between py-2 border-b border-surface-container-high last:border-0">
                        <span class="text-sm">{{ $item->name }}</span>
                        <span class="text-sm font-medium text-error">{{ $item->stock_quantity }} {{ $item->unit }} (reorder at {{ $item->reorder_point }})</span>
                    </div>
                @empty
                    <p class="text-sm text-secondary">All items well stocked</p>
                @endforelse
            </div>

            {{-- Today's Reservations --}}
            <div class="card border-l-4 border-l-primary">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Today's Reservations</h3>
                @forelse ($todayReservations as $res)
                    <div class="flex items-center justify-between py-2 border-b border-surface-container-high last:border-0">
                        <div>
                            <span class="text-sm font-medium">{{ $res->customer_name }}</span>
                            <span class="text-sm text-secondary ml-2">{{ $res->guest_count }} guests</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm">{{ $res->reservation_time?->format('g:i A') }}</span>
                            @if ($res->table)
                                <span class="text-sm text-secondary ml-2">Table {{ $res->table->table_number }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-secondary">No reservations today</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ==================== CHEF ==================== --}}
    @if ($user->hasRole('chef'))
        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card border-l-4 border-l-yellow-500">
                <p class="text-sm text-secondary font-medium">Pending</p>
                <p class="text-2xl font-bold mt-1">{{ $pendingOrders }}</p>
            </div>
            <div class="card border-l-4 border-l-orange-500">
                <p class="text-sm text-secondary font-medium">Preparing</p>
                <p class="text-2xl font-bold mt-1">{{ $preparingOrders }}</p>
            </div>
            <div class="card border-l-4 border-l-green-500">
                <p class="text-sm text-secondary font-medium">Ready</p>
                <p class="text-2xl font-bold mt-1">{{ $readyOrders }}</p>
            </div>
            <div class="card border-l-4 border-l-red-500">
                <p class="text-sm text-secondary font-medium">Low Stock Items</p>
                <p class="text-2xl font-bold mt-1">{{ $lowStockItems->count() }}</p>
            </div>
        </div>

        {{-- Active Orders --}}
        <div class="card">
            <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Active Orders</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($activeOrders as $order)
                    <div class="rounded-lg border p-4 {{ $order->elapsed_minutes > 10 ? 'border-red-300 bg-red-50' : ($order->elapsed_minutes > 5 ? 'border-yellow-300 bg-yellow-50' : 'border-surface-container-high') }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-sm">{{ $order->order_number }}</span>
                            <span class="text-xs font-medium px-2 py-0.5 rounded"
                                  style="background: {{ match($order->status) { 'pending' => '#fef3c7', 'confirmed' => '#dbeafe', 'preparing' => '#ffedd5', default => '#f3f4f6' } }};">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-secondary">
                            Table {{ $order->table?->table_number ?? '—' }} &middot; {{ $order->items->count() }} items
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs font-medium {{ $order->elapsed_minutes > 10 ? 'text-red-600' : ($order->elapsed_minutes > 5 ? 'text-yellow-600' : 'text-green-600') }}">
                                {{ $order->elapsed_minutes }} min ago
                            </span>
                            @if ($order->elapsed_minutes > 10)
                                <span class="text-xs font-bold text-red-600 animate-pulse">URGENT</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-secondary col-span-3">No active orders</p>
                @endforelse
            </div>
        </div>

        {{-- Low Stock --}}
        @if ($lowStockItems->isNotEmpty())
            <div class="card border-l-4 border-l-error">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Low Stock Alerts</h3>
                @foreach ($lowStockItems as $item)
                    <div class="flex items-center justify-between py-2 border-b border-surface-container-high last:border-0">
                        <span class="text-sm">{{ $item->name }}</span>
                        <span class="text-sm font-medium text-error">{{ $item->stock_quantity }} {{ $item->unit }} (reorder at {{ $item->reorder_point }})</span>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- ==================== WAITER ==================== --}}
    @if ($user->hasRole('waiter'))
        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="card">
                <p class="text-sm text-secondary font-medium">My Active Tables</p>
                <p class="text-2xl font-bold mt-1">{{ $myActiveTables }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">My Orders Today</p>
                <p class="text-2xl font-bold mt-1">{{ $myOrdersToday }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Pending Orders</p>
                <p class="text-2xl font-bold mt-1">{{ $pendingOrders }}</p>
            </div>
        </div>

        {{-- My Active Orders --}}
        <div class="card">
            <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">My Active Orders</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-secondary border-b border-surface-container-high">
                            <th class="pb-2 font-medium">Order</th>
                            <th class="pb-2 font-medium">Table</th>
                            <th class="pb-2 font-medium">Amount</th>
                            <th class="pb-2 font-medium">Status</th>
                            <th class="pb-2 font-medium">Elapsed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeOrders as $order)
                            <tr class="border-b border-surface-container-high">
                                <td class="py-2 font-mono text-xs">{{ $order->order_number }}</td>
                                <td class="py-2">{{ $order->table?->table_number ?? '—' }}</td>
                                <td class="py-2 font-medium">${{ number_format($order->total, 2) }}</td>
                                <td class="py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                          style="background: {{ match($order->status) { 'pending' => '#fef3c7', 'confirmed' => '#dbeafe', 'preparing' => '#ffedd5', 'ready' => '#dcfce7', default => '#f3f4f6' } }};
                                          color: {{ match($order->status) { 'pending' => '#92400e', 'confirmed' => '#1e40af', 'preparing' => '#9a3412', 'ready' => '#166534', default => '#374151' } }};">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <span class="{{ $order->elapsed_minutes > 10 ? 'text-red-600 font-bold' : ($order->elapsed_minutes > 5 ? 'text-yellow-600' : 'text-secondary') }}">
                                        {{ $order->elapsed_minutes }} min
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-secondary">No active orders</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ==================== CASHIER ==================== --}}
    @if ($user->hasRole('cashier'))
        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card">
                <p class="text-sm text-secondary font-medium">Today's Sales</p>
                <p class="text-2xl font-bold text-primary mt-1">${{ number_format($todaySales, 2) }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Orders Today</p>
                <p class="text-2xl font-bold mt-1">{{ $ordersToday }}</p>
            </div>
            @if ($activeShift)
                <div class="card border-l-4 border-l-green-500">
                    <p class="text-sm text-secondary font-medium">Active Shift</p>
                    <p class="text-lg font-bold mt-1">{{ $activeShift->name }}</p>
                    <p class="text-xs text-secondary">Opened {{ $activeShift->opened_at?->diffForHumans() }}</p>
                </div>
            @else
                <div class="card border-l-4 border-l-gray-300">
                    <p class="text-sm text-secondary font-medium">Shift</p>
                    <p class="text-lg font-bold mt-1 text-secondary">No open shift</p>
                </div>
            @endif
            <div class="card">
                <p class="text-sm text-secondary font-medium">Avg per Order</p>
                <p class="text-2xl font-bold mt-1">
                    ${{ $ordersToday > 0 ? number_format($todaySales / $ordersToday, 2) : '0.00' }}
                </p>
            </div>
        </div>

        @if ($activeShift)
            <div class="card">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Shift Summary</h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-secondary">Expected Cash</p>
                        <p class="text-lg font-bold">${{ number_format($activeShift->expected_cash ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-secondary">Card Total</p>
                        <p class="text-lg font-bold">${{ number_format($activeShift->card_total ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-secondary">Other Payments</p>
                        <p class="text-lg font-bold">${{ number_format($activeShift->other_total ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-secondary">Difference</p>
                        <p class="text-lg font-bold {{ ($activeShift->difference ?? 0) != 0 ? 'text-error' : '' }}">
                            ${{ number_format($activeShift->difference ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Recent Transactions --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="card">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Recent Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-secondary border-b border-surface-container-high">
                                <th class="pb-2 font-medium">Order</th>
                                <th class="pb-2 font-medium">Table</th>
                                <th class="pb-2 font-medium">Amount</th>
                                <th class="pb-2 font-medium">Method</th>
                                <th class="pb-2 font-medium">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTransactions as $order)
                                <tr class="border-b border-surface-container-high">
                                    <td class="py-2 font-mono text-xs">{{ $order->order_number }}</td>
                                    <td class="py-2">{{ $order->table?->table_number ?? '—' }}</td>
                                    <td class="py-2 font-medium">${{ number_format($order->total, 2) }}</td>
                                    <td class="py-2 capitalize">{{ $order->payment_method ?? '—' }}</td>
                                    <td class="py-2 text-secondary">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 text-center text-secondary">No transactions today</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Payment Methods</h3>
                <div class="space-y-3">
                    @forelse ($paymentMethodBreakdown as $pm)
                        <div class="flex items-center justify-between">
                            <span class="text-sm capitalize">{{ $pm->method }}</span>
                            <div class="text-right">
                                <span class="text-sm font-semibold">${{ number_format($pm->total_amount, 2) }}</span>
                                <span class="text-xs text-secondary ml-2">({{ $pm->total }})</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-secondary">No payments today</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== CUSTOMER ==================== --}}
    @if ($user->hasRole('customer'))
        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card">
                <p class="text-sm text-secondary font-medium">My Orders</p>
                <p class="text-2xl font-bold mt-1">{{ $myOrdersCount }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Loyalty Points</p>
                <p class="text-2xl font-bold text-primary mt-1">{{ $loyaltyPoints }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Upcoming Reservations</p>
                <p class="text-2xl font-bold mt-1">{{ $upcomingReservations?->count() ?? 0 }}</p>
            </div>
            <div class="card">
                <p class="text-sm text-secondary font-medium">Orders Today</p>
                <p class="text-2xl font-bold mt-1">{{ $ordersToday }}</p>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="card">
            <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">My Recent Orders</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-secondary border-b border-surface-container-high">
                            <th class="pb-2 font-medium">Order</th>
                            <th class="pb-2 font-medium">Items</th>
                            <th class="pb-2 font-medium">Total</th>
                            <th class="pb-2 font-medium">Status</th>
                            <th class="pb-2 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr class="border-b border-surface-container-high">
                                <td class="py-2 font-mono text-xs">{{ $order->order_number }}</td>
                                <td class="py-2">{{ $order->items->count() }}</td>
                                <td class="py-2 font-medium">${{ number_format($order->total, 2) }}</td>
                                <td class="py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                          style="background: {{ match($order->status) { 'pending' => '#fef3c7', 'confirmed' => '#dbeafe', 'preparing' => '#ffedd5', 'ready' => '#dcfce7', 'completed' => '#f3f4f6', 'cancelled' => '#fee2e2', default => '#f3f4f6' } }};
                                          color: {{ match($order->status) { 'pending' => '#92400e', 'confirmed' => '#1e40af', 'preparing' => '#9a3412', 'ready' => '#166534', 'completed' => '#374151', 'cancelled' => '#991b1b', default => '#374151' } }};">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="py-2 text-secondary">{{ $order->created_at->format('M d, g:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-secondary">No orders yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Upcoming Reservations --}}
        @if ($upcomingReservations?->isNotEmpty())
            <div class="card">
                <h3 class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4">Upcoming Reservations</h3>
                @foreach ($upcomingReservations as $res)
                    <div class="flex items-center justify-between py-2 border-b border-surface-container-high last:border-0">
                        <div>
                            <span class="text-sm font-medium">{{ $res->reservation_date->format('D, M d') }}</span>
                            <span class="text-sm text-secondary ml-2">{{ $res->reservation_time?->format('g:i A') }}</span>
                        </div>
                        <span class="text-sm">{{ $res->guest_count }} guests</span>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
