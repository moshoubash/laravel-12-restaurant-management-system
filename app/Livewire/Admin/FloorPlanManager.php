<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\Branch;
use App\Models\Tenant\FloorSection;
use App\Models\Tenant\Table;
use Livewire\Component;

class FloorPlanManager extends Component
{
    public $editingTable = null;
    public $editingSection = null;
    public $showTableForm = false;
    public $showSectionForm = false;

    public $tableNumber = '';
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

    public function getSectionBoundsProperty()
    {
        $bounds = [];
        foreach ($this->tablesBySection as $sectionName => $sectionTables) {
            $sectionModel = $this->sections->where('name', $sectionName)->first();
            $minX = $sectionTables->min(fn($t) => ($t->x_position ?? 100)) - 15;
            $minY = $sectionTables->min(fn($t) => ($t->y_position ?? 100)) - 15;
            $maxX = $sectionTables->max(fn($t) => ($t->x_position ?? 100) + ($t->width ?? 60)) + 15;
            $maxY = $sectionTables->max(fn($t) => ($t->y_position ?? 100) + ($t->height ?? 60)) + 15;
            $bounds[] = [
                'name' => $sectionName,
                'color' => $sectionModel ? $sectionModel->color : '#f3f4f6',
                'x' => $minX,
                'y' => $minY,
                'w' => $maxX - $minX,
                'h' => $maxY - $minY,
            ];
        }
        return $bounds;
    }

    public function updateTablePosition($tableId, $x, $y, $section = null)
    {
        $table = Table::findOrFail($tableId);
        $data = [
            'x_position' => (float) $x,
            'y_position' => (float) $y,
        ];
        if ($section) {
            $data['section'] = $section;
        }
        $table->update($data);
        $this->dispatch('toast', type: 'success', message: 'Table ' . $table->table_number . ' moved.');
    }

    public function createTable()
    {
        $this->editingTable = null;
        $this->tableNumber = (Table::max('table_number') ?? 0) + 1;
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
        $this->tableNumber = $table->table_number;
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
            'tableNumber' => 'required|integer|min:1',
            'tableCapacity' => 'required|integer|min:1',
            'tableSection' => 'required|string',
            'tableX' => 'required|numeric',
            'tableY' => 'required|numeric',
            'tableWidth' => 'required|numeric|min:20',
            'tableHeight' => 'required|numeric|min:20',
            'tableShape' => 'required|in:rectangle,circle,square',
        ]);

        $existing = Table::where('table_number', $this->tableNumber)
            ->when($this->editingTable, fn($q) => $q->where('id', '!=', $this->editingTable))
            ->first();
        if ($existing) {
            $this->addError('tableNumber', 'Table number already exists.');
            return;
        }

        $data = [
            'branch_id' => Branch::first()->id ?? throw new \Exception('No branch found. Create a branch first.'),
            'table_number' => $this->tableNumber,
            'capacity' => $this->tableCapacity,
            'section' => $this->tableSection,
            'x_position' => (float) $this->tableX,
            'y_position' => (float) $this->tableY,
            'width' => (float) $this->tableWidth,
            'height' => (float) $this->tableHeight,
            'shape' => $this->tableShape,
        ];

        if ($this->editingTable) {
            Table::findOrFail($this->editingTable)->update($data);
            $this->dispatch('toast', type: 'success', message: 'Table updated.');
        } else {
            if ((float) $this->tableX === 0.0 && (float) $this->tableY === 0.0) {
                $sectionBounds = collect($this->sectionBounds)->firstWhere('name', $this->tableSection);
                if ($sectionBounds) {
                    $data['x_position'] = $sectionBounds['x'] + 30;
                    $data['y_position'] = $sectionBounds['y'] + 40;
                }
            }
            $table = Table::create($data);
            $table->update(['qr_code' => url('/menu?table=' . $table->id)]);
            $this->dispatch('toast', type: 'success', message: 'Table created.');
        }

        $this->closeTableForm();
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
            $this->dispatch('toast', type: 'success', message: 'Section updated.');
        } else {
            FloorSection::create([
                'name' => $this->sectionName,
                'description' => $this->sectionDescription,
                'color' => $this->sectionColor,
                'sort_order' => $this->sections->max('sort_order') + 1,
                'branch_id' => Branch::first()->id ?? throw new \Exception('No branch found. Create a branch first.'),
            ]);
            $this->dispatch('toast', type: 'success', message: 'Section created.');
        }

        $this->closeSectionForm();
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

    public function arrangeTables()
    {
        foreach ($this->sections as $section) {
            $sectionTables = Table::where('section', $section->name)
                ->where('is_active', true)
                ->orderBy('table_number')
                ->get();

            $sectionBounds = collect($this->sectionBounds)->firstWhere('name', $section->name);
            $startX = $sectionBounds ? $sectionBounds['x'] + 30 : 30;
            $startY = $sectionBounds ? $sectionBounds['y'] + 40 : 40;
            $cols = 4;
            $gapX = 90;
            $gapY = 90;

            foreach ($sectionTables as $i => $table) {
                $col = $i % $cols;
                $row = intdiv($i, $cols);
                $table->update([
                    'x_position' => $startX + $col * $gapX,
                    'y_position' => $startY + $row * $gapY,
                ]);
            }
        }

        $this->dispatch('toast', type: 'success', message: 'Tables arranged.');
    }

    public function render()
    {
        return view('livewire.admin.floor-plan-manager', [
            'sections' => $this->sections,
            'tables' => $this->tables,
            'tablesBySection' => $this->tablesBySection,
            'sectionBounds' => $this->sectionBounds,
        ])->layout('layouts.admin');
    }
}
