<?php

namespace App\Livewire\Cashier;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Order;
use App\Models\Tenant\Shift;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Shifts extends Component
{
    public $showOpenForm = false;
    public $openingCash = 0;
    public $shiftName = '';

    public $showCloseForm = false;
    public $actualCash = 0;
    public $closeNotes = '';

    public $editingShiftId = null;
    public $showDetail = false;

    protected function rules()
    {
        return [
            'openingCash' => 'required|numeric|min:0',
            'shiftName' => 'required|string|max:100',
        ];
    }

    public function getActiveShiftProperty()
    {
        return Shift::where('user_id', Auth::guard('tenant')->id())
            ->where('status', 'open')
            ->first();
    }

    public function getShiftsProperty()
    {
        return Shift::where('user_id', Auth::guard('tenant')->id())
            ->latest('opened_at')
            ->take(30)
            ->get();
    }

    public function openShift()
    {
        $this->validate();

        if ($this->activeShift) {
            $this->addError('shiftName', 'You already have an open shift. Close it first.');
            return;
        }

        Shift::create([
            'branch_id' => Branch::first()->id,
            'user_id' => Auth::guard('tenant')->id(),
            'name' => $this->shiftName,
            'opening_cash' => $this->openingCash,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $this->showOpenForm = false;
        $this->openingCash = 0;
        $this->shiftName = '';
    }

    public function prepareClose()
    {
        $shift = $this->activeShift;
        if (!$shift) return;

        $orders = Order::where('shift_id', $shift->id)->where('payment_status', 'paid')->get();
        $cashTotal = $orders->where('payment_method', 'cash')->sum('total');
        $cardTotal = $orders->where('payment_method', 'card')->sum('total');
        $otherTotal = $orders->where('payment_method', '!=', 'cash')->where('payment_method', '!=', 'card')->sum('total');

        $shift->expected_cash = $cashTotal;
        $shift->card_total = $cardTotal;
        $shift->other_total = $otherTotal;

        $this->actualCash = (float) $cashTotal;
        $this->showCloseForm = true;
        $this->editingShiftId = $shift->id;
    }

    public function closeShift()
    {
        $this->validate(['actualCash' => 'required|numeric|min:0']);

        $shift = Shift::findOrFail($this->editingShiftId);
        $difference = (float) $this->actualCash - (float) $shift->expected_cash;

        $shift->update([
            'actual_cash' => $this->actualCash,
            'difference' => $difference,
            'notes' => $this->closeNotes ?: null,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        $this->showCloseForm = false;
        $this->editingShiftId = null;
        $this->actualCash = 0;
        $this->closeNotes = '';
    }

    public function viewShift($id)
    {
        $this->editingShiftId = $id;
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->editingShiftId = null;
    }

    public function render()
    {
        return view('livewire.cashier.shifts')
            ->layout('layouts.cashier');
    }
}
