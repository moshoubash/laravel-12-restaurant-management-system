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
        $branch = Branch::create([
            'name' => 'Main Branch',
            'slug' => 'main-branch',
            'is_active' => true,
        ]);

        $owner = User::create([
            'name' => 'Restaurant Owner',
            'email' => 'owner@resaas.test',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
        $owner->assignRole('owner');

        $manager = User::create([
            'name' => 'Restaurant Manager',
            'email' => 'manager@resaas.test',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'is_active' => true,
        ]);
        $manager->assignRole('manager');

        User::create([
            'name' => 'Chef',
            'email' => 'chef@resaas.test',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'is_active' => true,
        ])->assignRole('chef');

        User::create([
            'name' => 'Waiter',
            'email' => 'waiter@resaas.test',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'is_active' => true,
        ])->assignRole('waiter');

        User::create([
            'name' => 'Cashier',
            'email' => 'cashier@resaas.test',
            'password' => Hash::make('password'),
            'branch_id' => $branch->id,
            'is_active' => true,
        ])->assignRole('cashier');

        User::create([
            'name' => 'John Customer',
            'email' => 'customer@resaas.test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ])->assignRole('customer');

        DesignConfig::create([
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

        LoyaltyProgram::create([
            'branch_id' => $branch->id,
            'name' => 'Standard Loyalty',
            'points_per_currency' => 1,
            'minimum_points_redeem' => 100,
            'points_per_visit' => 10,
            'birthday_points' => 50,
            'review_points' => 20,
            'is_active' => true,
        ]);
    }
}
