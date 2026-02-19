@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Edit Banner</h4>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- UPDATE ke liye zaroori hai --}}

                <div class="row">
                    {{-- Desktop Image --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Desktop Image (1920x600 approx)</label>
                        <input type="file" name="desktop_image" class="form-control">

                        {{-- Show Existing Image --}}
                        <div class="mt-2">
                            <label class="small text-muted">Current Image:</label>
                            <br>
                            <img src="{{ asset($banner->desktop_image) }}" width="150" class="rounded border">
                        </div>
                    </div>

                    {{-- Mobile Image --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile Image (600x600 approx)</label>
                        <input type="file" name="mobile_image" class="form-control">

                        {{-- Show Existing Image --}}
                        <div class="mt-2">
                            <label class="small text-muted">Current Image:</label>
                            <br>
                            @if($banner->mobile_image)
                                <img src="{{ asset($banner->mobile_image) }}" width="100" class="rounded border">
                            @else
                                <span class="text-muted small">No mobile image uploaded.</span>
                            @endif
                        </div>
                    </div>

                    {{-- Link --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Link (Optional)</label>
                        <input type="text" name="link" class="form-control"
                               value="{{ old('link', $banner->link) }}" placeholder="e.g. /category/rudraksha">
                    </div>

                    {{-- Sort Order --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="{{ old('sort_order', $banner->sort_order) }}">
                    </div>

                    {{-- Status --}}
                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1"
                                   {{ $banner->status == 1 ? 'checked' : '' }}>
                            <label class="form-check-label">Active Status</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Banner</button>
            </form>
        </div>
    </div>
</div>
@endsection
