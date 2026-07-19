<?php

namespace App\Livewire\Admin;

use App\Models\Tenant\MenuCategory;
use App\Models\Tenant\MenuItem;
use App\Models\Tenant\MenuItemModifier;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Menu extends Component
{
    use WithPagination;

    public $selectedCategoryId = null;
    public $search = '';
    public $showCategoryForm = false;
    public $showItemForm = false;
    public $showModifierForm = false;
    public $editingCategory = null;
    public $editingItem = null;
    public $editingModifier = null;

    public $categoryName = '';
    public $categoryDescription = '';
    public $categorySortOrder = 0;

    public $itemCategoryId = '';
    public $itemName = '';
    public $itemDescription = '';
    public $itemPrice = '';
    public $itemComparePrice = '';
    public $itemCost = '';
    public $itemPreparationTime = '';
    public $itemIsAvailable = true;
    public $itemIsFeatured = false;
    public $itemAllergens = [];
    public $itemDietaryLabels = [];

    public $modifierName = '';
    public $modifierType = 'single';
    public $modifierOptions = '';
    public $modifierIsRequired = false;
    public $modifierMaxSelections = 1;

    protected function rules()
    {
        return [
            'categoryName' => 'required|string|max:255',
            'categoryDescription' => 'nullable|string',
            'categorySortOrder' => 'integer|min:0',
            'itemCategoryId' => 'required|exists:menu_categories,id',
            'itemName' => 'required|string|max:255',
            'itemDescription' => 'nullable|string',
            'itemPrice' => 'required|numeric|min:0',
            'itemComparePrice' => 'nullable|numeric|min:0',
            'itemCost' => 'nullable|numeric|min:0',
            'itemPreparationTime' => 'nullable|integer|min:0',
            'itemIsAvailable' => 'boolean',
            'itemIsFeatured' => 'boolean',
        ];
    }

    public function mount()
    {
        $first = MenuCategory::orderBy('sort_order')->first();
        if ($first) {
            $this->selectedCategoryId = $first->id;
        }
    }

    public function getCategoriesProperty()
    {
        return MenuCategory::withCount('items')
            ->orderBy('sort_order')
            ->get();
    }

    public function getItemsProperty()
    {
        if (!$this->selectedCategoryId) {
            return collect();
        }
        return MenuItem::where('menu_category_id', $this->selectedCategoryId)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('sort_order')
            ->get();
    }

    public function selectCategory($id)
    {
        $this->selectedCategoryId = $id;
        $this->search = '';
    }

    public function openCategoryForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingCategory = $id;
        $this->showCategoryForm = true;

        if ($id) {
            $cat = MenuCategory::findOrFail($id);
            $this->categoryName = $cat->name;
            $this->categoryDescription = $cat->description;
            $this->categorySortOrder = $cat->sort_order;
        } else {
            $this->categoryName = '';
            $this->categoryDescription = '';
            $this->categorySortOrder = MenuCategory::max('sort_order') + 1;
        }
    }

    public function cancelCategoryForm()
    {
        $this->showCategoryForm = false;
        $this->editingCategory = null;
        $this->categoryName = '';
        $this->categoryDescription = '';
        $this->categorySortOrder = 0;
        $this->resetErrorBag();
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'categoryDescription' => 'nullable|string',
            'categorySortOrder' => 'integer|min:0',
        ]);

        if ($this->editingCategory) {
            $cat = MenuCategory::findOrFail($this->editingCategory);
            $cat->update([
                'name' => $this->categoryName,
                'slug' => Str::slug($this->categoryName),
                'description' => $this->categoryDescription,
                'sort_order' => $this->categorySortOrder,
            ]);
        } else {
            $cat = MenuCategory::create([
                'name' => $this->categoryName,
                'slug' => Str::slug($this->categoryName),
                'description' => $this->categoryDescription,
                'sort_order' => $this->categorySortOrder,
            ]);
            $this->selectedCategoryId = $cat->id;
        }

        $this->showCategoryForm = false;
        $this->editingCategory = null;
    }

    public function deleteCategory($id)
    {
        $cat = MenuCategory::findOrFail($id);
        if ($cat->items()->exists()) {
            session()->flash('error', 'Cannot delete category with items. Move or delete items first.');
            return;
        }
        $cat->delete();
        if ($this->selectedCategoryId == $id) {
            $this->selectedCategoryId = MenuCategory::first()?->id;
        }
    }

    public function toggleCategory($id)
    {
        $cat = MenuCategory::findOrFail($id);
        $cat->update(['is_active' => !$cat->is_active]);
    }

    public function openItemForm($id = null)
    {
        $this->resetErrorBag();
        $this->editingItem = $id;
        $this->showItemForm = true;

        if ($id) {
            $item = MenuItem::with('modifiers')->findOrFail($id);
            $this->itemCategoryId = $item->menu_category_id;
            $this->itemName = $item->name;
            $this->itemDescription = $item->description;
            $this->itemPrice = $item->price;
            $this->itemComparePrice = $item->compare_price;
            $this->itemCost = $item->cost;
            $this->itemPreparationTime = $item->preparation_time;
            $this->itemIsAvailable = $item->is_available;
            $this->itemIsFeatured = $item->is_featured;
            $this->itemAllergens = $item->allergens ?? [];
            $this->itemDietaryLabels = $item->dietary_labels ?? [];
        } else {
            $this->itemCategoryId = $this->selectedCategoryId;
            $this->itemName = '';
            $this->itemDescription = '';
            $this->itemPrice = '';
            $this->itemComparePrice = '';
            $this->itemCost = '';
            $this->itemPreparationTime = '';
            $this->itemIsAvailable = true;
            $this->itemIsFeatured = false;
            $this->itemAllergens = [];
            $this->itemDietaryLabels = [];
        }
    }

    public function cancelItemForm()
    {
        $this->showItemForm = false;
        $this->editingItem = null;
        $this->itemCategoryId = '';
        $this->itemName = '';
        $this->itemDescription = '';
        $this->itemPrice = '';
        $this->itemComparePrice = '';
        $this->itemCost = '';
        $this->itemPreparationTime = '';
        $this->itemIsAvailable = true;
        $this->itemIsFeatured = false;
        $this->itemAllergens = [];
        $this->itemDietaryLabels = [];
        $this->resetErrorBag();
    }

    public function saveItem()
    {
        $this->validate([
            'itemCategoryId' => 'required|exists:menu_categories,id',
            'itemName' => 'required|string|max:255',
            'itemDescription' => 'nullable|string',
            'itemPrice' => 'required|numeric|min:0',
            'itemComparePrice' => 'nullable|numeric|min:0',
            'itemCost' => 'nullable|numeric|min:0',
            'itemPreparationTime' => 'nullable|integer|min:0',
            'itemIsAvailable' => 'boolean',
            'itemIsFeatured' => 'boolean',
        ]);

        $data = [
            'menu_category_id' => $this->itemCategoryId,
            'name' => $this->itemName,
            'slug' => Str::slug($this->itemName),
            'description' => $this->itemDescription,
            'price' => $this->itemPrice,
            'compare_price' => $this->itemComparePrice ?: null,
            'cost' => $this->itemCost ?: null,
            'preparation_time' => $this->itemPreparationTime ?: null,
            'is_available' => $this->itemIsAvailable,
            'is_featured' => $this->itemIsFeatured,
            'allergens' => $this->itemAllergens ?: null,
            'dietary_labels' => $this->itemDietaryLabels ?: null,
        ];

        if ($this->editingItem) {
            MenuItem::findOrFail($this->editingItem)->update($data);
        } else {
            $data['sort_order'] = MenuItem::where('menu_category_id', $this->itemCategoryId)->max('sort_order') + 1;
            $item = MenuItem::create($data);
            $this->selectedCategoryId = $item->menu_category_id;
        }

        $this->showItemForm = false;
        $this->editingItem = null;
    }

    public function deleteItem($id)
    {
        MenuItem::findOrFail($id)->delete();
    }

    public function toggleItem($id)
    {
        $item = MenuItem::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
    }

    public function toggleAvailable($id)
    {
        $item = MenuItem::findOrFail($id);
        $item->update(['is_available' => !$item->is_available]);
    }

    public function moveItemUp($id)
    {
        $item = MenuItem::findOrFail($id);
        $prev = MenuItem::where('menu_category_id', $item->menu_category_id)
            ->where('sort_order', '<', $item->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();
        if ($prev) {
            $tmp = $item->sort_order;
            $item->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $tmp]);
        }
    }

    public function moveItemDown($id)
    {
        $item = MenuItem::findOrFail($id);
        $next = MenuItem::where('menu_category_id', $item->menu_category_id)
            ->where('sort_order', '>', $item->sort_order)
            ->orderBy('sort_order')
            ->first();
        if ($next) {
            $tmp = $item->sort_order;
            $item->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $tmp]);
        }
    }

    public function openModifierForm($itemId, $modifierId = null)
    {
        $this->resetErrorBag();
        $this->editingItem = $itemId;
        $this->editingModifier = $modifierId;
        $this->showModifierForm = true;

        if ($modifierId) {
            $mod = MenuItemModifier::findOrFail($modifierId);
            $this->modifierName = $mod->name;
            $this->modifierType = $mod->type;
            $this->modifierOptions = collect($mod->options)->pluck('name')->implode("\n");
            $this->modifierIsRequired = $mod->is_required;
            $this->modifierMaxSelections = $mod->max_selections;
        } else {
            $this->modifierName = '';
            $this->modifierType = 'single';
            $this->modifierOptions = '';
            $this->modifierIsRequired = false;
            $this->modifierMaxSelections = 1;
        }
    }

    public function cancelModifierForm()
    {
        $this->showModifierForm = false;
        $this->editingModifier = null;
        $this->modifierName = '';
        $this->modifierType = 'single';
        $this->modifierOptions = '';
        $this->modifierIsRequired = false;
        $this->modifierMaxSelections = 1;
        $this->resetErrorBag();
    }

    public function saveModifier()
    {
        $this->validate([
            'modifierName' => 'required|string|max:255',
            'modifierType' => 'required|in:single,multiple',
            'modifierOptions' => 'required|string',
            'modifierIsRequired' => 'boolean',
            'modifierMaxSelections' => 'integer|min:1',
        ]);

        $options = collect(explode("\n", $this->modifierOptions))
            ->map(fn($line) => ['name' => trim($line)])
            ->filter(fn($o) => $o['name'] !== '')
            ->values()
            ->toArray();

        $data = [
            'menu_item_id' => $this->editingItem,
            'name' => $this->modifierName,
            'type' => $this->modifierType,
            'options' => $options,
            'is_required' => $this->modifierIsRequired,
            'max_selections' => $this->modifierMaxSelections,
        ];

        if ($this->editingModifier) {
            MenuItemModifier::findOrFail($this->editingModifier)->update($data);
        } else {
            $data['sort_order'] = MenuItemModifier::where('menu_item_id', $this->editingItem)->max('sort_order') + 1;
            MenuItemModifier::create($data);
        }

        $this->showModifierForm = false;
        $this->editingModifier = null;
    }

    public function deleteModifier($id)
    {
        MenuItemModifier::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.admin.menu')
            ->layout('layouts.admin');
    }
}
