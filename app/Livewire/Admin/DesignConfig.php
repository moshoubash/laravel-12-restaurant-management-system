<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class DesignConfig extends Component
{
    public function render()
    {
        return view('livewire.admin.design-config')->layout('layouts.admin');
    }
}
