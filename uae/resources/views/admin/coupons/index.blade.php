@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Marketing /</span> Coupons</h4>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Add Coupon
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">All Coupons</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Min Cart</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td><strong class="text-primary">{{ $coupon->code }}</strong></td>
                            <td>
                                @if($coupon->type == 'percent')
                                    <span class="badge bg-label-info">Percentage (%)</span>
                                @else
                                    <span class="badge bg-label-primary">Fixed (₹)</span>
                                @endif
                            </td>
                            <td>
                                {{ $coupon->type == 'fixed' ? '₹' : '' }}{{ $coupon->value }}{{ $coupon->type == 'percent' ? '%' : '' }}
                            </td>
                            <td>{{ $coupon->min_cart_amount ? '₹'.$coupon->min_cart_amount : '-' }}</td>
                            <td>
                                @if($coupon->expires_at)
                                    {{ \Carbon\Carbon::parse($coupon->expires_at)->format('d M, Y') }}
                                    @if(\Carbon\Carbon::now()->gt($coupon->expires_at))
                                        <span class="text-danger small">(Expired)</span>
                                    @endif
                                @else
                                    <span class="text-muted">Lifetime</span>
                                @endif
                            </td>
                            <td>
                                @if($coupon->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('admin.coupons.edit', $coupon->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No coupons found. Create one now!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
