<?php

use Illuminate\Support\Facades\Route;

$isCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains'));

if ($isCentralDomain) {
    Route::prefix('/central')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Central\AuthController::class, 'showLogin'])
            ->middleware('guest:web')
            ->name('central.login');

        Route::post('/login', [\App\Http\Controllers\Central\AuthController::class, 'login'])
            ->middleware('guest:web')
            ->name('central.login.submit');

        Route::get('/register', [\App\Http\Controllers\Central\AuthController::class, 'showRegister'])
            ->middleware('guest:web')
            ->name('central.register');

        Route::post('/register', [\App\Http\Controllers\Central\AuthController::class, 'register'])
            ->middleware('guest:web')
            ->name('central.register.submit');

        Route::post('/logout', [\App\Http\Controllers\Central\AuthController::class, 'logout'])
            ->middleware('auth:web')
            ->name('central.logout');

        Route::middleware('auth:web')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Central\DashboardController::class, 'index'])
                ->name('central.dashboard');

            Route::get('/tenants', [\App\Http\Controllers\Central\TenantController::class, 'index'])
                ->name('central.tenants.index');
            Route::get('/tenants/create', [\App\Http\Controllers\Central\TenantController::class, 'create'])
                ->name('central.tenants.create');
            Route::post('/tenants', [\App\Http\Controllers\Central\TenantController::class, 'store'])
                ->name('central.tenants.store');
            Route::get('/tenants/{tenant}/edit', [\App\Http\Controllers\Central\TenantController::class, 'edit'])
                ->name('central.tenants.edit');
            Route::put('/tenants/{tenant}', [\App\Http\Controllers\Central\TenantController::class, 'update'])
                ->name('central.tenants.update');
            Route::delete('/tenants/{tenant}', [\App\Http\Controllers\Central\TenantController::class, 'destroy'])
                ->name('central.tenants.destroy');
        });
    });
} else {
    Route::any('/central/{any?}', function () {
        return redirect('/login');
    })->where('any', '.*');
}
