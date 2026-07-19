<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;

class TenancyServiceProvider extends ServiceProvider
{
    public static string $controllerNamespace = '';

    public function events()
    {
        return [
            Events\CreatingTenant::class => [],
            Events\TenantCreated::class => [
                JobPipeline::make([
                    Jobs\CreateDatabase::class,
                    Jobs\MigrateDatabase::class,
                    Jobs\SeedDatabase::class,
                ])->send(function (Events\TenantCreated $event) {
                    return $event->tenant;
                })->shouldBeQueued(false),
            ],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],
            Events\TenantDeleted::class => [
                JobPipeline::make([
                    Jobs\DeleteDatabase::class,
                ])->send(function (Events\TenantDeleted $event) {
                    return $event->tenant;
                })->shouldBeQueued(false),
            ],
            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],
            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],
            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],
            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],
            Events\SyncedResourceSaved::class => [
                Listeners\UpdateSyncedResource::class,
            ],
            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/tenancy.php',
            'tenancy'
        );
    }

    public function boot(): void
    {
        $this->bootEvents();

        \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::$onFail = function ($e, $request, $next) {
            $centralDomains = config('tenancy.central_domains', []);
            if (in_array($request->getHost(), $centralDomains)) {
                return $next($request);
            }
            throw $e;
        };
    }

    protected function bootEvents(): void
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }

                Event::listen($event, $listener);
            }
        }
    }
}
