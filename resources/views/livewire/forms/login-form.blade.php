<div>
    <h1 class="text-2xl font-bold text-center">Sign In</h1>
    <form wire:submit="login" class="mt-6 space-y-4">
        <div>
            <label class="block text-sm font-medium">Email</label>
            <input type="email" wire:model="email" class="mt-1 block w-full border border-surface-container-high rounded px-3 py-2">
            @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Password</label>
            <input type="password" wire:model="password" class="mt-1 block w-full border border-surface-container-high rounded px-3 py-2">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model="remember" id="remember">
            <label for="remember" class="text-sm">Remember me</label>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-primary text-white rounded">Sign In</button>
    </form>
</div>
