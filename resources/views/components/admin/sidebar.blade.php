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
            <a href="{{ route($isOwnerOrAdmin ? 'tenant.admin.menu' : 'tenant.manager.menu') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.*.menu*') ? 'bg-primary-container text-on-primary-container' : '' }}">Menu</a>
            <a href="{{ route($isOwnerOrAdmin ? 'tenant.admin.inventory' : 'tenant.manager.inventory') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.*.inventory*') ? 'bg-primary-container text-on-primary-container' : '' }}">Inventory</a>
            @if ($isOwnerOrAdmin)
                <a href="{{ route('tenant.admin.tables') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.tables*') ? 'bg-primary-container text-on-primary-container' : '' }}">Tables</a>
                <a href="{{ route('tenant.admin.reservations') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.reservations*') ? 'bg-primary-container text-on-primary-container' : '' }}">Reservations</a>
                <a href="{{ route('tenant.admin.customers') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.customers*') ? 'bg-primary-container text-on-primary-container' : '' }}">Customers</a>
                <a href="{{ route('tenant.admin.branches') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.branches*') ? 'bg-primary-container text-on-primary-container' : '' }}">Branches</a>
            @endif
        @endif

        <div class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider px-3 pt-4 pb-1">Reports</div>
        <a href="{{ route($isOwnerOrAdmin ? 'tenant.admin.reports' : 'tenant.manager.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.*.reports*') ? 'bg-primary-container text-on-primary-container' : '' }}">Reports</a>

        @if ($isOwnerOrAdmin)
            <div class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider px-3 pt-4 pb-1">Administration</div>
            <a href="{{ route('tenant.admin.staff') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.staff*') ? 'bg-primary-container text-on-primary-container' : '' }}">Staff</a>
            <a href="{{ route('tenant.admin.users') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.users*') ? 'bg-primary-container text-on-primary-container' : '' }}">Users</a>
            <a href="{{ route('tenant.admin.design') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.design*') ? 'bg-primary-container text-on-primary-container' : '' }}">Design</a>
            <a href="{{ route('tenant.admin.smtp-settings') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.smtp-settings*') ? 'bg-primary-container text-on-primary-container' : '' }}">SMTP Settings</a>
            <a href="{{ route('tenant.admin.integrations') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.integrations*') ? 'bg-primary-container text-on-primary-container' : '' }}">Integrations</a>
            <a href="{{ route('tenant.admin.roles-permissions') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.roles-permissions*') ? 'bg-primary-container text-on-primary-container' : '' }}">Roles & Permissions</a>
            <a href="{{ route('tenant.admin.logs') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.admin.logs*') ? 'bg-primary-container text-on-primary-container' : '' }}">Logs</a>
        @endif
    </nav>
    <div class="p-4 border-t border-surface-container-high">
        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm text-error">Logout</button>
        </form>
    </div>
</aside>
