@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Product /</span> Add Sub-Category
            </h4>
            <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>

        <form action="{{ route('admin.subcategories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- LEFT COLUMN: Basic Info & SEO --}}
                <div class="col-xl-8 col-lg-7">

                    {{-- 1. Basic Information --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Basic Information</h5>
                        <div class="card-body">

                            {{-- Parent Category Selection --}}
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Parent Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="" selected disabled>Select Parent Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Name & Slug --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Ex: T-Shirts" value="{{ old('name') }}"
                                        required />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        id="slug" name="slug" placeholder="Ex: t-shirts" value="{{ old('slug') }}"
                                        readonly />
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 🟢 Main Image Section --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Sub-Category Image</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Upload Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror"
                                    name="image" accept="image/*">
                                <div class="form-text">Allowed formats: jpg, png, webp. Max size: 2MB.</div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image Alt Text (For SEO)</label>
                                <input type="text" class="form-control" name="image_alt" value="{{ old('image_alt') }}"
                                    placeholder="Describe the image">
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
                                    value="{{ old('meta_title') }}" placeholder="SEO Title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="2" placeholder="SEO Description">{{ old('meta_description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords"
                                    value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2, keyword3">
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
                                    value="{{ old('og_title') }}" placeholder="Social Share Title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">OG Description</label>
                                <textarea class="form-control" name="og_description" rows="2" placeholder="Social Share Description">{{ old('og_description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">OG Image</label>
                                <input type="file" class="form-control" name="og_image" accept="image/*">
                                <div class="form-text">Recommended size: 1200x630px</div>
                            </div>
                        </div>
                    </div>

                    {{-- 📝 BRAND STORY SECTION --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Sub-Category Story / Q&A</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Story Title</label>
                                <input type="text" class="form-control" name="story_title"
                                    value="{{ old('story_title') }}" placeholder="e.g. Benefits of 5 Mukhi Rudraksha">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Story Content</label>
                                <textarea class="form-control" id="story_editor" name="story_content">{{ old('story_content') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Status & Publish --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="active"
                                    value="1" checked>
                                <label class="form-check-label text-success fw-bold" for="active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="inactive"
                                    value="0">
                                <label class="form-check-label text-danger fw-bold" for="inactive">Inactive</label>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="bx bx-check-circle me-1"></i> Create Sub-Category
                            </button>
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
        // Auto Slug Generator
        document.getElementById('name').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            document.getElementById('slug').value = slug;
        });
    </script>
@endsection
