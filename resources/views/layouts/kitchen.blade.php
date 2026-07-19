<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'RESaaS') }} - Kitchen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-surface-container-lowest text-on-surface">
    @include('partials.dynamic-design-styles')
    <div class="flex h-screen overflow-hidden">
        @include('components.kitchen.sidebar')
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
    @stack('scripts')
</body>
</html>
