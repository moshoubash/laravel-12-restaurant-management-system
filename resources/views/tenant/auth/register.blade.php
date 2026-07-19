<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-primary">{{ config('app.name') }}</h1>
            <p class="mt-1 text-sm text-secondary">Create Account</p>
        </div>

        <form method="POST" action="{{ route('tenant.register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-on-surface">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 block w-full rounded border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                @error('name')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-on-surface">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 block w-full rounded border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                @error('email')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-on-surface">Password</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full rounded border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                @error('password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-on-surface">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="mt-1 block w-full rounded border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
            </div>
            <button type="submit" class="w-full rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">
                Register
            </button>
        </form>
    </div>
</x-guest-layout>
