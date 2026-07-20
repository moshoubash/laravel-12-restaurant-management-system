@php $currentRoute = request()->route()?->getName() ?? ''; @endphp
<aside class="sidebar w-56 hidden lg:flex">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sidebar-logo">K</div>
            <div>
                <p class="sidebar-brand-name">{{ config('app.name') }}</p>
                <p class="sidebar-brand-branch">Kitchen</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('tenant.dashboard') }}" class="sidebar-link {{ $currentRoute === 'tenant.dashboard' ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('tenant.kitchen.orders') }}" class="sidebar-link {{ str_contains($currentRoute, 'kitchen.orders') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            <span>Kitchen Orders</span>
        </a>

        <a href="{{ route('tenant.kitchen.prep-list') }}" class="sidebar-link {{ str_contains($currentRoute, 'kitchen.prep-list') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Prep List</span>
        </a>

        <a href="{{ route('tenant.kitchen.inventory-alerts') }}" class="sidebar-link {{ str_contains($currentRoute, 'kitchen.inventory-alerts') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Inventory Alerts</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('tenant.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link w-full text-error hover:bg-error/5">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>
