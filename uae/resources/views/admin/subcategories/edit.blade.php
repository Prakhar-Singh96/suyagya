@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Sub-Category /</span> Edit Sub-Category
            </h4>
            <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>

        <form action="{{ route('admin.subcategories.update', $subCategory->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- LEFT COLUMN: Basic Info & SEO --}}
                <div class="col-xl-8 col-lg-7">

                    <div class="card mb-4">
                        <h5 class="card-header">Basic Information</h5>
                        <div class="card-body">

                            {{-- Parent Category Selection --}}
                            <div class="mb-3">
                                <label class="form-label">Parent Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                                    required>
                                    <option value="" disabled>Select Parent Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ old('category_id', $subCategory->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $subCategory->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="slug" name="slug"
                                        value="{{ old('slug', $subCategory->slug) }}" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $subCategory->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 🟢 Main Image Section --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Sub-Category Image</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Upload Image</label>
                                <input type="file" class="form-control mb-2 @error('image') is-invalid @enderror"
                                    name="image" accept="image/*">

                                {{-- Show Existing Image --}}
                                @if ($subCategory->image)
                                    <div class="p-2 border rounded d-inline-block">
                                        <img src="{{ asset($subCategory->image) }}" alt="{{ $subCategory->image_alt }}"
                                            width="100" class="d-block rounded">
                                    </div>
                                @endif
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image Alt Text (For SEO)</label>
                                <input type="text" class="form-control" name="image_alt"
                                    value="{{ old('image_alt', $subCategory->image_alt) }}"
                                    placeholder="Describe the image">
                            </div>
                        </div>
                    </div>

                    {{-- SEO Configuration --}}
                    <div class="card mb-4">
                        <h5 class="card-header">SEO Configuration</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title"
                                    value="{{ old('meta_title', $subCategory->meta_title) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="2">{{ old('meta_description', $subCategory->meta_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords"
                                    value="{{ old('meta_keywords', $subCategory->meta_keywords) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Social Media (OG) --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Social Media (Open Graph)</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">OG Title</label>
                                <input type="text" class="form-control" name="og_title"
                                    value="{{ old('og_title', $subCategory->og_title) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">OG Description</label>
                                <textarea class="form-control" name="og_description" rows="2">{{ old('og_description', $subCategory->og_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">OG Image</label>
                                <input type="file" class="form-control mb-2" name="og_image" accept="image/*">

                                @if ($subCategory->og_image)
                                    <div class="p-2 border rounded d-inline-block">
                                        <img src="{{ asset($subCategory->og_image) }}" alt="OG Image" width="100">
                                    </div>
                                @endif
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
                                    value="{{ old('story_title', $subCategory->story_title) }}"
                                    placeholder="e.g. Benefits of 5 Mukhi Rudraksha">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Story Content</label>
                                <textarea class="form-control" id="story_editor" name="story_content">{{ old('story_content', $subCategory->story_content) }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Status --}}
                <div class="col-xl-4 col-lg-5">
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="active"
                                    value="1" {{ $subCategory->status == 1 ? 'checked' : '' }}>
                                <label class="form-check-label text-success" for="active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="inactive"
                                    value="0" {{ $subCategory->status == 0 ? 'checked' : '' }}>
                                <label class="form-check-label text-danger" for="inactive">Inactive</label>
                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary w-100">Update Sub-Category</button>
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
        document.getElementById('name').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            document.getElementById('slug').value = slug;
        });
    </script>
@endsection
