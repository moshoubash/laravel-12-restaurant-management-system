<?php

namespace App\Livewire\Kitchen;

use App\Models\Tenant\InventoryItem;
use Livewire\Component;

class InventoryAlerts extends Component
{
    public function getLowStockProperty()
    {
        return InventoryItem::with('branch')
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'reorder_point')
            ->orderBy('stock_quantity')
            ->get();
    }

    public function getOutOfStockProperty()
    {
        return InventoryItem::with('branch')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 0)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.kitchen.inventory-alerts')
            ->layout('layouts.kitchen');
    }
}
