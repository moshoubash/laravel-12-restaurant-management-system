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
<body class="font-sans antialiased bg-surface text-on-surface min-h-screen flex flex-col">
    @include('partials.dynamic-design-styles')

    <div class="flex-1 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center text-on-primary text-xl font-bold mx-auto mb-4">R</div>
                <h1 class="text-xl font-bold text-surface-900">{{ config('app.name') }}</h1>
            </div>
            <div class="card">
                {{ $slot }}
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
