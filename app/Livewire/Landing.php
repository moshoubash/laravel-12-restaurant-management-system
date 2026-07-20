<?php

namespace App\Livewire;

use Livewire\Component;

class Landing extends Component
{
    public function mount()
    {
        if (auth('tenant')->check()) {
            $user = auth('tenant')->user();
            if ($user->hasRole('customer')) {
                $this->redirect(route('tenant.customer.menu'), navigate: true);
            } else {
                $this->redirect(route('tenant.dashboard'), navigate: true);
            }
        }
    }

    public function render()
    {
        return view('livewire.landing')
            ->layout('layouts.public');
    }
}
