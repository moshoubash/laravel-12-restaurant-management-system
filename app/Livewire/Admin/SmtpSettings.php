<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class SmtpSettings extends Component
{
    public function render()
    {
        return view('livewire.admin.smtp-settings')->layout('layouts.admin');
    }
}
