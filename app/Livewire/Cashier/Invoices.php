<?php

namespace App\Livewire\Cashier;

use App\Models\Tenant\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class Invoices extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    public $selectedInvoice = null;
    public $showDetail = false;

    protected $queryString = ['search', 'filterStatus', 'filterDateFrom', 'filterDateTo'];

    public function getInvoicesProperty()
    {
        return Invoice::with('order')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%");
            }))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('issued_at', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('issued_at', '<=', $this->filterDateTo))
            ->latest('issued_at')
            ->paginate(20);
    }

    public function viewInvoice($id)
    {
        $this->selectedInvoice = Invoice::with(['order.items', 'customer'])->find($id);
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedInvoice = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.cashier.invoices')
            ->layout('layouts.cashier');
    }
}
