<?php

namespace App\Livewire\Manager;

use Livewire\Component;

class Orders extends Component
{
    public function render()
    {
        return view('livewire.manager.orders')->layout('layouts.admin');
    }
}
