<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use App\Models\Tenant\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;

class Reports extends Component
{
    #[Url]
    public string $dateRange = 'this_month';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    #[Url]
    public string $branchId = '';

    public function mount()
    {
        if (! $this->dateFrom) {
            $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        }
        if (! $this->dateTo) {
            $this->dateTo = now()->format('Y-m-d');
        }
    }

    public function updatedDateRange($value)
    {
        [$this->dateFrom, $this->dateTo] = match ($value) {
            'today' => [now()->format('Y-m-d'), now()->format('Y-m-d')],
            'yesterday' => [now()->subDay()->format('Y-m-d'), now()->subDay()->format('Y-m-d')],
            'this_week' => [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
            'last_week' => [now()->subWeek()->startOfWeek()->format('Y-m-d'), now()->subWeek()->endOfWeek()->format('Y-m-d')],
            'this_month' => [now()->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
            'last_month' => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
            'this_year' => [now()->startOfYear()->format('Y-m-d'), now()->format('Y-m-d')],
            default => [$this->dateFrom, $this->dateTo],
        };
    }

    protected function baseQuery()
    {
        $query = Order::query()
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ]);

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        return $query;
    }

    protected function getKpis(): array
    {
        $orders = $this->baseQuery();
        $paidOrders = (clone $orders)->where('payment_status', 'paid');

        $totalRevenue = (clone $paidOrders)->sum('total');
        $totalOrders = (clone $orders)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalCustomers = (clone $orders)->whereNotNull('customer_id')->distinct('customer_id')->count();

        return [
            'totalRevenue' => number_format($totalRevenue, 2),
            'totalOrders' => $totalOrders,
            'avgOrderValue' => number_format($avgOrderValue, 2),
            'totalCustomers' => $totalCustomers,
        ];
    }

    protected function getRevenueChart(): array
    {
        $rows = $this->baseQuery()
            ->where('payment_status', 'paid')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $rows->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('M d'))->toArray(),
            'data' => $rows->pluck('revenue')->map(fn ($v) => round((float) $v, 2))->toArray(),
        ];
    }

    protected function getTopSellingItems(): array
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();

        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereNull('orders.deleted_at');

        if ($this->branchId) {
            $query->where('orders.branch_id', $this->branchId);
        }

        return $query
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
    }

    protected function getOrdersByType(): array
    {
        return $this->baseQuery()
            ->select('order_type', DB::raw('COUNT(*) as count'))
            ->groupBy('order_type')
            ->pluck('count', 'order_type')
            ->toArray();
    }

    protected function getOrdersByStatus(): array
    {
        return $this->baseQuery()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    protected function getPaymentMethodBreakdown(): array
    {
        return $this->baseQuery()
            ->where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->get()
            ->toArray();
    }

    protected function getHourlyDistribution(): array
    {
        return $this->baseQuery()
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
    }

    protected function getRecentOrders()
    {
        return $this->baseQuery()
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getBranchesProperty()
    {
        return Branch::orderBy('name')->get();
    }

    public function render()
    {
        $kpis = $this->getKpis();
        $revenueChart = $this->getRevenueChart();
        $topItems = $this->getTopSellingItems();
        $ordersByType = $this->getOrdersByType();
        $ordersByStatus = $this->getOrdersByStatus();
        $paymentMethods = $this->getPaymentMethodBreakdown();
        $hourlyDistribution = $this->getHourlyDistribution();
        $recentOrders = $this->getRecentOrders();

        return view('livewire.admin.reports', compact(
            'kpis',
            'revenueChart',
            'topItems',
            'ordersByType',
            'ordersByStatus',
            'paymentMethods',
            'hourlyDistribution',
            'recentOrders'
        ))->layout('layouts.admin');
    }
}
