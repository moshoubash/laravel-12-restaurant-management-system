<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Profile extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public bool $showPasswordForm = false;
    public ?string $savedMessage = null;

    public function mount()
    {
        $user = auth('tenant')->user();
        if ($user) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
        }
    }

    public function updated($propertyName)
    {
        $this->savedMessage = null;
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(auth('tenant')->id())],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = auth('tenant')->user();
        if (! $user) return;

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        $this->savedMessage = 'Profile updated successfully.';
    }

    public function savePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password:tenant'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth('tenant')->user();
        if (! $user) return;

        $user->update(['password' => Hash::make($this->new_password)]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->showPasswordForm = false;
        $this->savedMessage = 'Password changed successfully.';
    }

    public function togglePasswordForm()
    {
        $this->showPasswordForm = !$this->showPasswordForm;
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
    }

    public function render()
    {
        $user = auth('tenant')->user();
        $layout = 'layouts.admin';

        if ($user) {
            if ($user->hasRole('chef')) $layout = 'layouts.kitchen';
            elseif ($user->hasRole('waiter')) $layout = 'layouts.waiter';
            elseif ($user->hasRole('cashier')) $layout = 'layouts.cashier';
            elseif ($user->hasRole('manager')) $layout = 'layouts.manager';
        }

        return view('livewire.profile', [
            'user' => $user,
        ])->layout($layout);
    }
}
