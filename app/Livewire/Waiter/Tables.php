<?php

namespace App\Livewire\Waiter;

use Livewire\Component;

class Tables extends Component
{
    public function render()
    {
        return view('livewire.waiter.tables')->layout('layouts.waiter');
    }
}
