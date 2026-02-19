@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between mb-4">
            <h4 class="fw-bold">Home Banners</h4>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary">Add Banner</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($banners as $banner)
                            <tr>
                                <td>
                                    <img src="{{ asset($banner->desktop_image) }}" width="100" class="rounded border">
                                </td>
                                <td>{{ $banner->link ?? '-' }}</td>
                                <td>
                                    @if ($banner->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2"> {{-- 👈 Flexbox aur gap add kiya --}}

                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        {{-- Delete Form --}}
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
