<?php

namespace App\Livewire\Cashier;

use Livewire\Component;

class Invoices extends Component
{
    public function render()
    {
        return view('livewire.cashier.invoices')->layout('layouts.cashier');
    }
}
