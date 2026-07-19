<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Reservation;
use App\Models\Tenant\Table;
use Livewire\Component;

class Reservations extends Component
{
    // Search and filters
    public $search = '';
    public $filterStatus = '';
    public $filterBranch = '';
    public $filterDate = '';

    // Modal state
    public $showForm = false;
    public $editingReservation = null;

    // Form fields
    public $branchId = '';
    public $tableId = '';
    public $customerId = '';
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $guestCount = 2;
    public $reservationDate = '';
    public $reservationTime = '';
    public $duration = 120;
    public $status = 'pending';
    public $specialRequests = '';
    public $notes = '';
    public $source = 'manual';

    // Cancel modal fields
    public $showCancelModal = false;
    public $cancelReservationId = null;
    public $cancellationReason = '';

    protected $rules = [
        'branchId' => 'required|exists:branches,id',
        'tableId' => 'nullable|exists:tables,id',
        'customerId' => 'nullable|exists:customers,id',
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'nullable|email|max:255',
        'customerPhone' => 'required|string|max:20',
        'guestCount' => 'required|integer|min:1|max:30',
        'reservationDate' => 'required|date',
        'reservationTime' => 'required|string',
        'duration' => 'required|integer|min:15|max:480',
        'status' => 'required|in:pending,confirmed,seated,cancelled',
        'specialRequests' => 'nullable|string|max:1000',
        'notes' => 'nullable|string|max:1000',
        'source' => 'required|in:manual,online',
    ];

    public function mount()
    {
        $branch = Branch::where('is_active', true)->first();
        if ($branch) {
            $this->filterBranch = $branch->id;
        }
    }

    public function getReservationsProperty()
    {
        return Reservation::with('branch', 'table', 'customer')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('customer_name', 'like', "%{$this->search}%")
                        ->orWhere('customer_email', 'like', "%{$this->search}%")
                        ->orWhere('customer_phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterBranch, fn($q) => $q->where('branch_id', $this->filterBranch))
            ->when($this->filterDate, fn($q) => $q->whereDate('reservation_date', $this->filterDate))
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get();
    }

    public function getBranchesProperty()
    {
        return Branch::where('is_active', true)->orderBy('name')->get();
    }

    public function getAvailableTimesProperty()
    {
        $times = [];
        $start = strtotime('12:00');
        $end = strtotime('23:00');

        for ($i = $start; $i <= $end; $i += 1800) {
            $times[] = date('H:i', $i);
        }

        return $times;
    }

    public function getAvailableTablesListProperty()
    {
        if (!$this->branchId || !$this->reservationDate || !$this->reservationTime || !$this->guestCount) {
            return collect();
        }

        $tables = Table::where('branch_id', $this->branchId)
            ->where('is_active', true)
            ->where('capacity', '>=', $this->guestCount)
            ->get();

        $start = \Carbon\Carbon::parse($this->reservationDate . ' ' . $this->reservationTime);
        $end = (clone $start)->addMinutes($this->duration);

        $query = Reservation::where('branch_id', $this->branchId)
            ->where('reservation_date', $this->reservationDate)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('table_id');

        if ($this->editingReservation) {
            $query->where('id', '!=', $this->editingReservation);
        }

        $reservations = $query->get();

        return $tables->filter(function ($table) use ($reservations, $start, $end) {
            $tableReservations = $reservations->where('table_id', $table->id);
            foreach ($tableReservations as $res) {
                $resDateStr = $res->reservation_date instanceof \DateTimeInterface 
                    ? $res->reservation_date->format('Y-m-d') 
                    : $res->reservation_date;
                $resTimeStr = $res->reservation_time instanceof \DateTimeInterface 
                    ? $res->reservation_time->format('H:i:s') 
                    : $res->reservation_time;
                $resStart = \Carbon\Carbon::parse($resDateStr . ' ' . $resTimeStr);
                $resEnd = (clone $resStart)->addMinutes($res->duration);

                if ($start->lt($resEnd) && $end->gt($resStart)) {
                    return false;
                }
            }
            return true;
        });
    }

    public function openForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingReservation = $id;
        $this->showForm = true;

        if ($id) {
            $res = Reservation::findOrFail($id);
            $this->branchId = $res->branch_id;
            $this->tableId = $res->table_id ?? '';
            $this->customerId = $res->customer_id ?? '';
            $this->customerName = $res->customer_name;
            $this->customerEmail = $res->customer_email ?? '';
            $this->customerPhone = $res->customer_phone ?? '';
            $this->guestCount = $res->guest_count;
            $this->reservationDate = $res->reservation_date->format('Y-m-d');
            $this->reservationTime = $res->reservation_time->format('H:i');
            $this->duration = $res->duration;
            $this->status = $res->status;
            $this->specialRequests = $res->special_requests ?? '';
            $this->notes = $res->notes ?? '';
            $this->source = $res->source;
        } else {
            $firstBranch = Branch::where('is_active', true)->first();
            $this->branchId = $firstBranch ? $firstBranch->id : '';
            $this->tableId = '';
            $this->customerId = '';
            $this->customerName = '';
            $this->customerEmail = '';
            $this->customerPhone = '';
            $this->guestCount = 2;
            $this->reservationDate = date('Y-m-d');
            $this->reservationTime = '19:00';
            $this->duration = 120;
            $this->status = 'pending';
            $this->specialRequests = '';
            $this->notes = '';
            $this->source = 'manual';
        }
    }

