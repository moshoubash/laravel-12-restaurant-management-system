<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'RESaaS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-surface-container-low text-on-surface min-h-screen flex items-center justify-center">
    @include('partials.dynamic-design-styles')
    <div class="w-full max-w-md p-6">
        {{ $slot }}
    </div>
    @livewireScripts
    @stack('scripts')
</body>
</html>
