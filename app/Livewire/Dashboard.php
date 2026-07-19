<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        if ($user->hasRole(['owner', 'admin'])) {
            return view('livewire.dashboard')->layout('layouts.admin');
        }

        if ($user->hasRole('chef')) {
            return view('livewire.dashboard')->layout('layouts.kitchen');
        }

        if ($user->hasRole('waiter')) {
            return view('livewire.dashboard')->layout('layouts.waiter');
        }

        if ($user->hasRole('cashier')) {
            return view('livewire.dashboard')->layout('layouts.cashier');
        }

        return view('livewire.dashboard')->layout('layouts.app');
    }
}
