@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Product /</span> Add Filter Value
        </h4>
        <a href="{{ route('admin.filter-values.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Filter Value Details</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.filter-values.store') }}" method="POST">
                        @csrf

                        {{-- Select Parent Filter --}}
                        <div class="mb-3">
                            <label class="form-label" for="filter_id">Select Filter <span class="text-danger">*</span></label>
                            <select class="form-select @error('filter_id') is-invalid @enderror" id="filter_id" name="filter_id" required>
                                <option value="" selected disabled>Select a Filter (e.g. Color)</option>
                                @foreach($filters as $filter)
                                    <option value="{{ $filter->id }}" {{ old('filter_id') == $filter->id ? 'selected' : '' }}>
                                        {{ $filter->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filter_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Filter Value --}}
                        <div class="mb-3">
                            <label class="form-label" for="value">Filter Value <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('value') is-invalid @enderror"
                                   id="value" name="value" placeholder="Ex: Red, Small, Cotton" value="{{ old('value') }}" required />
                            @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Add Value</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
