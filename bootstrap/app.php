<?php

use App\Providers\TenancyServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        TenancyServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware([
                'web',
                // 'tenant',
                'smtp',
                \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
            ])->group(base_path('routes/tenant.php'));

            Route::post(
                app(\Livewire\Mechanisms\HandleRequests\HandleRequests::class)->getUpdateUri(),
                [\Livewire\Mechanisms\HandleRequests\HandleRequests::class, 'handleUpdate'],
            )->middleware([
                'web',
                // 'tenant',
                'smtp',
                \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                \Livewire\Mechanisms\HandleRequests\RequireLivewireHeaders::class,
            ])->name('tenant.livewire.update');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(prepend: [
            \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            \App\Http\Middleware\ApplySmtpSettings::class,
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->redirectGuestsTo(function () {
            $isCentral = in_array(request()->getHost(), config('tenancy.central_domains'));
            return $isCentral ? route('central.login') : route('tenant.login');
        });

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'smtp' => \App\Http\Middleware\ApplySmtpSettings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
