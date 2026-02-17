@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User Management /</span> Wallet & Rewards</h4>

    {{-- Tabs Navigation --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-affiliates" aria-selected="true">
                    Affiliate Payouts (10%)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-customers" aria-selected="false">
                    Normal Customers (5%)
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- 🟢 Tab 1: Affiliates (Payout Available) --}}
            <div class="tab-pane fade show active" id="navs-affiliates" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Affiliate Name</th>
                                <th>Balance (Cashable)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($affiliates as $affiliate)
                            <tr>
                                <td><strong>{{ $affiliate->name }}</strong><br><small>{{ $affiliate->phone }}</small></td>
                                <td><span class="badge bg-label-success fs-6">{{ $affiliate->wallet_balance }} Coins</span></td>
                                <td>
                                    @if($affiliate->wallet_balance > 0)
                                        <form action="{{ route('admin.rewards.payout', $affiliate->id) }}" method="POST" onsubmit="return confirm('Are you sure you paid this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bx bx-money me-1"></i> Settle & Zero
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Paid Up</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    {{ $affiliates->links('pagination::bootstrap-5') }}
                </div>
            </div>

            {{-- ⚪ Tab 2: Normal Customers (Read-Only Balance) --}}
            <div class="tab-pane fade" id="navs-customers" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Wallet Balance</th>
                                <th>Joined At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td><strong>{{ $customer->name }}</strong><br><small>{{ $customer->phone }}</small></td>
                                <td><span class="fw-bold">{{ $customer->wallet_balance }} Coins</span></td>
                                <td>{{ $customer->created_at->format('d M, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
