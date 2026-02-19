@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Prepare Shipment: #{{ $order->order_number }}</h4>
    {{-- Error/Success Alerts --}}
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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Step 1: Weight & Dimensions</h5>
                <div class="card-body">
                    <form action="{{ route('admin.logistic.create_order', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pickup Warehouse *</label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh['warehouse_id'] }}">{{ $wh['warehouse_name'] }} ({{ $wh['address_city'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Weight (Kg)</label>
                                <input type="number" step="0.01" name="weight" class="form-control" value="0.5" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Length (cm)</label>
                                <input type="number" step="0.1" name="length" class="form-control" value="10" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Width (cm)</label>
                                <input type="number" step="0.1" name="width" class="form-control" value="10" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Height (cm)</label>
                                <input type="number" step="0.1" name="height" class="form-control" value="10" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Fetch Courier Rates</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
