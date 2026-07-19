<x-layouts.central>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $tenant->exists ? 'Edit Tenant' : 'Create Tenant' }}</h1>
            <p class="mt-2 text-sm text-secondary">Set up tenant details, plans, and domains.</p>
        </div>

        <div class="rounded-3xl border border-surface-container-high bg-surface-container p-6 shadow-sm">
            <form action="{{ $action }}" method="POST" class="space-y-6">
                @csrf
                @if($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Name</label>
                        <input type="text" name="name" value="{{ old('name', $tenant->name) }}" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                        @error('name')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $tenant->slug) }}" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                        @error('slug')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Plan</label>
                        <input type="text" name="plan" value="{{ old('plan', $tenant->plan ?? 'basic') }}" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('plan')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Max Staff</label>
                        <input type="number" name="max_staff" min="1" value="{{ old('max_staff', $tenant->max_staff ?? 10) }}" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('max_staff')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Max Branches</label>
                        <input type="number" name="max_branches" min="1" value="{{ old('max_branches', $tenant->max_branches ?? 1) }}" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('max_branches')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $tenant->currency ?? 'USD') }}" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                        @error('currency')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Timezone</label>
                        <input type="text" name="timezone" value="{{ old('timezone', $tenant->timezone ?? 'UTC') }}" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" required>
                        @error('timezone')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-surface-container-high text-primary focus:ring-primary">
                        <label for="is_active" class="text-sm text-on-surface">Active tenant</label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface">Domains</label>
                    <textarea name="domains" rows="4" class="mt-2 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary" placeholder="Enter one domain per line">{{ old('domains', $domains) }}</textarea>
                    <p class="mt-2 text-sm text-secondary">Enter one domain per line. The first domain will be the tenant's primary domain.</p>
                    @error('domains')<p class="mt-1 text-sm text-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-full bg-primary px-5 py-2 text-sm font-semibold text-on-primary hover:bg-primary/90">Save Tenant</button>
                    <a href="{{ route('central.tenants.index') }}" class="text-sm text-secondary hover:text-on-surface">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.central>
