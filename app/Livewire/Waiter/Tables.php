<?php

namespace App\Livewire\Waiter;

use App\Models\Tenant\Order;
use App\Models\Tenant\Table;
use Livewire\Component;

class Tables extends Component
{
    public $selectedTableId = null;
    public $selectedTableOrders = [];

    public function getTablesProperty()
    {
        return Table::where('is_active', true)->orderBy('section')->orderBy('table_number')->get();
    }

    public function getSectionsProperty()
    {
        return Table::where('is_active', true)->whereNotNull('section')->distinct()->orderBy('section')->pluck('section');
    }

    public function selectTable($id)
    {
        $this->selectedTableId = $id;
        $this->selectedTableOrders = Order::with('items')
            ->where('table_id', $id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.waiter.tables')
            ->layout('layouts.waiter');
    }
}
