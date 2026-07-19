<?php

namespace App\Livewire\Customer;

use App\Models\Tenant\Order;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';

    public $showEdit = false;

    public function mount()
    {
        $this->loadProfile();
    }

    protected function loadProfile()
    {
        $user = Auth::guard('tenant')->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
    }

    public function openEdit()
    {
        $this->showEdit = true;
    }

    public function cancelEdit()
    {
        $this->showEdit = false;
        $this->loadProfile();
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::guard('tenant')->user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $this->showEdit = false;
    }

    public function getRecentOrdersProperty()
    {
        return Order::where('customer_email', Auth::guard('tenant')->user()->email)
            ->orWhere('customer_name', Auth::guard('tenant')->user()->name)
            ->latest('ordered_at')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.customer.profile')->layout('layouts.customer');
    }
}
