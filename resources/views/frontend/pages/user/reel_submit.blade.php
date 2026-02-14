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
                        <a href="{{ route('user.orders') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-2">
                            <i class="las la-shopping-bag me-3 fs-4 text-primary"></i> My Orders
                        </a>
                        <a href="{{ route('user.submit_reel') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-2 active bg-primary-subtle text-primary">
                            <i class="las la-video me-3 fs-4"></i> Reel Link Submit
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

        {{-- Main Content --}}
        <div class="col-lg-9">
            {{-- Header Banner --}}
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #6f42c1 0%, #432b7d 100%); border-radius: 20px;">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="fw-bold mb-2 text-white">Earn 100% Cashback! 🎁</h3>
                            <p class="mb-0 opacity-75">Nazar Suraksha Bracelet पहनकर रील बनाएँ और अपने पूरे पैसे वापस पाएँ!</p>
                        </div>
                        <div class="col-md-4 text-end d-none d-md-block">
                            <i class="lab la-instagram display-3 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ Steps Section (Back Again) --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-3 text-center" style="border-radius: 15px;">
                        <div class="bg-primary-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="las la-truck fs-4 text-primary"></i>
                        </div>
                        <h6 class="fw-bold mb-1">1. Get Delivered</h6>
                        <small class="text-muted" style="font-size: 11px;">आर्डर मिलने के बाद बटन खुलेगा</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-3 text-center" style="border-radius: 15px;">
                        <div class="bg-success-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="lab la-instagram fs-4 text-success"></i>
                        </div>
                        <h6 class="fw-bold mb-1">2. Submit Reel</h6>
                        <small class="text-muted" style="font-size: 11px;">इंस्टाग्राम रील का लिंक यहाँ डालें</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-3 text-center" style="border-radius: 15px;">
                        <div class="bg-warning-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="las la-coins fs-4 text-warning"></i>
                        </div>
                        <h6 class="fw-bold mb-1">3. Get Coins</h6>
                        <small class="text-muted" style="font-size: 11px;">12-24 घंटों में वॉलेट में रिफंड</small>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 d-flex align-items-center">
                <i class="las la-list-ul me-2 text-primary"></i> Eligible Orders
            </h5>

            @forelse($orders as $order)
                <div class="card shadow-sm border-0 mb-3 overflow-hidden" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            @php
                                // 🚀 प्रभावी कैशबैक गणना
                                $totalDiscounts = ($order->coupon_discount ?? 0) + ($order->gaming_discount ?? 0) + ($order->prepaid_discount ?? 0);
                                $orderBaseTotal = $order->items->sum('total_price');
                                $effectiveCashback = 0;
                                $itemName = "";
                                $discountShare = 0;

                                foreach($order->items as $item) {
                                    if(stripos($item->product_name, 'Nazar Suraksha') !== false) {
                                        $itemName = $item->product_name;
                                        $itemBasePrice = $item->total_price;
                                        $itemProportion = $orderBaseTotal > 0 ? ($itemBasePrice / $orderBaseTotal) : 0;
                                        $discountShare = round($totalDiscounts * $itemProportion);
                                        $effectiveCashback += ($itemBasePrice - $discountShare);
                                    }
                                }
                                $effectiveCashback = min($effectiveCashback, $order->total_amount);
                            @endphp

                            <div class="col-md-5 border-end-md">
                                <span class="badge bg-light text-primary fw-bold mb-2">Order #{{ $order->order_number }}</span>
                                <div class="small fw-semibold text-dark mb-1">
                                    <i class="las la-gem text-primary"></i> {{ Str::limit($itemName, 35) }}
                                </div>
                                <a href="javascript:void(0)" class="small text-decoration-none" data-bs-toggle="collapse" data-bs-target="#calc-{{ $order->id }}">
                                    <i class="las la-info-circle"></i> How is cashback calculated?
                                </a>
                            </div>

                            <div class="col-md-7 ps-md-4 mt-3 mt-md-0">
                                @if(strtolower($order->status) != 'delivered')
                                    <div class="d-flex align-items-center p-3 rounded-4 bg-light border">
                                        <i class="las la-lock text-muted fs-3 me-3"></i>
                                        <div>
                                            <span class="d-block fw-bold text-muted small">SUBMISSION LOCKED</span>
                                            <small class="text-muted">बटन आर्डर <b>DELIVERED</b> होने पर खुलेगा।</small>
                                        </div>
                                    </div>
                                @elseif(!$order->reel_link)
                                    <form action="{{ route('user.store_reel') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                            <span class="input-group-text bg-white border-end-0"><i class="lab la-instagram text-danger fs-4"></i></span>
                                            <input type="url" name="reel_link" class="form-control border-start-0 ps-0" placeholder="Paste Reel Link" required>
                                            <button class="btn btn-dark px-4 fw-bold" type="submit">SUBMIT</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="p-3 rounded-4 {{ $order->cashback_status == 'credited' ? 'bg-success-subtle border-success' : 'bg-warning-subtle border-warning' }} border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block fw-bold text-uppercase" style="font-size: 10px;">Verification Status</small>
                                                @if($order->cashback_status == 'credited')
                                                    <span class="text-success fw-bold fs-5"><i class="las la-check-double"></i> ₹{{ number_format($effectiveCashback, 2) }} Credited</span>
                                                @else
                                                    <span class="text-dark fw-bold fs-5"><i class="las la-hourglass-half"></i> Verifying (12-24 Hours)</span>
                                                @endif
                                            </div>
                                            <a href="{{ $order->reel_link }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">View Reel</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Calculation Detail --}}
                            <div class="collapse col-12 mt-3" id="calc-{{ $order->id }}">
                                <div class="p-3 bg-light rounded-3 border small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Item Price:</span>
                                        <span>₹499.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-danger mb-1">
                                        <span>Pro-rata Discount Share:</span>
                                        <span>- ₹{{ number_format($discountShare, 2) }}</span>
                                    </div>
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between fw-bold text-success">
                                        <span>Final Cashback:</span>
                                        <span>₹{{ number_format($effectiveCashback, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 card border-0 shadow-sm bg-white" style="border-radius: 20px;">
                    <div class="card-body">No eligible orders found.</div>
                </div>
            @endforelse

            <div class="alert alert-warning border-0 shadow-sm mt-4 p-4" style="border-radius: 15px;">
                <h6 class="fw-bold text-dark"><i class="las la-info-circle me-2"></i> Important Rules:</h6>
                <ul class="small text-dark mb-0 opacity-75 mt-2">
                    <li>रील कम से कम 24 घंटे तक आपकी प्रोफाइल पर एक्टिव रहनी चाहिए।</li>
                    <li>कैशबैक आपके वॉलेट में <b>Coins</b> के रूप में आएगा (1 Coin = ₹1)।</li>
                    <li>Refer & Earn: अपने दोस्त को रेफर करें और उनके पहले ऑर्डर पर पाएँ <b>25 Coins</b>।</li>
                </ul>
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
