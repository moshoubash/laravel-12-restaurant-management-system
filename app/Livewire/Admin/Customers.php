<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Customer;
use Livewire\Component;

class Customers extends Component
{
    // Search and filters
    public $search = '';
    public $filterBranch = '';

    // Modal state
    public $showForm = false;
    public $editingCustomer = null;

    public $showHistory = false;
    public $historyCustomerId = null;

    // Form fields
    public $branchId = '';
    public $name = '';
    public $email = '';
    public $phone = '';
    public $birthday = '';
    public $anniversary = '';
    public $preferencesInput = '';
    public $allergiesInput = '';
    public $notes = '';
    public $isActive = true;

    protected $rules = [
        'branchId' => 'nullable|exists:branches,id',
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'birthday' => 'nullable|date',
        'anniversary' => 'nullable|date',
        'preferencesInput' => 'nullable|string|max:1000',
        'allergiesInput' => 'nullable|string|max:1000',
        'notes' => 'nullable|string|max:1000',
        'isActive' => 'boolean',
    ];

    public function getCustomersProperty()
    {
        return Customer::with('branch')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->orderBy('name')
            ->get();
    }

    public function getBranchesProperty()
    {
        return Branch::where('is_active', true)->orderBy('name')->get();
    }

    public function getCustomerHistoryProperty()
    {
        if (!$this->historyCustomerId) {
            return null;
        }

        return Customer::with([
            'orders' => fn($q) => $q->orderBy('ordered_at', 'desc'),
            'reservations' => fn($q) => $q->orderBy('reservation_date', 'desc'),
        ])->findOrFail($this->historyCustomerId);
    }

    public function openForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingCustomer = $id;
        $this->showForm = true;

        if ($id) {
            $customer = Customer::findOrFail($id);
            $this->branchId = $customer->branch_id ?? '';
            $this->name = $customer->name;
            $this->email = $customer->email ?? '';
            $this->phone = $customer->phone ?? '';
            $this->birthday = $customer->birthday ? $customer->birthday->format('Y-m-d') : '';
            $this->anniversary = $customer->anniversary ? $customer->anniversary->format('Y-m-d') : '';
            $this->preferencesInput = is_array($customer->preferences) ? implode(', ', $customer->preferences) : '';
            $this->allergiesInput = is_array($customer->allergies) ? implode(', ', $customer->allergies) : '';
            $this->notes = $customer->notes ?? '';
            $this->isActive = $customer->is_active;
        } else {
            $firstBranch = Branch::where('is_active', true)->first();
            $this->branchId = $firstBranch ? $firstBranch->id : '';
            $this->name = '';
            $this->email = '';
            $this->phone = '';
            $this->birthday = '';
            $this->anniversary = '';
            $this->preferencesInput = '';
            $this->allergiesInput = '';
            $this->notes = '';
            $this->isActive = true;
        }
    }

    public function save()
    {
        $this->validate();

        $preferences = array_filter(array_map('trim', explode(',', $this->preferencesInput)));
        $allergies = array_filter(array_map('trim', explode(',', $this->allergiesInput)));

        $data = [
            'branch_id' => $this->branchId ?: null,
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'birthday' => $this->birthday ?: null,
            'anniversary' => $this->anniversary ?: null,
            'preferences' => $preferences,
            'allergies' => $allergies,
            'notes' => $this->notes ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->editingCustomer) {
            Customer::findOrFail($this->editingCustomer)->update($data);
        } else {
            Customer::create($data);
        }

        $this->showForm = false;
        session()->flash('success', 'Customer profile saved successfully.');
    }

    public function openHistory($id)
    {
        $this->historyCustomerId = $id;
        $this->showHistory = true;
    }

    public function closeHistory()
    {
        $this->showHistory = false;
        $this->historyCustomerId = null;
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        session()->flash('success', 'Customer profile deleted.');
    }

    public function render()
    {
        return view('livewire.admin.customers')->layout('layouts.admin');
    }
}
