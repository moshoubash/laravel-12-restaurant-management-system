<?php

namespace App\Support;

use App\Models\Tenant\InventoryItem;
use App\Models\Tenant\Order;
use App\Models\Tenant\RecipeItem;

class InventoryHelper
{
    /**
     * Deduct inventory based on the menu items in an order.
     * Called when an order is served or completed.
     */
    public static function consumeOrderIngredients(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $orderItem) {
            $menuItemId = $orderItem->menu_item_id;
            if (!$menuItemId) continue;

            $recipeItems = RecipeItem::where('menu_item_id', $menuItemId)->get();
            if ($recipeItems->isEmpty()) continue;

            $qtyOrdered = (float) $orderItem->quantity;

            foreach ($recipeItems as $recipe) {
                $inventoryItem = InventoryItem::find($recipe->inventory_item_id);
                if (!$inventoryItem || !$inventoryItem->is_active) continue;

                $totalConsumed = $recipe->quantity * $qtyOrdered;
                $inventoryItem->decrement('stock_quantity', $totalConsumed);
            }
        }
    }
}
