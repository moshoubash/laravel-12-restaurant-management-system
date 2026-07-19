<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
})->name('tenant.fallback');

Route::middleware(['auth:tenant'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('tenant.dashboard');
    Route::get('/notifications', \App\Livewire\Notifications::class)->name('tenant.notifications');
    Route::get('/profile', \App\Livewire\Profile::class)->name('tenant.profile');

    // Owner / Admin routes
    Route::get('/admin/users', \App\Livewire\Admin\Users::class)->middleware(['role:owner|admin'])->name('tenant.admin.users');
    Route::get('/admin/menu', \App\Livewire\Admin\Menu::class)->middleware(['role:owner|admin'])->name('tenant.admin.menu');
    Route::get('/admin/tables', \App\Livewire\Admin\Tables::class)->middleware(['role:owner|admin'])->name('tenant.admin.tables');
    Route::get('/admin/reservations', \App\Livewire\Admin\Reservations::class)->middleware(['role:owner|admin'])->name('tenant.admin.reservations');
    Route::get('/admin/customers', \App\Livewire\Admin\Customers::class)->middleware(['role:owner|admin'])->name('tenant.admin.customers');
    Route::get('/admin/branches', \App\Livewire\Admin\Branches::class)->middleware(['role:owner|admin'])->name('tenant.admin.branches');
    Route::get('/admin/inventory', \App\Livewire\Admin\Inventory::class)->middleware(['role:owner|admin'])->name('tenant.admin.inventory');
    Route::get('/admin/reports', \App\Livewire\Admin\Reports::class)->middleware(['role:owner|admin'])->name('tenant.admin.reports');
    Route::get('/admin/design', \App\Livewire\Admin\DesignConfig::class)->middleware(['role:owner|admin'])->name('tenant.admin.design');
    Route::get('/admin/roles-permissions', \App\Livewire\Admin\RolesAndPermissions::class)->middleware(['role:owner|admin'])->name('tenant.admin.roles-permissions');
    Route::get('/admin/smtp-settings', \App\Livewire\Admin\SmtpSettings::class)->middleware(['role:owner|admin'])->name('tenant.admin.smtp-settings');
    Route::get('/admin/integrations', \App\Livewire\Admin\Integrations::class)->middleware(['role:owner|admin'])->name('tenant.admin.integrations');
    Route::get('/admin/logs', \App\Livewire\Admin\Logs::class)->middleware(['role:owner|admin'])->name('tenant.admin.logs');

    // Manager routes
    Route::get('/manager/orders', \App\Livewire\Manager\Orders::class)->middleware(['role:manager|owner|admin'])->name('tenant.manager.orders');
    Route::get('/manager/menu', \App\Livewire\Manager\Menu::class)->middleware(['role:manager|owner|admin'])->name('tenant.manager.menu');
    Route::get('/manager/inventory', \App\Livewire\Manager\Inventory::class)->middleware(['role:manager|owner|admin'])->name('tenant.manager.inventory');
    Route::get('/manager/reports', \App\Livewire\Manager\Reports::class)->middleware(['role:manager|owner|admin'])->name('tenant.manager.reports');

    // Kitchen routes
    Route::get('/kitchen/orders', \App\Livewire\Kitchen\OrderDisplay::class)->middleware(['role:chef|manager|owner|admin'])->name('tenant.kitchen.orders');

    // Waiter routes
    Route::get('/waiter/tables', \App\Livewire\Waiter\Tables::class)->middleware(['role:waiter|manager|owner|admin'])->name('tenant.waiter.tables');
    Route::get('/waiter/orders', \App\Livewire\Waiter\Orders::class)->middleware(['role:waiter|manager|owner|admin'])->name('tenant.waiter.orders');

    // Cashier routes
    Route::get('/cashier/pos', \App\Livewire\Cashier\Pos::class)->middleware(['role:cashier|manager|owner|admin'])->name('tenant.cashier.pos');
    Route::get('/cashier/invoices', \App\Livewire\Cashier\Invoices::class)->middleware(['role:cashier|manager|owner|admin'])->name('tenant.cashier.invoices');
    Route::get('/cashier/shifts', \App\Livewire\Cashier\Shifts::class)->middleware(['role:cashier|manager|owner|admin'])->name('tenant.cashier.shifts');

    // Customer portal (authenticated)
    Route::get('/customer/menu', \App\Livewire\Customer\Menu::class)->middleware(['role:customer'])->name('tenant.customer.menu');
    Route::get('/customer/orders', \App\Livewire\Customer\Orders::class)->middleware(['role:customer'])->name('tenant.customer.orders');
    Route::get('/customer/reservations', \App\Livewire\Customer\Reservations::class)->middleware(['role:customer'])->name('tenant.customer.reservations');
    Route::get('/customer/loyalty', \App\Livewire\Customer\Loyalty::class)->middleware(['role:customer'])->name('tenant.customer.loyalty');
});

require __DIR__ . '/auth-tenant.php';
