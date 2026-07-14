@extends('frontend.layouts.app')

@section('content')

<div class="container py-5 text-center">


<div class="mb-4">
    <i class="las la-check-circle text-success" style="font-size:100px;"></i>
</div>

<h1 class="fw-bold text-success">Order Placed Successfully!</h1>

<p class="lead">
    Thank you for your purchase.
</p>

<p>
    Order Number:
    <strong>{{ $order->order_number }}</strong>
</p>

<p>
    Amount Paid:
    <strong>₹{{ number_format($order->total_amount,2) }}</strong>
</p>

<a href="{{ route('user.orders') }}"
   class="btn btn-dark mt-3">
   View My Orders
</a>


</div>

@endsection

@section('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {

    console.log('PURCHASE EVENT FIRED');

    // Hotstar Purchase Event
    hspixel('track', 'Purchase', {
        purchase_id: '{{ $order->order_number }}', // Order Number
        value: {{ $order->total_amount }},        // Total Amount
        currency: 'INR',
        content_type: 'product',
        content_ids: [
            @foreach($order->items as $item)
                "{{ $item->product_id }}",
            @endforeach
        ]
    });

    fbq('track', 'Purchase', {
        content_ids: [
            @foreach($order->items as $item)
                "{{ $item->product_id }}",
            @endforeach
        ],
        content_type: 'product',
        value: {{ $order->total_amount }},
        currency: 'INR'
    });

});
</script>

@if(session('show_referral_popup'))

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    Swal.fire({
        title: '<span style="color: #28a745;">🎊 Order Successful!</span>',
        html: `
            <div class="text-center">
                <p class="mb-3">You've unlocked a special reward!</p>

                <div class="p-3 mb-3"
                     style="background:#fff8e1;border:2px dashed #ffb300;border-radius:12px;">

                    <small class="text-muted d-block mb-1">
                        Your Referral Code
                    </small>

                    <h2>{{ session('show_referral_popup') }}</h2>

                </div>
            </div>
        `,
        icon: 'success'
    });

});
</script>

@endif

@endsection
