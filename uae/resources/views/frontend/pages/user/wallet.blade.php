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
                        <a href="{{ route('user.submit_reel') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-2">
                            <i class="las la-video me-3 fs-4 text-primary"></i> Reel Link Submit
                        </a>
                        <a href="{{ route('user.wallet') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 mb-2 active bg-primary-subtle text-primary">
                            <i class="las la-coins me-3 fs-4"></i> User Wallet
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

        <div class="col-lg-9">
            <div class="row g-4">
                {{-- Wallet Balance Card --}}
                <div class="col-md-5">
                    <div class="card border-0 shadow text-white h-100" style="background: linear-gradient(135deg, #f39c12 0%, #d35400 100%); border-radius: 20px; overflow: hidden;">
                        <div class="card-body p-4 position-relative">
                            <div class="position-absolute end-0 top-0 p-3 opacity-25">
                                <i class="las la-wallet" style="font-size: 8rem;"></i>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                                    <i class="las la-coins fs-2 text-white"></i>
                                </div>
                                <h5 class="mb-0 fw-semibold">Available Balance</h5>
                            </div>
                            <h1 class="display-4 fw-bold mb-2">{{ number_format($user->wallet_balance, 0) }}</h1>
                            <p class="fs-5 mb-0 opacity-75">Coins <small class="ms-2">(1 Coin = ₹1)</small></p>
                        </div>
                        <div class="card-footer bg-black bg-opacity-10 border-0 py-3 text-center">
                            <small class="fw-medium">Coins can be used for your next purchase</small>
                        </div>
                    </div>
                </div>

                {{-- Referral Code Card --}}
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; border: 1px dashed #dee2e6 !important;">
                        <div class="card-body p-4 d-flex flex-column justify-content-center text-center">
                            <h6 class="text-uppercase text-muted fw-bold mb-3" style="letter-spacing: 1px;">Your Referral Code</h6>
                            @if(auth()->user()->referralCoupon)
                                <div class="d-flex align-items-center justify-content-center bg-light rounded-pill p-2 mb-3 border">
                                    <span class="fs-3 fw-bold text-dark px-4" id="refCode">{{ auth()->user()->referralCoupon->code }}</span>
                                    <button class="btn btn-primary rounded-pill px-4" onclick="copyToClipboard('{{ auth()->user()->referralCoupon->code }}')">
                                        <i class="las la-copy me-1"></i> Copy
                                    </button>
                                </div>
                                <p class="mb-0 text-muted">Invite a friend & get <span class="text-dark fw-bold">25 Coins</span> instantly on their first purchase!</p>
                            @else
                                <div class="alert alert-warning rounded-4 mb-0">
                                    <i class="las la-lock me-2"></i> Finish your first order to unlock your referral code!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- How to Earn Guide --}}
                <div class="col-12 mt-4">
                    <div class="card border-0 shadow-sm bg-white" style="border-radius: 20px;">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="fw-bold mb-0 text-dark">How to Earn More Coins?</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start p-3 rounded-4 bg-primary-subtle h-100">
                                        <i class="las la-shield-alt fs-1 text-primary me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Nazar Suraksha Cashback</h6>
                                            <p class="small text-muted mb-0">Buy Nazar Suraksha Bracelet, post a reel, and get <span class="text-primary fw-bold">100% money back</span> in your wallet.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start p-3 rounded-4 bg-success-subtle h-100">
                                        <i class="las la-user-friends fs-1 text-success me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Refer & Earn</h6>
                                            <p class="small text-muted mb-0">When your friend uses your code, they get a discount & you get <span class="text-success fw-bold">25 Coins</span> automatically.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transaction Table --}}
                <div class="col-12 mt-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="card-header bg-white py-4 px-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold"><i class="las la-history me-2 text-warning"></i>Coin History</h5>
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2 border">Recent Transactions</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 border-0">Transaction Details</th>
                                            <th class="text-center py-3 border-0">Amount</th>
                                            <th class="text-end pe-4 py-3 border-0">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $tx)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-light p-2 me-3">
                                                        <i class="las {{ $tx->type == 'credit' ? 'la-arrow-up text-success' : 'la-arrow-down text-danger' }} fs-5"></i>
                                                    </div>
                                                    <span class="text-dark fw-medium">{{ $tx->description }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="{{ $tx->type == 'credit' ? 'text-success' : 'text-danger' }} fw-bold fs-5">
                                                    {{ $tx->type == 'credit' ? '+' : '-' }}{{ number_format($tx->amount, 0) }}
                                                    <i class="las la-coins text-warning"></i>
                                                </span>
                                            </td>
                                            <td class="text-end pe-4 py-3">
                                                <span class="text-muted">{{ $tx->created_at->format('d M, Y') }}</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="mb-3 opacity-25">
                                                <p class="text-muted mb-0">No transactions yet. Start earning coins!</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Referral Code Copied: ' + text);
    });
}
</script>

<style>
    .list-group-item-action:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        transition: all 0.3s ease;
    }
    .active {
        border-right: 4px solid #f39c12 !important;
    }
    .border-dashed {
        border: 2px dashed #f39c12 !important;
    }
    .bg-primary-subtle { background-color: #e7f1ff !important; }
    .bg-success-subtle { background-color: #e6fcf5 !important; }
</style>
@endsection
