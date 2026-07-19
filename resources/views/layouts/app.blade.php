<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'RESaaS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-surface-container-low text-on-surface">
    <div class="flex h-screen overflow-hidden">
        <aside class="flex flex-col w-64 border-r bg-surface-container border-surface-container-high">
            <div class="p-4 border-b border-surface-container-high">
                <h1 class="text-lg font-bold text-primary">{{ config('app.name') }}</h1>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                {{-- <a href="{{ route('web.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-surface-container-high text-sm {{ request()->routeIs('web.dashboard') ? 'bg-primary-container text-on-primary-container' : '' }}">Dashboard</a> --}}
            </nav>
            <div class="p-4 border-t border-surface-container-high">
                <form method="POST" action="{{ route('tenant.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full gap-3 px-3 py-2 text-sm rounded hover:bg-surface-container-high text-error">Logout</button>
                </form>
            </div>
        </aside>
        <main class="flex-1 p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
    @include('partials.role-debug')
</body>
</html>
