<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'table_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'guest_count',
        'reservation_date',
        'reservation_time',
        'duration',
        'status',
        'special_requests',
        'source',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'guest_count' => 'integer',
            'duration' => 'integer',
            'reservation_date' => 'date',
            'reservation_time' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