    public function save()
    {
        $this->validate();

        // Check customer association
        $customer = Customer::where('email', $this->customerEmail)
            ->orWhere('phone', $this->customerPhone)
            ->first();

        if (!$customer && $this->customerName) {
            $customer = Customer::create([
                'branch_id' => $this->branchId,
                'name' => $this->customerName,
                'email' => $this->customerEmail ?: null,
                'phone' => $this->customerPhone,
                'total_visits' => 1,
                'is_active' => true,
            ]);
        }

        $data = [
            'branch_id' => $this->branchId,
            'table_id' => $this->tableId ?: null,
            'customer_id' => $customer?->id,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail ?: null,
            'customer_phone' => $this->customerPhone,
            'guest_count' => $this->guestCount,
            'reservation_date' => $this->reservationDate,
            'reservation_time' => $this->reservationTime,
            'duration' => $this->duration,
            'status' => $this->status,
            'special_requests' => $this->specialRequests ?: null,
            'notes' => $this->notes ?: null,
            'source' => $this->source,
        ];

        if ($this->editingReservation) {
            $res = Reservation::findOrFail($this->editingReservation);
            
            // Check seated state
            if ($this->status === 'seated' && $res->status !== 'seated' && $this->tableId) {
                Table::findOrFail($this->tableId)->update(['status' => 'occupied']);
            }

            $res->update($data);
        } else {
            if ($this->status === 'seated' && $this->tableId) {
                Table::findOrFail($this->tableId)->update(['status' => 'occupied']);
            }
            Reservation::create($data);
        }

        $this->showForm = false;
        session()->flash('success', 'Reservation saved successfully.');
    }

    public function confirmReservation($id)
    {
        $res = Reservation::findOrFail($id);
        $res->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
        session()->flash('success', 'Reservation has been confirmed.');
    }

    public function seatReservation($id)
    {
        $res = Reservation::findOrFail($id);
        $res->update([
            'status' => 'seated',
        ]);
        if ($res->table_id) {
            Table::findOrFail($res->table_id)->update(['status' => 'occupied']);
        }
        session()->flash('success', 'Guests checked-in and seated.');
    }

    public function openCancelModal($id)
    {
        $this->cancelReservationId = $id;
        $this->cancellationReason = '';
        $this->showCancelModal = true;
    }

    public function cancelReservation()
    {
        $this->validate([
            'cancellationReason' => 'required|string|max:255',
        ]);

        $res = Reservation::findOrFail($this->cancelReservationId);
        $res->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $this->cancellationReason,
        ]);

        if ($res->table_id) {
            Table::findOrFail($res->table_id)->update(['status' => 'available']);
        }

        $this->showCancelModal = false;
        session()->flash('success', 'Reservation has been cancelled.');
    }

    public function deleteReservation($id)
    {
        $res = Reservation::findOrFail($id);
        if ($res->table_id) {
            Table::findOrFail($res->table_id)->update(['status' => 'available']);
        }
        $res->delete();
        session()->flash('success', 'Reservation deleted.');
    }

    public function render()
    {
        return view('livewire.admin.reservations')->layout('layouts.admin');
    }
}
