<?php

namespace App\Livewire\Manager;

use App\Models\Tenant\FloorSection;
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
            $minX = $sectionTables->min(fn($t) => ($t->x_position ?? 100)) - 15;
            $minY = $sectionTables->min(fn($t) => ($t->y_position ?? 100)) - 15;
            $maxX = $sectionTables->max(fn($t) => ($t->x_position ?? 100) + ($t->width ?? 60)) + 15;
            $maxY = $sectionTables->max(fn($t) => ($t->y_position ?? 100) + ($t->height ?? 60)) + 15;
            $bounds[] = [
                'name' => $sectionName,
                'x' => $minX,
                'y' => $minY,
                'w' => $maxX - $minX,
                'h' => $maxY - $minY,
            ];
        }
        return $bounds;
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

    public function arrangeTables()
    {
        $sections = $this->sections;
        foreach ($sections as $section) {
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
        return view('livewire.manager.floor-plan', [
            'sectionBounds' => $this->sectionBounds,
        ])->layout('layouts.manager');
    }
}
