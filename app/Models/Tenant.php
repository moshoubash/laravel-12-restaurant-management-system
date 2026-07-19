<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\HasDataColumn;

class Tenant extends Model implements TenantContract, TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains, CentralConnection, HasDataColumn;

    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => \Stancl\Tenancy\Events\TenantCreated::class,
        'updated' => \Stancl\Tenancy\Events\TenantUpdated::class,
        'deleted' => \Stancl\Tenancy\Events\TenantDeleted::class,
        'saved' => \Stancl\Tenancy\Events\TenantSaved::class,
    ];

    public static function getDataColumn(): string
    {
        return 'settings';
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'domain',
            'plan',
            'max_staff',
            'max_branches',
            'is_active',
            'currency',
            'timezone',
            'settings',
        ];
    }

    public function getTenantKeyName(): string
    {
        return 'id';
    }

    public function getTenantKey()
    {
        return $this->getAttribute('id');
    }

    public function run(callable $callable)
    {
        tenancy()->initialize($this);
        $result = $callable();
        tenancy()->end();
        return $result;
    }

    public function getInternal(string $key)
    {
        return $this->getAttribute($key);
    }

    public function setInternal(string $key, $value)
    {
        $this->setAttribute($key, $value);
        return $this;
    }

    public static function internalPrefix(): string
    {
        return 'tenancy_';
    }
}
