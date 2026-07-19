<aside class="w-56 bg-surface-container border-r border-surface-container-high flex flex-col">
    <div class="p-4 border-b border-surface-container-high">
        <h1 class="text-lg font-bold text-primary">Kitchen</h1>
    </div>
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">
        <a href="{{ route('tenant.kitchen.orders') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Orders</a>
        <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm">Dashboard</a>
    </nav>
    <div class="p-4 border-t border-surface-container-high">
        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm text-error">Logout</button>
        </form>
    </div>
</aside>
