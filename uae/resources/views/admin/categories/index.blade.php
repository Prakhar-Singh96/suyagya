@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- 1. Header & Create Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Product /</span> Categories
        </h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Add Category
        </a>
    </div>

    {{-- 2. Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 3. Category List Table --}}
    <div class="card">
        <h5 class="card-header">Category List</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($categories as $key => $category)
                        <tr>
                            <td>{{ $key + 1 }}</td>

                            {{-- Icon Image --}}
                            <td>
                                @if($category->icon_image)
                                    <img src="{{ asset($category->icon_image) }}" alt="icon" class="rounded-circle" width="40" height="40">
                                @else
                                    <span class="badge bg-label-secondary">No Icon</span>
                                @endif
                            </td>

                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ $category->slug }}</td>

                            {{-- Status --}}
                            <td>
                                @if($category->status)
                                    <span class="badge bg-label-success me-1">Active</span>
                                @else
                                    <span class="badge bg-label-danger me-1">Inactive</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- Edit --}}
                                        <a class="dropdown-item" href="{{ route('admin.categories.edit', $category->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
                            <td colspan="6" class="text-center py-5">
                                <h6 class="text-muted">No Categories Found</h6>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-outline-primary mt-2">Create First Category</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
