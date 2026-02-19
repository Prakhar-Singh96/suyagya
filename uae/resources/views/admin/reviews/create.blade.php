@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Admin /</span> Add Review</h4>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.reviews.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- Product Selection --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-select select2" required>
                                <option value="">-- Choose Product --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rating --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rating <span class="text-danger">*</span></label>
                            <select name="rating" class="form-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                                <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                                <option value="3">⭐⭐⭐ (3 Stars)</option>
                                <option value="2">⭐⭐ (2 Stars)</option>
                                <option value="1">⭐ (1 Star)</option>
                            </select>
                        </div>

                        {{-- Customer Details --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name</label>
                            <input type="text" name="display_name" class="form-control" placeholder="e.g. Rahul Kumar"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Email</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. rahul@example.com"
                                required>
                        </div>

                        {{-- Review Content --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Review Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Best Product Ever!">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Review Content</label>
                            <textarea name="review" class="form-control" rows="4" placeholder="Write the full review here..." required></textarea>
                        </div>

                        {{-- Media Upload --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">Upload Photos/Videos (Multiple)</label>
                            <input type="file" name="media[]" class="form-control" multiple accept="image/*,video/mp4">
                            <small class="text-muted">You can select multiple files.</small>
                        </div>

                        {{-- Status --}}
                        <div class="col-12 mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="1" checked>
                                <label class="form-check-label text-success">Approved</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="0">
                                <label class="form-check-label text-warning">Pending</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
@endsection
