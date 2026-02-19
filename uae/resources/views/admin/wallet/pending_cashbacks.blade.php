@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Wallet /</span> Nazar Suraksha Cashbacks
    </h4>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 🚀 TABS NAVIGATION --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active fw-bold" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pending" aria-controls="navs-pending" aria-selected="true">
                    Pending Reels ({{ $pendingOrders->total() }})
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link fw-bold text-success" role="tab" data-bs-toggle="tab" data-bs-target="#navs-approved" aria-controls="navs-approved" aria-selected="false">
                    Approved History
                </button>
            </li>
        </ul>
        <div class="tab-content p-0">

            {{-- ✅ TAB 1: PENDING REELS --}}
            <div class="tab-pane fade show active" id="navs-pending" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount to Credit</th>
                                <th>Instagram Reel</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingOrders as $order)
                                <tr>
                                    <td><span class="fw-medium">#{{ $order->order_number }}</span></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $order->user->name }}</span>
                                            <small class="text-muted">{{ $order->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            // 1. कुल डिस्काउंट निकालें
                                            $totalDiscounts = ($order->coupon_discount ?? 0) + ($order->gaming_discount ?? 0) + ($order->prepaid_discount ?? 0);
                                            // 2. बेस टोटल (MRP + Siddh)
                                            $orderBaseTotal = $order->items->sum('total_price');
                                            $itemAmount = 0;

                                            foreach($order->items as $item) {
                                                if(stripos($item->product_name, 'Nazar Suraksha') !== false) {
                                                    $itemBasePrice = $item->total_price;
                                                    // 3. Pro-rata डिस्काउंट हिस्सा
                                                    $itemProportion = $orderBaseTotal > 0 ? ($itemBasePrice / $orderBaseTotal) : 0;
                                                    $itemAmount += ($itemBasePrice - round($totalDiscounts * $itemProportion));
                                                }
                                            }
                                            // 4. सुरक्षा चेक
                                            $itemAmount = min($itemAmount, $order->total_amount);
                                        @endphp
                                        <span class="text-success fw-bold">₹{{ number_format($itemAmount, 2) }}</span>
                                        <br><small class="text-muted" style="font-size: 9px;">(Effective Price)</small>
                                    </td>
                                    <td>
                                        <a href="{{ $order->reel_link }}" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="bx bxl-instagram me-1"></i> View Reel
                                        </a>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.wallet.approve', $order->id) }}" method="POST" onsubmit="return confirm('Credit ₹{{ $itemAmount }} to user wallet?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Approve & Credit</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4">No pending reels!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    {{ $pendingOrders->appends(['page_approved' => $approvedOrders->currentPage()])->links('pagination::bootstrap-5') }}
                </div>
            </div>

            {{-- ✅ TAB 2: APPROVED HISTORY --}}
            <div class="tab-pane fade" id="navs-approved" role="tabpanel">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount Credited</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approvedOrders as $order)
                                <tr>
                                    <td><span class="text-muted">#{{ $order->order_number }}</span></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>
                                        @php
                                            // पेंडिंग वाला ही सेम लॉजिक यहाँ भी ताकि सही अमाउंट दिखे
                                            $totalDiscounts = ($order->coupon_discount ?? 0) + ($order->gaming_discount ?? 0) + ($order->prepaid_discount ?? 0);
                                            $orderBaseTotal = $order->items->sum('total_price');
                                            $creditedAmount = 0;

                                            foreach($order->items as $item) {
                                                if(stripos($item->product_name, 'Nazar Suraksha') !== false) {
                                                    $itemBasePrice = $item->total_price;
                                                    $itemProportion = $orderBaseTotal > 0 ? ($itemBasePrice / $orderBaseTotal) : 0;
                                                    $creditedAmount += ($itemBasePrice - round($totalDiscounts * $itemProportion));
                                                }
                                            }
                                            // सुरक्षा चेक: अगर लॉजिक फेल हो तो वॉलेट ट्रांजैक्शन से बैकअप उठाएं
                                            $creditedAmount = min($creditedAmount, $order->total_amount);
                                        @endphp
                                        <span class="text-success fw-bold">₹{{ number_format($creditedAmount, 2) }}</span>
                                    </td>
                                    <td><span class="badge bg-label-success">Credited</span></td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4">No approval history found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    {{ $approvedOrders->appends(['page_pending' => $pendingOrders->currentPage()])->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- 🛠️ JS to keep active tab on page refresh --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var url = new URL(window.location.href);
        if (url.searchParams.has('page_approved')) {
            var tab = new bootstrap.Tab(document.querySelector('[data-bs-target="#navs-approved"]'));
            tab.show();
        }
    });
</script>
@endsection
