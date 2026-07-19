<?php

namespace App\Livewire\Customer;

use App\Models\Tenant\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Orders extends Component
{
    public function getOrdersProperty()
    {
        $user = Auth::guard('tenant')->user();
        return Order::with('items')
            ->where(function ($q) use ($user) {
                $q->where('customer_email', $user->email)
                  ->orWhere('customer_name', $user->name)
                  ->orWhere('user_id', $user->id);
            })
            ->latest('ordered_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.orders')->layout('layouts.customer');
    }
}
