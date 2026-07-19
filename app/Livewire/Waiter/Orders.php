<?php

namespace App\Livewire\Waiter;

use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use Livewire\Component;

class Orders extends Component
{
    public $filterStatus = '';
    public $filterType = '';

    public $selectedOrderId = null;
    public $showDetail = false;

    public function getOrdersProperty()
    {
        return Order::with(['items', 'table', 'user'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterType, fn($q) => $q->where('order_type', $this->filterType))
            ->whereIn('status', ['confirmed', 'preparing', 'ready', 'served'])
            ->latest('ordered_at')
            ->get();
    }

    public function getPendingOrdersProperty()
    {
        return Order::with('table')
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
    }

    public function getReadyOrdersProperty()
    {
        return Order::with('table')
            ->where('status', 'ready')
            ->count();
    }

    public function viewOrder($id)
    {
        $this->selectedOrderId = $id;
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedOrderId = null;
    }

    public function markServed($id)
    {
        $order = Order::findOrFail($id);
        if ($order->status === 'ready' || $order->status === 'served') {
            $order->update([
                'status' => 'served',
                'served_at' => now(),
            ]);
        }
    }

    public function itemServed($orderId, $itemId)
    {
        OrderItem::findOrFail($itemId)->update(['status' => 'served']);

        $order = Order::with('items')->find($orderId);
        if ($order && $order->items->every(fn($i) => $i->status === 'served')) {
            $order->update(['status' => 'served', 'served_at' => now()]);
        }
    }

    public function render()
    {
        return view('livewire.waiter.orders')
            ->layout('layouts.waiter');
    }
}
