<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-primary">{{ config('app.name') }}</h1>
            <p class="mt-1 text-sm text-secondary">Central Admin Login</p>
        </div>

        <form method="POST" action="{{ route('central.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-on-surface">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="block w-full px-3 py-2 mt-1 rounded border-surface-container-high bg-surface-container-lowest text-on-surface focus:border-primary focus:ring-primary">
                @error('email')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-on-surface">Password</label>
                <input id="password" type="password" name="password" required
                    class="block w-full px-3 py-2 mt-1 rounded border-surface-container-high bg-surface-container-lowest text-on-surface focus:border-primary focus:ring-primary">
                @error('password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" class="rounded border-surface-container-high text-primary focus:ring-primary">
                <label for="remember" class="ml-2 text-sm text-on-surface">Remember me</label>
            </div>
            <button type="submit" class="w-full px-4 py-2 text-sm font-bold rounded bg-primary text-on-primary hover:bg-primary-container">
                Log in
            </button>
        </form>
    </div>
</x-guest-layout>
