@extends('admin.layout.layout')

@section('content')
<div class="container mt-4">
    <h2>Broken Link Redirect Manager</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Add New Redirect Form --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Add New Redirect</div>
        <div class="card-body">
            <form action="{{ route('admin.redirects.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <label>Old Broken URL (e.g. /old-page)</label>
                        <input type="text" name="old_url" class="form-control" placeholder="/broken-link" required>
                    </div>
                    <div class="col-md-5">
                        <label>New Correct URL (e.g. /new-page)</label>
                        <input type="text" name="new_url" class="form-control" placeholder="/working-link" required>
                    </div>
                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-success w-100">Add Redirect</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Redirect List --}}
    <div class="card">
        <div class="card-header">All Redirects</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Old URL (Broken)</th>
                        <th>New URL (Target)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($redirects as $redirect)
                    <tr>
                        <td>{{ $redirect->old_url }}</td>
                        <td>{{ $redirect->new_url }}</td>
                        <td>
                            <form action="{{ route('admin.redirects.destroy', $redirect->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- Pagination --}}
            <div class="card-footer d-flex justify-content-end">
                 {{ $redirects->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
