<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'total_visits',
        'total_spent',
        'loyalty_points',
        'birthday',
        'anniversary',
        'preferences',
        'allergies',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_visits' => 'integer',
            'total_spent' => 'decimal:2',
            'loyalty_points' => 'integer',
            'birthday' => 'date',
            'anniversary' => 'date',
            'preferences' => 'array',
            'allergies' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }
}
