<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Supplier;
use Livewire\Component;

class Suppliers extends Component
{
    public $showForm = false;
    public $editingSupplier = null;
    public $name = '';
    public $contactPerson = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $paymentTerms = '';

    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'contactPerson' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'paymentTerms' => 'nullable|string|max:100',
        ];
    }

    public function getSuppliersProperty()
    {
        return Supplier::when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    public function openForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingSupplier = $id;
        $this->showForm = true;

        if ($id) {
            $s = Supplier::findOrFail($id);
            $this->name = $s->name;
            $this->contactPerson = $s->contact_person ?? '';
            $this->email = $s->email ?? '';
            $this->phone = $s->phone ?? '';
            $this->address = $s->address ?? '';
            $this->paymentTerms = $s->payment_terms ?? '';
        } else {
            $this->name = '';
            $this->contactPerson = '';
            $this->email = '';
            $this->phone = '';
            $this->address = '';
            $this->paymentTerms = '';
        }
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingSupplier = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'branch_id' => Branch::first()->id,
            'name' => $this->name,
            'contact_person' => $this->contactPerson ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
            'payment_terms' => $this->paymentTerms ?: null,
        ];

        if ($this->editingSupplier) {
            Supplier::findOrFail($this->editingSupplier)->update($data);
        } else {
            Supplier::create($data);
        }

        $this->showForm = false;
        $this->editingSupplier = null;
    }

    public function deleteSupplier($id)
    {
        Supplier::findOrFail($id)->delete();
    }

    public function toggleActive($id)
    {
        $s = Supplier::findOrFail($id);
        $s->update(['is_active' => !$s->is_active]);
    }

    public function render()
    {
        return view('livewire.admin.suppliers')
            ->layout('layouts.admin');
    }
}
