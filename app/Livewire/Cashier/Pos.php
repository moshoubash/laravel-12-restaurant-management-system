<?php

namespace App\Livewire\Cashier;

use App\Models\Tenant\Customer;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\MenuCategory;
use App\Models\Tenant\MenuItem;
use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Shift;
use App\Models\Tenant\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Pos extends Component
{
    public $selectedCategoryId = null;
    public $search = '';

    // Cart
    public $cart = [];
    public $orderType = 'dine_in';
    public $orderTableId = '';
    public $customerId = '';
    public $customerName = '';
    public $customerPhone = '';
    public $orderNotes = '';
    public $discountType = '';
    public $discountValue = 0;

    // Checkout / Payment
    public $showCheckout = false;
    public $paymentMethod = 'cash';
    public $paymentAmount = '';
    public $paymentReference = '';

    // Receipt / Success
    public $showSuccess = false;
    public $lastOrderNumber = '';
    public $lastOrderId = null;

    public function mount()
    {
        $first = MenuCategory::where('is_active', true)->orderBy('sort_order')->first();
        if ($first) {
            $this->selectedCategoryId = $first->id;
        }
    }

    public function getCategoriesProperty()
    {
        return MenuCategory::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function getItemsProperty()
    {
        return MenuItem::where('is_active', true)->where('is_available', true)
            ->when($this->selectedCategoryId, fn($q) => $q->where('menu_category_id', $this->selectedCategoryId))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->get();
    }

    public function getTablesProperty()
    {
        return Table::where('is_active', true)->orderBy('section')->orderBy('table_number')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::where('is_active', true)->orderBy('name')->get();
    }

    public function selectCategory($id)
    {
        $this->selectedCategoryId = $id;
        $this->search = '';
    }

    public function addItem($itemId)
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

    public function updateQty($key, $qty)
    {
        if ($qty < 1) {
            unset($this->cart[$key]);
            return;
        }
        $this->cart[$key]['quantity'] = $qty;
        $this->cart[$key]['total'] = round(($this->cart[$key]['price'] * $qty) + $this->cart[$key]['modifiers_price'], 2);
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
    }

    public function getSubtotalProperty()
    {
        return round(collect($this->cart)->sum('total'), 2);
    }

    public function getTaxProperty()
    {
        return round($this->subtotal * 0.08, 2);
    }

    public function getDiscountAmountProperty()
    {
        if ($this->discountType === 'percentage') {
            return round($this->subtotal * ($this->discountValue / 100), 2);
        }
        if ($this->discountType === 'fixed') {
            return min($this->discountValue, $this->subtotal);
        }
        return 0;
    }

    public function getTotalProperty()
    {
        return round(max(0, $this->subtotal + $this->tax - $this->discountAmount), 2);
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->discountType = '';
        $this->discountValue = 0;
        $this->customerId = '';
        $this->customerName = '';
        $this->orderNotes = '';
    }

    public function quickAddCustomer($name, $phone = '')
    {
        $customer = Customer::create([
            'name' => $name,
            'phone' => $phone ?: null,
            'is_active' => true,
        ]);
        $this->customerId = $customer->id;
        $this->customerName = $customer->name;
    }

    public function openCheckout()
    {
        if (empty($this->cart)) {
            $this->addError('cart', 'Cart is empty.');
            return;
        }
        $this->paymentAmount = (string) $this->total;
        $this->showCheckout = true;
    }

    public function processPayment()
    {
        $this->validate([
            'paymentMethod' => 'required|string',
            'paymentAmount' => 'required|numeric|min:' . $this->total,
        ]);

        $activeShift = Shift::where('user_id', Auth::guard('tenant')->id())->where('status', 'open')->first();

        $order = Order::create([
            'branch_id' => \App\Models\Tenant\Branch::first()->id,
            'order_number' => 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'table_id' => $this->orderTableId ?: null,
            'user_id' => Auth::guard('tenant')->id(),
            'shift_id' => $activeShift?->id,
            'customer_id' => $this->customerId ?: null,
            'customer_name' => $this->customerName ?: null,
            'order_type' => $this->orderType,
            'status' => 'completed',
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discountAmount,
            'discount_type' => $this->discountType ?: null,
            'discount_value' => $this->discountValue,
            'total' => $this->total,
            'paid_amount' => $this->total,
            'change_amount' => round((float) $this->paymentAmount - $this->total, 2),
            'payment_status' => 'paid',
            'payment_method' => $this->paymentMethod,
            'notes' => $this->orderNotes ?: null,
            'source' => 'pos',
            'ordered_at' => now(),
            'completed_at' => now(),
        ]);

        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'menu_item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'modifiers' => !empty($item['modifiers']) ? $item['modifiers'] : null,
                'modifiers_price' => $item['modifiers_price'],
                'total_price' => $item['total'],
                'notes' => $item['notes'] ?: null,
            ]);
        }

        Payment::create([
            'order_id' => $order->id,
            'amount' => $this->total,
            'method' => $this->paymentMethod,
            'status' => 'completed',
            'reference' => $this->paymentReference ?: null,
            'paid_at' => now(),
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . date('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            'order_id' => $order->id,
            'customer_id' => $this->customerId ?: null,
            'customer_name' => $this->customerName ?: null,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discountAmount,
            'total' => $this->total,
            'paid_amount' => $this->total,
            'due_amount' => 0,
            'status' => 'paid',
            'issued_at' => now(),
            'paid_at' => now(),
        ]);

        $this->lastOrderNumber = $order->order_number;
        $this->lastOrderId = $order->id;
        $this->showCheckout = false;
        $this->showSuccess = true;
        $this->clearCart();

        $this->dispatch('payment-completed', orderNumber: $order->order_number);
    }

    public function newOrder()
    {
        $this->showSuccess = false;
        $this->lastOrderNumber = '';
        $this->lastOrderId = null;
    }

    public function render()
    {
        return view('livewire.cashier.pos')
            ->layout('layouts.cashier');
    }
}
