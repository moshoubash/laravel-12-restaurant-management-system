<?php

namespace App\Livewire;

use App\Models\Tenant\DesignConfig;
use App\Models\Tenant\Order;
use Livewire\Component;

class Receipt extends Component
{
    public $orderId;

    public function mount($orderId = null)
    {
        $this->orderId = $orderId;
    }

    public function getOrderProperty()
    {
        if (!$this->orderId) return null;
        return Order::with(['items', 'table', 'branch', 'customer'])
            ->find($this->orderId);
    }

    public function getConfigProperty()
    {
        return DesignConfig::first();
    }

    public function render()
    {
        return view('livewire.receipt')
            ->layout('layouts.receipt');
    }
}
