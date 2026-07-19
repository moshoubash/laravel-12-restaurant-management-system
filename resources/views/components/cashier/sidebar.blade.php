<aside class="w-56 bg-surface-container border-r border-surface-container-high flex flex-col">
    <div class="p-4 border-b border-surface-container-high">
        <h1 class="text-lg font-bold text-primary">Cashier</h1>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="{{ route('tenant.cashier.pos') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">POS</a>
        <a href="{{ route('tenant.cashier.invoices') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Invoices</a>
        <a href="{{ route('tenant.cashier.shifts') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Shifts</a>
        <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Dashboard</a>
    </nav>
</aside>
