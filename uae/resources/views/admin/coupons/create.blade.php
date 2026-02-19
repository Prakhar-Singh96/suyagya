@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Coupons /</span> Create New</h4>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Coupon Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="code" placeholder="e.g. DIWALI20" required>
                            <small class="text-muted">Code must be unique (Letters & Numbers).</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Type</label>
                                <select name="type" class="form-select">
                                    <option value="fixed">Fixed Amount (₹)</option>
                                    <option value="percent">Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="value" class="form-control" placeholder="e.g. 100 or 10" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Min Cart Amount (Optional)</label>
                                <input type="number" step="0.01" name="min_cart_amount" class="form-control" placeholder="e.g. 500">
                                <small class="text-muted">Minimum purchase required to use this.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date (Optional)</label>
                                <input type="date" name="expires_at" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
