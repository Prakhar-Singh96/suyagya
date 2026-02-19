@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Home Page Settings</h4>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.home.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- LEFT COLUMN: SEO --}}
                <div class="col-md-8">
                    <div class="card mb-4">
                        <h5 class="card-header"><i class="bx bx-search"></i> SEO Configuration</h5>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="meta_title"
                                    value="{{ $setting->meta_title ?? '' }}"
                                    placeholder="Suyagya - Authentic Spiritual Products">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords"
                                    value="{{ $setting->meta_keywords ?? '' }}"
                                    placeholder="rudraksha, gemstone, mala, spiritual">
                                <div class="form-text">Comma separated keywords.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description" rows="3">{{ $setting->meta_description ?? '' }}</textarea>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label fw-bold">OG Image (Social Share)</label>
                                <input type="file" class="form-control" name="og_image">

                                @if (!empty($setting->og_image))
                                    <div class="mt-3">
                                        <label class="d-block small text-muted mb-1">Current Image:</label>
                                        <img src="{{ asset($setting->og_image) }}" alt="OG Image" class="img-thumbnail"
                                            width="150">
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: BRAND STORY --}}
                <div class="col-md-12">
                    <div class="card mb-4">
                        <h5 class="card-header"><i class="bx bx-book-content"></i> Brand Story / Footer Content</h5>
                        <div class="card-body">
                            <div class="alert alert-info small">
                                This content will be displayed at the bottom of the Home Page (Accordion/SEO Text).
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Story Title (Heading)</label>
                                <input type="text" class="form-control" name="story_title"
                                    value="{{ $setting->story_title ?? '' }}" placeholder="e.g. Why Choose Suyagya?">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Story Content</label>
                                <textarea class="form-control" id="editor" name="story_content">{{ $setting->story_content ?? '' }}</textarea>
                            </div>

                            <div class="card mb-4">
                                <h5 class="card-header d-flex justify-content-between align-items-center">
                                    <span>Product FAQs</span>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="addFaqRow()">
                                        <i class="bx bx-plus"></i> Add Question
                                    </button>
                                </h5>
                                <div class="card-body">
                                    <div id="faq-container">
                                        {{-- EDIT PAGE LOGIC: Existing FAQs --}}
                                        @if (isset($setting) && !empty($setting->faq_content))
                                            @foreach ($setting->faq_content as $index => $faq)
                                                <div class="faq-row border rounded p-3 mb-3 position-relative bg-light">
                                                    <button type="button"
                                                        class="btn btn-danger btn-xs position-absolute top-0 end-0 m-2"
                                                        onclick="this.closest('.faq-row').remove()">×</button>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Question</label>
                                                        <input type="text" name="faqs[{{ $index }}][question]"
                                                            class="form-control" value="{{ $faq['question'] }}" required>
                                                    </div>
                                                    <div>
                                                        <label class="form-label small fw-bold">Answer</label>
                                                        <textarea name="faqs[{{ $index }}][answer]" class="form-control" rows="2" required>{{ $faq['answer'] }}</textarea>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <small class="text-muted">These FAQs will appear on the Product Detail Page.</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg mt-3">Save Settings</button>
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
        ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
        }).catch(error => {
            console.error(error);
        });

        let faqIndex = 1000; // High number to avoid conflicts
        function addFaqRow() {
            const container = document.getElementById('faq-container');
            const html = `
            <div class="faq-row border rounded p-3 mb-3 position-relative bg-light">
                <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-2" onclick="this.closest('.faq-row').remove()">×</button>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Question</label>
                    <input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="e.g. Is this original?" required>
                </div>
                <div>
                    <label class="form-label small fw-bold">Answer</label>
                    <textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="2" placeholder="Yes, it comes with a lab certificate." required></textarea>
                </div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', html);
            faqIndex++;
        }
    </script>
@endsection
