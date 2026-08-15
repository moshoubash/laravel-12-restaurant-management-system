<?php

namespace Database\Seeders;

use App\Models\Tenant\Branch;
use App\Models\Tenant\DesignConfig;
use App\Models\Tenant\LoyaltyProgram;
use App\Models\Tenant\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate([
            'slug' => 'main-branch',
        ], [
            'name' => 'Main Branch',
            'is_active' => true,
        ]);

        $users = [
            ['name' => 'Restaurant Owner', 'email' => 'owner@resaas.test', 'role' => 'owner', 'branch_id' => $branch->id],
            ['name' => 'Restaurant Manager', 'email' => 'manager@resaas.test', 'role' => 'manager', 'branch_id' => $branch->id],
            ['name' => 'Chef', 'email' => 'chef@resaas.test', 'role' => 'chef', 'branch_id' => $branch->id],
            ['name' => 'Waiter', 'email' => 'waiter@resaas.test', 'role' => 'waiter', 'branch_id' => $branch->id],
            ['name' => 'Cashier', 'email' => 'cashier@resaas.test', 'role' => 'cashier', 'branch_id' => $branch->id],
            ['name' => 'John Customer', 'email' => 'customer@resaas.test', 'role' => 'customer', 'branch_id' => null],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'password' => Hash::make('password'),
                'branch_id' => $userData['branch_id'],
                'is_active' => true,
            ]);

            if (! $user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }

        DesignConfig::firstOrCreate([], [
            'colors' => [
                'primary' => '232 89 12',
                'primary-container' => '255 237 213',
                'on-surface' => '30 30 30',
                'surface-container-lowest' => '255 255 255',
                'surface-container-low' => '250 250 249',
                'surface-container' => '245 245 244',
                'surface-container-high' => '231 229 228',
                'surface-container-highest' => '214 211 209',
                'secondary' => '120 113 108',
                'error' => '220 38 38',
                'on-primary-container' => '87 33 0',
            ],
            'font' => 'Inter',
        ]);

        LoyaltyProgram::firstOrCreate([
            'branch_id' => $branch->id,
            'name' => 'Standard Loyalty',
        ], [
            'points_per_currency' => 1,
            'minimum_points_redeem' => 100,
            'points_per_visit' => 10,
            'birthday_points' => 50,
            'review_points' => 20,
            'is_active' => true,
        ]);
    }
}
