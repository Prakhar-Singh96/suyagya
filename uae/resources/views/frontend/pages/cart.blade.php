@extends('frontend.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold font-heading mb-4">Shopping Cart</h2>

    @if($cartItems->count() > 0)
        <div class="row">
            {{-- Cart Items List --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        @php $total = 0; @endphp
                        @foreach($cartItems as $item)
                            @php
                                $price = $item->product->price;
                                if($item->is_siddh) { $price += $item->product->siddh_price; } // Add Siddh Price
                                $subtotal = $price * $item->quantity;
                                $total += $subtotal;
                            @endphp

                            <div class="d-flex align-items-center p-3 border-bottom">
                                <img src="{{ asset($item->product->main_image) }}" class="rounded" width="80" height="80" style="object-fit: cover;">

                                <div class="ms-3 flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $item->product->name }}</h6>
                                    @if($item->is_siddh)
                                        <span class="badge bg-warning text-dark small mb-2">Siddh / Energized (+₹{{ $item->product->siddh_price }})</span>
                                    @endif
                                    <div class="text-muted small">Price: ₹{{ number_format($price) }}</div>
                                </div>

                                {{-- Quantity Selector --}}
                                <div class="text-center mx-3" style="width: 120px;">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="changeCartQty({{ $item->id }}, 'minus')">-</button>

                                        <input type="text" class="form-control text-center fw-bold"
                                            id="qty_input_{{ $item->id }}"
                                            value="{{ $item->quantity }}"
                                            readonly>

                                        {{-- Max Stock attribute hidden pass kar rahe hain JS ke liye --}}
                                        <input type="hidden" id="max_stock_{{ $item->id }}" value="{{ $item->product->quantity }}">

                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="changeCartQty({{ $item->id }}, 'plus')">+</button>
                                    </div>
                                </div>

                                <div class="fw-bold mx-3">₹{{ number_format($subtotal) }}</div>

                                <a href="{{ route('cart.remove', $item->id) }}" class="text-danger"><i class="las la-trash fs-4"></i></a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Checkout Sidebar --}}
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span class="fw-bold">₹{{ number_format($total) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping</span>
                            <span class="text-success fw-bold">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4 fs-5 fw-bold">
                            <span>Total</span>
                            <span>₹{{ number_format($total) }}</span>
                        </div>
                        <button class="btn btn-dark w-100 py-3 fw-bold text-uppercase" onclick="openDirectCheckout(this)">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="las la-shopping-cart fs-1 text-muted opacity-50 mb-3" style="font-size: 5rem;"></i>
            <h4>Your cart is empty</h4>
            <a href="{{ url('/') }}" class="btn btn-warning mt-3">Start Shopping</a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function changeCartQty(cartId, action) {
        let input = $('#qty_input_' + cartId);
        let currentQty = parseInt(input.val());
        let maxStock = parseInt($('#max_stock_' + cartId).val());
        let newQty = currentQty;

        if (action === 'plus') {
            if (currentQty < maxStock) {
                newQty = currentQty + 1;
            } else {
                alert('Sorry! Only ' + maxStock + ' units available in stock.');
                return;
            }
        } else {
            if (currentQty > 1) {
                newQty = currentQty - 1;
            } else {
                return; // 1 se kam nahi hone denge
            }
        }

        // UI Update (Instant feedback)
        input.val(newQty);

        // AJAX Request to Backend
        $.ajax({
            url: "{{ route('cart.update.qty') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                cart_id: cartId,
                quantity: newQty
            },
            success: function(response) {
                if(response.status) {
                    // Update Item Subtotal & Grand Total on screen
                    // Hamein Subtotal aur Total ke IDs dene honge HTML me
                    // Page reload is simplest option for accurate totals if IDs missing
                    location.reload();
                } else {
                    alert(response.message);
                    input.val(currentQty); // Revert on error
                }
            }
        });
    }
</script>
@endsection
