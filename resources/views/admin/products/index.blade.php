@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Product List</h4>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
    </div>

    <div class="card">
        {{-- Search Box --}}
            <div class="d-flex align-items-center">
                <form action="{{ route('admin.products.index') }}" method="GET">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}" aria-label="Search..." aria-describedby="basic-addon-search31">
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $key => $product)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <img src="{{ asset($product->main_image) }}" class="rounded" width="50">
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $product->name }}</span>
                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td>
                            {{ $product->category->name }}
                            @if($product->subCategory)
                                <br><small class="text-muted">> {{ $product->subCategory->name }}</small>
                            @endif
                        </td>
                        <td>₹{{ number_format($product->price, 2) }}</td>
                        <td>
                            <span class="badge {{ $product->quantity > 0 ? 'bg-label-info' : 'bg-label-danger' }}">
                                {{ $product->quantity }}
                            </span>
                        </td>
                        <td>
                            @if($product->status)
                                <span class="badge bg-label-success">Active</span>
                            @else
                                <span class="badge bg-label-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="card-footer d-flex justify-content-end">
             {{ $products->appends(request()->input())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
