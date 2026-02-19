@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Product /</span> Sub-Categories
            </h4>
            <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Add Sub-Category
            </a>
        </div>

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Table --}}
        <div class="card">
            <h5 class="card-header">Sub-Category List</h5>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Parent Category</th> {{-- 🟢 New Column --}}
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($subCategories as $key => $sub)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                {{-- 🟢 Image Column --}}
                                <td>
                                    @if ($sub->image)
                                        <img src="{{ asset($sub->image) }}" alt="{{ $sub->image_alt }}" class="rounded"
                                            width="50" height="50" style="object-fit: cover;">
                                    @else
                                        <span class="badge bg-label-secondary">No Img</span>
                                    @endif
                                </td>
                                <td><strong>{{ $sub->name }}</strong></td>

                                {{-- Show Parent Category Name --}}
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $sub->category ? $sub->category->name : 'No Parent' }}
                                    </span>
                                </td>

                                <td>{{ $sub->slug }}</td>

                                <td>
                                    @if ($sub->status)
                                        <span class="badge bg-label-success">Active</span>
                                    @else
                                        <span class="badge bg-label-danger">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.subcategories.edit', $sub->id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.subcategories.destroy', $sub->id) }}"
                                                method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf @method('DELETE')
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
                                <td colspan="6" class="text-center py-4 text-muted">No Sub-Categories Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
