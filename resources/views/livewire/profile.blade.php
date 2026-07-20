<div class="max-w-2xl mx-auto space-y-8">
    <div class="section-header">
        <div>
            <h1 class="section-title">Profile</h1>
            <p class="section-description">Manage your account settings and password</p>
        </div>
    </div>

    {{-- Success message --}}
    @if($savedMessage)
        <div class="flex items-center gap-3 p-4 rounded-xl bg-success/10 border border-success/20 text-sm">
            <svg class="w-5 h-5 text-success flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-success-800 font-medium">{{ $savedMessage }}</span>
            <button wire:click="$set('savedMessage', null)" class="ml-auto text-surface-400 hover:text-surface-600">&times;</button>
        </div>
    @endif

    {{-- Profile Info Card --}}
    <div class="card">
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-surface-100">
            <div class="avatar avatar-xl">
                <div class="avatar-fallback text-xl">{{ substr($this->name, 0, 2) }}</div>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-surface-900">{{ $this->name }}</h2>
                <p class="text-sm text-surface-500">{{ $this->email }}</p>
                @if($user?->roles->isNotEmpty())
                    <div class="flex items-center gap-1.5 mt-1">
                        @foreach($user->roles as $role)
                            <span class="badge badge-completed">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <form wire:submit="saveProfile" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="input-group">
                    <label for="name" class="input-label">Full Name</label>
                    <input id="name" type="text" wire:model="name" class="input @error('name') border-error @enderror" />
                    @error('name') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="input-group">
                    <label for="email" class="input-label">Email Address</label>
                    <input id="email" type="email" wire:model="email" class="input @error('email') border-error @enderror" />
                    @error('email') <p class="input-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="input-group">
                <label for="phone" class="input-label">Phone Number</label>
                <input id="phone" type="tel" wire:model="phone" class="input @error('phone') border-error @enderror" placeholder="+1 (555) 000-0000" />
                @error('phone') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Password Card --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold text-surface-900">Password</h2>
                <p class="text-sm text-surface-500 mt-0.5">Update your password to keep your account secure</p>
            </div>
            <button wire:click="togglePasswordForm" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                {{ $showPasswordForm ? 'Cancel' : 'Change Password' }}
            </button>
        </div>

        @if($showPasswordForm)
            <form wire:submit="savePassword" class="space-y-5 pt-4 border-t border-surface-100">
                <div class="input-group">
                    <label for="current_password" class="input-label">Current Password</label>
                    <input id="current_password" type="password" wire:model="current_password" class="input @error('current_password') border-error @enderror" />
                    @error('current_password') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="input-group">
                        <label for="new_password" class="input-label">New Password</label>
                        <input id="new_password" type="password" wire:model="new_password" class="input @error('new_password') border-error @enderror" />
                        @error('new_password') <p class="input-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="input-group">
                        <label for="new_password_confirmation" class="input-label">Confirm New Password</label>
                        <input id="new_password_confirmation" type="password" wire:model="new_password_confirmation" class="input" />
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Update Password
                </button>
            </form>
        @endif
    </div>
</div>
