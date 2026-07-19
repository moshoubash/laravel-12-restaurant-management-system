<?php

namespace App\Livewire\Customer;

use Livewire\Component;

class Loyalty extends Component
{
    public function render()
    {
        return view('livewire.customer.loyalty')->layout('layouts.customer');
    }
}
