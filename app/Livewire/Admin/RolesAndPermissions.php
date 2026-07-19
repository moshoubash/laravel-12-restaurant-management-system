<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class RolesAndPermissions extends Component
{
    public function render()
    {
        return view('livewire.admin.roles-permissions')->layout('layouts.admin');
    }
}
