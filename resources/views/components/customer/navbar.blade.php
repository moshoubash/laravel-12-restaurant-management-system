<header class="fixed top-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-sm border-b border-surface-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-on-primary text-xs font-bold">R</div>
                <span class="text-base font-bold text-surface-900">{{ config('app.name') }}</span>
            </div>

            {{-- Desktop Nav --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('tenant.public.menu') }}" class="px-4 py-2 text-sm font-medium text-surface-600 hover:text-surface-900 hover:bg-surface-100 rounded-lg transition-colors {{ request()->routeIs('tenant.public.menu') ? 'text-primary bg-primary-50' : '' }}">
                    Menu
                </a>
                @auth('tenant')
                    <a href="{{ route('tenant.customer.orders') }}" class="px-4 py-2 text-sm font-medium text-surface-600 hover:text-surface-900 hover:bg-surface-100 rounded-lg transition-colors {{ request()->routeIs('tenant.customer.orders*') ? 'text-primary bg-primary-50' : '' }}">
                        Orders
                    </a>
                    <a href="{{ route('tenant.customer.reservations') }}" class="px-4 py-2 text-sm font-medium text-surface-600 hover:text-surface-900 hover:bg-surface-100 rounded-lg transition-colors {{ request()->routeIs('tenant.customer.reservations*') ? 'text-primary bg-primary-50' : '' }}">
                        Reservations
                    </a>
                    <a href="{{ route('tenant.customer.loyalty') }}" class="px-4 py-2 text-sm font-medium text-surface-600 hover:text-surface-900 hover:bg-surface-100 rounded-lg transition-colors {{ request()->routeIs('tenant.customer.loyalty*') ? 'text-primary bg-primary-50' : '' }}">
                        Loyalty
                    </a>
                @endauth
            </nav>

            <div class="flex items-center gap-2">
                @auth('tenant')
                    <a href="{{ route('tenant.customer.profile') }}" class="btn-ghost btn-sm hidden md:flex">
                        <div class="avatar avatar-sm">
                            <div class="avatar-fallback">{{ substr(auth('tenant')->user()?->name ?? 'U', 0, 2) }}</div>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('tenant.logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="btn-ghost btn-sm text-surface-500 hover:text-error">
                            Sign Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('tenant.login') }}" class="btn-ghost btn-sm">Sign In</a>
                @endauth

                {{-- Mobile menu toggle --}}
                <button class="btn-icon md:hidden" aria-label="Menu" x-data @click="$dispatch('toggle-mobile-menu')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>
</header>
