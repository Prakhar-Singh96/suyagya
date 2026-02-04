@extends('frontend.layouts.app')

@section('content')
    {{-- Title Section --}}
    <div class="py-3 text-center border-bottom">
        <div class="container">
            <h1 class="font-heading fw-bold text-dark mb-0">{{ isset($subCategory) ? $subCategory->name : $category->name }}
            </h1>
            <p class="text-muted fw-bold small mb-0">({{ $products->total() }} products)</p>
            <p class="text-muted fw-bold small mb-0" style="max-width: 600px; margin: 0 auto;">
                {{ isset($subCategory) ? $subCategory->description : $category->description }}
            </p>
        </div>
    </div>

    <div class="container py-3">
        <div class="row">
            {{-- 🟢 सब-कैटेगरी और सॉर्टिंग को एक ही लाइन में लाने वाला टूलबार --}}
            {{-- 🟢 मास्टर टूलबार: सब कुछ एक ही लाइन में --}}
            <div class="col-12 mb-4">
            {{-- 🟢 मोबाइल पर कॉलम (Stack) और डेस्कटॉप पर रो (Row) --}}
            <div class="d-flex flex-column d-lg-flex flex-lg-row align-items-center justify-content-between border-bottom pb-3 gap-3">

                {{-- 1. मोबाइल फिल्टर बटन: अब यह चिप्स के ऊपर या साइड में सही से अलाइन होगा --}}
                <div class="w-100 d-flex justify-content-between align-items-center d-lg-none">
                    <button class="btn btn-white border d-flex align-items-center py-2 px-3 shadow-sm rounded-3"
                            type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterSidebar">
                        <span class="fw-bold"><i class="las la-sliders-h me-1"></i> Filter & Sort</span>
                    </button>
                </div>

                {{-- 🚀 2. सेंटर चिप्स: मोबाइल पर फुल विड्थ स्क्रॉल और डेस्कटॉप पर सेंटर --}}
                <div class="subcategory-nav-container d-flex align-items-center justify-content-start justify-content-lg-center flex-grow-1 overflow-auto w-100"
                     style="white-space: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;">

                    <div class="d-flex gap-2 pe-3"> {{-- pe-3 ताकि आखिरी चिप कटे नहीं --}}
                        @if(isset($category) && method_exists($category, 'subCategories') && $category->subCategories->count() > 0)
                            <a href="{{ route('products.category', $category->slug) }}"
                               class="chip-item {{ !isset($subCategory) ? 'active' : '' }}">
                                All
                            </a>

                            @foreach($category->subCategories as $sc)
                                <a href="{{ route('products.subcategory', ['cat_slug' => $category->slug, 'sub_slug' => $sc->slug]) }}"
                                   class="chip-item {{ (isset($subCategory) && $subCategory->id == $sc->id) ? 'active' : '' }}">
                                    {{ $sc->name }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- 3. डेस्कटॉप सॉर्टिंग ड्रॉपडाउन (सिर्फ डेस्कटॉप पर) --}}
                <div class="dropdown d-none d-lg-block">
                    @php
                        $sortOptions = ['newest' => 'Newest', 'oldest' => 'Oldest', 'best-selling' => 'Best Selling', 'price_asc' => 'Price: Low-High', 'price_desc' => 'Price: High-Low'];
                        $currentSort = request('sort', 'newest');
                    @endphp
                    <a class="text-dark fw-bold text-decoration-none dropdown-toggle small border p-2 px-3 rounded bg-white shadow-sm d-flex align-items-center"
                       href="#" role="button" data-bs-toggle="dropdown" style="height: 40px; min-width: 150px;">
                        Sort by: {{ $sortOptions[$currentSort] ?? 'Newest' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        @foreach ($sortOptions as $key => $label)
                            <li><a class="dropdown-item small {{ $currentSort == $key ? 'active bg-light' : '' }}"
                                   href="{{ request()->fullUrlWithQuery(['sort' => $key]) }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>

            {{-- 🖥️ DESKTOP SIDEBAR --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="filter-sidebar border rounded p-3 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-uppercase">Filters</h6>
                        @if (request()->has('filter') || request()->has('min_price'))
                            <a href="{{ url()->current() }}" class="text-danger x-small fw-bold">Clear All</a>
                        @endif
                    </div>
                    <form action="" method="GET">
                        @include('frontend.includes.filter_form_content')
                    </form>
                </div>
            </div>

            {{-- 🟢 PRODUCT GRID --}}
            <div class="col-lg-9">
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
                    <div class="mt-5 d-flex justify-content-center">{{ $products->links('pagination::bootstrap-5') }}</div>
                @else
                    <div class="text-center py-5">
                        <h4>No products found</h4>
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

    {{-- 📱 MOBILE FILTER SIDEBAR (Japam Style Offcanvas) --}}
    <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileFilterSidebar">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">Filter & Sort</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <form action="" method="GET" class="p-4 h-100 d-flex flex-column">
                {{-- मोबाइल सॉर्टिंग --}}
                <div class="mb-4">
                    <label class="fw-bold mb-2">Sort By</label>
                    <select name="sort" class="form-select border shadow-none" onchange="this.form.submit()">
                        @foreach ($sortOptions as $key => $label)
                            <option value="{{ $key }}" {{ $currentSort == $key ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @include('frontend.includes.filter_form_content')

                {{-- Sticky Footer Button --}}
                <div class="mt-auto pt-4 pb-2">
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold text-uppercase rounded-3">
                        Show {{ $products->total() }} Results
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        #mobileFilterSidebar {
            width: 85%;
            max-width: 400px;
        }

        .filter-group label {
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #000;
            border-color: #000;
        }

        /* स्लाइडर को दिखने लायक ऊँचाई और मार्जिन दें */
        /* स्लाइडर को दिखने लायक ऊँचाई दें */
        .price-slider {
            margin-top: 10px;
        }

        /* कंटेनर को पूरी चौड़ाई दें और स्क्रॉल छुपाएं */
    .subcategory-nav-container {
        scrollbar-width: none; /* Firefox */
    }
    .subcategory-nav-container::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }

    /* चिप्स का स्टाइल */
    .chip-item {
        display: inline-block;
        padding: 7px 18px;
        border-radius: 50px;
        border: 1px solid #e0e0e0;
        background-color: #fff;
        color: #444;
        text-decoration: none !important;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    /* मोबाइल पर चिप्स को कटने से बचाने के लिए */
    @media (max-width: 991px) {
        .subcategory-nav-container {
            padding: 5px 0;
            margin: 0 -15px; /* कंटेनर के बाहर तक स्क्रॉल करने के लिए */
            padding-left: 15px; /* पहली चिप को गैप देने के लिए */
        }
    }

    .chip-item.active {
        background-color: #000;
        color: #fff;
        border-color: #000;
    }
    </style>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 🚀 सभी स्लाइडर (डेस्कटॉप + मोबाइल) को एक साथ पकड़ें
            var sliders = document.querySelectorAll('.price-slider');
            var minInputs = document.querySelectorAll('input[name="min_price"]');
            var maxInputs = document.querySelectorAll('input[name="max_price"]');

            var minVal = parseInt("{{ request('min_price', 0) }}");
            var maxVal = parseInt("{{ request('max_price', 20000) }}");

            // हर स्लाइडर के लिए अलग से इन्सटेंस बनाएँ
            sliders.forEach(function(slider) {
                if (slider) {
                    noUiSlider.create(slider, {
                        start: [minVal, maxVal],
                        connect: true,
                        range: {
                            'min': 0,
                            'max': 200000
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

                    // अपडेट लॉजिक: एक स्लाइडर हिलेगा तो दोनों इनपुट अपडेट होंगे
                    slider.noUiSlider.on('update', function(values, handle) {
                        var value = values[handle];
                        if (handle === 0) {
                            minInputs.forEach(input => {
                                input.value = value;
                            });
                        } else {
                            maxInputs.forEach(input => {
                                input.value = value;
                            });
                        }
                    });

                    // इनपुट बदलने पर स्लाइडर भी हिले
                    minInputs.forEach(input => {
                        input.addEventListener('change', function() {
                            slider.noUiSlider.set([this.value, null]);
                        });
                    });
                    maxInputs.forEach(input => {
                        input.addEventListener('change', function() {
                            slider.noUiSlider.set([null, this.value]);
                        });
                    });
                }
            });
        });
    </script>
@endsection
