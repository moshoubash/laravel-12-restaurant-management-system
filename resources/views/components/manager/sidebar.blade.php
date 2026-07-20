<aside class="w-64 bg-surface-container border-r border-surface-container-high flex flex-col">
    <div class="p-4 border-b border-surface-container-high">
        <h1 class="text-lg font-bold text-primary">{{ config('app.name') }}</h1>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.dashboard') ? 'bg-primary-container text-on-primary-container' : '' }}">Dashboard</a>
        <div class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider px-3 pt-4 pb-1">Operations</div>
        <a href="{{ route('tenant.manager.orders') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.manager.orders*') ? 'bg-primary-container text-on-primary-container' : '' }}">Orders</a>
        <a href="{{ route('tenant.manager.staff-shifts') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.manager.staff-shifts*') ? 'bg-primary-container text-on-primary-container' : '' }}">Staff Shifts</a>
        <a href="{{ route('tenant.manager.floor-plan') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.manager.floor-plan*') ? 'bg-primary-container text-on-primary-container' : '' }}">Floor Plan</a>
        <div class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider px-3 pt-4 pb-1">Reports</div>
        <a href="{{ route('tenant.manager.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.manager.reports*') ? 'bg-primary-container text-on-primary-container' : '' }}">Reports</a>
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
