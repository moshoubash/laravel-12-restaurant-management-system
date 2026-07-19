<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionsByGroup = [
            'users' => [
                'view users', 'create users', 'edit users', 'delete users',
            ],
            'menu' => [
                'view menu', 'create menu', 'edit menu', 'delete menu',
            ],
            'tables' => [
                'view tables', 'create tables', 'edit tables', 'delete tables',
            ],
            'orders' => [
                'view orders', 'create orders', 'edit orders', 'delete orders',
                'cancel orders', 'process orders',
            ],
            'reservations' => [
                'view reservations', 'create reservations', 'edit reservations', 'delete reservations',
            ],
            'customers' => [
                'view customers', 'create customers', 'edit customers', 'delete customers',
            ],
            'payments' => [
                'view payments', 'process payments', 'refund payments',
            ],
            'inventory' => [
                'view inventory', 'create inventory', 'edit inventory', 'delete inventory',
            ],
            'reports' => [
                'view reports',
            ],
            'settings' => [
                'view settings', 'edit settings',
            ],
            'branches' => [
                'view branches', 'create branches', 'edit branches', 'delete branches',
            ],
        ];

        $guard = 'tenant';

        foreach ($permissionsByGroup as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guard,
                    'group' => $group,
                ]);
            }
        }

        $roles = [
            'owner' => Permission::all(),
            'admin' => Permission::all(),
            'manager' => ['view orders', 'create orders', 'edit orders', 'cancel orders', 'process orders',
                'view menu', 'create menu', 'edit menu',
                'view tables', 'create tables', 'edit tables',
                'view reservations', 'create reservations', 'edit reservations',
                'view customers', 'view inventory', 'create inventory', 'edit inventory',
                'view reports', 'view settings', 'edit settings',
                'view payments', 'process payments',
                'view users',
            ],
            'chef' => ['view orders', 'process orders',
                'view menu', 'view inventory',
            ],
            'waiter' => ['view tables', 'view orders', 'create orders', 'edit orders', 'process orders',
                'view menu',
                'view customers', 'create customers',
                'view payments', 'process payments',
            ],
            'cashier' => ['view orders', 'view payments', 'process payments', 'refund payments',
                'view menu', 'view tables',
                'view customers',
            ],
            'customer' => ['view menu', 'create orders', 'view orders',
                'create reservations', 'view reservations',
                'view customers', 'edit customers',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => $guard,
            ]);

            if ($permissions instanceof \Illuminate\Support\Collection) {
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions(
                    Permission::whereIn('name', $permissions)->where('guard_name', $guard)->get()
                );
            }
        }
    }
}
