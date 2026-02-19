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

                                        {{-- 💍 Ring Size Display (New) --}}
                                        @if($item->ring_size)
                                            <span class="ms-3 border-start ps-3 text-dark">
                                                <strong>Size:</strong> {{ $item->ring_size }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- ✨ Siddh Details (New) --}}
                                    @if ($item->is_siddh)
                                        <div class="mt-1">
                                            <span class="badge bg-warning text-dark x-small">
                                                <i class="las la-star"></i> Siddh Enabled
                                            </span>
                                            <small class="text-muted ms-1">(Incl. ₹{{ number_format($item->siddh_amount, 2) }})</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <p class="fw-bold mb-0">₹{{ number_format($item->price, 2) }}</p>
                                    {{-- Selling Price vs Base Price logic here --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 🚚 Order Tracking Timeline --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Status</h5>
                    </div>
                    <div class="card-body">
                        @if ($order->status == 'cancelled')
                            <div class="alert alert-danger text-center">
                                <i class="las la-times-circle fs-2"></i>
                                <h5 class="mt-2">This Order has been Cancelled</h5>
                            </div>
                        @else
                            <div class="track-container">
                                <div class="track-line"></div>
                                <div class="track-step active">
                                    <div class="icon"><i class="las la-clipboard-check"></i></div>
                                    <div class="text">Order Placed</div>
                                    <div class="date">{{ date('d M', strtotime($order->created_at)) }}</div>
                                </div>
                                <div class="track-step {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'active' : '' }}">
                                    <div class="icon"><i class="las la-cog"></i></div>
                                    <div class="text">Processing</div>
                                </div>
                                <div class="track-step {{ in_array($order->status, ['shipped', 'delivered']) ? 'active' : '' }}">
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
            </div>

            <div class="col-lg-4">

                {{-- 💰 💳 BILLING SUMMARY (New & Detailed) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold">Payment Details</h6>
                    </div>
                    <div class="card-body bg-light-subtle pt-0">
                        {{-- Total MRP --}}
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Total MRP</span>
                            <span class="text-dark">₹{{ number_format($order->mrp_total, 2) }}</span>
                        </div>

                        {{-- Product Discount (MRP - Subtotal before coupons) --}}
                        @php
                            $sellingPriceTotal = $order->items->sum(fn($i) => $i->price * $i->quantity);
                            $productDiscount = $order->mrp_total - $sellingPriceTotal;
                        @endphp

                        @if($productDiscount > 0)
                        <div class="d-flex justify-content-between mb-2 small text-success">
                            <span>Product Discount</span>
                            <span>- ₹{{ number_format($productDiscount, 2) }}</span>
                        </div>
                        @endif

                        {{-- 🎁 Admin Coupon Discount --}}
                        @if($order->coupon_discount > 0)
                        <div class="d-flex justify-content-between mb-2 small text-primary">
                            <span>Coupon ({{ $order->coupon_code }})</span>
                            <span>- ₹{{ number_format($order->coupon_discount, 2) }}</span>
                        </div>
                        @endif

                        {{-- 🎮 Gaming Reward --}}
                        @if($order->gaming_discount > 0)
                        <div class="d-flex justify-content-between mb-2 small fw-bold text-info">
                            <span><i class="las la-gamepad"></i> Game Reward</span>
                            <span>- ₹{{ number_format($order->gaming_discount, 2) }}</span>
                        </div>
                        @endif

                        <hr class="my-3 opacity-10">

                        {{-- Final Amount --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Total Paid</h5>
                            <h5 class="fw-bold text-primary mb-0">₹{{ number_format($order->total_amount, 2) }}</h5>
                        </div>
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
                            <p class="mb-1 small text-muted">{{ $addr['city'] ?? '' }}, {{ $addr['state'] ?? '' }} - {{ $addr['pincode'] ?? '' }}</p>
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
