<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Reports extends Component
{
    public function render()
    {
        return view('livewire.admin.reports')->layout('layouts.admin');
    }
}
