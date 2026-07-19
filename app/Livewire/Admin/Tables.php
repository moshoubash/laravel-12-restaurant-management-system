<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Table;
use Livewire\Component;

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
        return Table::when($this->filterSection, fn($q) => $q->where('section', $this->filterSection))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('section')
            ->orderBy('table_number')
            ->get();
    }

    public function getSectionsProperty()
    {
        return Table::whereNotNull('section')->distinct()->pluck('section');
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
            Table::create($data);
        }

        $this->showForm = false;
        $this->editingTable = null;
    }

    public function deleteTable($id)
    {
        Table::findOrFail($id)->delete();
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
