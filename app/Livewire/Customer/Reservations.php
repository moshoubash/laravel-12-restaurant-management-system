<?php

namespace App\Livewire\Customer;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Reservation;
use App\Support\NotificationHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Reservations extends Component
{
    // Form fields
    public $branchId = '';
    public $guestCount = 2;
    public $reservationDate = '';
    public $reservationTime = '';
    public $specialRequests = '';

    public $showBookModal = false;
    public $isAvailable = true;

    protected $rules = [
        'branchId' => 'required|exists:branches,id',
        'guestCount' => 'required|integer|min:1|max:30',
        'reservationDate' => 'required|date|after_or_equal:today',
        'reservationTime' => 'required|string',
        'specialRequests' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $branch = Branch::where('is_active', true)->first();
        if ($branch) {
            $this->branchId = $branch->id;
        }
        $this->reservationDate = date('Y-m-d');
        $this->reservationTime = '19:00';
        $this->checkAvailability();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if (in_array($propertyName, ['branchId', 'guestCount', 'reservationDate', 'reservationTime'])) {
            $this->checkAvailability();
        }
    }

    public function checkAvailability()
    {
        if ($this->branchId && $this->guestCount && $this->reservationDate && $this->reservationTime) {
            $table = Reservation::getAvailableTable(
                $this->branchId,
                $this->reservationDate,
                $this->reservationTime,
                $this->guestCount
            );
            $this->isAvailable = $table !== null;
        } else {
            $this->isAvailable = false;
        }
    }

    public function getReservationsProperty()
    {
        $user = Auth::guard('tenant')->user();
        return Reservation::with('branch', 'table')
            ->where(function ($q) use ($user) {
                $q->where('customer_email', $user->email);
                if ($user->phone) {
                    $q->orWhere('customer_phone', $user->phone);
                }
            })
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

    public function cancelBooking()
    {
        $this->resetErrorBag();
        $this->showBookModal = false;
        $this->specialRequests = '';
    }

    public function openBookModal()
    {
        $this->resetErrorBag();
        $this->specialRequests = '';
        $branch = Branch::where('is_active', true)->first();
        if ($branch) {
            $this->branchId = $branch->id;
        }
        $this->reservationDate = date('Y-m-d');
        $this->reservationTime = '19:00';
        $this->checkAvailability();
        $this->showBookModal = true;
    }

    public function book()
    {
        $this->validate();
        $this->checkAvailability();

        if (!$this->isAvailable) {
            session()->flash('error', 'No tables are available for the selected slot.');
            return;
        }

        $user = Auth::guard('tenant')->user();
        $customer = Customer::where('email', $user->email)->first();

        $table = Reservation::getAvailableTable(
            $this->branchId,
            $this->reservationDate,
            $this->reservationTime,
            $this->guestCount
        );

        Reservation::create([
            'branch_id' => $this->branchId,
            'table_id' => $table?->id,
            'customer_id' => $customer?->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? 'N/A',
            'guest_count' => $this->guestCount,
            'reservation_date' => $this->reservationDate,
            'reservation_time' => $this->reservationTime,
            'duration' => 120,
            'status' => 'pending',
            'special_requests' => $this->specialRequests ?: null,
            'source' => 'online',
        ]);

        if ($customer) {
            $customer->increment('total_visits');
        } else {
            Customer::create([
                'branch_id' => $this->branchId,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'total_visits' => 1,
                'is_active' => true,
            ]);
        }

        NotificationHelper::sendToRole(
            'manager',
            'New Online Reservation',
            $user->name . ' — ' . $this->guestCount . ' guests on ' . $this->reservationDate . ' at ' . $this->reservationTime,
            'reservation',
            route('tenant.admin.reservations')
        );

        $this->showBookModal = false;
        session()->flash('success', 'Reservation booked successfully! It is currently pending confirmation.');
    }

    public function cancelReservation($id)
    {
        $user = Auth::guard('tenant')->user();
        $reservation = Reservation::findOrFail($id);

        // Ensure user owns this reservation
        if ($reservation->customer_email !== $user->email && $reservation->customer_phone !== $user->phone) {
            abort(403);
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Cancelled by customer',
        ]);

        NotificationHelper::sendToRole(
            'manager',
            'Reservation Cancelled by Customer',
            $user->name . ' — ' . $reservation->reservation_date . ' at ' . $reservation->reservation_time,
            'warning',
            route('tenant.admin.reservations')
        );

        session()->flash('success', 'Reservation has been cancelled.');
    }

    public function render()
    {
        return view('livewire.customer.reservations')->layout('layouts.customer');
    }
}
