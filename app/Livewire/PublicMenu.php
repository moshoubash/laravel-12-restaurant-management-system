<?php

namespace App\Livewire;

use App\Mail\OrderConfirmation;
use App\Models\Tenant\MenuCategory;
use App\Models\Tenant\MenuItem;
use App\Models\Tenant\MenuItemModifier;
use App\Models\Tenant\Order;
use App\Models\Tenant\OrderItem;
use App\Models\Tenant\Table;
use App\Support\NotificationHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class PublicMenu extends Component
{
    public $selectedCategoryId = null;
    public $tableId = null;

    // Cart
    public $cart = [];

    // Checkout
    public $showCheckout = false;
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $orderNotes = '';
    public $orderType = 'takeaway';

    // Modifier selection
    public $showModifierPicker = false;
    public $modifierItemId = null;
    public $modifierItemKey = null;
    public $selectedModifiers = [];
    public $modifierGroups = [];

    public $orderNumber = '';

    public function mount()
    {
        $this->selectedCategoryId = request('category');
        $this->tableId = request('table');

        if ($this->tableId) {
            $this->orderType = 'dine_in';
        }

        if (!$this->selectedCategoryId) {
            $first = MenuCategory::where('is_active', true)->orderBy('sort_order')->first();
            if ($first) {
                $this->selectedCategoryId = $first->id;
            }
        }

        $this->restoreCart();
    }

    public function getCategoriesProperty()
    {
        return MenuCategory::where('is_active', true)
            ->withCount(['items' => fn($q) => $q->where('is_active', true)->where('is_available', true)])
            ->orderBy('sort_order')
            ->get();
    }

    public function getItemsProperty()
    {
        if (!$this->selectedCategoryId) return collect();
        return MenuItem::with('modifiers')
            ->where('menu_category_id', $this->selectedCategoryId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getTableProperty()
    {
        return $this->tableId ? Table::find($this->tableId) : null;
    }

    public function selectCategory($id)
    {
        $this->selectedCategoryId = $id;
    }

    public function addToCart($itemId)
    {
        $item = MenuItem::with('modifiers')->findOrFail($itemId);

        if ($item->modifiers->isNotEmpty()) {
            $this->modifierItemId = $itemId;
            $this->modifierItemKey = null;
            $this->modifierGroups = $item->modifiers;
            $this->selectedModifiers = [];
            $this->showModifierPicker = true;
            return;
        }

        $this->addItemToCart($item);
    }

    public function confirmModifiers()
    {
        $item = MenuItem::with('modifiers')->findOrFail($this->modifierItemId);
        $this->addItemToCart($item, $this->selectedModifiers);
        $this->showModifierPicker = false;
        $this->modifierItemId = null;
        $this->modifierGroups = [];
        $this->selectedModifiers = [];
    }

    public function cancelModifiers()
    {
        $this->showModifierPicker = false;
        $this->modifierItemId = null;
        $this->modifierGroups = [];
        $this->selectedModifiers = [];
    }

    protected function addItemToCart($item, $selectedModifiers = [])
    {
        $modifiersPrice = 0;
        $modifierNames = [];

        if ($selectedModifiers) {
            foreach ($selectedModifiers as $groupId => $optionIndexes) {
                $group = $item->modifiers->find($groupId);
                if (!$group) continue;
                $options = is_array($optionIndexes) ? $optionIndexes : [$optionIndexes];
                foreach ($options as $idx) {
                    if (isset($group->options[$idx])) {
                        $modifierNames[] = $group->options[$idx]['name'];
                        $modifiersPrice += $group->options[$idx]['price'] ?? 0;
                    }
                }
            }
        }

        $key = 'item_' . $item->id . '_' . Str::random(4);
        $this->cart[$key] = [
            'id' => $key,
            'menu_item_id' => $item->id,
            'name' => $item->name,
            'price' => (float) $item->price,
            'quantity' => 1,
            'modifiers' => $modifierNames,
            'modifiers_price' => $modifiersPrice,
            'total' => (float) $item->price + $modifiersPrice,
        ];

        $this->saveCart();
        $this->dispatch('added-to-cart', name: $item->name);
    }

    public function updateQty($key, $qty)
    {
        if ($qty < 1) {
            unset($this->cart[$key]);
        } else {
            $this->cart[$key]['quantity'] = $qty;
            $this->cart[$key]['total'] = ($this->cart[$key]['price'] + $this->cart[$key]['modifiers_price']) * $qty;
        }
        $this->saveCart();
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
        $this->saveCart();
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum('total');
    }

    public function getCartCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function openCheckout()
    {
        if (empty($this->cart)) return;
        $this->showCheckout = true;
    }

    public function placeOrder()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'nullable|email|max:255',
            'customerPhone' => 'nullable|string|max:20',
            'orderNotes' => 'nullable|string|max:500',
        ]);

        if (empty($this->cart)) return;

        $subtotal = $this->cartTotal;
        $tax = round($subtotal * 0.08, 2);
        $total = $subtotal + $tax;

        $order = Order::create([
            'branch_id' => \App\Models\Tenant\Branch::first()->id,
            'order_number' => 'ONL-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'table_id' => $this->tableId ?: null,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail ?: null,
            'customer_phone' => $this->customerPhone ?: null,
            'order_type' => $this->orderType,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'notes' => $this->orderNotes ?: null,
            'source' => 'online',
            'ordered_at' => now(),
        ]);

        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'menu_item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'modifiers' => !empty($item['modifiers']) ? json_encode($item['modifiers']) : null,
                'modifiers_price' => $item['modifiers_price'],
                'total_price' => $item['total'],
            ]);
        }

        $this->cart = [];
        $this->saveCart();
        $this->orderNumber = $order->order_number;

        if ($this->customerEmail) {
            try {
                Mail::to($this->customerEmail)->queue(new OrderConfirmation($order));
            } catch (\Throwable $e) {
                logger()->warning('Failed to send order confirmation email: ' . $e->getMessage());
            }
        }

        $tableInfo = $order->table?->table_number ? 'Table ' . $order->table->table_number : 'Takeaway';
        NotificationHelper::sendToRole('chef', 'New Online Order #' . $order->order_number, $tableInfo . ' — ' . count($this->cart) . ' items', 'order', route('tenant.kitchen.orders'));

        $this->dispatch('confirmed-order');
    }

    public function backToMenu()
    {
        $this->showCheckout = false;
        $this->orderNumber = '';
        $this->customerName = '';
        $this->customerEmail = '';
        $this->customerPhone = '';
        $this->orderNotes = '';
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->saveCart();
    }

    protected function saveCart()
    {
        session(['public_cart' => $this->cart]);
    }

    protected function restoreCart()
    {
        $this->cart = session('public_cart', []);
    }

    public function render()
    {
        return view('livewire.public-menu')
            ->layout('layouts.public');
    }
}
