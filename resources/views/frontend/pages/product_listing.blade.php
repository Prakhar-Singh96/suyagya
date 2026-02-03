@extends('frontend.layouts.app')

@section('content')

    {{-- Title Section --}}
    <div class="py-3 text-center border-bottom">
        <div class="container">
            <h1 class="font-heading fw-bold text-dark mb-o">
                {{ isset($subCategory) ? $subCategory->name : $category->name }}
            </h1>
            <p class="text-muted fw-bold small mb-0">({{ $products->total() }} products)</p>
            <p class="text-muted fw-bold small mb-0" style="max-width: 600px; margin: 0 auto;">
                {{ isset($subCategory) ? $subCategory->description : $category->description }}
            </p>
        </div>
    </div>

    <div class="container py-3">
        <div class="row">

            <div class="col-lg-3">
                {{-- Mobile Filter Collapse (Hidden on Desktop) --}}
                <div class="collapse d-lg-block mb-4" id="mobileFilterCollapse">
                    <div class="filter-sidebar border rounded p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-uppercase ls-1 d-none d-lg-block">Filters</h6>
                            @if (request()->has('filter') || request()->has('min_price'))
                                <a href="{{ url()->current() }}"
                                    class="text-danger x-small text-decoration-none fw-bold ms-auto ms-lg-0">Clear All</a>
                            @endif
                        </div>
                        {{-- Form content remains same --}}
                        <form id="filterForm" action="" method="GET">
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif

                            {{-- Price Filter --}}
                            <div class="filter-group border-bottom py-2">
                                <a class="d-flex justify-content-between align-items-center text-dark text-decoration-none fw-bold mb-3"
                                    data-bs-toggle="collapse" href="#collapsePrice" role="button">
                                    Price <i class="las la-angle-down"></i>
                                </a>

                                <div class="collapse show" id="collapsePrice">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="position-relative w-100">
                                            <span class="position-absolute text-muted small"
                                                style="left: 8px; top: 7px;">₹</span>
                                            <input type="number" name="min_price" id="input-min"
                                                class="price-input-box ps-3" placeholder="0"
                                                value="{{ request('min_price') }}">
                                        </div>
                                        <span class="text-muted">-</span>
                                        <div class="position-relative w-100">
                                            <span class="position-absolute text-muted small"
                                                style="left: 8px; top: 7px;">₹</span>
                                            <input type="number" name="max_price" id="input-max"
                                                class="price-input-box ps-3" placeholder="Max"
                                                value="{{ request('max_price') }}">
                                        </div>
                                        <button type="submit" class="btn btn-dark btn-sm rounded-1 px-3">
                                            <i class="las la-angle-right"></i>
                                        </button>
                                    </div>
                                    <div class="px-2 pb-2">
                                        <div id="price-slider"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Dynamic Filters --}}
                            @foreach ($filters as $filter)
                                <div class="filter-group border-bottom py-2">
                                    <a class="d-flex justify-content-between align-items-center text-dark text-decoration-none fw-bold mb-2"
                                        data-bs-toggle="collapse" href="#collapse{{ $filter->id }}" role="button">
                                        {{ $filter->name }}
                                        <i class="las la-angle-down"></i>
                                    </a>
                                    <div class="collapse show" id="collapse{{ $filter->id }}">
                                        <div class="filter-options mt-2">
                                            @foreach ($filter->filterValues as $value)
                                                <div
                                                    class="form-check mb-1 d-flex justify-content-between align-items-center">
                                                    <div>
                                                        @php
                                                            $isChecked = false;
                                                            if (
                                                                request('filter') &&
                                                                isset(request('filter')[$filter->id])
                                                            ) {
                                                                $isChecked = in_array(
                                                                    $value->id,
                                                                    request('filter')[$filter->id],
                                                                );
                                                            }
                                                        @endphp
                                                        <input class="form-check-input filter-checkbox shadow-none"
                                                            type="checkbox" name="filter[{{ $filter->id }}][]"
                                                            value="{{ $value->id }}" id="val_{{ $value->id }}"
                                                            {{ $isChecked ? 'checked' : '' }}
                                                            onchange="this.form.submit()">
                                                        <label class="form-check-label text-muted small ms-1"
                                                            for="val_{{ $value->id }}">
                                                            {{ $value->value }}
                                                        </label>
                                                    </div>
                                                    <span class="text-muted x-small">({{ $value->products_count }})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </form>
                    </div>
                </div>
            </div>

            {{-- 🟢 PRODUCT GRID --}}
            <div class="col-lg-9">
                {{-- Toolbar --}}
                {{-- 🟢 TOOLBAR: मोबाइल के लिए बेहतर अलाइनमेंट --}}
                {{-- 🟢 TOOLBAR: डेस्कटॉप पर राइट अलाइन और मोबाइल पर फुल विड्थ --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-2 border-bottom">

                    {{-- 📱 मोबाइल फिल्टर बटन: सिर्फ मोबाइल (d-lg-none) पर दिखेगा --}}
                    <button
                        class="btn btn-white border d-lg-none d-flex justify-content-between align-items-center py-2 px-3"
                        style="flex: 1; min-width: 140px; font-size: 14px; background: #fff;" type="button"
                        data-bs-toggle="collapse" data-bs-target="#mobileFilterCollapse">
                        <span class="fw-bold"><i class="las la-filter me-1"></i> Filters</span>
                        <i class="las la-angle-down ms-2"></i>
                    </button>

                    {{-- 🏷️ सॉर्टिंग ड्रॉपडाउन: डेस्कटॉप पर राइट साइड में रहेगा --}}
                    <div class="dropdown ms-auto" style="min-width: 180px;">
                        @php
                            $sortOptions = [
                                'newest' => 'Newest',
                                'oldest' => 'Oldest',
                                'best-selling' => 'Best Selling',
                                'price_asc' => 'Price: Low-High',
                                'price_desc' => 'Price: High-Low',
                            ];
                            $currentSort = request('sort', 'newest');
                            $sortLabel = $sortOptions[$currentSort] ?? 'Newest';
                        @endphp

                        <a class="text-dark fw-bold text-decoration-none dropdown-toggle small border p-2 rounded d-flex justify-content-between align-items-center"
                            href="#" role="button" data-bs-toggle="dropdown"
                            style="background: #fff; height: 40px; width: 100%;">
                            <span><span class="text-muted fw-normal me-1">Sort by:</span> {{ $sortLabel }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1">
                            @foreach ($sortOptions as $key => $label)
                                <li><a class="dropdown-item small {{ $currentSort == $key ? 'active bg-light text-dark fw-bold' : '' }}"
                                        href="{{ request()->fullUrlWithQuery(['sort' => $key]) }}">{{ $label }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @if ($products->count() > 0)
                    <div class="row g-3">
                        @foreach ($products as $product)
                            <div class="col-6 col-md-4">
                                <div class="product-card h-100 position-relative">
                                    <div class="img-box mb-3 position-relative overflow-hidden rounded-0">
                                        <a href="{{ route('product.detail', $product->slug) }}">
                                            <img src="{{ asset($product->main_image) }}"
                                                alt="{{ $product->main_image_alt ?? $product->name }}"
                                                class="img-fluid w-100 object-fit-cover" style="aspect-ratio: 1/1;">
                                        </a>
                                        @if ($product->discount > 0)
                                            <span
                                                class="badge bg-danger position-absolute top-0 start-0 m-2 rounded-0 fw-normal px-2">
                                                {{ round($product->discount) }}% OFF
                                            </span>
                                        @endif
                                        <button type="button"
                                            class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 rounded-circle shadow-sm wishlist-btn"
                                            onclick="toggleWishlist({{ $product->id }}, this)"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">

                                            @php
                                                // Check if user has liked this product (Optimization Tip: Load this via logic later, abhi simple check)
                                                $isInWishlist =
                                                    Auth::check() &&
                                                    \App\Models\Wishlist::where('user_id', Auth::id())
                                                        ->where('product_id', $product->id)
                                                        ->exists();
                                            @endphp

                                            <i
                                                class="{{ $isInWishlist ? 'las la-heart text-danger' : 'lar la-heart' }} fs-5"></i>
                                        </button>
                                    </div>
                                    <div class="product-info text-left">
                                        <h3 class="h6 mb-1">
                                            <a href="{{ route('product.detail', $product->slug) }}"
                                                class="text-decoration-none text-dark fw-bold text-truncate d-block"
                                                style="font-family: 'Merriweather', serif;">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        {{-- ⭐ Dynamic Rating Logic --}}
                                        @php
                                            $avgRating = $product->reviews->avg('rating') ?? 0;
                                            $reviewCount = $product->reviews->count();
                                        @endphp
                                        <div class="text-warning d-flex align-items-center"
                                            style="font-size: 18px; margin-bottom: 6px;">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= round($avgRating))
                                                    <i class="las la-star"></i>
                                                @elseif($i - 0.5 <= $avgRating)
                                                    <i class="las la-star-half-alt"></i>
                                                @else
                                                    <i class="lar la-star"></i>
                                                @endif
                                            @endfor
                                            <span class="text-muted ms-1 text-dark fw-bold">({{ $reviewCount }})</span>
                                        </div>

                                        <div class="mb-2" style="text-align: left">
                                            <span class="fw-bold fs-6">₹{{ number_format($product->price, 0) }}</span>
                                            @if ($product->mrp_price > $product->price)
                                                <span
                                                    class="text-decoration-line-through text-muted ms-2 small">₹{{ number_format($product->mrp_price, 0) }}</span>
                                            @endif
                                        </div>
                                        <button class="btn btn-earthy w-100 btn-sm"
                                            onclick="addToCart({{ $product->id }}, 1, 0, this)">
                                            Add to cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3"><i class="las la-search fs-1 text-muted"></i></div>
                        <h4 class="h5">No products found</h4>
                        <p class="text-muted">Try removing some filters to see results.</p>
                        <a href="{{ url()->current() }}" class="btn btn-dark rounded-0 px-4">Clear Filters</a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @php
        $dTitle = '';
        $dContent = '';

        // Logic: Agar SubCategory set hai to uska data, nahi to Category ka data
        if (isset($subCategory) && !empty($subCategory->story_title)) {
            $dTitle = $subCategory->story_title;
            $dContent = $subCategory->story_content;
        } elseif (isset($category) && !empty($category->story_title)) {
            $dTitle = $category->story_title;
            $dContent = $category->story_content;
        }
    @endphp

    {{-- Include Partial --}}
    @include('frontend.includes.brand_story', [
        'storyTitle' => $dTitle,
        'storyContent' => $dContent,
    ])

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var slider = document.getElementById('price-slider');
            var inputMin = document.getElementById('input-min');
            var inputMax = document.getElementById('input-max');

            if (slider) {
                var minVal = parseInt("{{ request('min_price', 0) }}");
                var maxVal = parseInt("{{ request('max_price', 10000) }}");

                noUiSlider.create(slider, {
                    start: [minVal, maxVal || 10000],
                    connect: true,
                    range: {
                        'min': 0,
                        'max': 20000
                    },
                    step: 100,
                    format: {
                        to: function(value) {
                            return Math.round(value);
                        },
                        from: function(value) {
                            return Number(value);
                        }
                    }
                });

                slider.noUiSlider.on('update', function(values, handle) {
                    var value = values[handle];
                    if (handle === 0) {
                        if (inputMin) inputMin.value = value;
                    } else {
                        if (inputMax) inputMax.value = value;
                    }
                });

                if (inputMin) {
                    inputMin.addEventListener('change', function() {
                        slider.noUiSlider.set([this.value, null]);
                    });
                }
                if (inputMax) {
                    inputMax.addEventListener('change', function() {
                        slider.noUiSlider.set([null, this.value]);
                    });
                }
            }
        });
    </script>
@endsection
