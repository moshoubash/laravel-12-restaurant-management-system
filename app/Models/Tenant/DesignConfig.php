<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'colors',
        'logo',
        'favicon',
        'font',
        'receipt_header',
        'receipt_footer',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
        ];
    }
}
