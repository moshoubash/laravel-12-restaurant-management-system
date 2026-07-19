<aside class="w-64 bg-surface-container border-r border-surface-container-high flex flex-col">
    <div class="p-4 border-b border-surface-container-high">
        <h1 class="text-lg font-bold text-primary">{{ config('app.name') }}</h1>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.dashboard') ? 'bg-primary-container text-on-primary-container' : '' }}">
            <span>Dashboard</span>
        </a>
        <a href="{{ route('tenant.admin.users') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Users</a>
        <a href="{{ route('tenant.admin.menu') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Menu</a>
        <a href="{{ route('tenant.admin.tables') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Tables</a>
        <a href="{{ route('tenant.admin.reservations') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Reservations</a>
        <a href="{{ route('tenant.admin.customers') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Customers</a>
        <a href="{{ route('tenant.admin.branches') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Branches</a>
        <a href="{{ route('tenant.admin.inventory') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Inventory</a>
        <hr class="my-2 border-surface-container-high">
        <a href="{{ route('tenant.admin.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Reports</a>
        <a href="{{ route('tenant.admin.design') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Design</a>
        <a href="{{ route('tenant.admin.smtp-settings') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">SMTP Settings</a>
        <a href="{{ route('tenant.admin.integrations') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Integrations</a>
        <a href="{{ route('tenant.admin.roles-permissions') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Roles & Permissions</a>
        <a href="{{ route('tenant.admin.logs') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Logs</a>
    </nav>
</aside>
