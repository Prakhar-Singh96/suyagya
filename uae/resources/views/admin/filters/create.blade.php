@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Product /</span> Add Filter
        </h4>
        <a href="{{ route('admin.filters.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-xl-6"> {{-- Small width is enough for this --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Filter Name</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.filters.store') }}" method="POST">
                        @csrf

                        {{-- Name --}}
                        <div class="mb-3">
                            <label class="form-label" for="name">Filter Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" placeholder="Ex: Color, Size, Material" value="{{ old('name') }}" required />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="mb-3">
                            <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                   id="slug" name="slug" placeholder="Ex: color" value="{{ old('slug') }}" readonly />
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Create Filter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto Slug Generator
    document.getElementById('name').addEventListener('input', function() {
        let name = this.value;
        let slug = name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
