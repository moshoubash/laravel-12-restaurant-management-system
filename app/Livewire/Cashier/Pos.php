<?php

namespace App\Livewire\Cashier;

use Livewire\Component;

class Pos extends Component
{
    public function render()
    {
        return view('livewire.cashier.pos')->layout('layouts.cashier');
    }
}
