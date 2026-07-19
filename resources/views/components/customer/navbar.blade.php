<nav class="fixed top-0 left-0 right-0 bg-surface-container-lowest border-b border-surface-container-high z-50">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('landing.home') }}" class="font-bold text-lg text-primary">{{ config('app.name') }}</a>
        <div class="flex items-center gap-4 text-sm">
            @auth('tenant')
                <a href="{{ route('tenant.customer.menu') }}" class="hover:text-primary">Menu</a>
                <a href="{{ route('tenant.customer.orders') }}" class="hover:text-primary">Orders</a>
                <a href="{{ route('tenant.customer.reservations') }}" class="hover:text-primary">Reservations</a>
                <a href="{{ route('tenant.customer.loyalty') }}" class="hover:text-primary">Loyalty</a>
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button type="submit" class="hover:text-primary">Logout</button>
                </form>
            @else
                <a href="{{ route('tenant.login') }}" class="hover:text-primary">Login</a>
            @endauth
        </div>
    </div>
</nav>
