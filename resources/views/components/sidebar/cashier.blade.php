@php $currentRoute = request()->route()?->getName() ?? ''; @endphp
<aside class="sidebar w-56 hidden lg:flex">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sidebar-logo">$</div>
            <div>
                <p class="sidebar-brand-name">{{ config('app.name') }}</p>
                <p class="sidebar-brand-branch">Cashier</p>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('tenant.dashboard') }}" class="sidebar-link {{ $currentRoute === 'tenant.dashboard' ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('tenant.cashier.pos') }}" class="sidebar-link {{ str_contains($currentRoute, 'cashier.pos') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            <span>POS</span>
        </a>

        <a href="{{ route('tenant.cashier.invoices') }}" class="sidebar-link {{ str_contains($currentRoute, 'cashier.invoices') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Invoices</span>
        </a>

        <a href="{{ route('tenant.cashier.shifts') }}" class="sidebar-link {{ str_contains($currentRoute, 'cashier.shifts') ? 'sidebar-link-active' : '' }}">
            <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Shifts</span>
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
