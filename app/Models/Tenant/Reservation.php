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

    public static function getAvailableTable($branchId, $date, $time, $guestCount, $duration = 120, $excludeReservationId = null)
    {
        $start = \Carbon\Carbon::parse($date . ' ' . $time);
        $end = (clone $start)->addMinutes($duration);

        $tables = Table::where('branch_id', $branchId)
            ->where('is_active', true)
            ->where('capacity', '>=', $guestCount)
            ->orderBy('capacity', 'asc')
            ->get();

        $query = self::where('branch_id', $branchId)
            ->where('reservation_date', $date)
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('table_id');

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        $reservations = $query->get();

        foreach ($tables as $table) {
            $isOccupied = false;
            $tableReservations = $reservations->where('table_id', $table->id);
            foreach ($tableReservations as $res) {
                $resDateStr = $res->reservation_date instanceof \DateTimeInterface 
                    ? $res->reservation_date->format('Y-m-d') 
                    : $res->reservation_date;
                $resTimeStr = $res->reservation_time instanceof \DateTimeInterface 
                    ? $res->reservation_time->format('H:i:s') 
                    : $res->reservation_time;
                $resStart = \Carbon\Carbon::parse($resDateStr . ' ' . $resTimeStr);
                $resEnd = (clone $resStart)->addMinutes($res->duration);

                if ($start->lt($resEnd) && $end->gt($resStart)) {
                    $isOccupied = true;
                    break;
                }
            }
            if (!$isOccupied) {
                return $table;
            }
        }

        return null;
    }
}
