<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Branch;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Staff extends Component
{
    public $showForm = false;
    public $editingUser = null;
    public $search = '';
    public $filterRole = '';
    public $filterBranch = '';

    public $name = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';
    public $phone = '';
    public $pin = '';
    public $branchId = '';
    public $isActive = true;
    public $selectedRoles = [];

    public function getStaffProperty()
    {
        return User::with('roles', 'branch')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->when($this->filterRole, fn($q) => $q->role($this->filterRole))
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->orderBy('name')
            ->get();
    }

    public function getRolesListProperty()
    {
        return ['owner', 'admin', 'manager', 'chef', 'waiter', 'cashier', 'customer'];
    }

    public function getBranchesProperty()
    {
        return Branch::orderBy('name')->get();
    }

    public function openForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingUser = $id;
        $this->showForm = true;

        if ($id) {
            $user = User::with('roles')->findOrFail($id);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->password = '';
            $this->passwordConfirmation = '';
            $this->phone = $user->phone ?? '';
            $this->pin = $user->pin ?? '';
            $this->branchId = (string) $user->branch_id ?? '';
            $this->isActive = $user->is_active;
            $this->selectedRoles = $user->roles->pluck('name')->toArray();
        } else {
            $this->name = '';
            $this->email = '';
            $this->password = '';
            $this->passwordConfirmation = '';
            $this->phone = '';
            $this->pin = '';
            $this->branchId = '';
            $this->isActive = true;
            $this->selectedRoles = [];
        }
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingUser = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->passwordConfirmation = '';
        $this->phone = '';
        $this->pin = '';
        $this->branchId = '';
        $this->isActive = true;
        $this->selectedRoles = [];
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique(User::class, 'email')->ignore($this->editingUser),
            ],
            'password' => $this->editingUser ? 'nullable|min:8' : 'required|min:8',
            'passwordConfirmation' => $this->editingUser ? 'nullable|same:password' : 'required|same:password',
            'phone' => 'nullable|string|max:20',
            'pin' => 'nullable|string|max:6',
            'branchId' => 'nullable|exists:branches,id',
            'isActive' => 'boolean',
            'selectedRoles' => 'required|array|min:1',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'pin' => $this->pin ?: null,
            'branch_id' => $this->branchId ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingUser) {
            $user = User::findOrFail($this->editingUser);
            $user->update($data);
            $user->syncRoles($this->selectedRoles);
        } else {
            $data['password'] = Hash::make($this->password);
            $user = User::create($data);
            $user->assignRole($this->selectedRoles);
        }

        $this->cancelForm();
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth('tenant')->id()) {
            session()->flash('error', 'Cannot delete yourself.');
            return;
        }
        $user->delete();
    }

    public function render()
    {
        return view('livewire.admin.staff')
            ->layout('layouts.admin');
    }
}
