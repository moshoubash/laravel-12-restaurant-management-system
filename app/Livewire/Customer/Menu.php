<?php

namespace App\Livewire\Customer;

use Livewire\Component;

class Menu extends Component
{
    public function render()
    {
        return view('livewire.customer.menu')->layout('layouts.customer');
    }
}
