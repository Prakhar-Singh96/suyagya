@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Coupons /</span> Edit Coupon</h4>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Update Coupon: <span class="text-primary">{{ $coupon->code }}</span></h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="code" value="{{ $coupon->code }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Type</label>
                                <select name="type" class="form-select">
                                    <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                    <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="value" class="form-control" value="{{ $coupon->value }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Min Cart Amount (Optional)</label>
                                <input type="number" step="0.01" name="min_cart_amount" class="form-control" value="{{ $coupon->min_cart_amount }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date (Optional)</label>
                                {{-- Date format issue fix ke liye Y-m-d format zaroori hai --}}
                                <input type="date" name="expires_at" class="form-control" value="{{ $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $coupon->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $coupon->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
