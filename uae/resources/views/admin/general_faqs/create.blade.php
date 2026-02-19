@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Add General FAQ</h4>
        <a href="{{ route('admin.general-faqs.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.general-faqs.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Question <span class="text-danger">*</span></label>
                        <input type="text" name="question" class="form-control" placeholder="e.g. What is your return policy?" required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Answer <span class="text-danger">*</span></label>
                        <textarea name="answer" id="editor" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Save FAQ</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor')).catch(error => { console.error(error); });
</script>
@endsection
