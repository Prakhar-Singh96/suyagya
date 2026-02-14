@extends('frontend.layouts.app')

@section('content')

<div class="container py-5">
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 px-2">My Account</h5>
                    <div class="list-group list-group-flush border-0">
                        <a href="{{ route('user.orders') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-2 active bg-primary-subtle text-primary">
                            <i class="las la-shopping-bag me-3"></i> My Orders
                        </a>
                        <a href="{{ route('user.submit_reel') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-2">
                            <i class="las la-video me-3 fs-4 text-primary"></i> Reel Link Submit
                        </a>
                        <a href="{{ route('user.wallet') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-2">
                            <i class="las la-coins me-3 fs-4 text-primary"></i> User Wallet
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action border-0 py-3 rounded-3 text-danger fw-bold">
                                <i class="las la-sign-out-alt me-3 fs-4"></i> Logout
                            </button>
                        </form>
                    </div>
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
<style>
    .bg-primary-subtle { background-color: #e7f1ff !important; }
    .bg-success-subtle { background-color: #e6fcf5 !important; }
    .bg-warning-subtle { background-color: #fff8e1 !important; }
    .border-end-md { border-right: 1px solid #eee; }
    @media (max-width: 768px) { .border-end-md { border-right: none; } }
    .list-group-item-action:hover { transform: translateX(5px); transition: all 0.3s ease; }
    .active { border-right: 4px solid #f39c12 !important; }
</style>
@endsection
@section('scripts')
@if(session('show_referral_popup'))
{{-- SweetAlert2 Library --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '<span style="color: #28a745;">🎊 Order Successful!</span>',
            html: `
                <div class="text-center">
                    <p class="mb-3">You've unlocked a special reward!</p>
                    <div class="p-3 mb-3" style="background: #fff8e1; border: 2px dashed #ffb300; border-radius: 12px;">
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 10px;">Your Referral Code</small>
                        <h2 class="fw-bold text-primary mb-0" style="letter-spacing: 2px;">{{ session('show_referral_popup') }}</h2>
                    </div>
                    <p class="small text-muted mb-3">इस कोड को अपने दोस्तों के साथ शेयर करें। उनके पहले ऑर्डर पर आपको मिलेंगे <b>25 Coins</b>!</p>
                    <div class="d-flex align-items-center justify-content-center bg-light p-2 rounded-3 mb-3">
                        <i class="las la-wallet fs-4 text-warning me-2"></i>
                        <span class="small fw-bold">1 Coin = ₹1 (Next Order Discount)</span>
                    </div>
                </div>
            `,
            icon: 'success',
            confirmButtonText: '<i class="lab la-whatsapp"></i> Share on WhatsApp',
            confirmButtonColor: '#25D366',
            showCancelButton: true,
            cancelButtonText: 'Close',
            customClass: {
                popup: 'rounded-4'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let code = "{{ session('show_referral_popup') }}";
                let shareText = `Hey! I just shopped from Suyagya. Use my code *${code}* on your purchase to get exclusive benefits! 🛍️✨\nCheck here: ${window.location.origin}`;
                window.open(`https://wa.me/?text=${encodeURIComponent(shareText)}`, '_blank');
            }
        });
    });
</script>
@endif
@endsection
