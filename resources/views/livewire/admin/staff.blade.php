<div class="space-y-4">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-bold text-on-surface">Staff</h2>
            <input wire:model.live.debounce="search" type="text" placeholder="Search staff..." class="w-48 rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface placeholder-secondary focus:border-primary focus:ring-primary">
            <select wire:model.live="filterRole" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="">All Roles</option>
                @foreach($this->rolesList as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterBranch" class="rounded border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface">
                <option value="">All Branches</option>
                @foreach($this->branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <button wire:click="openForm" class="rounded bg-primary px-3 py-1.5 text-sm font-bold text-on-primary hover:bg-primary-container">+ Add Staff</button>
    </div>

    {{-- Staff list --}}
    <div class="space-y-2">
        @if(session('error'))
            <p class="text-sm text-error">{{ session('error') }}</p>
        @endif
        @forelse($this->staff as $user)
            <div wire:key="user-{{ $user->id }}" class="flex items-center gap-4 rounded-lg border border-surface-container-high bg-surface-container-lowest p-4 {{ !$user->is_active ? 'opacity-50' : '' }}">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-on-surface">{{ $user->name }}</span>
                        @foreach($user->roles as $role)
                            <span class="rounded bg-primary-container px-1.5 py-0.5 text-[10px] font-bold text-on-primary-container">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </div>
                    <div class="mt-0.5 text-sm text-secondary">
                        {{ $user->email }}
                        @if($user->phone) · {{ $user->phone }} @endif
                        @if($user->branch) · {{ $user->branch->name }} @endif
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <button wire:click="toggleActive({{ $user->id }})" class="rounded p-1.5 hover:bg-surface-container" title="{{ $user->is_active ? 'Disable' : 'Enable' }}">
                        @if($user->is_active)
                            <svg class="h-4 w-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        @else
                            <svg class="h-4 w-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                        @endif
                    </button>
                    <button wire:click="openForm({{ $user->id }})" class="rounded p-1.5 hover:bg-surface-container" title="Edit">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete {{ $user->name }}?" class="rounded p-1.5 hover:bg-surface-container" title="Delete">
                        <svg class="h-4 w-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-secondary">No staff found. Click "+ Add Staff" to create one.</div>
        @endforelse
    </div>

    {{-- Staff form modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelForm">
            <div class="w-full max-w-lg rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-on-surface">{{ $editingUser ? 'Edit' : 'Add' }} Staff</h3>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-on-surface">Name</label>
                        <input wire:model="name" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('name') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-on-surface">Email</label>
                        <input wire:model="email" type="email" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('email') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Password</label>
                        <input wire:model="password" type="password" placeholder="{{ $editingUser ? 'Leave blank to keep' : '' }}" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('password') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Confirm Password</label>
                        <input wire:model="passwordConfirmation" type="password" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                        @error('passwordConfirmation') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Phone</label>
                        <input wire:model="phone" type="tel" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">PIN <span class="font-normal text-secondary">(for POS)</span></label>
                        <input wire:model="pin" type="text" maxlength="6" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface">Branch</label>
                        <select wire:model="branchId" class="mt-1 block w-full rounded border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:border-primary focus:ring-primary">
                            <option value="">None</option>
                            @foreach($this->branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input wire:model="isActive" type="checkbox" class="rounded border-surface-container-high text-primary focus:ring-primary">
                            Active
                        </label>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-on-surface mb-2">Roles</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($this->rolesList as $role)
                                <label class="flex items-center gap-1.5 rounded border border-surface-container-high px-3 py-1.5 text-sm cursor-pointer hover:bg-surface-container {{ in_array($role, $selectedRoles) ? 'bg-primary-container border-primary text-on-primary-container' : '' }}">
                                    <input wire:model="selectedRoles" type="checkbox" value="{{ $role }}" class="sr-only">
                                    {{ ucfirst($role) }}
                                </label>
                            @endforeach
                        </div>
                        @error('selectedRoles') <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="cancelForm" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                    <button wire:click="save" class="rounded bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary-container">Save</button>
                </div>
            </div>
        </div>
    @endif
</div>
