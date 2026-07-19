<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'sku',
        'category',
        'unit',
        'stock_quantity',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'unit_cost',
        'supplier_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'decimal:2',
            'min_stock_level' => 'decimal:2',
            'max_stock_level' => 'decimal:2',
            'reorder_point' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
