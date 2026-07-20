<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ config('app.name', 'RESaaS') }} | Central</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-surface text-on-surface">
    @include('partials.dynamic-design-styles')
    <div class="min-h-screen flex flex-col">
        <header class="sticky top-0 z-40 bg-surface/95 backdrop-blur-sm border-b border-surface-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-on-primary text-xs font-bold">R</div>
                    <span class="text-base font-bold text-surface-900">{{ config('app.name') }}</span>
                    <span class="text-xs font-medium text-surface-500 px-2 py-0.5 bg-surface-100 rounded-md">Central</span>
                </div>
                <nav class="flex items-center gap-2">
                    <a href="{{ route('central.dashboard') }}" class="px-3 py-2 text-sm font-medium text-surface-600 hover:text-surface-900 hover:bg-surface-100 rounded-lg transition-colors {{ request()->routeIs('central.dashboard') ? 'text-primary bg-primary-50' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('central.tenants.index') }}" class="px-3 py-2 text-sm font-medium text-surface-600 hover:text-surface-900 hover:bg-surface-100 rounded-lg transition-colors {{ request()->routeIs('central.tenants*') ? 'text-primary bg-primary-50' : '' }}">
                        Tenants
                    </a>
                    <form method="POST" action="{{ route('central.logout') }}">
                        @csrf
                        <button type="submit" class="btn-ghost btn-sm text-surface-500 hover:text-error ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign Out
                        </button>
                    </form>
                </nav>
            </div>
        </header>
        <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 py-8">
            @yield('content')
        </main>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
