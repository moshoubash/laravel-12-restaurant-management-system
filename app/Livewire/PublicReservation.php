<?php

namespace App\Livewire;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Reservation;
use Livewire\Component;

class PublicReservation extends Component
{
    public $branchId = '';
    public $guestCount = 2;
    public $reservationDate = '';
    public $reservationTime = '';
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $specialRequests = '';

    public $isBooked = false;
    public $successDetails = [];
    public $isAvailable = true;

    protected $rules = [
        'branchId' => 'required|exists:branches,id',
        'guestCount' => 'required|integer|min:1|max:30',
        'reservationDate' => 'required|date|after_or_equal:today',
        'reservationTime' => 'required|string',
        'customerName' => 'required|string|max:255',
        'customerEmail' => 'nullable|email|max:255',
        'customerPhone' => 'required|string|max:20',
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

    public function getBranchesProperty()
    {
        return Branch::where('is_active', true)->orderBy('name')->get();
    }

    public function getAvailableTimesProperty()
    {
        $times = [];
        $start = strtotime('12:00');
        $end = strtotime('23:00');

        for ($i = $start; $i <= $end; $i += 1800) { // every 30 minutes
            $times[] = date('H:i', $i);
        }

        return $times;
    }

    public function book()
    {
        $this->validate();
        $this->checkAvailability();

        if (!$this->isAvailable) {
            session()->flash('error', 'Unfortunately, there is no available table for the selected date, time, or guest count.');
            return;
        }

        $table = Reservation::getAvailableTable(
            $this->branchId,
            $this->reservationDate,
            $this->reservationTime,
            $this->guestCount
        );

        $reservation = Reservation::create([
            'branch_id' => $this->branchId,
            'table_id' => $table?->id,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail ?: null,
            'customer_phone' => $this->customerPhone,
            'guest_count' => $this->guestCount,
            'reservation_date' => $this->reservationDate,
            'reservation_time' => $this->reservationTime,
            'duration' => 120, // default 2 hours
            'status' => 'pending',
            'special_requests' => $this->specialRequests ?: null,
            'source' => 'online',
        ]);

        // Also check if customer account or customer record should be created/updated
        $customer = \App\Models\Tenant\Customer::where('email', $this->customerEmail)
            ->orWhere('phone', $this->customerPhone)
            ->first();

        if ($customer) {
            $customer->increment('total_visits');
        } else {
            \App\Models\Tenant\Customer::create([
                'branch_id' => $this->branchId,
                'name' => $this->customerName,
                'email' => $this->customerEmail ?: null,
                'phone' => $this->customerPhone,
                'total_visits' => 1,
                'is_active' => true,
            ]);
        }

        $this->isBooked = true;
        $this->successDetails = [
            'id' => $reservation->id,
            'customer_name' => $reservation->customer_name,
            'branch_name' => $reservation->branch->name,
            'table_number' => $table?->table_number,
            'date' => $reservation->reservation_date->format('Y-m-d'),
            'time' => $reservation->reservation_time->format('H:i'),
            'guest_count' => $reservation->guest_count,
        ];

        $this->reset(['customerName', 'customerEmail', 'customerPhone', 'specialRequests']);
    }

    public function resetBooking()
    {
        $this->isBooked = false;
        $this->successDetails = [];
        $this->checkAvailability();
    }

    public function render()
    {
        return view('livewire.public-reservation')
            ->layout('layouts.public');
    }
}
