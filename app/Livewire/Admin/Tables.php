<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Order;
use App\Models\Tenant\Table;
use Livewire\Component;
use Illuminate\Support\Str;

class Tables extends Component
{
    public $showForm = false;
    public $editingTable = null;
    public $tableNumber = '';
    public $capacity = 4;
    public $section = '';
    public $shape = 'rectangle';
    public $width = 60;
    public $height = 60;
    public $xPosition = 0;
    public $yPosition = 0;
    public $filterSection = '';
    public $filterStatus = '';

    public $showDetail = false;
    public $selectedTableId = null;

    protected function rules()
    {
        return [
            'tableNumber' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'section' => 'nullable|string|max:100',
            'shape' => 'required|in:rectangle,circle',
            'width' => 'required|numeric|min:30|max:200',
            'height' => 'required|numeric|min:30|max:200',
            'xPosition' => 'numeric',
            'yPosition' => 'numeric',
        ];
    }

    public function getTablesProperty()
    {
        return Table::with(['orders' => function ($q) {
            $q->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
              ->with('items')
              ->latest('ordered_at');
        }])
            ->when($this->filterSection, fn($q) => $q->where('section', $this->filterSection))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('section')
            ->orderBy('table_number')
            ->get();
    }

    public function getSectionsProperty()
    {
        return Table::whereNotNull('section')->distinct()->pluck('section');
    }

    public function getSelectedTableProperty()
    {
        if (!$this->selectedTableId) return null;
        return Table::with(['orders' => function ($q) {
            $q->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
              ->with(['items', 'user'])
              ->latest('ordered_at');
        }, 'reservations' => function ($q) {
            $q->where('reservation_date', '>=', now()->startOfDay())
              ->where('status', '!=', 'cancelled')
              ->latest('reservation_date');
        }])->find($this->selectedTableId);
    }

    public function viewTable($id)
    {
        $this->selectedTableId = $id;
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedTableId = null;
    }

    public function openForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingTable = $id;
        $this->showForm = true;

        if ($id) {
            $table = Table::findOrFail($id);
            $this->tableNumber = $table->table_number;
            $this->capacity = $table->capacity;
            $this->section = $table->section ?? '';
            $this->shape = $table->shape;
            $this->width = $table->width;
            $this->height = $table->height;
            $this->xPosition = $table->x_position ?? 0;
            $this->yPosition = $table->y_position ?? 0;
        } else {
            $this->tableNumber = (Table::max('table_number') ?? 0) + 1;
            $this->capacity = 4;
            $this->section = '';
            $this->shape = 'rectangle';
            $this->width = 60;
            $this->height = 60;
            $this->xPosition = 0;
            $this->yPosition = 0;
        }
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingTable = null;
        $this->tableNumber = '';
        $this->capacity = 4;
        $this->section = '';
        $this->shape = 'rectangle';
        $this->width = 60;
        $this->height = 60;
        $this->xPosition = 0;
        $this->yPosition = 0;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        $existing = Table::where('table_number', $this->tableNumber)
            ->when($this->editingTable, fn($q) => $q->where('id', '!=', $this->editingTable))
            ->first();
        if ($existing) {
            $this->addError('tableNumber', 'Table number already exists.');
            return;
        }

        $data = [
            'branch_id' => \App\Models\Tenant\Branch::first()->id ?? throw new \Exception('No branch found. Create a branch first.'),
            'table_number' => $this->tableNumber,
            'capacity' => $this->capacity,
            'section' => $this->section ?: null,
            'shape' => $this->shape,
            'width' => $this->width,
            'height' => $this->height,
            'x_position' => $this->xPosition,
            'y_position' => $this->yPosition,
        ];

        if ($this->editingTable) {
            Table::findOrFail($this->editingTable)->update($data);
        } else {
            $table = Table::create($data);
            $table->update(['qr_code' => $this->generateQrUrl($table)]);
        }

        $this->showForm = false;
        $this->editingTable = null;
    }

    public function deleteTable($id)
    {
        Table::findOrFail($id)->delete();
    }

    public function generateQrUrl($table)
    {
        return url('/menu?table=' . $table->id);
    }

    public function regenerateQr($id)
    {
        $table = Table::findOrFail($id);
        $table->update(['qr_code' => $this->generateQrUrl($table)]);
    }

    public function updatePosition($id, $x, $y)
    {
        Table::findOrFail($id)->update(['x_position' => $x, 'y_position' => $y]);
    }

    public function setStatus($id, $status)
    {
        Table::findOrFail($id)->update(['status' => $status]);
    }

    public function render()
    {
        return view('livewire.admin.tables')
            ->layout('layouts.admin');
    }
}
