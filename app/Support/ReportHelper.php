<?php

namespace App\Support;

use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportHelper
{
    public static function gatherData(string $dateFrom, string $dateTo, ?int $branchId = null): array
    {
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->endOfDay();

        $baseQuery = Order::query()
            ->whereBetween('orders.created_at', [$from, $to])
            ->when($branchId, fn($q) => $q->where('orders.branch_id', $branchId));

        $paidQuery = (clone $baseQuery)->where('payment_status', 'paid');

        $totalRevenue = (clone $paidQuery)->sum('total');
        $totalOrders = (clone $baseQuery)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalCustomers = (clone $baseQuery)->whereNotNull('customer_id')->distinct('customer_id')->count();

        $topItems = (clone $baseQuery)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                'order_items.menu_item_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('order_items.menu_item_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get()
            ->toArray();

        $ordersByType = (clone $baseQuery)
            ->select('order_type', DB::raw('COUNT(*) as count'))
            ->groupBy('order_type')
            ->pluck('count', 'order_type')
            ->toArray();

        $paymentMethods = (clone $paidQuery)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->get()
            ->toArray();

        $hourlyDistribution = (clone $baseQuery)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        $revenueChart = (clone $paidQuery)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        return [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'totalCustomers' => $totalCustomers,
            'topItems' => $topItems,
            'ordersByType' => $ordersByType,
            'paymentMethods' => $paymentMethods,
            'hourlyDistribution' => $hourlyDistribution,
            'revenueChart' => $revenueChart,
        ];
    }

    public static function toExcelRows(array $data): array
    {
        $rows = [];

        $rows[] = ['Report Summary', '', '', '', ''];
        $rows[] = ['Period', $data['dateFrom'] . ' to ' . $data['dateTo'], '', '', ''];
        $rows[] = ['', '', '', '', ''];
        $rows[] = ['Key Metrics', '', '', '', ''];
        $rows[] = ['Total Revenue', number_format($data['totalRevenue'], 2), '', '', ''];
        $rows[] = ['Total Orders', $data['totalOrders'], '', '', ''];
        $rows[] = ['Avg Order Value', number_format($data['avgOrderValue'], 2), '', '', ''];
        $rows[] = ['Unique Customers', $data['totalCustomers'], '', '', ''];
        $rows[] = ['', '', '', '', ''];

        $rows[] = ['Revenue Chart', 'Date', 'Revenue', '', ''];
        foreach ($data['revenueChart'] as $point) {
            $rows[] = ['', $point['date'] ?? '', number_format((float)($point['revenue'] ?? 0), 2), '', ''];
        }
        $rows[] = ['', '', '', '', ''];

        $rows[] = ['Top Selling Items', 'Item', 'Quantity', 'Revenue', ''];
        foreach ($data['topItems'] as $item) {
            $rows[] = ['', $item['menu_item_name'] ?? '', $item['total_qty'] ?? 0, number_format((float)($item['total_revenue'] ?? 0), 2), ''];
        }
        $rows[] = ['', '', '', '', ''];

        $rows[] = ['Orders by Type', 'Type', 'Count', '', ''];
        foreach ($data['ordersByType'] as $type => $count) {
            $rows[] = ['', $type, $count, '', ''];
        }
        $rows[] = ['', '', '', '', ''];

        $rows[] = ['Payment Methods', 'Method', 'Count', 'Revenue', ''];
        foreach ($data['paymentMethods'] as $pm) {
            $rows[] = ['', $pm['payment_method'] ?? '', $pm['count'] ?? 0, number_format((float)($pm['revenue'] ?? 0), 2), ''];
        }
        $rows[] = ['', '', '', '', ''];

        $rows[] = ['Hourly Distribution', 'Hour', 'Orders', '', ''];
        foreach ($data['hourlyDistribution'] as $hour => $count) {
            $rows[] = ['', sprintf('%02d:00', $hour), $count, '', ''];
        }

        return $rows;
    }
}
