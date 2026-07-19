<?php

namespace App\Livewire\Customer;

use Livewire\Component;

class Orders extends Component
{
    public function render()
    {
        return view('livewire.customer.orders')->layout('layouts.customer');
    }
}
