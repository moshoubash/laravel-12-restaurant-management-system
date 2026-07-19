<?php

namespace App\Livewire\Manager;

use Livewire\Component;

class Inventory extends Component
{
    public function render()
    {
        return view('livewire.manager.inventory')->layout('layouts.admin');
    }
}
