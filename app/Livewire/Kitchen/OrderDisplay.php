<?php

namespace App\Livewire\Kitchen;

use Livewire\Component;

class OrderDisplay extends Component
{
    public function render()
    {
        return view('livewire.kitchen.order-display')->layout('layouts.kitchen');
    }
}
