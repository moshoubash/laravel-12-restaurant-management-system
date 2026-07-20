<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Branch;
use App\Models\Tenant\InventoryItem;
use App\Models\Tenant\Supplier;
use Livewire\Component;

class Inventory extends Component
{
    public $showForm = false;
    public $editingItem = null;
    public $name = '';
    public $sku = '';
    public $category = '';
    public $unit = 'pcs';
    public $stockQuantity = 0;
    public $minStockLevel = 0;
    public $maxStockLevel = null;
    public $reorderPoint = 0;
    public $unitCost = 0;
    public $supplierId = '';
    public $isActive = true;

    public $search = '';
    public $filterCategory = '';
    public $filterLowStock = false;

    public $showAdjust = false;
    public $adjustingItem = null;
    public $adjustQuantity = 0;
    public $adjustReason = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:' . (new InventoryItem)->getTable() . ',sku,' . ($this->editingItem ?? ''),
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'stockQuantity' => 'required|numeric|min:0',
            'minStockLevel' => 'required|numeric|min:0',
            'maxStockLevel' => 'nullable|numeric|min:0',
            'reorderPoint' => 'required|numeric|min:0',
            'unitCost' => 'required|numeric|min:0',
            'supplierId' => 'nullable|exists:' . (new Supplier)->getTable() . ',id',
        ];
    }

    public function getItemsProperty()
    {
        return InventoryItem::with(['supplier', 'branch'])
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

    public function getSuppliersProperty()
    {
        return Supplier::where('is_active', true)->orderBy('name')->get();
    }

    public function openForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingItem = $id;
        $this->showForm = true;

        if ($id) {
            $item = InventoryItem::findOrFail($id);
            $this->name = $item->name;
            $this->sku = $item->sku ?? '';
            $this->category = $item->category ?? '';
            $this->unit = $item->unit;
            $this->stockQuantity = (float) $item->stock_quantity;
            $this->minStockLevel = (float) $item->min_stock_level;
            $this->maxStockLevel = $item->max_stock_level ? (float) $item->max_stock_level : null;
            $this->reorderPoint = (float) $item->reorder_point;
            $this->unitCost = (float) $item->unit_cost;
            $this->supplierId = (string) ($item->supplier_id ?? '');
            $this->isActive = $item->is_active;
        } else {
            $this->name = '';
            $this->sku = '';
            $this->category = '';
            $this->unit = 'pcs';
            $this->stockQuantity = 0;
            $this->minStockLevel = 0;
            $this->maxStockLevel = null;
            $this->reorderPoint = 0;
            $this->unitCost = 0;
            $this->supplierId = '';
            $this->isActive = true;
        }
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingItem = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'branch_id' => Branch::first()->id,
            'name' => $this->name,
            'sku' => $this->sku ?: null,
            'category' => $this->category ?: null,
            'unit' => $this->unit,
            'stock_quantity' => $this->stockQuantity,
            'min_stock_level' => $this->minStockLevel,
            'max_stock_level' => $this->maxStockLevel ?: null,
            'reorder_point' => $this->reorderPoint,
            'unit_cost' => $this->unitCost,
            'supplier_id' => $this->supplierId ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->editingItem) {
            InventoryItem::findOrFail($this->editingItem)->update($data);
        } else {
            InventoryItem::create($data);
        }

        $this->showForm = false;
        $this->editingItem = null;
    }

    public function deleteItem($id)
    {
        InventoryItem::findOrFail($id)->delete();
    }

    public function toggleActive($id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
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
        $this->resetErrorBag();
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
        return view('livewire.admin.inventory')
            ->layout('layouts.admin');
    }
}
