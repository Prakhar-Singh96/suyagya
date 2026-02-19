@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Select Courier for #{{ $order->order_number }}</h4>
    <div class="card">
        <h5 class="card-header">Step 2: Available Courier Partners</h5>
        <div class="card-body">
            <form action="{{ route('admin.logistic.manifest', $order->id) }}" method="POST">
                @csrf
                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-hover border">
                        <thead class="table-light">
                            <tr><th>Select</th><th>Courier</th><th>Cost</th></tr>
                        </thead>
                        <tbody>
                            @foreach($rates as $rate)
                            <tr>
                                <td>
                                    <input class="form-check-input" type="radio" name="courier_id" value="{{ $rate['courier_id'] }}" required>
                                </td>
                                <td>{{ $rate['courier_name'] }}</td>
                                <td>₹{{ number_format($rate['total_shipping_charges'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-success w-100">Manifest & Ship Order</button>
            </form>
        </div>
    </div>
</div>
@endsection
