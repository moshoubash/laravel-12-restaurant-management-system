@extends('layouts.central')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Tenants</h1>
                <p class="mt-2 text-sm text-secondary">Manage tenant accounts, billing plans, and mapped domains.</p>
            </div>
            <a href="{{ route('central.tenants.create') }}" class="inline-flex items-center rounded-full bg-primary px-4 py-2 text-sm font-semibold text-on-primary hover:bg-primary/90">Create Tenant</a>
        </div>

        @if(session('success'))
            <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto rounded-3xl border border-surface-container-high bg-surface-container p-4 shadow-sm">
            <table class="min-w-full text-left text-sm text-on-surface">
                <thead>
                    <tr class="border-b border-surface-container-high text-sm font-semibold text-secondary">
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Domains</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                        <tr class="border-b border-surface-container-high hover:bg-surface-container-lowest">
                            <td class="px-4 py-3 font-semibold">{{ $tenant->name }}</td>
                            <td class="px-4 py-3">{{ $tenant->slug }}</td>
                            <td class="px-4 py-3">{{ ucfirst($tenant->plan) }}</td>
                            <td class="px-4 py-3">
                                @foreach($tenant->domains as $domain)
                                    <span class="inline-flex rounded-full bg-surface-container-high px-2 py-1 text-[11px] font-medium text-secondary">{{ $domain->domain }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">{{ $tenant->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="px-4 py-3 space-x-2">
                                <a href="{{ route('central.tenants.edit', $tenant) }}" class="rounded-full bg-surface-container-high px-3 py-1 text-sm text-primary hover:bg-surface-container">Edit</a>
                                <form action="{{ route('central.tenants.destroy', $tenant) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this tenant?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full bg-error px-3 py-1 text-sm text-on-error hover:bg-red-600/10">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-secondary">No tenants created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
