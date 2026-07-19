<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\FloorSection;
use App\Models\Tenant\Table;
use Livewire\Component;

class FloorPlanManager extends Component
{
    public $editingTable = null;
    public $editingSection = null;
    public $showTableForm = false;
    public $showSectionForm = false;

    public $tableName = '';
    public $tableCapacity = 4;
    public $tableSection = '';
    public $tableX = 0;
    public $tableY = 0;
    public $tableWidth = 60;
    public $tableHeight = 60;
    public $tableShape = 'rectangle';

    public $sectionName = '';
    public $sectionDescription = '';
    public $sectionColor = '#FF6B35';

    public function getTablesProperty()
    {
        return Table::where('is_active', true)
            ->orderBy('section')
            ->orderBy('table_number')
            ->get();
    }

    public function getSectionsProperty()
    {
        return FloorSection::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getTablesBySectionProperty()
    {
        return $this->tables->groupBy('section');
    }

    public function createTable()
    {
        $this->editingTable = null;
        $this->tableName = '';
        $this->tableCapacity = 4;
        $this->tableSection = $this->sections->first()?->name ?? '';
        $this->tableX = 0;
        $this->tableY = 0;
        $this->tableWidth = 60;
        $this->tableHeight = 60;
        $this->tableShape = 'rectangle';
        $this->showTableForm = true;
    }

    public function editTable($tableId)
    {
        $table = Table::findOrFail($tableId);
        $this->editingTable = $tableId;
        $this->tableName = 'Table ' . $table->table_number;
        $this->tableCapacity = $table->capacity;
        $this->tableSection = $table->section ?? '';
        $this->tableX = $table->x_position ?? 0;
        $this->tableY = $table->y_position ?? 0;
        $this->tableWidth = $table->width;
        $this->tableHeight = $table->height;
        $this->tableShape = $table->shape;
        $this->showTableForm = true;
    }

    public function saveTable()
    {
        $this->validate([
            'tableCapacity' => 'required|integer|min:1',
            'tableSection' => 'required|string',
            'tableX' => 'required|numeric',
            'tableY' => 'required|numeric',
            'tableWidth' => 'required|numeric|min:20',
            'tableHeight' => 'required|numeric|min:20',
            'tableShape' => 'required|in:rectangle,circle,square',
        ]);

        if ($this->editingTable) {
            $table = Table::findOrFail($this->editingTable);
            $table->update([
                'capacity' => $this->tableCapacity,
                'section' => $this->tableSection,
                'x_position' => (float) $this->tableX,
                'y_position' => (float) $this->tableY,
                'width' => (float) $this->tableWidth,
                'height' => (float) $this->tableHeight,
                'shape' => $this->tableShape,
            ]);
        }

        $this->closeTableForm();
        $this->dispatch('toast', type: 'success', message: 'Table updated.');
    }

    public function closeTableForm()
    {
        $this->showTableForm = false;
        $this->editingTable = null;
    }

    public function deleteTable($tableId)
    {
        Table::findOrFail($tableId)->delete();
        $this->dispatch('toast', type: 'success', message: 'Table deleted.');
    }

    public function createSection()
    {
        $this->editingSection = null;
        $this->sectionName = '';
        $this->sectionDescription = '';
        $this->sectionColor = '#FF6B35';
        $this->showSectionForm = true;
    }

    public function editSection($sectionId)
    {
        $section = FloorSection::findOrFail($sectionId);
        $this->editingSection = $sectionId;
        $this->sectionName = $section->name;
        $this->sectionDescription = $section->description ?? '';
        $this->sectionColor = $section->color;
        $this->showSectionForm = true;
    }

    public function saveSection()
    {
        $this->validate([
            'sectionName' => 'required|string|max:255',
            'sectionDescription' => 'nullable|string|max:500',
            'sectionColor' => 'required|regex:/^#[0-9A-F]{6}$/i',
        ]);

        if ($this->editingSection) {
            $section = FloorSection::findOrFail($this->editingSection);
            $section->update([
                'name' => $this->sectionName,
                'description' => $this->sectionDescription,
                'color' => $this->sectionColor,
            ]);
        } else {
            FloorSection::create([
                'name' => $this->sectionName,
                'description' => $this->sectionDescription,
                'color' => $this->sectionColor,
                'sort_order' => $this->sections->max('sort_order') + 1,
            ]);
        }

        $this->closeSectionForm();
        $this->dispatch('toast', type: 'success', message: 'Section updated.');
    }

    public function closeSectionForm()
    {
        $this->showSectionForm = false;
        $this->editingSection = null;
    }

    public function deleteSection($sectionId)
    {
        FloorSection::findOrFail($sectionId)->delete();
        $this->dispatch('toast', type: 'success', message: 'Section deleted.');
    }

    public function render()
    {
        return view('livewire.admin.floor-plan-manager', [
            'sections' => $this->sections,
            'tables' => $this->tables,
            'tablesBySection' => $this->tablesBySection,
        ])->layout('layouts.admin');
    }
}
