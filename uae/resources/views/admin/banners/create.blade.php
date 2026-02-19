@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Add New Banner</h4>

        <div class="card">
            <div class="card-body">
                {{-- Error Debugging Block --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- Desktop Image --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Desktop Image (1920x600 approx)</label>
                            <input type="file" name="desktop_image" class="form-control" required>
                        </div>

                        {{-- Mobile Image --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Image (600x600 approx)</label>
                            {{-- 🟢 Removed 'required' attribute --}}
                            <input type="file" name="mobile_image" class="form-control">
                            <div class="form-text">Optional. If not provided, desktop image might be used.</div>
                        </div>

                        {{-- Link --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link (Optional)</label>
                            <input type="text" name="link" class="form-control"
                                placeholder="e.g. /category/rudraksha">
                        </div>

                        {{-- Sort Order --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>

                        {{-- Status --}}
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                                <label class="form-check-label">Active Status</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Banner</button>
                </form>
            </div>
        </div>
    </div>
@endsection
