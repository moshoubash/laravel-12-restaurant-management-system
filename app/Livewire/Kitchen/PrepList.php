<?php

namespace App\Livewire\Kitchen;

use App\Models\Tenant\Order;
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
        Order::findOrFail($id)->update(['status' => 'preparing', 'preparing_at' => now()]);
    }

    public function markReady($id)
    {
        Order::findOrFail($id)->update(['status' => 'ready', 'ready_at' => now()]);
    }

    public function render()
    {
        return view('livewire.kitchen.prep-list')
            ->layout('layouts.kitchen');
    }
}
