<?php

namespace App\Livewire\Kitchen;

use App\Models\Tenant\Order;
use Livewire\Component;

class OrderDisplay extends Component
{
    public $activeTab = 'new';

    public function getNewOrdersProperty()
    {
        return Order::with(['items', 'table'])
            ->whereIn('status', ['confirmed'])
            ->orderBy('confirmed_at')
            ->get();
    }

    public function getPreparingOrdersProperty()
    {
        return Order::with(['items', 'table'])
            ->where('status', 'preparing')
            ->orderBy('preparing_at')
            ->get();
    }

    public function getReadyOrdersProperty()
    {
        return Order::with(['items', 'table'])
            ->where('status', 'ready')
            ->orderBy('ready_at')
            ->get();
    }

    public function startPreparing($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'preparing', 'preparing_at' => now()]);
    }

    public function markReady($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'ready', 'ready_at' => now()]);
    }

    public function render()
    {
        return view('livewire.kitchen.order-display')
            ->layout('layouts.kitchen');
    }
}
