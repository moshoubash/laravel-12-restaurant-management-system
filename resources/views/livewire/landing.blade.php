<div class="min-h-screen bg-surface-container-lowest">
    <div class="px-4 py-16 mx-auto max-w-7xl">
        <h1 class="text-5xl font-bold text-primary">Welcome to Our Restaurant</h1>
        <p class="mt-4 text-xl text-secondary">Browse our menu, place orders, and make reservations.</p>
        <div class="flex gap-4 mt-8">
            <a href="{{ route('tenant.menu') }}" class="px-6 py-3 font-bold text-white rounded bg-primary hover:bg-primary-container">View Menu</a>
            <a href="{{ route('tenant.reserve') }}" class="px-6 py-3 font-bold text-white rounded bg-secondary hover:bg-secondary/90">Book a Table</a>
            <a href="/dashboard" class="px-6 py-3 font-semibold border rounded border-surface-container-high text-on-surface hover:bg-surface-container">Staff Login</a>
        </div>
    </div>
</div>
