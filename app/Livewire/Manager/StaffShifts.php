<?php

namespace App\Livewire\Manager;

use App\Models\Tenant\StaffShift;
use App\Models\Tenant\User;
use Livewire\Component;

class StaffShifts extends Component
{
    public $filterDate = '';
    public $filterStatus = '';

    public function getShiftsProperty()
    {
        return StaffShift::with('user', 'branch')
            ->when($this->filterDate, fn($q) => $q->whereDate('clock_in', $this->filterDate))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest('clock_in')
            ->get();
    }

    public function getStaffProperty()
    {
        return User::with('roles')->where('is_active', true)->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.manager.staff-shifts')
            ->layout('layouts.admin');
    }
}
