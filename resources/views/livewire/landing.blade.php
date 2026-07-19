<div class="min-h-screen bg-surface-container-lowest">
    <div class="max-w-7xl mx-auto px-4 py-16">
        <h1 class="text-5xl font-bold text-primary">Welcome to Our Restaurant</h1>
        <p class="mt-4 text-xl text-secondary">Browse our menu, place orders, and make reservations.</p>
        <div class="mt-8 flex gap-4">
            <a href="{{ route('tenant.menu') }}" class="px-6 py-3 bg-primary text-white font-bold rounded hover:bg-primary-container">View Menu</a>
            <a href="{{ route('tenant.reserve') }}" class="px-6 py-3 bg-secondary text-white font-bold rounded hover:bg-secondary/90">Book a Table</a>
            <a href="{{ route('tenant.login') }}" class="px-6 py-3 border border-surface-container-high rounded font-semibold text-on-surface hover:bg-surface-container">Staff Login</a>
        </div>
    </div>
</div>
