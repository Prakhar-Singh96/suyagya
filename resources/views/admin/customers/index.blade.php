@extends('admin.layout.layout')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Users /</span> Customer List
        </h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Customers & Affiliates</h5>

            <div class="d-flex align-items-center">
                <form action="{{ route('admin.customers.index') }}" method="GET">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}" aria-label="Search..." aria-describedby="basic-addon-search31">
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Role</th> {{-- 🚀 नया कॉलम --}}
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
                                    <div class="avatar avatar-sm me-2">
                                        @if($customer->avatar_original != null)
                                            <img src="{{ uploaded_asset($customer->avatar_original) }}" alt="Avatar" class="rounded-circle">
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-body text-truncate fw-semibold">{{ $customer->name }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- 🚀 Role Column with Dynamic Badges --}}
                            <td>
                                @if($customer->user_type == 'affiliate')
                                    <span class="badge bg-label-warning">
                                        <i class="bx bx-star me-1"></i> Affiliate
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">Customer</span>
                                @endif
                            </td>

                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td><span class="badge bg-label-success me-1">{{ $customer->created_at->format('d M, Y') }}</span></td>

                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- 🚀 Edit Link Updated --}}
                                        <a class="dropdown-item" href="{{ route('admin.customers.edit', $customer->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit / KYC
                                        </a>

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
                            <td colspan="7" class="text-center py-4 text-muted">No Customers Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex justify-content-end">
             {{ $customers->appends(request()->input())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection
