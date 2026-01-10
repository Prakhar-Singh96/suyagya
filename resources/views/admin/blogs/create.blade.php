@extends('admin.layout.layout')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Add New Blog</h4>

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- LEFT COLUMN: Content --}}
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Blog Title *</label>
                            <input type="text" class="form-control" name="title" id="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug *</label>
                            <input type="text" class="form-control" name="slug" id="slug" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea id="editor" name="content"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: SEO & Settings --}}
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Publish & Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Main Image *</label>
                            <input type="file" class="form-control" name="main_image" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image Alt Text (SEO)</label>
                            <input type="text" class="form-control" name="img_alt">
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" placeholder="keyword1, keyword2">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OG Image (Social Share)</label>
                            <input type="file" class="form-control" name="og_image">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Publish Blog</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        ckfinder: { uploadUrl: "{{ route('admin.product.upload_image') }}" } // Reuse existing upload route
    }).catch(error => console.error(error));

    // Auto Slug
    document.getElementById('title').addEventListener('input', function() {
        let slug = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
