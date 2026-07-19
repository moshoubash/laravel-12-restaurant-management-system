<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; padding: 24px;">
    <div style="max-width: 480px; margin: 0 auto; background: white; border-radius: 12px; padding: 32px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <h1 style="font-size: 24px; margin: 0; color: #1a1a1a;">Order Confirmed</h1>
            <p style="color: #666; margin-top: 4px;">Thank you for your order!</p>
        </div>

        <div style="text-align: center; margin-bottom: 24px; padding: 16px; background: #f0fdf4; border-radius: 8px;">
            <p style="font-size: 12px; color: #666; margin: 0;">Order Number</p>
            <p style="font-size: 28px; font-weight: bold; color: #16a34a; margin: 4px 0;">{{ $order->order_number }}</p>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #666;">Customer</td>
                <td style="padding: 8px 0; font-size: 14px; text-align: right;">{{ $order->customer_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #666;">Type</td>
                <td style="padding: 8px 0; font-size: 14px; text-align: right; text-transform: capitalize;">{{ $order->order_type }}</td>
            </tr>
            @if($order->table)
            <tr>
                <td style="padding: 8px 0; font-size: 14px; color: #666;">Table</td>
                <td style="padding: 8px 0; font-size: 14px; text-align: right;">#{{ $order->table->table_number }}</td>
            </tr>
            @endif
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
            <tr style="border-top: 1px solid #e5e5e5;">
                <th style="padding: 12px 0 8px; font-size: 12px; color: #666; text-align: left; text-transform: uppercase;">Item</th>
                <th style="padding: 12px 0 8px; font-size: 12px; color: #666; text-align: center; text-transform: uppercase;">Qty</th>
                <th style="padding: 12px 0 8px; font-size: 12px; color: #666; text-align: right; text-transform: uppercase;">Price</th>
            </tr>
            @foreach($order->items as $item)
            <tr>
                <td style="padding: 6px 0; font-size: 14px;">
                    {{ $item->menu_item_name }}
                    @if($item->modifiers)
                        <br><span style="font-size: 12px; color: #666;">{{ is_array($item->modifiers) ? implode(', ', $item->modifiers) : $item->modifiers }}</span>
                    @endif
                </td>
                <td style="padding: 6px 0; font-size: 14px; text-align: center;">{{ $item->quantity }}</td>
                <td style="padding: 6px 0; font-size: 14px; text-align: right;">${{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </table>

        <div style="border-top: 2px solid #1a1a1a; margin-top: 12px; padding-top: 12px; text-align: right;">
            <p style="margin: 0; font-weight: bold; font-size: 18px;">Total: ${{ number_format($order->total, 2) }}</p>
        </div>

        @if($order->notes)
            <div style="margin-top: 16px; padding: 12px; background: #f9f9f9; border-radius: 8px;">
                <p style="margin: 0; font-size: 13px; color: #666;">Notes:</p>
                <p style="margin: 4px 0 0; font-size: 13px;">{{ $order->notes }}</p>
            </div>
        @endif

        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e5e5e5; text-align: center; font-size: 12px; color: #999;">
            <p style="margin: 0;">{{ config('app.name') }} &mdash; Your order is being prepared.</p>
        </div>
    </div>
</body>
</html>
