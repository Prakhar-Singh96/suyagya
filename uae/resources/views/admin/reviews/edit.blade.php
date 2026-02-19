@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Reviews /</span> Edit Review</h4>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- UPDATE ke liye zaroori hai --}}

                <div class="row">
                    {{-- Product Selection --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Product <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select select2" required>
                            <option value="">-- Choose Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ $review->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rating --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                        <select name="rating" class="form-select" required>
                            @foreach([5,4,3,2,1] as $r)
                                <option value="{{ $r }}" {{ $review->rating == $r ? 'selected' : '' }}>
                                    {{ $r }} Star{{ $r > 1 ? 's' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Customer Details --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="display_name" class="form-control"
                               value="{{ old('display_name', $review->display_name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $review->email) }}" required>
                    </div>

                    {{-- Review Content --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Review Title</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $review->title) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Review Content</label>
                        <textarea name="review" class="form-control" rows="4" required>{{ old('review', $review->review) }}</textarea>
                    </div>

                    {{-- Media Display & Upload --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Attached Media</label>

                        {{-- Show Existing Media --}}
                        @if($review->media && count($review->media) > 0)
                            <div class="d-flex gap-2 mb-3 flex-wrap">
                                @foreach($review->media as $file)
                                    <div class="border rounded p-1" style="width: 100px; height: 100px; overflow: hidden;">
                                        @if(Str::endsWith($file, ['.mp4', '.mov']))
                                            <video src="{{ asset($file) }}" style="width: 100%; height: 100%; object-fit: cover;"></video>
                                        @else
                                            <img src="{{ asset($file) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <label class="form-label small text-muted">Upload New Media (Will be added to existing)</label>
                        <input type="file" name="media[]" class="form-control" multiple accept="image/*,video/mp4">
                    </div>

                    {{-- Status --}}
                    <div class="col-12 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="1"
                                {{ $review->status == 1 ? 'checked' : '' }}>
                            <label class="form-check-label text-success">Approved</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" value="0"
                                {{ $review->status == 0 ? 'checked' : '' }}>
                            <label class="form-check-label text-warning">Pending</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Review</button>
            </form>
        </div>
    </div>
</div>
@endsection
