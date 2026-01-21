@extends('admin.layout.layout')

@section('content')

{{-- 1. MAIN WRAPPER (Ye missing tha, isliye gap aa raha tha) --}}
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- 2. PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Users /</span> Customer List
        </h4>
        {{-- Optional: Add Button (Commented) --}}
        {{-- <a href="#" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add Customer</a> --}}
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 3. CARD START --}}
    <div class="card">

        {{-- Card Header with Search --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Customers</h5>

            {{-- Search Box --}}
            <div class="d-flex align-items-center">
                <form action="{{ route('admin.customers.index') }}" method="GET">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}" aria-label="Search..." aria-describedby="basic-addon-search31">
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Joined At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($customers as $key => $customer)
                        <tr>
                            <td>{{ ($customers->currentPage()-1) * $customers->perPage() + $key + 1 }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    {{-- Avatar --}}
                                    <div class="avatar-wrapper">
                                        <div class="avatar avatar-sm me-2">
                                            @if($customer->avatar_original != null)
                                                <img src="{{ uploaded_asset($customer->avatar_original) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Name --}}
                                    <div class="d-flex flex-column">
                                        <span class="text-body text-truncate fw-semibold">{{ $customer->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td><span class="badge bg-label-success me-1">{{ $customer->created_at->format('d M, Y') }}</span></td>

                            {{-- Actions Dropdown --}}
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- View/Edit --}}
                                        <a class="dropdown-item" href="javascript:void(0);">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
                            <td colspan="6" class="text-center py-4 text-muted">No Customers Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer d-flex justify-content-end">
             {{ $customers->appends(request()->input())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection
