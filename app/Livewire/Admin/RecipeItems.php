<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\InventoryItem;
use App\Models\Tenant\MenuItem;
use App\Models\Tenant\RecipeItem as RecipeItemModel;
use Livewire\Component;

class RecipeItems extends Component
{
    public $selectedMenuItemId = null;
    public $items = [];

    public function mount()
    {
        $first = MenuItem::where('is_active', true)->first();
        if ($first) {
            $this->selectItem($first->id);
        }
    }

    public function getMenuItemsProperty()
    {
        return MenuItem::where('is_active', true)->orderBy('name')->get();
    }

    public function getInventoryItemsProperty()
    {
        return InventoryItem::where('is_active', true)->orderBy('name')->get();
    }

    public function getSelectedItemProperty()
    {
        return $this->selectedMenuItemId ? MenuItem::find($this->selectedMenuItemId) : null;
    }

    public function selectItem($id)
    {
        $this->selectedMenuItemId = $id;
        $this->loadItems();
    }

    protected function loadItems()
    {
        $this->items = RecipeItemModel::with('inventoryItem')
            ->where('menu_item_id', $this->selectedMenuItemId)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'inventory_item_id' => (string) $r->inventory_item_id,
                'inventory_item_name' => $r->inventoryItem?->name ?? '',
                'unit' => $r->unit ?? $r->inventoryItem?->unit ?? '',
                'quantity' => (float) $r->quantity,
            ])
            ->toArray();
    }

    public function addIngredient()
    {
        $this->items[] = ['id' => null, 'inventory_item_id' => '', 'inventory_item_name' => '', 'unit' => '', 'quantity' => 1];
    }

    public function removeIngredient($index)
    {
        $item = $this->items[$index];
        if ($item['id']) {
            RecipeItemModel::find($item['id'])?->delete();
        }
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        if (str_ends_with($key, '.inventory_item_id')) {
            $index = explode('.', $key)[0];
            $inv = InventoryItem::find($value);
            if ($inv) {
                $this->items[$index]['inventory_item_name'] = $inv->name;
                $this->items[$index]['unit'] = $inv->unit;
            }
        }
    }

    public function save()
    {
        if (!$this->selectedMenuItemId) return;

        $existingIds = RecipeItemModel::where('menu_item_id', $this->selectedMenuItemId)->pluck('id');
        $keepIds = [];

        foreach ($this->items as $item) {
            if (empty($item['inventory_item_id'])) continue;

            $data = [
                'menu_item_id' => $this->selectedMenuItemId,
                'inventory_item_id' => $item['inventory_item_id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?: null,
            ];

            if ($item['id']) {
                RecipeItemModel::find($item['id'])?->update($data);
                $keepIds[] = $item['id'];
            } else {
                $r = RecipeItemModel::create($data);
                $keepIds[] = $r->id;
            }
        }

        RecipeItemModel::where('menu_item_id', $this->selectedMenuItemId)
            ->whereNotIn('id', $keepIds)
            ->delete();

        $this->loadItems();
    }

    public function render()
    {
        return view('livewire.admin.recipe-items')
            ->layout('layouts.admin');
    }
}
