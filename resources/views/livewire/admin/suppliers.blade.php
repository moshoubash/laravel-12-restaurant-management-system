<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-on-surface">Suppliers</h2>
        <button wire:click="openForm" class="rounded-lg bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary/90">+ Add Supplier</button>
    </div>

    <input wire:model.live.debounce="search" placeholder="Search suppliers..." class="w-full max-w-xs rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">

    <div class="overflow-x-auto rounded-xl border border-surface-container-high">
        <table class="w-full text-sm">
            <thead class="bg-surface-container">
                <tr class="text-left text-secondary">
                    <th class="px-4 py-3 font-medium">Name</th>
                    <th class="px-4 py-3 font-medium">Contact</th>
                    <th class="px-4 py-3 font-medium">Email</th>
                    <th class="px-4 py-3 font-medium">Phone</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high">
                @forelse($this->suppliers as $supplier)
                    <tr class="hover:bg-surface-container/50">
                        <td class="px-4 py-3 font-medium text-on-surface">{{ $supplier->name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $supplier->contact_person ?? '—' }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $supplier->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $supplier->phone ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleActive({{ $supplier->id }})" @class([
                                'px-2 py-0.5 rounded-full text-xs font-bold',
                                'bg-success/20 text-success' => $supplier->is_active,
                                'bg-surface-container-high text-secondary' => !$supplier->is_active,
                            ])>{{ $supplier->is_active ? 'Active' : 'Inactive' }}</button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="openForm({{ $supplier->id }})" class="text-xs font-medium text-primary hover:underline mr-3">Edit</button>
                            <button wire:click="deleteSupplier({{ $supplier->id }})" wire:confirm="Delete {{ $supplier->name }}?" class="text-xs font-medium text-error hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-secondary">No suppliers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="cancelForm">
            <div class="w-full max-w-md rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl">
                <h3 class="text-lg font-bold text-on-surface mb-4">{{ $editingSupplier ? 'Edit' : 'Add' }} Supplier</h3>
                <form wire:submit="save" class="flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Name *</label>
                        <input wire:model="name" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Contact Person</label>
                        <input wire:model="contactPerson" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Email</label>
                            <input wire:model="email" type="email" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface mb-1">Phone</label>
                            <input wire:model="phone" type="tel" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Address</label>
                        <textarea wire:model="address" rows="2" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-on-surface mb-1">Payment Terms</label>
                        <input wire:model="paymentTerms" placeholder="e.g. Net 30" class="w-full rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="cancelForm" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm font-medium text-on-surface hover:bg-surface-container transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-sm font-bold hover:bg-primary/90">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
