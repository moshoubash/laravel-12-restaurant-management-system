<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'points_per_currency',
        'minimum_points_redeem',
        'points_per_visit',
        'birthday_points',
        'review_points',
        'is_active',
        'tiers',
    ];

    protected function casts(): array
    {
        return [
            'points_per_currency' => 'decimal:2',
            'minimum_points_redeem' => 'integer',
            'points_per_visit' => 'integer',
            'birthday_points' => 'integer',
            'review_points' => 'integer',
            'is_active' => 'boolean',
            'tiers' => 'array',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
