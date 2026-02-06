@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between mb-4">
            <h4 class="fw-bold">Order Details: #{{ $order->order_number }}</h4>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="row">
            {{-- LEFT SIDE: Products & Info --}}
            <div class="col-md-8">
                <div class="card mb-4">
                    <h5 class="card-header">Ordered Items</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset($item->product->main_image ?? '') }}" width="50"
                                                    class="rounded me-2">
                                                {{ $item->product_name }}
                                                @if ($item->is_siddh)
                                                    <small class="text-warning d-block">+
                                                        ₹{{ number_format($item->siddh_amount) }} Energization
                                                        Charge</small>
                                                @endif
                                                @if ($item->ring_size)
                                                    <span class="badge bg-label-secondary small">Size:
                                                        {{ $item->ring_size }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>₹{{ number_format($item->price) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ number_format($item->total_price) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- 📊 ORDER SUMMARY & CALCULATIONS --}}
                <div class="card mb-4">
                    <h5 class="card-header border-bottom">Order Price Summary</h5>
                    <div class="card-body pt-3">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <p class="d-flex justify-content-between"><span>Items Total (MRP):</span>
                                    <strong>₹{{ number_format($order->mrp_total) }}</strong>
                                </p>
                                <p class="d-flex justify-content-between text-success"><span>Coupon Discount
                                        ({{ $order->coupon_code ?? 'None' }}):</span> <strong>-
                                        ₹{{ number_format($order->coupon_discount) }}</strong></p>
                                <p class="d-flex justify-content-between text-success"><span>Gaming/Lucky Draw:</span>
                                    <strong>- ₹{{ number_format($order->gaming_discount) }}</strong>
                                </p>
                                <p class="d-flex justify-content-between text-success"><span>Prepaid Discount:</span>
                                    <strong>- ₹{{ number_format($order->prepaid_discount) }}</strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="d-flex justify-content-between fs-5 fw-bold text-primary">
                                    <span>Final Amount:</span>
                                    <span>₹{{ number_format($order->total_amount) }}</span>
                                </p>
                                <p class="d-flex justify-content-between small"><span>Payment Method:</span> <span
                                        class="badge bg-label-info">{{ strtoupper($order->payment_method) }}</span></p>
                                <p class="d-flex justify-content-between small"><span>Payment Status:</span>
                                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ strtoupper($order->payment_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 📝 CUSTOMER ACCOUNT INFO --}}
                <div class="card mb-4 border-info">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">Registered Customer Details</h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3 bg-label-primary p-2 rounded">
                                <i class="las la-user fs-3"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-bold">{{ $order->user->name ?? 'Guest User' }}</p>
                                <p class="mb-0 small text-muted">{{ $order->user->email ?? 'No Email' }}</p>
                                <p class="mb-0 small text-muted">User ID: #{{ $order->user_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="card">
                    <h5 class="card-header">Shipping Details</h5>
                    <div class="card-body">
                        @php
                            $addr = is_array($order->shipping_address)
                                ? $order->shipping_address
                                : json_decode($order->shipping_address, true);
                        @endphp
                        <p><strong>Name:</strong> {{ $addr['name'] ?? '' }}</p>
                        <p><strong>Phone:</strong> {{ $addr['phone'] ?? '' }}</p>
                        <p><strong>Address:</strong> {{ $addr['address_line1'] ?? '' }}, {{ $addr['city'] ?? '' }},
                            {{ $addr['state'] ?? '' }} - {{ $addr['pincode'] ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- RIGHT SIDE: Status Update --}}
            <div class="col-md-4">
                <div class="card mb-4">
                    <h5 class="card-header">Update Status</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Order Status --}}
                            <div class="mb-3">
                                <label class="form-label">Order Status</label>
                                <select name="status" class="form-select" id="orderStatus"
                                    onchange="toggleTracking(this.value)">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                        Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped
                                        (Dispatched)</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                        Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                </select>
                            </div>

                            {{-- Payment Status --}}
                            <div class="mb-3">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select">
                                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>
                                        Failed</option>
                                </select>
                            </div>

                            {{-- 🚚 Tracking Details (Hidden by default, shown via JS) --}}
                            <div id="trackingBox"
                                style="display: {{ $order->status == 'shipped' || old('status') == 'shipped' ? 'block' : 'none' }}; border:1px solid #ddd; padding:10px; border-radius:5px; background:#f9f9f9;"
                                class="mb-3">
                                <h6 class="text-primary mb-3">Tracking Information</h6>

                                <div class="mb-2">
                                    <label class="small fw-bold">Courier Name *</label>
                                    <input type="text" name="courier_name" class="form-control form-control-sm"
                                        value="{{ old('courier_name', $order->courier_name) }}"
                                        placeholder="e.g. BigShip / BlueDart">
                                </div>

                                <div class="mb-2">
                                    <label class="small fw-bold">AWB / Tracking ID *</label>
                                    <input type="text" name="awb_number" class="form-control form-control-sm"
                                        value="{{ old('awb_number', $order->awb_number) }}" placeholder="e.g. 123456789">
                                </div>

                                <div class="mb-2">
                                    <label class="small fw-bold">Tracking URL (Optional)</label>
                                    <input type="text" name="tracking_url" class="form-control form-control-sm"
                                        value="{{ old('tracking_url', $order->tracking_url) }}" placeholder="https://...">
                                </div>

                                <div class="mb-2">
                                    <label class="small fw-bold">Expected Delivery</label>
                                    <input type="date" name="expected_delivery_date" class="form-control form-control-sm"
                                        value="{{ old('expected_delivery_date', $order->expected_delivery_date) }}">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Update Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Simple Script to Toggle Tracking Fields --}}
    <script>
        function toggleTracking(status) {
            const box = document.getElementById('trackingBox');
            if (status === 'shipped') {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }
    </script>
@endsection
