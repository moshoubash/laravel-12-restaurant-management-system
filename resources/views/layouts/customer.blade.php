<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ config('app.name', 'RESaaS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-surface text-on-surface">
    @include('partials.dynamic-design-styles')

    @include('components.customer.navbar')

    <main class="min-h-screen pt-16">
        {{ $slot }}
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
