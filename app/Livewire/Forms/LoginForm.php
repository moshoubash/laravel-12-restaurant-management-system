<?php

namespace App\Livewire\Forms;

use Livewire\Component;

class LoginForm extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $guard = tenancy()->initialized ? 'tenant' : 'web';

        if (auth($guard)->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended(route('tenant.dashboard'));
        }

        $this->addError('email', 'Invalid credentials.');
    }

    public function render()
    {
        return view('livewire.forms.login-form')
            ->layout('layouts.guest');
    }
}
