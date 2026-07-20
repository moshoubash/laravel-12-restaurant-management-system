<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'RESaaS') }} | Central</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-surface-container-low text-on-surface">
    <div class="min-h-screen flex flex-col">
        <header class="bg-surface-container border-b border-surface-container-high">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
                <div>
                    <a href="{{ route('central.dashboard') }}" class="text-lg font-bold text-primary">{{ config('app.name') }}</a>
                    <span class="ml-3 text-sm text-secondary">Central Admin</span>
                </div>
                <nav class="flex items-center gap-3">
                    <a href="{{ route('central.tenants.index') }}" class="text-sm text-on-surface hover:text-primary">Tenants</a>
                    <a href="{{ route('central.dashboard') }}" class="text-sm text-on-surface hover:text-primary">Dashboard</a>
                    <form method="POST" action="{{ route('central.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-error hover:text-red-600">Logout</button>
                    </form>
                </nav>
            </div>
        </header>
        <main class="flex-1 max-w-7xl mx-auto px-4 py-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
