<?php

namespace App\Livewire\Kitchen;

use App\Models\Tenant\Order;
use App\Support\NotificationHelper;
use Livewire\Component;

class PrepList extends Component
{
    public function getOrdersProperty()
    {
        return Order::with(['table', 'items', 'user'])
            ->whereIn('status', ['confirmed', 'preparing'])
            ->latest('ordered_at')
            ->get();
    }

    public function markPreparing($id)
    {
        $order = Order::with('table')->findOrFail($id);
        $order->update(['status' => 'preparing', 'preparing_at' => now()]);

        $totalItems = $order->items->sum('quantity');
        NotificationHelper::sendToRole('admin', 'Order #' . $order->order_number . ' In Preparation', $totalItems . ' items', 'order');
    }

    public function markReady($id)
    {
        $order = Order::with('table')->findOrFail($id);
        $order->update(['status' => 'ready', 'ready_at' => now()]);

        $title = 'Order #' . $order->order_number . ' Ready';
        $message = 'Table ' . ($order->table?->table_number ?? 'Takeaway');
        NotificationHelper::sendToRole('waiter', $title, $message, 'order', route('tenant.waiter.orders'));

        if ($assignedUser = $order->user) {
            NotificationHelper::send($assignedUser, $title, $message, 'order', route('tenant.waiter.orders'));
        }
    }

    public function render()
    {
        return view('livewire.kitchen.prep-list')
            ->layout('layouts.kitchen');
    }
}
