<?php

namespace App\Livewire;

use App\Models\Tenant\Customer;
use App\Models\Tenant\InventoryItem;
use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Reservation;
use App\Models\Tenant\Shift;
use App\Models\Tenant\StaffShift;
use App\Models\Tenant\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public float $todayRevenue = 0;
    public int $ordersToday = 0;
    public int $activeTables = 0;
    public int $staffOnDuty = 0;
    public float $avgOrderValue = 0;
    public int $occupancyRate = 0;
    public float $revenueTrend = 0;
    public int $ordersTrend = 0;
    public array $weeklyRevenue = [];
    public float $maxRevenue = 0;
    public $recentOrders;
    public $topItems;
    public $lowStockItems;
    public array $ordersByStatus = [];
    public $todayReservations;

    public int $pendingOrders = 0;
    public int $preparingOrders = 0;
    public int $readyOrders = 0;
    public $activeOrders;

    public int $myOrdersToday = 0;
    public int $myActiveTables = 0;

    public $activeShift;
    public float $todaySales = 0;
    public $recentTransactions;
    public $paymentMethodBreakdown;

    public int $myOrdersCount = 0;
    public int $loyaltyPoints = 0;
    public $upcomingReservations;

    public function mount()
    {
        $user = auth('tenant')->user();

        if ($user->hasAnyRole(['owner', 'admin', 'manager'])) {
            $this->loadAdminManagerData();
        }

        if ($user->hasRole('chef')) {
            $this->loadChefData();
        }

        if ($user->hasRole('waiter')) {
            $this->loadWaiterData();
        }

        if ($user->hasRole('cashier')) {
            $this->loadCashierData();
        }

        if ($user->hasRole('customer')) {
            $this->loadCustomerData();
        }
    }

    protected function loadAdminManagerData(): void
    {
        $todayPaid = Order::whereDate('created_at', today())->where('payment_status', 'paid');
        $this->todayRevenue = (float) (clone $todayPaid)->sum('total');
        $this->ordersToday = Order::whereDate('created_at', today())->count();
        $this->activeTables = Table::where('status', 'occupied')->count();
        $totalTables = Table::count();
        $this->occupancyRate = $totalTables > 0 ? (int) round(($this->activeTables / $totalTables) * 100) : 0;
        $this->staffOnDuty = StaffShift::whereNull('clock_out')->count();
        $this->avgOrderValue = $this->ordersToday > 0 ? $this->todayRevenue / $this->ordersToday : 0;

        $yesterdayRevenue = (float) Order::whereDate('created_at', today()->subDay())->where('payment_status', 'paid')->sum('total');
        $this->revenueTrend = $yesterdayRevenue > 0 ? round((($this->todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1) : 0;
        $yesterdayOrders = Order::whereDate('created_at', today()->subDay())->count();
        $this->ordersTrend = $yesterdayOrders > 0 ? round((($this->ordersToday - $yesterdayOrders) / $yesterdayOrders) * 100, 1) : 0;

        $this->weeklyRevenue = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            $amount = Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')->sum('total');
            return ['label' => $date->format('D'), 'amount' => (float) $amount];
        })->toArray();
        $this->maxRevenue = max(array_column($this->weeklyRevenue, 'amount')) ?: 1;

        $this->recentOrders = Order::with(['table', 'user'])
            ->latest()->take(10)->get();

        $this->topItems = OrderItem::select('menu_item_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', fn($q) => $q->whereDate('created_at', today()))
            ->groupBy('menu_item_name')->orderByDesc('total_qty')->limit(5)->get();

        $this->ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->whereDate('created_at', today())
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $this->lowStockItems = InventoryItem::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_point')->get();

        $this->todayReservations = Reservation::with(['table', 'customer'])
            ->whereDate('reservation_date', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('reservation_time')->get();
    }

    protected function loadChefData(): void
    {
        $this->pendingOrders = Order::where('status', 'pending')->count();
        $this->preparingOrders = Order::where('status', 'preparing')->count();
        $this->readyOrders = Order::where('status', 'ready')->count();

        $this->activeOrders = Order::with(['table', 'items'])
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->orderBy('created_at')->get()
            ->map(fn($o) => $o->setAttribute('elapsed_minutes', $o->created_at->diffInMinutes(now())));

        $this->lowStockItems = InventoryItem::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_point')->get();
    }

    protected function loadWaiterData(): void
    {
        $userId = auth('tenant')->id();
        $this->myActiveTables = Table::where('status', 'occupied')->count();
        $this->myOrdersToday = Order::where('user_id', $userId)
            ->whereDate('created_at', today())->count();
        $this->pendingOrders = Order::whereIn('status', ['pending', 'confirmed'])->count();

        $this->activeOrders = Order::with(['table'])
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->latest()->take(10)->get()
            ->map(fn($o) => $o->setAttribute('elapsed_minutes', $o->created_at->diffInMinutes(now())));
    }

    protected function loadCashierData(): void
    {
        $this->activeShift = Shift::where('status', 'open')->first();
        $this->todaySales = (float) Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')->sum('total');
        $this->ordersToday = Order::whereDate('created_at', today())->count();

        $this->recentTransactions = Order::with(['table'])
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->latest()->take(10)->get();

        $this->paymentMethodBreakdown = Payment::select('method',
            DB::raw('count(*) as total'),
            DB::raw('sum(amount) as total_amount'))
            ->whereDate('created_at', today())
            ->groupBy('method')->get();
    }

    protected function loadCustomerData(): void
    {
        $customer = Customer::where('email', auth('tenant')->user()->email)->first();
        if ($customer) {
            $this->myOrdersCount = $customer->orders()->count();
            $this->recentOrders = $customer->orders()->latest()->take(5)->get();
            $this->loyaltyPoints = $customer->loyalty_points ?? 0;
            $this->upcomingReservations = $customer->reservations()
                ->where('reservation_date', '>=', today())
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('reservation_date')->orderBy('reservation_time')
                ->take(5)->get();
        }
        $this->ordersToday = Order::whereDate('created_at', today())->count();
    }

    public function render()
    {
        $user = auth('tenant')->user();

        if ($user->hasRole(['owner', 'admin'])) {
            return view('livewire.dashboard')->layout('layouts.admin');
        }
        if ($user->hasRole('manager')) {
            return view('livewire.dashboard')->layout('layouts.manager');
        }
        if ($user->hasRole('chef')) {
            return view('livewire.dashboard')->layout('layouts.kitchen');
        }
        if ($user->hasRole('waiter')) {
            return view('livewire.dashboard')->layout('layouts.waiter');
        }
        if ($user->hasRole('cashier')) {
            return view('livewire.dashboard')->layout('layouts.cashier');
        }
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
