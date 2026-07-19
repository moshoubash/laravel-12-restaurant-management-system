<?php

namespace App\Livewire\Cashier;

use Livewire\Component;

class Shifts extends Component
{
    public function render()
    {
        return view('livewire.cashier.shifts')->layout('layouts.cashier');
    }
}
