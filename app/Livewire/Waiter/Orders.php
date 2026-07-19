<?php

namespace App\Livewire\Waiter;

use Livewire\Component;

class Orders extends Component
{
    public function render()
    {
        return view('livewire.waiter.orders')->layout('layouts.waiter');
    }
}
