@extends('layouts.central')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-sm">
            <h1 class="text-2xl font-bold">Central Dashboard</h1>
            <p class="mt-2 text-sm text-secondary">Manage tenants and system settings from one place.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-sm">
                <h2 class="text-lg font-semibold">Tenant count</h2>
                <p class="mt-4 text-4xl font-bold">{{ $tenantCount }}</p>
            </div>
            <div class="rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-sm">
                <h2 class="text-lg font-semibold">Tenant management</h2>
                <p class="mt-4 text-sm text-secondary">Create, edit, and delete tenant accounts. Add domains for each tenant to use tenant subdomains or custom domains.</p>
                <a href="{{ route('central.tenants.index') }}" class="inline-flex mt-4 items-center rounded-full bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary/90">View tenants</a>
            </div>
        </div>
    </div>
@endsection
