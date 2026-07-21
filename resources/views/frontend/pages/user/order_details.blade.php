@extends('frontend.layouts.app')

@section('content')
    <div class="container py-5">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('user.orders') }}" class="text-decoration-none text-dark fw-bold">
                <i class="las la-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- 📦 Order Items --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Items</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($order->items as $item)
                            <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                <div class="me-3">
                                    <img src="{{ asset($item->product->main_image ?? 'assets/img/placeholder.jpg') }}"
                                        width="80" class="rounded border bg-light">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $item->product_name }}</h6>
                                    <div class="small text-muted mb-2">
                                        <span>Qty: {{ $item->quantity }}</span>
                                        {{-- 🚀 नया: वजन (Weight) यहाँ दिखाएँ --}}
                                        @if ($item->weight)
                                            <span class="ms-3 border-start ps-3 text-dark"><strong>Weight:</strong>
                                                {{ $item->weight }}</span>
                                        @endif

                                        @if ($item->ring_size)
                                            <span class="ms-3 border-start ps-3 text-dark"><strong>Size:</strong>
                                                {{ $item->ring_size }}</span>
                                        @endif
                                    </div>

                                    {{-- ✨ Siddh Details --}}
                                    @if ($item->is_siddh)
                                        <div class="mt-1">
                                            <span class="badge bg-warning text-dark x-small"><i class="las la-star"></i>
                                                Siddh Enabled</span>
                                            <small class="text-muted ms-1">(Incl.
                                                ₹{{ number_format($item->siddh_amount, 2) }})</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    {{-- पुरानी MRP --}}
                                    <p class="text-muted text-decoration-line-through small mb-0">
                                        ₹{{ number_format($item->mrp_price * $item->quantity, 2) }}
                                    </p>
                                    {{-- 🚀 नया: आइटम का फाइनल टोटल (Siddh मिलाकर) --}}
                                    <p class="fw-bold mb-0" style="font-size: 1.1rem; color: #000;">
                                        ₹{{ number_format($item->total_price, 2) }}
                                    </p>
                                    <small class="text-muted">(₹{{ number_format($item->price + $item->siddh_amount) }} x
                                        {{ $item->quantity }})</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Status (Same as your code) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Status</h5>
                    </div>
                    <div class="card-body">
                        @if ($order->status == 'cancelled')
                            <div class="alert alert-danger text-center"><i class="las la-times-circle fs-2"></i>
                                <h5 class="mt-2">Cancelled</h5>
                            </div>
                        @else
                            <div class="track-container">
                                <div class="track-line"></div>
                                <div class="track-step active">
                                    <div class="icon"><i class="las la-clipboard-check"></i></div>
                                    <div class="text">Order Placed</div>
                                    <div class="date">{{ date('d M', strtotime($order->created_at)) }}</div>
                                </div>
                                <div
                                    class="track-step {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="icon"><i class="las la-cog"></i></div>
                                    <div class="text">Processing</div>
                                </div>
                                <div
                                    class="track-step {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="icon"><i class="las la-shipping-fast"></i></div>
                                    <div class="text">Shipped</div>
                                </div>
                                <div class="track-step {{ $order->status == 'delivered' ? 'active' : '' }}">
                                    <div class="icon"><i class="las la-box-open"></i></div>
                                    <div class="text">Delivered</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 🚫 CANCEL ORDER SECTION --}}
                @if (!in_array($order->status, ['shipped', 'delivered', 'cancelled']))
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold text-danger mb-2">Need to cancel?</h6>
                            {{-- <p class="small text-muted mb-3">You can cancel this order before it is shipped.</p> --}}

                            <form id="cancelOrderForm">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="button" onclick="confirmCancellation()" id="btn_cancel_order"
                                    class="btn btn-outline-danger w-100 fw-bold rounded-pill">
                                    <i class="las la-times-circle"></i> Cancel Order
                                </button>
                            </form>

                            @if ($order->payment_status == 'paid')
                                <div class="alert alert-info mt-3 py-2 px-3 small border-0 mb-0"
                                    style="border-radius: 10px;">
                                    <i class="las la-info-circle"></i>
                                    <strong>Refund Info:</strong> Since this is a paid order, your refund will be initiated
                                    to
                                    your original payment method.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                {{-- 💰 💳 BILLING SUMMARY (Updated with Siddh Row) --}}
                <div class="card border-0 shadow-sm mb-4"
                    style="background-color: #fdfaf4; border: 1px solid #f1e0c5 !important;">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark">Payment Breakdown</h6>
                    </div>
                    <div class="card-body pt-0">
                        {{-- Total MRP --}}
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Total MRP</span>
                            <span class="text-dark">₹{{ number_format($order->mrp_total, 2) }}</span>
                        </div>

                        {{-- Product Discount --}}
                        @php
                            $sellingPriceSubtotal = $order->items->sum(fn($i) => $i->price * $i->quantity);
                            $productDiscount = $order->mrp_total - $sellingPriceSubtotal;
                        @endphp
                        @if ($productDiscount > 0)
                            <div class="d-flex justify-content-between mb-2 small text-success fw-bold">
                                <span>Product Discount</span>
                                <span>- ₹{{ number_format($productDiscount, 2) }}</span>
                            </div>
                        @endif

                        {{-- 🎁 Coupons/Rewards (Admin + Game + Prepaid) --}}
                        @php
                            $totalCoupons =
                                $order->coupon_discount +
                                $order->gaming_discount +
                                $order->prepaid_discount +
                                ($order->wallet_amount ?? 0);
                        @endphp

                        @if ($totalCoupons > 0)
                            <div class="d-flex justify-content-between mb-2 small text-danger fw-bold">
                                <span>Total Offers Applied</span>
                                <span>- ₹{{ number_format($totalCoupons, 2) }}</span>
                            </div>
                        @endif

                        {{-- Siddh Charge --}}
                        @php $totalSiddh = $order->items->sum(fn($i) => $i->siddh_amount * $i->quantity); @endphp
                        @if ($totalSiddh > 0)
                            <div class="d-flex justify-content-between mb-2 small text-dark fw-bold">
                                <span>Siddh Protection Charge</span>
                                <span>+ ₹{{ number_format($totalSiddh, 2) }}</span>
                            </div>
                        @endif

                        {{-- 🚀 नया: अगर ऑर्डर में COD शिपिंग चार्ज सेव है, तो यूज़र को साफ़-साफ़ अलग रो में दिखेगा --}}
                        @if (isset($order->shipping_charge) && $order->shipping_charge > 0)
                            <div class="d-flex justify-content-between mb-2 small text-danger fw-bold">
                                <span><i class="las la-truck"></i> COD Charge</span>
                                <span>+ ₹{{ number_format($order->shipping_charge, 2) }}</span>
                            </div>
                        @endif

                        {{-- 🎁 Packing Type Display with Image (नया कोड यहाँ डालना है) --}}
                        @if(isset($order->packing_type) && $order->packing_type == 'wooden_box')
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-2 p-2 rounded" style="background-color: #fcf6ec; border: 1px dashed #c49a6c;">
                                <div>
                                    <span class="small fw-bold d-block" style="color: #8b5a2b;"><i class="las la-box fs-5"></i> Packing Type</span>
                                    <span class="badge bg-success mt-1">Premium Wooden Box (FREE)</span>
                                </div>
                                {{-- 📸 Wooden Box Image --}}
                                <img src="{{ asset('assets/img/wooden_box.png') }}" alt="Wooden Box" class="rounded border shadow-sm" style="width: 55px; height: 55px; object-fit: cover;">
                            </div>
                        @else
                            <div class="d-flex justify-content-between mt-3 mb-2 small fw-bold text-muted">
                                <span><i class="las la-box fs-5"></i> Packing Type</span>
                                <span>Standard Suyagya Box</span>
                            </div>
                        @endif

                        <hr class="my-3 border-dark opacity-10">

                        {{-- 🔥 Order Grand Total --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Order Total</h6>
                            <h6 class="fw-bold text-dark mb-0">₹{{ number_format($order->total_amount, 2) }}</h6>
                        </div>

                        {{-- 🔵 PARTIAL PAYMENT DETAILS --}}
                        @if ($order->is_partial)
                            <div class="p-3 rounded-3 mb-2" style="background-color: #fff; border: 1px dashed #28a745;">
                                <div class="d-flex justify-content-between mb-1 small text-success fw-bold">
                                    <span><i class="las la-check-circle"></i> Paid Online (Advance)</span>
                                    <span>- ₹{{ number_format($order->total_amount - $order->balance_amount, 2) }}</span>
                                </div>
                                <div
                                    class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-light">
                                    <span class="fw-bold text-dark" style="font-size: 14px;">Payable at Delivery
                                        (COD)</span>
                                    <span
                                        class="badge bg-success fs-6">₹{{ number_format($order->balance_amount, 2) }}</span>
                                </div>
                            </div>
                            <p class="x-small text-muted text-center mt-2 mb-0">
                                <i class="las la-info-circle"></i> Please pay the balance amount to the delivery partner.
                            </p>
                        @else
                            {{-- Non-Partial Orders --}}
                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3"
                                style="background-color: #e8f5e9;">
                                <span
                                    class="fw-bold text-success">{{ $order->payment_status == 'paid' ? 'Total Paid' : 'To be Paid' }}</span>
                                <span
                                    class="fw-bold text-success fs-5">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- Order Summary Info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <p class="mb-2"><strong>Order ID:</strong> #{{ $order->id }}</p>
                        <p class="mb-2"><strong>Date:</strong> {{ date('d M Y', strtotime($order->created_at)) }}</p>
                        <p class="mb-2">
                            <strong>Payment:</strong>
                            <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                {{ strtoupper($order->payment_status) }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                    </div>
                </div>

                {{-- 🏠 Shipping Address --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Delivery Address</h6>
                        @php
                            $addr = is_array($order->shipping_address)
                                ? $order->shipping_address
                                : json_decode($order->shipping_address, true);
                        @endphp

                        @if ($addr)
                            <p class="mb-1 fw-bold">{{ $addr['name'] ?? 'N/A' }}</p>
                            <p class="mb-1 small text-muted">{{ $addr['address_line1'] ?? '' }}</p>
                            <p class="mb-1 small text-muted">{{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }} -
                                {{ $addr['pincode'] ?? '' }}</p>
                            <p class="mb-0 small"><strong>Phone:</strong> {{ $addr['phone'] ?? '' }}</p>
                        @else
                            <p class="text-muted">Address not available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmCancellation() {
            Swal.fire({
                title: 'Confirm Cancellation?',
                text: "Are you sure you want to cancel this order? This action cannot be reversed.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Cancel Order',
                cancelButtonText: 'No, Keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitCancelRequest();
                }
            })
        }

        function submitCancelRequest() {
            var btn = $('#btn_cancel_order');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Cancelling...');

            $.ajax({
                url: "{{ route('checkout.cancel') }}", // ✅ आपके द्वारा बताया गया रूट नेम
                type: "POST",
                data: $('#cancelOrderForm').serialize(),
                success: function(res) {
                    if (res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Cancelled!',
                            text: res.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // स्टेटस अपडेट करने के लिए रीलोड
                        });
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                        btn.prop('disabled', false).html(
                            '<i class="las la-times-circle fs-5 me-1"></i> Cancel My Order');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to process cancellation. Please contact support.', 'error');
                    btn.prop('disabled', false).html(
                        '<i class="las la-times-circle fs-5 me-1"></i> Cancel My Order');
                }
            });
        }
    </script>
@endsection
