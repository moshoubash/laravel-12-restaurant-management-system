<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'type',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'total_hours',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'break_start' => 'datetime',
            'break_end' => 'datetime',
            'total_hours' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
