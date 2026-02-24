@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">Edit Blog</h4>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Back to List</a>
        </div>

        {{-- Error Handling --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Update ke liye zaroori hai --}}

            <div class="row">
                {{-- LEFT COLUMN: Content --}}
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Blog Title *</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ old('title', $blog->title) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug *</label>
                                <input type="text" class="form-control" name="slug" id="slug"
                                    value="{{ old('slug', $blog->slug) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content *</label>
                                <textarea id="editor" name="content">{{ old('content', $blog->content) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Blog FAQs</h5>
                            <button type="button" class="btn btn-sm btn-info" onclick="addFaq()">+ Add FAQ</button>
                        </div>
                        <div class="card-body" id="faq-container">
                            {{-- 1. पहले चेक करें कि क्या वैलिडेशन एरर के बाद 'old' डेटा मौजूद है --}}
                            @php
                                $faqs = old('faqs', $blog->faqs ?? []);
                            @endphp

                            @if (!empty($faqs))
                                @foreach ($faqs as $index => $faq)
                                    <div class="faq-row border p-3 mb-3 rounded shadow-sm">
                                        <div class="mb-2">
                                            <label class="form-label">Question</label>
                                            {{-- 2. वैल्यू में old() का सही इस्तेमाल एरे के लिए --}}
                                            <input type="text" name="faqs[{{ $index }}][question]"
                                                class="form-control" value="{{ $faq['question'] ?? '' }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Answer</label>
                                            <textarea name="faqs[{{ $index }}][answer]" class="form-control" rows="2">{{ $faq['answer'] ?? '' }}</textarea>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="this.parentElement.remove()">Remove</button>
                                    </div>
                                @endforeach
                            @endif
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
                                {{-- Checkbox logic for Active/Inactive --}}
                                <input class="form-check-input" type="checkbox" name="status" value="1"
                                    {{ $blog->status == 1 ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Main Image</label>

                                {{-- Preview Existing Image --}}
                                @if ($blog->main_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($blog->main_image) }}" alt="Current Image"
                                            class="rounded border p-1" width="100%">
                                    </div>
                                @endif

                                <input type="file" class="form-control" name="main_image">
                                <small class="text-muted">Upload only if you want to change.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Image Alt Text (SEO)</label>
                                <input type="text" class="form-control" name="img_alt"
                                    value="{{ old('img_alt', $blog->img_alt) }}">
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
                                <input type="text" class="form-control" name="meta_title"
                                    value="{{ old('meta_title', $blog->meta_title) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="3">{{ old('meta_description', $blog->meta_description) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords"
                                    value="{{ old('meta_keywords', $blog->meta_keywords) }}"
                                    placeholder="keyword1, keyword2">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">OG Image (Social Share)</label>

                                {{-- Preview Existing OG Image --}}
                                @if ($blog->og_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($blog->og_image) }}" class="rounded border p-1"
                                            width="100">
                                    </div>
                                @endif

                                <input type="file" class="form-control" name="og_image">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Update Blog</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor.create(document.querySelector('#editor'), {
            ckfinder: {
                uploadUrl: "{{ route('admin.product.upload_image') }}"
            }
        }).catch(error => console.error(error));

        // Auto Slug Script (Optional: Edit page par slug auto-update karna hai ya nahi, ye apki choice hai)
        document.getElementById('title').addEventListener('input', function() {
            let slug = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g,
                '-');
            document.getElementById('slug').value = slug;
        });

        let faqIndex = {{ isset($blog) && $blog->faqs ? count($blog->faqs) : 0 }};

        function addFaq() {
            let html = `
            <div class="faq-row border p-3 mb-3 rounded shadow-sm">
                <div class="mb-2">
                    <label class="form-label">Question</label>
                    <input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="Enter Question">
                </div>
                <div class="mb-2">
                    <label class="form-label">Answer</label>
                    <textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="2" placeholder="Enter Answer"></textarea>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.parentElement.remove()">Remove</button>
            </div>`;
            document.getElementById('faq-container').insertAdjacentHTML('beforeend', html);
            faqIndex++;
        }
    </script>
@endsection
