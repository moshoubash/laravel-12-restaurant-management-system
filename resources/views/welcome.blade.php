<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Restaurant Management</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-surface-container-lowest text-on-surface">
    <div class="min-h-screen flex flex-col items-center justify-center p-8">
        <h1 class="text-4xl font-bold text-primary">{{ config('app.name') }}</h1>
        <p class="mt-2 text-lg text-secondary">Restaurant Management SaaS</p>
        <div class="mt-8 flex gap-4">
            @php
                $isCentral = in_array(request()->getHost(), config('tenancy.central_domains'));
                $loginRoute = $isCentral ? route('central.login') : url('/login');
            @endphp
            <a href="{{ $loginRoute }}" class="px-6 py-2 bg-primary text-white rounded">Login</a>
            <a href="http://resaas.test" class="px-6 py-2 border border-surface-container-high rounded">Tenant Demo</a>
        </div>
    </div>
</body>
</html>
