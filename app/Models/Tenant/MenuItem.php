<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'menu_category_id',
        'branch_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'cost',
        'image',
        'allergens',
        'dietary_labels',
        'is_featured',
        'is_available',
        'is_active',
        'sort_order',
        'preparation_time',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'allergens' => 'array',
            'dietary_labels' => 'array',
            'preparation_time' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function modifiers()
    {
        return $this->hasMany(MenuItemModifier::class);
    }
}
