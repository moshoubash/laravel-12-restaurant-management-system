<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Tables extends Component
{
    public function render()
    {
        return view('livewire.admin.tables')
            ->layout('layouts.admin');
    }
}
