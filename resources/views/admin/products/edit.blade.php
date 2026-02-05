@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Product /</span> Edit Product</h4>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Oops! Something went wrong.</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
            id="productForm">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- LEFT COLUMN --}}
                <div class="col-xl-8 col-lg-7">

                    {{-- 1. Product Info --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Product Information</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $product->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" id="slug" name="slug"
                                    value="{{ old('slug', $product->slug) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea class="form-control" id="editor" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Images --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Images</h5>
                        <div class="card-body">
                            {{-- Main Image --}}
                            <div class="mb-4 border p-3 rounded">
                                <label class="form-label fw-bold">Update Main Image</label>
                                @if ($product->main_image)
                                    <div class="mb-2"><img src="{{ asset($product->main_image) }}" width="80"
                                            class="rounded border"></div>
                                @endif
                                <input type="file" class="form-control mb-2" name="main_image">
                                <input type="text" class="form-control form-control-sm" name="main_image_alt"
                                    value="{{ old('main_image_alt', $product->main_image_alt) }}" placeholder="Alt Text">
                            </div>
                            <div class="mb-4 border p-3 rounded">
                                <label class="form-label fw-bold">Update product Main Image</label>
                                @if ($product->product_main_image)
                                    <div class="mb-2"><img src="{{ asset($product->product_main_image) }}" width="80"
                                            class="rounded border"></div>
                                @endif
                                <input type="file" class="form-control mb-2" name="product_main_image">
                                <input type="text" class="form-control form-control-sm" name="product_main_image_alt"
                                    value="{{ old('product_main_image_alt', $product->product_main_image_alt) }}"
                                    placeholder="Alt Text">
                            </div>

                            <hr>

                            {{-- Gallery --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gallery Images</label>

                                {{-- Existing --}}
                                {{-- Existing Gallery Images --}}
                                @if ($product->images->count() > 0)
                                    <div class="row g-2 mb-3">
                                        @foreach ($product->images as $img)
                                            <div class="col-3 position-relative" id="db_img_{{ $img->id }}">

                                                @php
                                                    // 1. Check Extension
                                                    $extension = pathinfo($img->image, PATHINFO_EXTENSION);
                                                    $isVideo = in_array(strtolower($extension), [
                                                        'mp4',
                                                        'mov',
                                                        'avi',
                                                        'webm',
                                                    ]);
                                                @endphp

                                                {{-- 2. Conditional Display --}}
                                                @if ($isVideo)
                                                    {{-- 🎥 Video Preview --}}
                                                    <video src="{{ asset($img->image) }}"
                                                        class="w-100 rounded border bg-black" controls
                                                        style="height: 100px; object-fit: cover;">
                                                    </video>
                                                @else
                                                    {{-- 🖼️ Image Preview --}}
                                                    <img src="{{ asset($img->image) }}" class="w-100 rounded border"
                                                        style="height: 100px; object-fit: cover;">
                                                @endif

                                                {{-- Delete Button --}}
                                                <button type="button"
                                                    class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1"
                                                    onclick="deleteExistingImage({{ $img->id }})"
                                                    style="z-index: 10;">×</button>

                                                {{-- Alt Text Input --}}
                                                <input type="text" name="existing_alts[{{ $img->id }}]"
                                                    class="form-control form-control-sm mt-1" value="{{ $img->alt }}"
                                                    placeholder="Alt">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Add New --}}
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <label for="gallery-input" class="btn btn-outline-primary btn-sm"><i
                                            class="bx bx-plus me-1"></i> Add More</label>
                                    <input type="file" id="gallery-input" name="gallery_images[]" multiple
                                        style="display: none;" onchange="handleFiles(this.files)">
                                </div>
                                <div id="gallery-preview-container" class="row g-3"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. 🔥 CONFIGURATION SECTION (EDIT MODE) 🔥 --}}
                    <div class="card mb-4 border-primary">
                        <div
                            class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                            <h5 class="mb-0 text-white">Product Configuration</h5>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input bg-white" type="checkbox" id="is_gemstone"
                                    name="is_gemstone" value="1" {{ $product->is_gemstone ? 'checked' : '' }}
                                    onchange="toggleConfigMode()" style="cursor: pointer;">
                                <label class="form-check-label text-white fw-bold ms-2" for="is_gemstone"
                                    style="cursor: pointer;">Gemstone Mode</label>
                            </div>
                        </div>

                        <div class="card-body pt-4">

                            {{-- 🛑 A. WEIGHT VARIANTS SECTION --}}
                            <div id="standard_variant_section"
                                style="{{ $product->is_gemstone ? 'display:none;' : '' }}">
                                <div class="alert alert-secondary d-flex align-items-center p-2 mb-3">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <small>For <strong>Simple Products</strong>, ignore this section. Add rows only for
                                        <strong>Weight Variants</strong>.</small>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle table-sm">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="min-width: 120px;">Weight</th>
                                                <th style="min-width: 100px;">MRP</th>
                                                <th style="min-width: 100px;">Price</th>
                                                <th style="min-width: 70px;">Disc%</th>
                                                <th style="min-width: 80px;">Stock</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="variants-container">
                                            {{-- Loop Existing Variants --}}
                                            @if (!$product->is_gemstone && $product->variants->count() > 0)
                                                @foreach ($product->variants as $index => $v)
                                                    <tr class="variant-row">
                                                        <td><input type="text"
                                                                name="variants[{{ $index }}][weight]"
                                                                class="form-control form-control-sm"
                                                                value="{{ $v->weight }}"></td>
                                                        <td><input type="number" step="0.01"
                                                                name="variants[{{ $index }}][mrp]"
                                                                class="form-control form-control-sm v-mrp"
                                                                value="{{ $v->mrp_price }}" oninput="calculateRow(this)">
                                                        </td>
                                                        <td><input type="number" step="0.01"
                                                                name="variants[{{ $index }}][price]"
                                                                class="form-control form-control-sm v-price"
                                                                value="{{ $v->selling_price }}"
                                                                oninput="calculateRow(this)"></td>
                                                        <td><input type="number" step="0.01"
                                                                name="variants[{{ $index }}][discount]"
                                                                class="form-control form-control-sm v-discount bg-light"
                                                                value="{{ $v->discount }}" readonly></td>
                                                        <td><input type="number"
                                                                name="variants[{{ $index }}][qty]"
                                                                class="form-control form-control-sm v-qty"
                                                                value="{{ $v->quantity }}"
                                                                oninput="checkVariantsPresence()"></td>
                                                        <td class="text-center"><button type="button"
                                                                class="btn btn-danger btn-sm remove-row"><i
                                                                    class="bx bx-trash"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-dark btn-sm mt-3" id="add-variant-btn">
                                    <i class="bx bx-plus"></i> Add Variant Row
                                </button>
                            </div>

                            {{-- 💎 B. GEMSTONE VARIANTS SECTION --}}
                            <div id="gemstone_variant_section"
                                style="{{ !$product->is_gemstone ? 'display:none;' : '' }}">
                                <div class="alert alert-warning d-flex align-items-center p-2 mb-3">
                                    <i class="bx bx-diamond me-2"></i>
                                    <small><strong>Gemstone Mode:</strong> Define Ratti & Types.</small>
                                </div>

                                {{-- Gemstone Table --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle table-sm">
                                        <thead class="bg-warning text-dark">
                                            <tr>
                                                <th style="min-width: 100px;">Type</th>
                                                <th style="min-width: 80px;">Ratti</th>
                                                <th style="min-width: 100px;">Material</th>
                                                <th style="min-width: 100px;">MRP</th>
                                                <th style="min-width: 100px;">Price</th>
                                                <th style="min-width: 70px;">Disc%</th>
                                                <th style="min-width: 80px;">Qty</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="gem_variants_body">
                                            {{-- Loop Existing Gems --}}
                                            @if ($product->is_gemstone && $product->gemstoneVariants->count() > 0)
                                                @foreach ($product->gemstoneVariants as $index => $gv)
                                                    <tr class="gem-row">
                                                        <td>
                                                            <select name="gem_variants[{{ $index }}][type]"
                                                                class="form-select form-select-sm"
                                                                onchange="toggleGemRowFields(this)">
                                                                <option value="loose"
                                                                    {{ $gv->type == 'loose' ? 'selected' : '' }}>Gemstone
                                                                </option>
                                                                <option value="ring"
                                                                    {{ $gv->type == 'ring' ? 'selected' : '' }}>Ring
                                                                </option>
                                                                <option value="pendant"
                                                                    {{ $gv->type == 'pendant' ? 'selected' : '' }}>Pendant
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text"
                                                                name="gem_variants[{{ $index }}][ratti]"
                                                                class="form-control form-control-sm"
                                                                value="{{ $gv->ratti_size }}"></td>
                                                        <td>
                                                            <select name="gem_variants[{{ $index }}][material]"
                                                                class="form-select form-select-sm gem-mat"
                                                                {{ $gv->type == 'loose' ? 'disabled' : '' }}>
                                                                <option value="">-</option>
                                                                <option value="silver"
                                                                    {{ $gv->material == 'silver' ? 'selected' : '' }}>
                                                                    Silver
                                                                </option>
                                                                <option value="panchdhatu"
                                                                    {{ $gv->material == 'panchdhatu' ? 'selected' : '' }}>
                                                                    Panchdhatu</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="number"
                                                                name="gem_variants[{{ $index }}][mrp]"
                                                                class="form-control form-control-sm v-mrp"
                                                                value="{{ $gv->mrp }}" oninput="calculateRow(this)">
                                                        </td>
                                                        <td><input type="number"
                                                                name="gem_variants[{{ $index }}][price]"
                                                                class="form-control form-control-sm v-price"
                                                                value="{{ $gv->price }}" oninput="calculateRow(this)">
                                                        </td>
                                                        {{-- Discount calculation logic needed here for existing rows or handled by js on input --}}
                                                        @php $disc = ($gv->mrp > 0 && $gv->mrp > $gv->price) ? round((($gv->mrp - $gv->price)/$gv->mrp)*100, 2) : 0; @endphp
                                                        <td><input type="number"
                                                                name="gem_variants[{{ $index }}][discount]"
                                                                class="form-control form-control-sm v-discount bg-light"
                                                                value="{{ $disc }}" readonly></td>

                                                        <td><input type="number"
                                                                name="gem_variants[{{ $index }}][qty]"
                                                                class="form-control form-control-sm v-qty"
                                                                value="{{ $gv->quantity }}"
                                                                oninput="checkVariantsPresence()"></td>
                                                        <td class="text-center"><button type="button"
                                                                class="btn btn-danger btn-sm remove-row"><i
                                                                    class="bx bx-trash"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-warning text-dark btn-sm mt-3"
                                    onclick="addGemRow()">
                                    <i class="bx bx-plus"></i> Add Gemstone Variant
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Filters --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Filters</h5>
                        <div class="card-body">
                            @foreach ($filters as $filter)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ $filter->name }}</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($filter->filterValues as $value)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="filter_values[]"
                                                    value="{{ $value->id }}" id="filter_{{ $value->id }}"
                                                    {{ $product->filterValues->contains($value->id) ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="filter_{{ $value->id }}">{{ $value->value }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 5. SEO --}}
                    <div class="card mb-4">
                        <h5 class="card-header">SEO</h5>
                        <div class="card-body">
                            <div class="mb-3"><label class="form-label">Meta Title</label><input type="text"
                                    class="form-control" name="meta_title" value="{{ $product->meta_title }}"></div>
                            <div class="mb-3"><label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="meta_description">{{ $product->meta_description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="meta_keywords">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                    value="{{ $product->meta_keywords }}" placeholder="keyword1, keyword2, keyword3">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Update OG Image</label>

                                {{-- 1. File Input (Remove value attribute) --}}
                                <input type="file" class="form-control" name="og_image">

                                {{-- 2. Check if image exists in DB, then show preview --}}
                                @if (!empty($product->og_image))
                                    <div class="mt-2">
                                        <small class="text-muted">Current Image:</small><br>
                                        {{-- Image path ko asset() ke andar daalein --}}
                                        <img src="{{ asset($product->og_image) }}" alt="OG Image"
                                            style="width: 120px; height: auto; border: 1px solid #ddd; padding: 3px; border-radius: 4px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Astrology Settings (Chatbot ke liye)</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Astro Planet (Grah)</label>
                                        <input type="text" name="astro_planet" class="form-control"
                                            value="{{ $product->astro_planet ?? '' }}" placeholder="e.g. Jupiter, Mars">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Astro Rashi</label>
                                        <input type="text" name="astro_rashi" class="form-control"
                                            value="{{ $product->astro_rashi ?? '' }}"
                                            placeholder="e.g. Leo, Aries, Cancer">
                                        <small class="text-muted">Comma (,) se separate karein</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Astro Benefits</label>
                                        <textarea name="astro_benefits" class="form-control" rows="2">{{ $product->astro_benefits ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. Brand Story (Dynamic for this product) --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Product Story / Q&A</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Story Title</label>
                                <input type="text" class="form-control" name="story_title"
                                    value="{{ isset($product) ? $product->story_title : old('story_title', $product->story_title) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Story Content</label>
                                <textarea class="form-control" id="story_editor" name="story_content">{{ isset($product) ? $product->story_content : old('story_content', $product->story_content) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ❓ FAQ SECTION (REPEATER) --}}
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
                                @if (isset($product) && !empty($product->faq_content))
                                    @foreach ($product->faq_content as $index => $faq)
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
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-xl-4 col-lg-5">
                    {{-- Base Pricing --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Pricing & Stock</h5>
                        <div class="card-body">
                            {{-- Logic: If variants exist (check via JS on load), make readonly --}}
                            <div class="mb-3">
                                <label class="form-label">MRP (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="mrp_price" name="mrp_price"
                                    step="0.01" value="{{ $product->mrp_price }}" required
                                    oninput="calcSimpleProduct(this)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Discount (%)</label>
                                <input type="number" class="form-control" id="discount" name="discount"
                                    step="0.01" value="{{ $product->discount }}" oninput="calcSimpleProduct(this)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Selling Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01"
                                    value="{{ $product->price }}" required oninput="calcSimpleProduct(this)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Quantity</label>
                                <input type="number" class="form-control" id="total_quantity" name="quantity"
                                    value="{{ $product->quantity }}" required>
                            </div>

                            <hr>
                            <div class="mb-3"><label class="form-label">SKU</label><input type="text"
                                    class="form-control" name="sku" value="{{ $product->sku }}"></div>
                            <div class="mb-3"><label class="form-label">Weight (kg)</label><input type="text"
                                    class="form-control" name="weight" value="{{ $product->weight }}"></div>
                        </div>
                    </div>

                    {{-- Settings --}}
                    <div class="card mb-4">
                        <h5 class="card-header">Settings</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category_id" id="category_id" required>
                                    <option value="" disabled>Select</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sub Category</label>
                                <select class="form-select" name="sub_category_id" id="sub_category_id">
                                    <option value="{{ $product->sub_category_id }}">
                                        {{ $product->subCategory->name ?? 'Select' }}</option>
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sort Order (Position)</label>
                                <input type="number" class="form-control" name="sort_order"
                                    value="{{ old('sort_order', $product->sort_order ?? 0) }}"
                                    placeholder="e.g. 1 for Top">
                                <small class="text-muted">छोटा नंबर (जैसे 1) सबसे ऊपर दिखेगा।</small>
                            </div>

                            <hr>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                    value="1" {{ $product->is_featured ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_best_seller"
                                    name="is_best_seller" value="1" {{ $product->is_best_seller ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_best_seller">Best Seller</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="emi_available" name="emi_available"
                                    value="1" {{ $product->emi_available ? 'checked' : '' }}>
                                <label class="form-check-label" for="emi_available">EMI Available</label>
                            </div>

                            <hr>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_siddh_enabled"
                                    name="is_siddh_enabled" value="1"
                                    {{ $product->is_siddh_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_siddh_enabled">Siddh Version</label>
                            </div>
                            <div class="mb-3"><input type="number" class="form-control form-control-sm"
                                    name="siddh_price" value="{{ $product->siddh_price }}"
                                    placeholder="Siddh Price (₹)"></div>

                            {{-- <button type="submit" class="btn btn-primary w-100 btn-lg mt-2">Update Product</button> --}}
                        </div>
                    </div>

                    {{-- 🔥 ADDITIONAL CATEGORIES SECTION (EDIT) --}}
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Additional Categories (Multi-Listing)</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="add-cat-row">
                                <i class="bx bx-plus"></i> Add More
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr class="table-light">
                                            <th>Category</th>
                                            <th>Sub Category</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="additional-cats-container">

                                        {{-- 🔄 EXISTING ROWS LOOP --}}
                                        @foreach ($product->additionalCategories as $index => $adCat)
                                            <tr id="acr-{{ $index }}">
                                                <td>
                                                    <select name="additional_cats[{{ $index }}][category_id]"
                                                        class="form-select form-select-sm"
                                                        onchange="loadAddSubCat(this, {{ $index }})">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->id }}"
                                                                {{ $adCat->id == $cat->id ? 'selected' : '' }}>
                                                                {{ $cat->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="additional_cats[{{ $index }}][sub_category_id]"
                                                        class="form-select form-select-sm"
                                                        id="add-sub-{{ $index }}">
                                                        <option value="">Select Sub Category</option>
                                                        {{-- Fetch Subcategories for this specific category directly --}}
                                                        @php
                                                            // Quick query to get subcategories for this row's category
$rowSubs = \App\Models\SubCategory::where(
    'category_id',
                                                                $adCat->id,
                                                            )->get();
                                                        @endphp
                                                        @foreach ($rowSubs as $sub)
                                                            <option value="{{ $sub->id }}"
                                                                {{ $adCat->pivot->sub_category_id == $sub->id ? 'selected' : '' }}>
                                                                {{ $sub->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm p-1"
                                                        onclick="removeCatRow({{ $index }})">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 btn-lg mt-2">Update Product</button>
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
        // CKEditor
        ClassicEditor
            .create(document.querySelector('#editor'), {
                ckfinder: {
                    // Token hata diya hai, simple URL rakhein
                    uploadUrl: "{{ route('admin.product.upload_image') }}"
                }
            })
            .catch(error => {
                console.error(error);
            });

        // NEW: Story Editor
        ClassicEditor.create(document.querySelector('#story_editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList',
                'blockQuote'
            ], // Thoda simple toolbar rakh sakte hain
        }).catch(error => {
            console.error(error);
        });


        document.getElementById('name').addEventListener('input', function() {
            let slug = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g,
                '-');
            document.getElementById('slug').value = slug;
        });

        // SubCategory AJAX
        $('#category_id').change(function() {
            let catId = $(this).val();
            let subCatSelect = $('#sub_category_id');
            subCatSelect.html('<option value="">Loading...</option>');
            $.ajax({
                url: "{{ url('admin/get-subcategories') }}/" + catId,
                type: 'GET',
                success: function(data) {
                    subCatSelect.html('<option value="">Select Sub Category</option>');
                    $.each(data, function(key, val) {
                        subCatSelect.append('<option value="' + val.id + '">' + val.name +
                            '</option>');
                    });
                }
            });
        });

        // Delete Existing Image
        function deleteExistingImage(id) {
            if (confirm('Delete this image?')) {
                $.ajax({
                    url: "{{ url('admin/delete-gallery-image') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) $('#db_img_' + id).remove();
                    }
                });
            }
        }

        // ==========================================
        // 🔄 MASTER TOGGLE & SYNC LOGIC
        // ==========================================
        function toggleConfigMode() {
            const isGemstone = document.getElementById('is_gemstone').checked;
            const standardSection = document.getElementById('standard_variant_section');
            const gemstoneSection = document.getElementById('gemstone_variant_section');

            if (isGemstone) {
                standardSection.style.display = 'none';
                gemstoneSection.style.display = 'block';
                checkVariantsPresence();
            } else {
                standardSection.style.display = 'block';
                gemstoneSection.style.display = 'none';
                checkVariantsPresence();
            }
        }

        function checkVariantsPresence() {
            const isGemstone = document.getElementById('is_gemstone').checked;
            const weightRows = document.querySelectorAll('.variant-row');
            const gemRows = document.querySelectorAll('.gem-row');

            const inputs = [document.getElementById('mrp_price'), document.getElementById('price'), document.getElementById(
                'discount'), document.getElementById('total_quantity')];
            let hasVariants = false;

            if (isGemstone && gemRows.length > 0) hasVariants = true;
            if (!isGemstone && weightRows.length > 0) hasVariants = true;

            if (hasVariants) {
                inputs.forEach(input => {
                    input.setAttribute('readonly', true);
                    input.classList.add('bg-light');
                });
                calculateTotals();
            } else {
                inputs.forEach(input => {
                    input.removeAttribute('readonly');
                    input.classList.remove('bg-light');
                });
            }
        }
        // Run on load
        document.addEventListener("DOMContentLoaded", function() {
            toggleConfigMode();
        });

        // ==========================================
        // ⚖️ WEIGHT VARIANTS
        // ==========================================
        let variantIndex = 5000; // Start high to avoid conflicts
        document.getElementById('add-variant-btn').addEventListener('click', function() {
            let container = document.getElementById('variants-container');
            let html = `
                <tr class="variant-row">
                    <td><input type="text" name="variants[${variantIndex}][weight]" class="form-control form-control-sm" placeholder="e.g. 500g"></td>
                    <td><input type="number" step="0.01" name="variants[${variantIndex}][mrp]" class="form-control form-control-sm v-mrp" placeholder="MRP" oninput="calculateRow(this)"></td>
                    <td><input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control form-control-sm v-price" placeholder="Price" oninput="calculateRow(this)"></td>
                    <td><input type="number" step="0.01" name="variants[${variantIndex}][discount]" class="form-control form-control-sm v-discount bg-light" placeholder="%" readonly></td>
                    <td><input type="number" name="variants[${variantIndex}][qty]" class="form-control form-control-sm v-qty" placeholder="Qty" value="1" oninput="checkVariantsPresence()"></td>
                    <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bx bx-trash"></i></button></td>
                </tr>`;
            container.insertAdjacentHTML('beforeend', html);
            variantIndex++;
            checkVariantsPresence();
        });

        // ==========================================
        // 💎 GEMSTONE VARIANTS
        // ==========================================
        let gemIndex = 9000;

        function addGemRow() {
            const html = `
            <tr class="gem-row">
                <td>
                    <select name="gem_variants[${gemIndex}][type]" class="form-select form-select-sm" onchange="toggleGemRowFields(this)">
                        <option value="loose">Gemstone</option>
                        <option value="ring">Ring</option>
                        <option value="pendant">Pendant</option>
                    </select>
                </td>
                <td><input type="text" name="gem_variants[${gemIndex}][ratti]" class="form-control form-control-sm" placeholder="Ratti"></td>
                <td>
                    <select name="gem_variants[${gemIndex}][material]" class="form-select form-select-sm gem-mat" disabled>
                        <option value="">-</option>
                        <option value="silver">Silver</option>
                        <option value="panchdhatu">Panchdhatu</option>
                    </select>
                </td>
                <td><input type="number" name="gem_variants[${gemIndex}][mrp]" class="form-control form-control-sm v-mrp" placeholder="MRP" oninput="calculateRow(this)"></td>
                <td><input type="number" name="gem_variants[${gemIndex}][price]" class="form-control form-control-sm v-price" placeholder="Price" oninput="calculateRow(this)"></td>
                <td><input type="number" name="gem_variants[${gemIndex}][discount]" class="form-control form-control-sm v-discount bg-light" placeholder="%" readonly></td>

                <td><input type="number" name="gem_variants[${gemIndex}][qty]" class="form-control form-control-sm v-qty" placeholder="Qty" value="1" oninput="checkVariantsPresence()"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="bx bx-trash"></i></button></td>
            </tr>`;
            document.getElementById('gem_variants_body').insertAdjacentHTML('beforeend', html);
            gemIndex++;
            checkVariantsPresence();
        }

        function toggleGemRowFields(select) {
            const row = select.closest('tr');
            const matSelect = row.querySelector('.gem-mat');
            if (select.value === 'loose') {
                matSelect.disabled = true;
                matSelect.value = "";
            } else {
                matSelect.disabled = false;
            }
        }

        // ==========================================
        // 🔥 CALCS & HELPERS
        // ==========================================
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
                checkVariantsPresence();
            }
        });

        function calculateRow(input) {
            let row = input.closest('tr');
            let mrp = parseFloat(row.querySelector('.v-mrp').value) || 0;
            let price = parseFloat(row.querySelector('.v-price').value) || 0;
            let discInput = row.querySelector('.v-discount');

            if (mrp > 0 && price > 0) {
                let disc = ((mrp - price) / mrp) * 100;
                discInput.value = disc.toFixed(2);
            }
            checkVariantsPresence();
        }

        function calculateTotals() {
            const isGemstone = document.getElementById('is_gemstone').checked;
            let minPrice = Infinity;
            let minMrp = 0;
            let totalQty = 0;
            let found = false;

            let rows = isGemstone ? document.querySelectorAll('.gem-row') : document.querySelectorAll('.variant-row');

            rows.forEach(row => {
                let price = parseFloat(row.querySelector('.v-price').value) || 0;
                let mrp = parseFloat(row.querySelector('.v-mrp').value) || 0;
                let qty = parseInt(row.querySelector('.v-qty').value) || 0;

                if (price > 0) {
                    found = true;
                    if (price < minPrice) {
                        minPrice = price;
                        minMrp = mrp;
                    }
                    totalQty += qty;
                }
            });

            if (found && minPrice !== Infinity) {
                document.getElementById('price').value = minPrice;
                document.getElementById('mrp_price').value = minMrp;
                document.getElementById('total_quantity').value = totalQty;

                if (minMrp > 0 && minPrice > 0) {
                    let d = ((minMrp - minPrice) / minMrp) * 100;
                    document.getElementById('discount').value = d.toFixed(2);
                }
            }
        }

        function calcSimpleProduct(input) {
            if (document.getElementById('price').hasAttribute('readonly')) return;

            const mrp = parseFloat(document.getElementById('mrp_price').value) || 0;
            const priceInput = document.getElementById('price');
            const discountInput = document.getElementById('discount');

            if (input.id === 'mrp_price' || input.id === 'discount') {
                const disc = parseFloat(discountInput.value) || 0;
                if (mrp > 0) priceInput.value = (mrp - (mrp * disc / 100)).toFixed(2);
            } else if (input.id === 'price') {
                const price = parseFloat(priceInput.value) || 0;
                if (mrp > 0 && price > 0) discountInput.value = ((mrp - price) / mrp * 100).toFixed(2);
            }
        }

        // ... (Image handling code stays same) ...
        // Global DataTransfer object to hold files
        const dt = new DataTransfer();

        function handleFiles(files) {
            const container = document.getElementById('gallery-preview-container');
            const input = document.getElementById('gallery-input');

            // Loop through new files and add them to DataTransfer
            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                // Prevent duplicates
                let isDuplicate = false;
                for (let j = 0; j < dt.files.length; j++) {
                    if (dt.files[j].name === file.name && dt.files[j].size === file.size) {
                        isDuplicate = true;
                        break;
                    }
                }

                if (!isDuplicate) {
                    dt.items.add(file);

                    // Create Preview Element
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Generate unique ID
                        const fileId = file.name.replace(/[^a-zA-Z0-9]/g, '');

                        // 🔥 CHECK FILE TYPE (Image vs Video)
                        let mediaHtml = '';
                        if (file.type.startsWith('image/')) {
                            mediaHtml =
                                `<img src="${e.target.result}" width="60" height="60" class="object-fit-cover rounded me-3">`;
                        } else if (file.type.startsWith('video/')) {
                            mediaHtml = `
                        <video width="60" height="60" class="object-fit-cover rounded me-3 bg-black" muted>
                            <source src="${e.target.result}" type="${file.type}">
                        </video>
                    `;
                        } else {
                            // Fallback for unknown file types
                            mediaHtml =
                                `<div class="d-flex align-items-center justify-content-center bg-light rounded me-3" style="width:60px; height:60px;"><i class="bx bx-file fs-3"></i></div>`;
                        }

                        const html = `
                <div class="col-md-6" id="preview-${fileId}">
                    <div class="d-flex align-items-center border p-2 rounded position-relative bg-white">
                        ${mediaHtml}  {{-- 👈 Inserted Media Here --}}
                        <div class="flex-grow-1">
                            <small class="text-muted d-block text-truncate" style="max-width: 150px;">${file.name}</small>
                            <input type="text" name="gallery_alts[]" class="form-control form-control-sm mt-1" placeholder="Alt Text">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm ms-2 p-1" onclick="removeFile('${file.name}', '${fileId}')" style="line-height: 1;">
                            <i class="bx bx-x fs-5"></i>
                        </button>
                    </div>
                </div>
            `;
                        container.insertAdjacentHTML('beforeend', html);
                    }
                    reader.readAsDataURL(file);
                }
            }

            // Update the input files property
            input.files = dt.files;
        }

        // ==========================================
        // 🔗 ADDITIONAL CATEGORIES JS (EDIT PAGE)
        // ==========================================

        // Start index from existing count so IDs don't clash
        let catRowIndex = {{ $product->additionalCategories->count() + 1 }};

        // 1. Add Row
        $('#add-cat-row').click(function() {
            let html = `
            <tr id="acr-${catRowIndex}">
                <td>
                    <select name="additional_cats[${catRowIndex}][category_id]" class="form-select form-select-sm" onchange="loadAddSubCat(this, ${catRowIndex})" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="additional_cats[${catRowIndex}][sub_category_id]" class="form-select form-select-sm" id="add-sub-${catRowIndex}">
                        <option value="">Select Sub Category</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm p-1" onclick="removeCatRow(${catRowIndex})">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            $('#additional-cats-container').append(html);
            catRowIndex++;
        });

        // 2. Remove Row
        window.removeCatRow = function(index) {
            $('#acr-' + index).remove();
        }

        // 3. Load SubCategory via AJAX
        window.loadAddSubCat = function(select, index) {
            let catId = $(select).val();
            let subSelect = $('#add-sub-' + index);

            subSelect.html('<option value="">Loading...</option>');

            if (catId) {
                $.ajax({
                    url: "{{ url('admin/get-subcategories') }}/" + catId,
                    type: 'GET',
                    success: function(data) {
                        subSelect.html('<option value="">Select Sub Category</option>');
                        $.each(data, function(key, val) {
                            subSelect.append('<option value="' + val.id + '">' + val.name +
                                '</option>');
                        });
                    }
                });
            } else {
                subSelect.html('<option value="">Select Sub Category</option>');
            }
        }

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
