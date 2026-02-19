@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">Product Reviews</h4>
            <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add Manual Review
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Media</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviews as $key => $review)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($review->product)
                                        <a href="{{ url('product/' . $review->product->slug) }}" target="_blank">
                                            {{ Str::limit($review->product->name, 20) }}
                                        </a>
                                    @else
                                        <span class="text-danger">Product Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $review->display_name }}</strong><br>
                                    <small class="text-muted">{{ $review->email }}</small>
                                </td>
                                <td>
                                    <span class="text-warning">
                                        @for ($i = 0; $i < $review->rating; $i++)
                                            <i class="bx bxs-star"></i>
                                        @endfor
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $review->title }}</strong><br>
                                    <small>{{ Str::limit($review->review, 50) }}</small>
                                </td>
                                <td>
                                    @if ($review->media && count($review->media) > 0)
                                        <span class="badge bg-label-info">{{ count($review->media) }} Files</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($review->status == 1)
                                        <a href="{{ route('admin.reviews.toggle', $review->id) }}"
                                            class="badge bg-label-success">Approved</a>
                                    @else
                                        <a href="{{ route('admin.reviews.toggle', $review->id) }}"
                                            class="badge bg-label-warning">Pending</a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.reviews.edit', $review->id) }}"
                                        class="btn btn-sm btn-icon text-primary">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon delete-record text-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
