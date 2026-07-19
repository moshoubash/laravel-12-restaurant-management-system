<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateTenant extends Command
{
    protected $signature = 'tenant:create {name} {slug} {domain} {--plan=basic}';
    protected $description = 'Create a new tenant with database and seed data';

    public function handle(): int
    {
        $tenant = Tenant::create([
            'name' => $this->argument('name'),
            'slug' => $this->argument('slug'),
            'domain' => $this->argument('domain'),
            'plan' => $this->option('plan'),
        ]);

        $tenant->domains()->create([
            'domain' => $this->argument('domain'),
        ]);

        $this->info("Tenant [{$tenant->name}] created successfully!");
        $this->info("Domain: {$tenant->domain}");
        $this->info("Database: tenant_{$tenant->id}");

        return Command::SUCCESS;
    }
}
