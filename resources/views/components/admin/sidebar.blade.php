@php
    $user = auth()->user();
    $isOwnerOrAdmin = $user && $user->hasAnyRole(['owner', 'admin']);
    $isManager = $user && $user->hasAnyRole(['manager', 'owner', 'admin']);
@endphp
<aside class="w-64 bg-surface-container border-r border-surface-container-high flex flex-col">
    <div class="p-4 border-b border-surface-container-high">
        <h1 class="text-lg font-bold text-primary">{{ config('app.name') }}</h1>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.dashboard') ? 'bg-primary-container text-on-primary-container' : '' }}">
            <span>Dashboard</span>
        </a>

        @if ($isManager)
            <div class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider px-3 pt-4 pb-1">Operations</div>
            <a href="{{ route('tenant.manager.orders') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.manager.orders*') ? 'bg-primary-container text-on-primary-container' : '' }}">Orders</a>
            <a href="{{ route('tenant.manager.staff-shifts') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.manager.staff-shifts*') ? 'bg-primary-container text-on-primary-container' : '' }}">Staff Shifts</a>
            @if ($isOwnerOrAdmin)
                <a href="{{ route('tenant.admin.menu') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.*.menu*') ? 'bg-primary-container text-on-primary-container' : '' }}">Menu</a>
                <a href="{{ route('tenant.admin.tables') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.tables*') ? 'bg-primary-container text-on-primary-container' : '' }}">Tables</a>
                <a href="{{ route('tenant.admin.floor-plan') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.floor-plan*') ? 'bg-primary-container text-on-primary-container' : '' }}">Floor Plan</a>
                <a href="{{ route('tenant.admin.reservations') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.reservations*') ? 'bg-primary-container text-on-primary-container' : '' }}">Reservations</a>
                <a href="{{ route('tenant.admin.customers') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.customers*') ? 'bg-primary-container text-on-primary-container' : '' }}">Customers</a>
                <a href="{{ route('tenant.admin.suppliers') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.suppliers*') ? 'bg-primary-container text-on-primary-container' : '' }}">Suppliers</a>
                <a href="{{ route('tenant.admin.purchase-orders') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.purchase-orders*') ? 'bg-primary-container text-on-primary-container' : '' }}">Purchase Orders</a>
                <a href="{{ route('tenant.admin.recipes') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.recipes*') ? 'bg-primary-container text-on-primary-container' : '' }}">Recipes</a>
            @endif
        @endif

        <div class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider px-3 pt-4 pb-1">Reports</div>
        <a href="{{ route($isOwnerOrAdmin ? 'tenant.admin.reports' : 'tenant.manager.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.*.reports*') ? 'bg-primary-container text-on-primary-container' : '' }}">Reports</a>

        @if ($isOwnerOrAdmin)
            <div class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider px-3 pt-4 pb-1">Administration</div>
            <a href="{{ route('tenant.admin.staff') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.staff*') ? 'bg-primary-container text-on-primary-container' : '' }}">Staff</a>
            {{-- <a href="{{ route('tenant.admin.users') }}" class="...">Users</a> --}}
            {{-- <a href="{{ route('tenant.admin.design') }}" class="...">Design</a> --}}
            {{-- <a href="{{ route('tenant.admin.smtp-settings') }}" class="...">SMTP Settings</a> --}}
            {{-- <a href="{{ route('tenant.admin.integrations') }}" class="...">Integrations</a> --}}
            {{-- <a href="{{ route('tenant.admin.roles-permissions') }}" class="...">Roles & Permissions</a> --}}
            {{-- <a href="{{ route('tenant.admin.logs') }}" class="...">Logs</a> --}}
        @endif
    </nav>
    <div class="p-4 border-t border-surface-container-high">
        <div class="mb-3">
            <label class="block text-xs font-medium text-secondary uppercase tracking-wider mb-1">Language</label>
            <form action="{{ route('tenant.locale.switch') }}" method="POST" class="flex gap-2">
                @csrf
                <select name="locale" onchange="this.form.submit()" class="flex-1 rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                    <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>العربية</option>
                </select>
            </form>
        </div>
        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm text-error">Logout</button>
        </form>
    </div>
</aside>
