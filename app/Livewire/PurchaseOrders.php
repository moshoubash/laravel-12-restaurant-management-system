<?php

namespace App\Livewire;

use App\Models\Tenant\Branch;
use App\Models\Tenant\InventoryItem;
use App\Models\Tenant\PurchaseOrder;
use App\Models\Tenant\PurchaseOrderItem;
use App\Models\Tenant\Supplier;
use Illuminate\Support\Str;
use Livewire\Component;

class PurchaseOrders extends Component
{
    public $showForm = false;
    public $editingOrder = null;

    public $supplierId = '';
    public $notes = '';

    public $items = [];

    public $search = '';
    public $filterStatus = '';

    protected function rules()
    {
        return [
            'supplierId' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ];
    }

    public function getSuppliersProperty()
    {
        return Supplier::where('is_active', true)->orderBy('name')->get();
    }

    public function getInventoryItemsProperty()
    {
        return InventoryItem::where('is_active', true)->orderBy('name')->get();
    }

    public function getOrdersProperty()
    {
        return PurchaseOrder::with(['supplier', 'items'])
            ->when($this->search, fn($q) => $q->where('order_number', 'like', "%{$this->search}%"))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->get();
    }

    public function addItem()
    {
        $this->items[] = ['inventory_item_id' => '', 'item_name' => '', 'unit' => '', 'quantity' => 1, 'unit_cost' => 0, 'total_cost' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        if (str_ends_with($key, '.inventory_item_id')) {
            $index = explode('.', $key)[0];
            $item = InventoryItem::find($value);
            if ($item) {
                $this->items[$index]['item_name'] = $item->name;
                $this->items[$index]['unit'] = $item->unit;
                $this->items[$index]['unit_cost'] = (float) $item->unit_cost;
                $this->recalcItem($index);
            }
        }
        if (str_ends_with($key, '.quantity') || str_ends_with($key, '.unit_cost')) {
            $index = explode('.', $key)[0];
            $this->recalcItem($index);
        }
    }

    protected function recalcItem($index)
    {
        $qty = (float) ($this->items[$index]['quantity'] ?? 0);
        $cost = (float) ($this->items[$index]['unit_cost'] ?? 0);
        $this->items[$index]['total_cost'] = round($qty * $cost, 2);
    }

    public function getTotalProperty()
    {
        return collect($this->items)->sum('total_cost');
    }

    public function openForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingOrder = $id;
        $this->showForm = true;

        if ($id) {
            $po = PurchaseOrder::with('items')->findOrFail($id);
            $this->supplierId = (string) ($po->supplier_id ?? '');
            $this->notes = $po->notes ?? '';
            $this->items = $po->items->map(fn($i) => [
                'inventory_item_id' => (string) $i->inventory_item_id,
                'item_name' => $i->item_name,
                'unit' => $i->unit,
                'quantity' => (float) $i->quantity,
                'unit_cost' => (float) $i->unit_cost,
                'total_cost' => (float) $i->total_cost,
            ])->toArray();
        } else {
            $this->supplierId = '';
            $this->notes = '';
            $this->items = [];
            $this->addItem();
        }
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingOrder = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $total = $this->total;

        if ($this->editingOrder) {
            $po = PurchaseOrder::findOrFail($this->editingOrder);
            if (!in_array($po->status, ['draft', 'ordered'])) {
                $this->addError('status', 'Cannot edit a received or cancelled order.');
                return;
            }
            $po->update([
                'supplier_id' => $this->supplierId ?: null,
                'total' => $total,
                'notes' => $this->notes ?: null,
            ]);
            $po->items()->delete();
        } else {
            $po = PurchaseOrder::create([
                'branch_id' => Branch::first()->id,
                'order_number' => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'supplier_id' => $this->supplierId ?: null,
                'status' => 'draft',
                'total' => $total,
                'notes' => $this->notes ?: null,
                'ordered_at' => now(),
            ]);
        }

        foreach ($this->items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'inventory_item_id' => $item['inventory_item_id'] ?: null,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_cost' => $item['unit_cost'],
                'total_cost' => $item['total_cost'],
            ]);
        }

        $this->showForm = false;
        $this->editingOrder = null;
    }

    public function markOrdered($id)
    {
        PurchaseOrder::findOrFail($id)->update(['status' => 'ordered', 'ordered_at' => now()]);
    }

    public function markReceived($id)
    {
        $po = PurchaseOrder::with('items')->findOrFail($id);
        foreach ($po->items as $item) {
            InventoryItem::where('id', $item->inventory_item_id)->increment('stock_quantity', $item->quantity);
            $item->update(['received_quantity' => $item->quantity]);
        }
        $po->update(['status' => 'received', 'received_at' => now()]);
    }

    public function markCancelled($id)
    {
        PurchaseOrder::findOrFail($id)->update(['status' => 'cancelled']);
    }

    public function render()
    {
        return view('livewire.purchase-orders')
            ->layout('layouts.admin');
    }
}
