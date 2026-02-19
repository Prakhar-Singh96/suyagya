<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; background-color: #333; color: #fff; padding: 10px; border-radius: 8px 8px 0 0; }
        .details { margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #eee; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>{{ $isAdmin ? 'New Order Received!' : 'Order Confirmed!' }}</h2>
    </div>

    <div class="details">
        <p>Hi {{ $isAdmin ? 'Admin' : $order->shipping_address['name'] }},</p>

        @if($isAdmin)
            <p>A new order has been placed. Check details below:</p>
        @else
            <p>Thank you for shopping with Suyagya! Your order has been placed successfully.</p>
        @endif

        <p><strong>Order No:</strong> {{ $order->order_number }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
        <p><strong>Payment Mode:</strong> {{ $order->payment_method }}</p>
    </div>

    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }} {{ $item->is_siddh ? '(Siddh)' : '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Amount: ₹{{ number_format($order->total_amount, 2) }}
    </div>

    <h3>Shipping Address</h3>
    <p>
        {{ $order->shipping_address['name'] }}<br>
        {{ $order->shipping_address['address_line1'] }}<br>
        {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} - {{ $order->shipping_address['pincode'] }}<br>
        <strong>Phone:</strong> {{ $order->shipping_address['phone'] }}<br>
        <strong>Email:</strong> {{ $order->shipping_address['email'] ?? 'N/A' }}
    </p>

    <div style="text-align: center; margin-top: 20px; color: #777; font-size: 12px;">
        &copy; {{ date('Y') }} Suyagya. All rights reserved.
    </div>
</div>

</body>
</html>
