<div class="flex flex-col gap-4">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <h2 class="text-lg font-bold text-on-surface">Invoices</h2>
        <div class="flex items-center gap-2">
            <input wire:model.live.debounce="search" placeholder="Search invoices..." class="rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface w-48 focus:outline-none focus:ring-2 focus:ring-primary">
            <select wire:model.live="filterStatus" class="rounded-lg border border-surface-container-high bg-surface-container-lowest px-3 py-1.5 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">All Status</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border border-surface-container-high">
        <table class="w-full text-sm">
            <thead class="bg-surface-container">
                <tr class="text-left text-secondary">
                    <th class="px-4 py-3 font-medium">Invoice #</th>
                    <th class="px-4 py-3 font-medium">Customer</th>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium text-right">Total</th>
                    <th class="px-4 py-3 font-medium text-right">Paid</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-high">
                @forelse($this->invoices as $invoice)
                    <tr class="hover:bg-surface-container/50">
                        <td class="px-4 py-3 font-medium text-on-surface">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $invoice->customer_name ?? 'Walk-in' }}</td>
                        <td class="px-4 py-3 text-secondary">{{ $invoice->issued_at?->format('M j, Y g:i A') }}</td>
                        <td class="px-4 py-3 text-right font-medium text-on-surface">${{ number_format($invoice->total, 2) }}</td>
                        <td class="px-4 py-3 text-right text-secondary">${{ number_format($invoice->paid_amount, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-block px-2 py-0.5 rounded-full text-xs font-bold',
                                'bg-success/20 text-success' => $invoice->status === 'paid',
                                'bg-warning/20 text-warning' => $invoice->status === 'pending',
                                'bg-error/20 text-error' => $invoice->status === 'overdue' || $invoice->status === 'cancelled',
                            ])>{{ ucfirst($invoice->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="viewInvoice({{ $invoice->id }})" class="text-xs font-medium text-primary hover:underline">View</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-secondary">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="text-sm text-secondary">
        Showing {{ $this->invoices->firstItem() ?? 0 }} - {{ $this->invoices->lastItem() ?? 0 }} of {{ $this->invoices->total() }}
    </div>

    {{-- Detail modal --}}
    @if($showDetail && $selectedInvoice)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" wire:click.self="closeDetail">
            <div class="w-full max-w-lg rounded-xl bg-surface-container border border-surface-container-high p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-on-surface">Invoice {{ $selectedInvoice->invoice_number }}</h3>
                    <span @class([
                        'px-2 py-0.5 rounded-full text-xs font-bold',
                        'bg-success/20 text-success' => $selectedInvoice->status === 'paid',
                        'bg-warning/20 text-warning' => $selectedInvoice->status === 'pending',
                        'bg-error/20 text-error' => $selectedInvoice->status === 'overdue' || $selectedInvoice->status === 'cancelled',
                    ])>{{ ucfirst($selectedInvoice->status) }}</span>
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-secondary">Customer</span>
                        <span class="font-medium text-on-surface">{{ $selectedInvoice->customer_name ?? 'Walk-in' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary">Date Issued</span>
                        <span class="text-on-surface">{{ $selectedInvoice->issued_at?->format('M j, Y g:i A') }}</span>
                    </div>
                </div>

                {{-- Items --}}
                @if($selectedInvoice->order)
                    <div class="mt-4 border-t border-surface-container-high pt-4">
                        <h4 class="text-sm font-bold text-on-surface mb-2">Order #{{ $selectedInvoice->order->order_number }}</h4>
                        <div class="space-y-1">
                            @foreach($selectedInvoice->order->items as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-secondary">{{ $item['quantity'] }}× {{ $item['menu_item_name'] }}</span>
                                    <span class="text-on-surface">${{ number_format($item['total_price'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Totals --}}
                <div class="mt-4 border-t border-surface-container-high pt-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-secondary">Subtotal</span>
                        <span class="text-on-surface">${{ number_format($selectedInvoice->subtotal, 2) }}</span>
                    </div>
                    @if($selectedInvoice->tax > 0)
                        <div class="flex justify-between">
                            <span class="text-secondary">Tax</span>
                            <span class="text-on-surface">${{ number_format($selectedInvoice->tax, 2) }}</span>
                        </div>
                    @endif
                    @if($selectedInvoice->discount > 0)
                        <div class="flex justify-between">
                            <span class="text-secondary">Discount</span>
                            <span class="text-success">-${{ number_format($selectedInvoice->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-base pt-2 border-t border-surface-container-high">
                        <span>Total</span>
                        <span class="text-primary">${{ number_format($selectedInvoice->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-secondary">Paid</span>
                        <span class="text-success">${{ number_format($selectedInvoice->paid_amount, 2) }}</span>
                    </div>
                    @if($selectedInvoice->due_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-secondary">Due</span>
                            <span class="text-error">${{ number_format($selectedInvoice->due_amount, 2) }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeDetail" class="rounded-lg border border-surface-container-high px-4 py-2 text-sm text-on-surface hover:bg-surface-container">Close</button>
                    @if($selectedInvoice->order_id)
                        <a href="{{ route('tenant.receipt', ['orderId' => $selectedInvoice->order_id]) }}" target="_blank" class="rounded-lg bg-primary px-4 py-2 text-sm font-bold text-on-primary hover:bg-primary/90">🖨️ Print Receipt</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
