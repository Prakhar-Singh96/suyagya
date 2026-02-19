@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Logistic Manager (Shipping Dashboard)</h4>

    {{-- Error/Success Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>BigShip Status</th> {{-- New Column --}}
                        <th>Courier / AWB</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($orders as $order)
                    <tr>
                        {{-- 1. Order Number --}}
                        <td>
                            <strong>{{ $order->order_number }}</strong><br>
                            <span class="text-muted small">{{ $order->created_at->format('d M, Y') }}</span>
                        </td>

                        {{-- 2. Customer Info --}}
                        <td>
                            @php
                                $addr = is_array($order->shipping_address) ? $order->shipping_address : json_decode($order->shipping_address, true);
                            @endphp
                            <span class="fw-bold">{{ $addr['name'] ?? 'Guest' }}</span><br>
                            <small>{{ $addr['phone'] ?? '' }}</small>
                        </td>

                        {{-- 3. BigShip Status Logic --}}
                        <td>
                            @if($order->awb_number)
                                {{-- Case A: Shipped (AWB Generated) --}}
                                <span class="badge bg-success">Shipped</span>
                            @elseif($order->system_order_id)
                                {{-- Case B: Order Created on BigShip but Not Manifested --}}
                                <span class="badge bg-info">Draft Created</span><br>
                                <small class="text-muted">ID: {{ $order->system_order_id }}</small>
                            @else
                                {{-- Case C: Not Started --}}
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>

                        {{-- 4. Courier Info --}}
                        <td>
                            @if($order->awb_number)
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-primary">{{ $order->courier_name }}</span>
                                    <small>AWB: {{ $order->awb_number }}</small>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        {{-- 5. Action Buttons --}}
                        <td>
                            @if($order->awb_number)
                                {{-- Agar Ship ho gya hai to Label Download aur Track ka option --}}
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.logistic.label', $order->id) }}">
                                            <i class="bx bx-printer me-1"></i> Print Label
                                        </a>

                                        {{-- Cancel Form --}}
                                        <form action="{{ route('admin.logistic.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this shipment?');">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-1"></i> Cancel Ship
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($order->system_order_id)
                                {{-- Agar Order Create ho gya par Manifest nahi hua --}}
                                {{-- Seedha Rates wale page par bhejo, wapas create nahi karna --}}
                                <form action="{{ route('admin.logistic.create_order', $order->id) }}" method="POST">
                                    @csrf
                                    {{-- Hidden fields required for validation pass (Dummy values kyuki ID already hai) --}}
                                    <input type="hidden" name="warehouse_id" value="1">
                                    <input type="hidden" name="weight" value="0.5">
                                    <input type="hidden" name="length" value="10">
                                    <input type="hidden" name="width" value="10">
                                    <input type="hidden" name="height" value="10">

                                    <button type="submit" class="btn btn-sm btn-info">
                                        Select Courier
                                    </button>
                                </form>
                            @else
                                {{-- Bilkul Naya Order --}}
                                <a href="{{ route('admin.logistic.ship', $order->id) }}" class="btn btn-sm btn-primary">
                                    Ship Now
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No Orders Ready for Shipping.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
