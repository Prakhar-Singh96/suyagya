@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">General FAQs</h4>
        <a href="{{ route('admin.general-faqs.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> Add New FAQ
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">{{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sort</th>
                        <th>Question</th>
                        <th>Answer (Preview)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faqs as $faq)
                    <tr>
                        <td><span class="badge bg-label-primary">{{ $faq->sort_order }}</span></td>
                        <td class="fw-bold text-wrap" style="max-width: 250px;">{{ $faq->question }}</td>
                        <td class="text-wrap" style="max-width: 300px;">{{ Str::limit(strip_tags($faq->answer), 80) }}</td>
                        <td>
                            @if($faq->status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.general-faqs.edit', $faq->id) }}" class="btn btn-sm btn-outline-warning"><i class="bx bx-edit-alt"></i></a>

                            <form action="{{ route('admin.general-faqs.destroy', $faq->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No FAQs found. Add some!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
