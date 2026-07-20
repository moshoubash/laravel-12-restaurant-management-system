<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report {{ $dateFrom }} to {{ $dateTo }}</title>
</head>
<body style="margin:0; padding:10px 12px; box-sizing:border-box; font-family: DejaVu Sans, sans-serif; font-size: 7.5px; color: #1c1917;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin:0 0 8px 0; padding:0 0 4px 0; border-bottom:1.5px solid #333; box-sizing:border-box;">
        <h1 style="margin:0; padding:0; font-size:13px; font-weight:700; box-sizing:border-box;">ReSaaS Report</h1>
        <span style="margin:0; padding:0; font-size:7px; color:#555; box-sizing:border-box;">{{ $dateFrom }} to {{ $dateTo }}</span>
    </div>

    <div style="display:flex; flex-direction:row; gap:4px; margin:0 0 6px 0; padding:0; box-sizing:border-box;">
        <div style="flex:1; border:0.5px solid #d6d3d1; padding:3px 4px; text-align:center; white-space:nowrap; box-sizing:border-box; margin:0;">
            <div style="margin:0; padding:0; font-size:6px; text-transform:uppercase; color:#555; box-sizing:border-box;">Revenue</div>
            <div style="margin:1px 0 0 0; padding:0; font-size:10px; font-weight:700; box-sizing:border-box;">${{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div style="flex:1; border:0.5px solid #d6d3d1; padding:3px 4px; text-align:center; white-space:nowrap; box-sizing:border-box; margin:0;">
            <div style="margin:0; padding:0; font-size:6px; text-transform:uppercase; color:#555; box-sizing:border-box;">Orders</div>
            <div style="margin:1px 0 0 0; padding:0; font-size:10px; font-weight:700; box-sizing:border-box;">{{ $totalOrders }}</div>
        </div>
        <div style="flex:1; border:0.5px solid #d6d3d1; padding:3px 4px; text-align:center; white-space:nowrap; box-sizing:border-box; margin:0;">
            <div style="margin:0; padding:0; font-size:6px; text-transform:uppercase; color:#555; box-sizing:border-box;">Avg Order</div>
            <div style="margin:1px 0 0 0; padding:0; font-size:10px; font-weight:700; box-sizing:border-box;">${{ number_format($avgOrderValue, 2) }}</div>
        </div>
        <div style="flex:1; border:0.5px solid #d6d3d1; padding:3px 4px; text-align:center; white-space:nowrap; box-sizing:border-box; margin:0;">
            <div style="margin:0; padding:0; font-size:6px; text-transform:uppercase; color:#555; box-sizing:border-box;">Customers</div>
            <div style="margin:1px 0 0 0; padding:0; font-size:10px; font-weight:700; box-sizing:border-box;">{{ $totalCustomers }}</div>
        </div>
    </div>

    <div style="display:flex; gap:8px; margin:0; padding:0; box-sizing:border-box;">
        <div style="flex:1; margin:0; padding:0; box-sizing:border-box;">
            <div style="font-size:8px; font-weight:700; margin:6px 0 3px 0; padding:0; box-sizing:border-box;">Top Items</div>
            <table style="width:100%; border-collapse:collapse; margin:0 0 6px 0; padding:0; box-sizing:border-box;">
                <tr>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Item</th>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:right; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Qty</th>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:right; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Revenue</th>
                </tr>
                @forelse($topItems as $item)
                    <tr>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; margin:0; box-sizing:border-box;">{{ $item['menu_item_name'] ?? '' }}</td>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; text-align:right; margin:0; box-sizing:border-box;">{{ $item['total_qty'] ?? 0 }}</td>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; text-align:right; margin:0; box-sizing:border-box;">${{ number_format((float)($item['total_revenue'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; color:#888; margin:0; box-sizing:border-box;">No data</td></tr>
                @endforelse
            </table>
        </div>
        <div style="flex:1; margin:0; padding:0; box-sizing:border-box;">
            <div style="font-size:8px; font-weight:700; margin:6px 0 3px 0; padding:0; box-sizing:border-box;">Revenue Trend</div>
            <table style="width:100%; border-collapse:collapse; margin:0 0 6px 0; padding:0; box-sizing:border-box;">
                <tr>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Date</th>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:right; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Revenue</th>
                </tr>
                @forelse($revenueChart as $point)
                    <tr>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; margin:0; box-sizing:border-box;">{{ \Carbon\Carbon::parse($point['date'])->format('M d') }}</td>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; text-align:right; margin:0; box-sizing:border-box;">${{ number_format((float)($point['revenue'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; color:#888; margin:0; box-sizing:border-box;">No data</td></tr>
                @endforelse
            </table>
        </div>
    </div>

    <div style="display:flex; gap:8px; margin:0; padding:0; box-sizing:border-box;">
        <div style="flex:1; margin:0; padding:0; box-sizing:border-box;">
            <div style="font-size:8px; font-weight:700; margin:6px 0 3px 0; padding:0; box-sizing:border-box;">Orders by Type</div>
            <table style="width:100%; border-collapse:collapse; margin:0 0 6px 0; padding:0; box-sizing:border-box;">
                <tr>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Type</th>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:right; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Orders</th>
                </tr>
                @forelse($ordersByType as $type => $count)
                    <tr>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; margin:0; box-sizing:border-box;">{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; text-align:right; margin:0; box-sizing:border-box;">{{ $count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; color:#888; margin:0; box-sizing:border-box;">No data</td></tr>
                @endforelse
            </table>
        </div>
        <div style="flex:1; margin:0; padding:0; box-sizing:border-box;">
            <div style="font-size:8px; font-weight:700; margin:6px 0 3px 0; padding:0; box-sizing:border-box;">Payment Methods</div>
            <table style="width:100%; border-collapse:collapse; margin:0 0 6px 0; padding:0; box-sizing:border-box;">
                <tr>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Method</th>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:right; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Orders</th>
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:right; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">Revenue</th>
                </tr>
                @forelse($paymentMethods as $pm)
                    <tr>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; margin:0; box-sizing:border-box;">{{ ucfirst($pm['payment_method'] ?? 'N/A') }}</td>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; text-align:right; margin:0; box-sizing:border-box;">{{ $pm['count'] ?? 0 }}</td>
                        <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; text-align:right; margin:0; box-sizing:border-box;">${{ number_format((float)($pm['revenue'] ?? 0), 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; color:#888; margin:0; box-sizing:border-box;">No data</td></tr>
                @endforelse
            </table>
        </div>
    </div>

    <div style="margin:6px 0 0 0; padding:0; box-sizing:border-box;">
        <div style="font-size:8px; font-weight:700; margin:6px 0 3px 0; padding:0; box-sizing:border-box;">Hourly Distribution</div>
        <table style="width:100%; border-collapse:collapse; margin:0 0 6px 0; padding:0; box-sizing:border-box;">
            <tr>
                @for ($h = 0; $h <= 23; $h++)
                    <th style="background:#e7e5e4; padding:3px 5px; text-align:center; font-size:6.5px; font-weight:700; text-transform:uppercase; border:0.5px solid #d6d3d1; margin:0; box-sizing:border-box;">{{ sprintf('%02d', $h) }}</th>
                @endfor
            </tr>
            <tr>
                @for ($h = 0; $h <= 23; $h++)
                    <td style="padding:2px 5px; border:0.5px solid #d6d3d1; font-size:7px; text-align:center; margin:0; box-sizing:border-box;">{{ $hourlyDistribution[$h] ?? 0 }}</td>
                @endfor
            </tr>
        </table>
    </div>
</body>
</html>
