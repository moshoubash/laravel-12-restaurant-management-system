<?php

namespace App\Livewire\Manager;

use Livewire\Component;

class Reports extends Component
{
    public function render()
    {
        return view('livewire.manager.reports')->layout('layouts.admin');
    }
}
