<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ config('app.name', 'RESaaS') }} — @yield('title', 'Welcome')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen font-sans antialiased bg-surface text-on-surface">
    @include('partials.dynamic-design-styles')

    <div class="flex items-center justify-center flex-1 p-4">
        <div class="w-full max-w-md">
            <div class="card">
                {{ $slot }}
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
