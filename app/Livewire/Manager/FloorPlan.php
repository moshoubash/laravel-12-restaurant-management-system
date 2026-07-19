<?php

namespace App\Livewire\Manager;

use App\Models\Tenant\Table;
use Livewire\Component;

class FloorPlan extends Component
{
    public $selectedTableId = null;
    public $showTableModal = false;
    public $tableData = [];

    public function getTablesProperty()
    {
        return Table::where('is_active', true)
            ->orderBy('section')
            ->orderBy('table_number')
            ->get();
    }

    public function getTablesBySectionProperty()
    {
        return $this->tables->groupBy('section');
    }

    public function selectTable($tableId)
    {
        $table = Table::findOrFail($tableId);
        $this->selectedTableId = $tableId;
        $this->tableData = $table->toArray();
        $this->showTableModal = true;
    }

    public function closeTableModal()
    {
        $this->showTableModal = false;
        $this->selectedTableId = null;
        $this->tableData = [];
    }

    public function updateTablePosition($tableId, $x, $y)
    {
        $table = Table::findOrFail($tableId);
        $table->update([
            'x_position' => (float) $x,
            'y_position' => (float) $y,
        ]);
        $this->dispatch('toast', type: 'success', message: 'Table position updated.');
    }

    public function render()
    {
        return view('livewire.manager.floor-plan')->layout('layouts.admin');
    }
}
