@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Category /</span> Add New Category
            </h4>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Back to List
            </a>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- LEFT COLUMN: Basic & SEO --}}
                <div class="col-xl-8 col-lg-7">

                    {{-- 1. Basic Information Card --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Basic Information</h5>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Category Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Ex: Electronics"
                                        value="{{ old('name') }}" required />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="slug">Slug <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                        id="slug" name="slug" placeholder="Ex: electronics"
                                        value="{{ old('slug') }}" readonly />
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 2. SEO Configuration Card --}}
                    <div class="card mb-4">
                        <h5 class="card-header">SEO Configuration</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="meta_title">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title"
                                    value="{{ old('meta_title') }}" placeholder="SEO Title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="meta_description">Meta Description</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
                                    placeholder="SEO Description">{{ old('meta_description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="meta_keywords">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                    value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2, keyword3">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Open Graph (Social Media) --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Social Media (Open Graph)</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="og_title">OG Title</label>
                                <input type="text" class="form-control" id="og_title" name="og_title"
                                    value="{{ old('og_title') }}" placeholder="Social Share Title">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="og_description">OG Description</label>
                                <textarea class="form-control" id="og_description" name="og_description" rows="2"
                                    placeholder="Social Share Description">{{ old('og_description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="og_image">OG Image</label>
                                <input type="file" class="form-control" id="og_image" name="og_image"
                                    accept="image/*">
                                <div class="form-text">Recommended size: 1200x630px</div>
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
                                    value="{{ old('story_title') }}" placeholder="e.g. Why Choose Rudraksha?">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Story Content</label>
                                <textarea class="form-control" id="story_editor" name="story_content">{{ old('story_content') }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Images & Status --}}
                <div class="col-xl-4 col-lg-5">

                    {{-- Images Card --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Images & Alt Text</h5>
                        <div class="card-body">

                            {{-- Icon Image --}}
                            <div class="mb-4">
                                <label for="icon_image" class="form-label fw-bold">Icon Image</label>
                                <input class="form-control mb-2" type="file" id="icon_image" name="icon_image"
                                    accept="image/*">
                                <input type="text" class="form-control form-control-sm" name="icon_alt"
                                    placeholder="Icon Alt Text (SEO)" value="{{ old('icon_alt') }}">
                            </div>

                            {{-- Cover Image --}}
                            <div class="mb-4">
                                <label for="cover_image" class="form-label fw-bold">Cover Image</label>
                                <input class="form-control mb-2" type="file" id="cover_image" name="cover_image"
                                    accept="image/*">
                                <input type="text" class="form-control form-control-sm" name="cover_alt"
                                    placeholder="Cover Alt Text (SEO)" value="{{ old('cover_alt') }}">
                            </div>

                            {{-- Banner Image --}}
                            <div class="mb-3">
                                <label for="banner_image" class="form-label fw-bold">Banner Image</label>
                                <input class="form-control mb-2" type="file" id="banner_image" name="banner_image"
                                    accept="image/*">
                                <input type="text" class="form-control form-control-sm" name="banner_alt"
                                    placeholder="Banner Alt Text (SEO)" value="{{ old('banner_alt') }}">
                            </div>

                        </div>
                    </div>

                    {{-- Status Card --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_active"
                                    value="1" checked>
                                <label class="form-check-label text-success fw-bold" for="status_active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive"
                                    value="0">
                                <label class="form-check-label text-danger fw-bold" for="status_inactive">Inactive</label>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary w-100">Create Category</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            if (nameInput && slugInput) {
                nameInput.addEventListener('input', function() {
                    let name = this.value;
                    let slug = name.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '') // Remove invalid chars
                        .replace(/\s+/g, '-') // Replace spaces with -
                        .replace(/-+/g, '-'); // Remove duplicate -

                    slugInput.value = slug;
                });
            }
        });
    </script>
@endsection
