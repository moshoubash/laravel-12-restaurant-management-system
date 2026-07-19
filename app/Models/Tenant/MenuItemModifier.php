<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemModifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'name',
        'type',
        'options',
        'is_required',
        'max_selections',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'max_selections' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
