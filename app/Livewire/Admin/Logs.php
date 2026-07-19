<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Logs extends Component
{
    public function render()
    {
        return view('livewire.admin.logs')->layout('layouts.admin');
    }
}
