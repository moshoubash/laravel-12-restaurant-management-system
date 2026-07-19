<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_item_name',
        'quantity',
        'unit_price',
        'modifiers',
        'modifiers_price',
        'total_price',
        'notes',
        'status',
        'preparation_time',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'modifiers' => 'array',
            'modifiers_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'preparation_time' => 'integer',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
