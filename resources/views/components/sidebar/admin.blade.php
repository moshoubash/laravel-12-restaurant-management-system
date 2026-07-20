@php
    $user = auth()->user();
    $isOwnerOrAdmin = $user && $user->hasAnyRole(['owner', 'admin']);
    $isManager = $user && $user->hasAnyRole(['manager', 'owner', 'admin']);
    $currentRoute = request()->route()?->getName() ?? '';
@endphp
<aside class="hidden sidebar lg:flex" x-data="{ collapsed: false }" x-on:toggle-sidebar.window="collapsed = !collapsed">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sidebar-logo">R</div>
            <div>
                <p class="sidebar-brand-name">{{ config('app.name') }}</p>
                <p class="sidebar-brand-branch">{{ auth()->user()->getRoleNames()->first() ?? 'Staff' }} panel</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Operations</div>

        <a href="{{ route('tenant.dashboard') }}" class="sidebar-link {{ $currentRoute === 'tenant.dashboard' ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>

        {{-- <a href="{{ route('tenant.cashier.pos') }}" class="sidebar-link">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <span>POS</span>
        </a>

        <a href="{{ route('tenant.manager.orders') }}" class="sidebar-link {{ str_contains($currentRoute, 'manager.orders') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Orders</span>
        </a>

        <a href="{{ route('tenant.kitchen.orders') }}" class="sidebar-link">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            <span>Kitchen Display</span>
        </a> --}}

        @if ($isOwnerOrAdmin)
            <a href="{{ route('tenant.admin.floor-plan') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.floor-plan') ? 'sidebar-link-active' : '' }}">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                <span>Floor Plan</span>
            </a>
        @else
            <a href="{{ route('tenant.manager.floor-plan') }}" class="sidebar-link {{ str_contains($currentRoute, 'manager.floor-plan') ? 'sidebar-link-active' : '' }}">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                <span>Floor Plan</span>
            </a>
        @endif

        <a href="{{ route($isOwnerOrAdmin ? 'tenant.admin.reservations' : 'tenant.admin.reservations') }}" class="sidebar-link {{ str_contains($currentRoute, 'reservations') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Reservations</span>
        </a>

        <a href="{{ route('tenant.admin.menu') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.menu') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M12 12h.01M12 16h.01"/></svg>
            <span>Menu</span>
        </a>

        <div class="sidebar-section">Business</div>

        <a href="{{ route('tenant.admin.customers') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.customers') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
            <span>Customers</span>
        </a>

        <a href="{{ route('tenant.admin.staff') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.staff') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Staff</span>
        </a>

        <a href="{{ route('tenant.admin.inventory') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.inventory') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Inventory</span>
        </a>

        <a href="{{ route($isOwnerOrAdmin ? 'tenant.admin.reports' : 'tenant.manager.reports') }}" class="sidebar-link {{ str_contains($currentRoute, 'reports') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Reports</span>
        </a>

        @if ($isOwnerOrAdmin)
            <div class="sidebar-section">Administration</div>

            <a href="{{ route('tenant.admin.suppliers') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.suppliers') ? 'sidebar-link-active' : '' }}">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Suppliers</span>
            </a>

            <a href="{{ route('tenant.admin.purchase-orders') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.purchase-orders') ? 'sidebar-link-active' : '' }}">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Purchase Orders</span>
            </a>

            <a href="{{ route('tenant.admin.recipes') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.recipes') ? 'sidebar-link-active' : '' }}">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>Recipes</span>
            </a>

            {{-- <a href="{{ route('tenant.admin.design') }}" class="sidebar-link {{ str_contains($currentRoute, 'admin.design') ? 'sidebar-link-active' : '' }}">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                <span>Design</span>
            </a> --}}
        @endif
    </nav>

    <div class="sidebar-footer">
        {{-- <div class="flex items-center gap-2 px-3 py-2">
            <form action="{{ route('tenant.locale.switch') }}" method="POST" class="flex-1">
                @csrf
                <select name="locale" onchange="this.form.submit()" class="input-select text-xs py-1.5 px-2">
                    <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                    <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>العربية</option>
                </select>
            </form>
        </div> --}}
        <form method="POST" action="{{ route('tenant.logout') }}" class="px-3">
            @csrf
            <button type="submit" class="w-full sidebar-link text-error hover:bg-error/5">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>
