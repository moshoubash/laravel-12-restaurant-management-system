<?php

namespace App\Livewire;

use App\Models\Tenant\Order;
use Livewire\Component;

class OrderTracking extends Component
{
    public $orderNumber = '';
    public $order = null;
    public $searched = false;

    public function track()
    {
        $this->validate(['orderNumber' => 'required|string|max:50']);
        $this->order = Order::with('items')
            ->where('order_number', $this->orderNumber)
            ->first();
        $this->searched = true;
    }

    public function render()
    {
        return view('livewire.order-tracking')
            ->layout('layouts.public');
    }
}
