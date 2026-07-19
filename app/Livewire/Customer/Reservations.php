<?php

namespace App\Livewire\Customer;

use Livewire\Component;

class Reservations extends Component
{
    public function render()
    {
        return view('livewire.customer.reservations')->layout('layouts.customer');
    }
}
