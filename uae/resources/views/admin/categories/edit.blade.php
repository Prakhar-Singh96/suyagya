@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Category /</span> Edit Category
            </h4>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>

        {{-- Form Start --}}
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Update ke liye zaroori hai --}}

            <div class="row">
                {{-- LEFT COLUMN: Basic Info & SEO --}}
                <div class="col-xl-8 col-lg-7">

                    {{-- 1. Basic Information --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Basic Information</h5>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $category->name) }}" required />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        id="slug" name="slug" value="{{ old('slug', $category->slug) }}" readonly />
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 2. SEO Configuration --}}
                    <div class="card mb-4">
                        <h5 class="card-header">SEO Configuration</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title"
                                    value="{{ old('meta_title', $category->meta_title) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="2">{{ old('meta_description', $category->meta_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords"
                                    value="{{ old('meta_keywords', $category->meta_keywords) }}"
                                    placeholder="keyword1, keyword2">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Social Media (Open Graph) --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Social Media (Open Graph)</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">OG Title</label>
                                <input type="text" class="form-control" name="og_title"
                                    value="{{ old('og_title', $category->og_title) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">OG Description</label>
                                <textarea class="form-control" name="og_description" rows="2">{{ old('og_description', $category->og_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">OG Image</label>
                                <input type="file" class="form-control mb-2" name="og_image" accept="image/*">

                                @if ($category->og_image)
                                    <div class="p-2 border rounded d-inline-block">
                                        <img src="{{ asset($category->og_image) }}" alt="OG Image" width="100">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 📝 BRAND STORY SECTION --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Category Story / Q&A (Bottom Content)</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Story Title</label>
                                <input type="text" class="form-control" name="story_title"
                                    value="{{ old('story_title', $category->story_title) }}"
                                    placeholder="e.g. Why Choose Rudraksha?">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Story Content</label>
                                <textarea class="form-control" id="story_editor" name="story_content">{{ old('story_content', $category->story_content) }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Images & Status --}}
                <div class="col-xl-4 col-lg-5">

                    {{-- Status Card --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_active"
                                    value="1" {{ $category->status == 1 ? 'checked' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="status_active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive"
                                    value="0" {{ $category->status == 0 ? 'checked' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="status_inactive">Inactive</label>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary w-100">Update Category</button>
                        </div>
                    </div>

                    {{-- Images Card --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Images & Alt Text</h5>
                        <div class="card-body">

                            {{-- Icon Image --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Icon Image</label>
                                <input class="form-control mb-2" type="file" name="icon_image" accept="image/*">
                                <input type="text" class="form-control form-control-sm mb-2" name="icon_alt"
                                    placeholder="Icon Alt Text" value="{{ old('icon_alt', $category->icon_alt) }}">

                                @if ($category->icon_image)
                                    <img src="{{ asset($category->icon_image) }}" alt="Icon"
                                        class="rounded border p-1" width="60">
                                @endif
                            </div>

                            {{-- Cover Image --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Cover Image</label>
                                <input class="form-control mb-2" type="file" name="cover_image" accept="image/*">
                                <input type="text" class="form-control form-control-sm mb-2" name="cover_alt"
                                    placeholder="Cover Alt Text" value="{{ old('cover_alt', $category->cover_alt) }}">

                                @if ($category->cover_image)
                                    <img src="{{ asset($category->cover_image) }}" alt="Cover"
                                        class="rounded border p-1 w-100" style="max-height: 100px; object-fit: cover;">
                                @endif
                            </div>

                            {{-- Banner Image --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Banner Image</label>
                                <input class="form-control mb-2" type="file" name="banner_image" accept="image/*">
                                <input type="text" class="form-control form-control-sm mb-2" name="banner_alt"
                                    placeholder="Banner Alt Text" value="{{ old('banner_alt', $category->banner_alt) }}">

                                @if ($category->banner_image)
                                    <img src="{{ asset($category->banner_image) }}" alt="Banner"
                                        class="rounded border p-1 w-100" style="max-height: 80px; object-fit: cover;">
                                @endif
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        if (document.querySelector('#story_editor')) {
            ClassicEditor.create(document.querySelector('#story_editor'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', 'link'],
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Auto Slug Generator (Optional: Only updates if user changes name)
        document.getElementById('name').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            document.getElementById('slug').value = slug;
        });
    </script>
@endsection
