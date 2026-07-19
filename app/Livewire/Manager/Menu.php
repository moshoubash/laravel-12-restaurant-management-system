<?php

namespace App\Livewire\Manager;

use Livewire\Component;

class Menu extends Component
{
    public function render()
    {
        return view('livewire.manager.menu')->layout('layouts.admin');
    }
}
