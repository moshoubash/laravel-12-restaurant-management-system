<aside class="flex flex-col w-64 border-r bg-surface-container border-surface-container-high">
    <div class="p-4 border-b border-surface-container-high">
        <h1 class="text-lg font-bold text-primary">{{ config('app.name') }}</h1>
    </div>
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.dashboard') ? 'bg-primary-container text-on-primary-container' : '' }}">Dashboard</a>
        <a href="{{ route('tenant.cashier.pos') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.cashier.pos*') ? 'bg-primary-container text-on-primary-container' : '' }}">POS</a>
        <a href="{{ route('tenant.cashier.invoices') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.cashier.invoices*') ? 'bg-primary-container text-on-primary-container' : '' }}">Invoices</a>
        <a href="{{ route('tenant.cashier.shifts') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('tenant.cashier.shifts*') ? 'bg-primary-container text-on-primary-container' : '' }}">Shifts</a>
    </nav>
    <div class="p-4 border-t border-surface-container-high">
        <div class="mb-3">
            <label class="block mb-1 text-xs font-medium tracking-wider uppercase text-secondary">Language</label>
            <form action="{{ route('tenant.locale.switch') }}" method="POST" class="flex gap-2">
                @csrf
                <select name="locale" onchange="this.form.submit()" class="flex-1 px-3 py-2 text-sm border rounded-lg border-surface-container-high bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                    <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>العربية</option>
                </select>
            </form>
        </div>
        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full gap-3 px-3 py-2 text-sm rounded hover:bg-surface-container-high text-error">Logout</button>
        </form>
    </div>
</aside>
