<?php

namespace App\Livewire\Manager;

use App\Models\Tenant\MenuCategory;
use App\Models\Tenant\MenuItem;
use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use App\Models\Tenant\Table;
use Illuminate\Support\Str;
use Livewire\Component;

class Orders extends Component
{
    public $activeTab = 'active';
    public $selectedOrderId = null;
    public $showCreateForm = false;
    public $showDetail = false;

    // Create order form
    public $orderTableId = '';
    public $orderType = 'dine_in';
    public $customerName = '';
    public $orderNotes = '';
    public $selectedCategoryId = null;
    public $cart = [];

    public function mount()
    {
        $firstCat = MenuCategory::where('is_active', true)->orderBy('sort_order')->first();
        if ($firstCat) {
            $this->selectedCategoryId = $firstCat->id;
        }
    }

    public function getOrdersProperty()
    {
        $query = Order::with(['table', 'items', 'user'])
            ->orderBy('created_at', 'desc');

        return match ($this->activeTab) {
            'active' => (clone $query)->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])->get(),
            'completed' => (clone $query)->where('status', 'completed')->take(50)->get(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->take(50)->get(),
            default => $query->take(50)->get(),
        };
    }

    public function getTablesProperty()
    {
        return Table::where('is_active', true)->orderBy('section')->orderBy('table_number')->get();
    }

    public function getCategoriesProperty()
    {
        return MenuCategory::where('is_active', true)->with(['items' => fn($q) => $q->where('is_active', true)->where('is_available', true)->orderBy('sort_order')])->orderBy('sort_order')->get();
    }

    public function selectCategory($id)
    {
        $this->selectedCategoryId = $id;
    }

    public function addToCart($itemId)
    {
        $item = MenuItem::with('modifiers')->findOrFail($itemId);
        $key = 'item_' . $itemId . '_' . Str::random(4);

        $this->cart[$key] = [
            'id' => $key,
            'menu_item_id' => $item->id,
            'name' => $item->name,
            'price' => (float) $item->price,
            'quantity' => 1,
            'modifiers' => [],
            'modifiers_price' => 0,
            'total' => (float) $item->price,
            'notes' => '',
        ];
    }

    public function updateCartQty($key, $qty)
    {
        if ($qty < 1) {
            unset($this->cart[$key]);
            return;
        }
        $this->cart[$key]['quantity'] = $qty;
        $this->cart[$key]['total'] = $this->cart[$key]['price'] * $qty + $this->cart[$key]['modifiers_price'];
    }

    public function removeFromCart($key)
    {
        unset($this->cart[$key]);
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('total');
    }

    public function getCartItemsCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function openCreateForm()
    {
        $this->reset(['orderTableId', 'orderType', 'customerName', 'orderNotes', 'cart', 'selectedCategoryId']);
        $this->selectedCategoryId = MenuCategory::where('is_active', true)->first()?->id;
        $this->showCreateForm = true;
    }

    public function createOrder()
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Add at least one item to the order.');
            return;
        }

        $subtotal = $this->cartTotal;
        $tax = round($subtotal * 0.08, 2);
        $total = $subtotal + $tax;

        $order = Order::create([
            'branch_id' => \App\Models\Tenant\Branch::first()->id,
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'table_id' => $this->orderTableId ?: null,
            'user_id' => auth('tenant')->id(),
            'customer_name' => $this->customerName ?: null,
            'order_type' => $this->orderType,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'notes' => $this->orderNotes ?: null,
            'source' => 'pos',
            'ordered_at' => now(),
        ]);

        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'menu_item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'modifiers' => $item['modifiers'] ?: null,
                'modifiers_price' => $item['modifiers_price'],
                'total_price' => $item['total'],
                'notes' => $item['notes'] ?: null,
            ]);
        }

        $this->showCreateForm = false;
        $this->selectedOrderId = $order->id;
        $this->showDetail = true;
    }

    public function viewOrder($id)
    {
        $this->selectedOrderId = $id;
        $this->showDetail = true;
    }

    public function getSelectedOrderProperty()
    {
        return $this->selectedOrderId
            ? Order::with(['items', 'table', 'user', 'payments'])->find($this->selectedOrderId)
            : null;
    }

    public function updateStatus($id, $status)
    {
        $order = Order::findOrFail($id);
        $timestampField = match ($status) {
            'confirmed' => 'confirmed_at',
            'preparing' => 'preparing_at',
            'ready' => 'ready_at',
            'served' => 'served_at',
            'completed' => 'completed_at',
            'cancelled' => 'cancelled_at',
            default => null,
        };

        $data = ['status' => $status];
        if ($timestampField) {
            $data[$timestampField] = now();
        }

        $order->update($data);
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedOrderId = null;
    }

    public function render()
    {
        return view('livewire.manager.orders')
            ->layout('layouts.admin');
    }
}
