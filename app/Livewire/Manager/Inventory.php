<?php

namespace App\Livewire\Manager;

use App\Models\Tenant\InventoryItem;
use Livewire\Component;

class Inventory extends Component
{
    public $search = '';
    public $filterCategory = '';
    public $filterLowStock = false;

    public $showAdjust = false;
    public $adjustingItem = null;
    public $adjustQuantity = 0;
    public $adjustReason = '';

    public function getItemsProperty()
    {
        return InventoryItem::with('supplier')
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->when($this->filterCategory, fn($q) => $q->where('category', $this->filterCategory))
            ->when($this->filterLowStock, fn($q) => $q->whereColumn('stock_quantity', '<=', 'reorder_point'))
            ->orderBy('name')
            ->get();
    }

    public function getCategoriesProperty()
    {
        return InventoryItem::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort();
    }

    public function openAdjust($id)
    {
        $item = InventoryItem::findOrFail($id);
        $this->adjustingItem = $id;
        $this->adjustQuantity = 0;
        $this->adjustReason = '';
        $this->showAdjust = true;
    }

    public function cancelAdjust()
    {
        $this->showAdjust = false;
        $this->adjustingItem = null;
    }

    public function saveAdjust()
    {
        $this->validate([
            'adjustQuantity' => 'required|numeric',
            'adjustReason' => 'required|string|max:255',
        ]);

        $item = InventoryItem::findOrFail($this->adjustingItem);
        $item->increment('stock_quantity', $this->adjustQuantity);

        $this->showAdjust = false;
        $this->adjustingItem = null;
    }

    public function render()
    {
        return view('livewire.manager.inventory')
            ->layout('layouts.manager');
    }
}
