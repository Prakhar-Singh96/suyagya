@extends('frontend.layouts.app')

@section('content')

<div class="container py-5">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">My Account</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="{{ route('user.orders') }}" class="text-decoration-none text-primary fw-bold">
                                <i class="las la-shopping-bag me-2"></i> My Orders
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">
                                    <i class="las la-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Order List --}}
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">My Order History</h5>
                </div>
                <div class="card-body">

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            {{-- Order ID --}}
                                            <td>
                                                <span class="fw-bold text-primary">#{{ $order->id }}</span>
                                                <br>
                                                <small class="text-muted">{{ $order->order_number }}</small>
                                            </td>

                                            {{-- Date --}}
                                            <td>{{ date('d M Y', strtotime($order->created_at)) }}</td>

                                            {{-- Total Amount --}}
                                            <td>₹{{ number_format($order->total_amount, 2) }}</td>

                                            {{-- Payment Status Column (Updated) --}}
                                            <td>
                                                @php
                                                    $payStatus = strtolower($order->payment_status);
                                                    $ordStatus = strtolower($order->status);
                                                @endphp

                                                @if($payStatus == 'paid')
                                                    <span class="badge bg-success">PAID</span>
                                                    @if($order->transaction_id)
                                                        <div style="font-size: 10px; margin-top: 2px;">
                                                            TXN: {{ Str::limit($order->transaction_id, 10) }}
                                                        </div>
                                                    @endif
                                                {{-- Agar Order Cancelled hai ya Payment Failed hai --}}
                                                @elseif($ordStatus == 'cancelled' || $payStatus == 'failed')
                                                    <span class="badge bg-danger">FAILED</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">PENDING</span>
                                                @endif
                                            </td>

                                            {{-- Order Status Column (Updated Logic) --}}
                                            <td>
                                                @php
                                                    $status = strtolower($order->status);
                                                    $color = 'secondary'; // Default

                                                    if($status == 'pending')    $color = 'warning text-dark';
                                                    if($status == 'processing') $color = 'info text-white';
                                                    if($status == 'shipped')    $color = 'primary';
                                                    if($status == 'delivered')  $color = 'success';
                                                    if($status == 'cancelled')  $color = 'danger'; // Red color for cancelled
                                                @endphp
                                                <span class="badge bg-{{ $color }} text-uppercase">
                                                    {{ $order->status }}
                                                </span>
                                            </td>

                                            {{-- Actions --}}
                                            <td>
                                                <a href="{{ route('user.order_details', $order->id) }}"
                                                   class="btn btn-sm btn-outline-dark rounded-pill">
                                                    View Details
                                                </a>

                                                {{-- 🔥 Pay Now Button: Sirf tab dikhega jab Pending ho aur Cancel na ho --}}
                                                @if(strtolower($order->payment_status) == 'pending' && strtolower($order->status) != 'cancelled')
                                                    {{-- Note: Is button ke liye apko alag se route/function banana padega --}}
                                                    {{-- <a href="{{ route('retry.payment', $order->id) }}" class="btn btn-sm btn-danger rounded-pill ms-1">Pay Now</a> --}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h4>No orders yet!</h4>
                            <a href="{{ url('/') }}" class="btn btn-primary px-4 rounded-pill">Start Shopping</a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
