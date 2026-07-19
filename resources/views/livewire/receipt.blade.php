<div class="min-h-screen flex flex-col">
    <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>

    @if($this->order)
        <div class="receipt-paper">
        {{-- Business header --}}
        <div class="text-center">
            @if($this->config?->logo)
                <img src="{{ Storage::url($this->config->logo) }}" alt="Logo" style="max-height: 50px; margin: 0 auto 8px;">
            @endif
            <h2 style="margin: 0; font-size: 16px; font-weight: bold;">{{ $this->order->branch?->name ?? config('app.name') }}</h2>
            @if($this->order->branch?->address)
                <p style="margin: 2px 0; font-size: 11px;">{{ $this->order->branch->address }}</p>
            @endif
            @if($this->order->branch?->phone)
                <p style="margin: 2px 0; font-size: 11px;">Tel: {{ $this->order->branch->phone }}</p>
            @endif
        </div>

        @if($this->config?->receipt_header)
            <div class="divider"></div>
            <p class="text-center" style="font-size: 11px; margin: 8px 0;">{{ $this->config->receipt_header }}</p>
        @endif

        <div class="divider"></div>

        {{-- Order info --}}
        <div style="font-size: 12px;">
            <div class="row"><span>Order:</span><span>{{ $this->order->order_number }}</span></div>
            <div class="row"><span>Date:</span><span>{{ $this->order->ordered_at?->format('Y-m-d H:i') }}</span></div>
            @if($this->order->table)
                <div class="row"><span>Table:</span><span>T{{ $this->order->table->table_number }}</span></div>
            @endif
            <div class="row"><span>Type:</span><span>{{ ucfirst($this->order->order_type) }}</span></div>
            @if($this->order->customer_name)
                <div class="row"><span>Customer:</span><span>{{ $this->order->customer_name }}</span></div>
            @endif
            @if($this->order->user)
                <div class="row"><span>Server:</span><span>{{ $this->order->user->name }}</span></div>
            @endif
        </div>

        <div class="divider border-dashed"></div>

        {{-- Items --}}
        <div style="font-size: 12px;">
            @foreach($this->order->items as $item)
                <div style="margin-bottom: 4px;">
                    <div class="row">
                        <span>{{ $item->quantity }}× {{ $item->menu_item_name }}</span>
                        <span>{{ number_format($item->total_price, 2) }}</span>
                    </div>
                    @if($item->modifiers)
                        <div style="font-size: 10px; color: #666; padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 12px;">
                            {{ is_array($item->modifiers) ? implode(', ', $item->modifiers) : $item->modifiers }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="divider border-dashed"></div>

        {{-- Totals --}}
        <div style="font-size: 12px;">
            <div class="row"><span>Subtotal:</span><span>{{ number_format($this->order->subtotal, 2) }}</span></div>
            @if($this->order->tax > 0)
                <div class="row"><span>Tax:</span><span>{{ number_format($this->order->tax, 2) }}</span></div>
            @endif
            @if($this->order->discount > 0)
                <div class="row"><span>Discount:</span><span>-{{ number_format($this->order->discount, 2) }}</span></div>
            @endif
            <div class="row" style="font-size: 14px; font-weight: bold; margin-top: 4px;">
                <span>TOTAL:</span><span>{{ number_format($this->order->total, 2) }}</span>
            </div>
            @if($this->order->payment_method)
                <div class="row"><span>Payment:</span><span>{{ ucfirst($this->order->payment_method) }}</span></div>
            @endif
        </div>

        @if($this->config?->receipt_footer)
            <div class="divider"></div>
            <p class="text-center" style="font-size: 11px; margin: 8px 0;">{{ $this->config->receipt_footer }}</p>
        @endif

        <div class="divider"></div>
        <p class="text-center" style="font-size: 10px; margin-top: 8px;">{{ config('app.name') }} — Thank you!</p>
    </div>
@else
    <div class="receipt-paper text-center">
        <p>Order not found.</p>
    </div>
@endif
</div>