@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Rewards /</span> Referral History
    </h4>

    <div class="card">
        <h5 class="card-header">Referral & Cashback Logs</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User (Beneficiary)</th>
                        <th>Order #</th>
                        <th>Reward Amount</th>
                        <th>Description / Activity</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d M, Y H:i') }}</td>
                            <td>
                                <strong>{{ $log->user->name }}</strong><br>
                                <small class="text-muted">{{ ucfirst($log->user->user_type) }}</small>
                            </td>
                            <td>
                                @if($log->order)
                                    <span class="badge bg-label-primary">#{{ $log->order->order_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success">+ {{ $log->amount }} Coins</span>
                            </td>
                            <td>
                                {{-- यहाँ आपकी ट्रांजेक्शन डिस्क्रिप्शन दिखेगी --}}
                                <span class="text-wrap" style="max-width: 300px; display: block;">
                                    {{ $log->description }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
