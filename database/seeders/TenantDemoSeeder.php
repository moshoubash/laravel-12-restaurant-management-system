<?php

namespace Database\Seeders;

use App\Models\Tenant\Branch;
use App\Models\Tenant\Customer;
use App\Models\Tenant\FloorSection;
use App\Models\Tenant\InventoryItem;
use App\Models\Tenant\MenuCategory;
use App\Models\Tenant\MenuItem;
use App\Models\Tenant\MenuItemModifier;
use App\Models\Tenant\PurchaseOrder;
use App\Models\Tenant\PurchaseOrderItem;
use App\Models\Tenant\Reservation;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantDemoSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first() ?? Branch::create([
            'name' => 'Main Branch',
            'slug' => 'main-branch',
            'address' => '123 Main Street',
            'phone' => '+1 555 123 4567',
            'email' => 'main@resaas.test',
            'is_active' => true,
        ]);

        // Create floor sections for floor plan
        $indoorSection = FloorSection::firstOrCreate(
            ['branch_id' => $branch->id, 'name' => 'Indoor'],
            ['color' => '#FF6B35', 'sort_order' => 1, 'is_active' => true]
        );

        $starters = MenuCategory::firstOrCreate(['name' => 'Starters'], ['slug' => 'starters', 'sort_order' => 1, 'is_active' => true]);
        $mains = MenuCategory::firstOrCreate(['name' => 'Mains'], ['slug' => 'mains', 'sort_order' => 2, 'is_active' => true]);
        $drinks = MenuCategory::firstOrCreate(['name' => 'Drinks'], ['slug' => 'drinks', 'sort_order' => 3, 'is_active' => true]);

        $items = [
            [
                'category' => $starters,
                'name' => 'Hummus Platter',
                'description' => 'Creamy hummus served with warm pita bread and olives.',
                'price' => 8.50,
                'sort_order' => 1,
                'modifiers' => [
                    [
                        'name' => 'Extras',
                        'type' => 'multiple',
                        'options' => [
                            ['name' => 'Extra Pita', 'price' => 1.50],
                            ['name' => 'Extra Olives', 'price' => 1.00],
                        ],
                        'is_required' => false,
                        'max_selections' => 2,
                    ],
                ],
            ],
            [
                'category' => $mains,
                'name' => 'Chicken Shawarma',
                'description' => 'Marinated chicken served with rice, salad, and tahini sauce.',
                'price' => 14.00,
                'sort_order' => 1,
                'modifiers' => [
                    [
                        'name' => 'Side',
                        'type' => 'single',
                        'options' => [
                            ['name' => 'French Fries', 'price' => 0],
                            ['name' => 'Rice', 'price' => 0],
                            ['name' => 'Salad', 'price' => 0],
                        ],
                        'is_required' => true,
                        'max_selections' => 1,
                    ],
                ],
            ],
            [
                'category' => $drinks,
                'name' => 'Mint Lemonade',
                'description' => 'Fresh lemonade with mint and a hint of sweetness.',
                'price' => 4.50,
                'sort_order' => 1,
                'modifiers' => [],
            ],
        ];

        foreach ($items as $itemData) {
            $item = MenuItem::firstOrCreate([
                'menu_category_id' => $itemData['category']->id,
                'name' => $itemData['name'],
            ], [
                'branch_id' => $branch->id,
                'slug' => Str::slug($itemData['name']),
                'description' => $itemData['description'],
                'price' => $itemData['price'],
                'is_available' => true,
                'is_active' => true,
                'sort_order' => $itemData['sort_order'],
                'preparation_time' => 10,
            ]);

            foreach ($itemData['modifiers'] as $modifierData) {
                MenuItemModifier::firstOrCreate([
                    'menu_item_id' => $item->id,
                    'name' => $modifierData['name'],
                ], [
                    'type' => $modifierData['type'],
                    'options' => $modifierData['options'],
                    'is_required' => $modifierData['is_required'],
                    'max_selections' => $modifierData['max_selections'],
                    'sort_order' => 1,
                ]);
            }
        }

        foreach (range(1, 5) as $index) {
            $isIndoor = $index <= 3;
            // Grid positions for tables: 2x2 for indoor, 1x2 for patio
            $x = $isIndoor ? (($index - 1) % 2) * 200 + 50 : 100;
            $y = $isIndoor ? floor(($index - 1) / 2) * 150 + 50 : ($index - 4) * 150 + 50;

            Table::firstOrCreate([
                'table_number' => $index,
            ], [
                'branch_id' => $branch->id,
                'section' => $isIndoor ? 'Indoor' : 'Patio',
                'seats' => $isIndoor ? 4 : 6,
                'is_active' => true,
                'qr_code' => '/menu?table=' . $index,
                'x_position' => $x,
                'y_position' => $y,
                'width' => 80,
                'height' => 80,
                'shape' => $isIndoor ? 'square' : 'circle',
                'status' => $index % 3 === 0 ? 'occupied' : 'available',
            ]);
        }

        Customer::firstOrCreate([
            'email' => 'guest@resaas.test',
        ], [
            'name' => 'Guest Customer',
            'phone' => '+1 555 987 6543',
            'loyalty_points' => 15,
            'is_active' => true,
        ]);

        $supplier = Supplier::firstOrCreate([
            'name' => 'Local Produce Co.',
        ], [
            'phone' => '+1 555 222 3333',
            'email' => 'orders@produce.example',
            'is_active' => true,
        ]);

        $inventoryItems = [
            ['name' => 'Tomatoes', 'unit' => 'kg', 'quantity' => 20, 'reorder_level' => 5, 'supplier_id' => $supplier->id],
            ['name' => 'Lemons', 'unit' => 'kg', 'quantity' => 15, 'reorder_level' => 5, 'supplier_id' => $supplier->id],
        ];

        foreach ($inventoryItems as $inventoryData) {
            InventoryItem::firstOrCreate([
                'name' => $inventoryData['name'],
            ], array_merge($inventoryData, [
                'branch_id' => $branch->id,
                'is_active' => true,
            ]));
        }

        $po = PurchaseOrder::firstOrCreate([
            'order_number' => 'PO-' . date('Ymd'),
        ], [
            'supplier_id' => $supplier->id,
            'branch_id' => $branch->id,
            'status' => 'draft',
            'order_date' => now(),
        ]);

        PurchaseOrderItem::firstOrCreate([
            'purchase_order_id' => $po->id,
            'inventory_item_id' => InventoryItem::where('name', 'Tomatoes')->value('id'),
        ], [
            'quantity' => 10,
            'unit_price' => 1.20,
        ]);

        Reservation::firstOrCreate([
            'customer_name' => 'Farah Alami',
            'reservation_date' => now()->addDays(1)->toDateString(),
            'reservation_time' => '19:00:00',
        ], [
            'branch_id' => $branch->id,
            'table_id' => Table::where('table_number', 1)->value('id'),
            'customer_email' => 'farah@example.com',
            'customer_phone' => '+1 555 444 2211',
            'party_size' => 4,
            'status' => 'pending',
            'notes' => 'Window seat preferred',
        ]);
    }
}
