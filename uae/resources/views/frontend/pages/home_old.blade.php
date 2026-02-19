@extends('frontend.layouts.app')

@section('title', 'Suyagya | Authentic Spiritual Products')

@section('content')

    {{-- Note: We rely on Slick Carousel JS being loaded from vendors.js or CDN --}}

    {{-- 💎 1. CATEGORY SCROLL SECTION --}}
    <section class="py-4 bg-white shadow-sm">
        <div class="container">

            {{-- Slider Container --}}
            {{-- opacity: 0 means shuru me invisible rahega --}}
            <div id="categoryScroll" class="category-slider" style="opacity: 0; transition: opacity 0.5s;">
                @foreach ($categories as $category)
                    <div class="px-2"> {{-- Spacing --}}
                        <div class="text-center category-item">
                            <a href="{{ url('category/' . $category['slug']) }}" class="text-decoration-none d-block">

                                {{-- Image Circle --}}
                                <div class="mx-auto mb-2 rounded-circle border p-1"
                                    style="width: 125px; height: 125px; border-color: #C19A6B !important; overflow: hidden;">
                                    <img src="{{ asset($category->icon_image) }}" alt="{{ $category['name'] }}"
                                        class="w-100 h-100 object-fit-cover rounded-circle">
                                </div>

                                {{-- Name --}}
                                <span class="small fw-bold text-dark d-block">
                                    {{ $category['name'] }}
                                </span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    {{-- 🖼️ 2. HERO SLIDER SECTION (DYNAMIC) --}}
    <section class="home-banner-area">
        <div class="container-fluid px-0">
            <div class="row g-0">
                <div class="col-12">

                    <div id="heroSlider">
                        @if (isset($banners) && count($banners) > 0)
                            @foreach ($banners as $banner)
                                <div>
                                    {{-- Link Logic: Agar link hai to <a> tag lagaye --}}
                                    <a href="{{ $banner->link ?? '#' }}" class="d-block">
                                        <picture>
                                            {{-- Mobile Image Logic: Check if exists, else use desktop image --}}
                                            @if ($banner->mobile_image)
                                                <source media="(max-width: 767px)"
                                                    srcset="{{ asset($banner->mobile_image) }}">
                                            @else
                                                <source media="(max-width: 767px)"
                                                    srcset="{{ asset($banner->desktop_image) }}">
                                            @endif

                                            {{-- Desktop Image (Main) --}}
                                            <img class="bnanner-img w-100" src="{{ asset($banner->desktop_image) }}"
                                                alt="Banner">
                                        </picture>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            {{-- Fallback: Agar Admin ne koi banner nahi dala --}}
                            <div>
                                <img src="https://placehold.co/1920x600?text=Welcome+to+Suyagya" class="w-100">
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- 🛒 3. FEATURED PRODUCTS (DYNAMIC) --}}
    <section class="py-5 featured-products-section" style="background-color: var(--light)">
        <div class="container">

            {{-- Heading --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box">
                    <h2 class="m-0">Featured Products</h2>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="row g-4">

                @foreach ($featuredProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card-minimal">

                            {{-- Image Area --}}
                            <div class="img-box">
                                @if ($product->discount > 0)
                                    <span class="badge bg-danger text-white position-absolute top-0 start-0 m-2 fw-bold"
                                        style="z-index: 2;">
                                        {{ $product->discount }}% OFF
                                    </span>
                                @endif
                                <button class="btn-wishlist">
                                    <i class="las la-heart"></i>
                                </button>
                                <a href="{{ route('product.detail', $product->slug) }}">
                                    <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}">
                                </a>
                            </div>

                            {{-- Details Area --}}
                            <div class="product-details text-start">
                                <a href="{{ route('product.detail', $product->slug) }}" class="product-title">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="d-flex align-items-center rating-row">
                                    <span class="stars text-warning">
                                        <i class="las la-star"></i><i class="las la-star"></i><i class="las la-star"></i><i
                                            class="las la-star"></i><i class="las la-star"></i>
                                    </span>
                                    <span class="review-count">(25)</span>
                                </div>

                                <div class="price-row">
                                    <span class="price-current">₹{{ number_format($product->price) }}</span>
                                    @if ($product->mrp_price > $product->price)
                                        <span class="price-old">₹{{ number_format($product->mrp_price) }}</span>
                                    @endif
                                </div>

                                {{-- Add to Cart --}}
                                <button class="btn btn-earthy" onclick="addToCart({{ $product->id }}, 1, 0, this)"
                                    data-id="{{ $product->id }}">
                                    Add to Cart
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach

                @if ($featuredProducts->count() == 0)
                    <div class="col-12 text-center text-muted">No featured products found.</div>
                @endif

            </div>

            {{-- View All --}}
            <div class="text-center mt-5">
                <a href="{{ url('products') }}" class="btn btn-view-all rounded-pill px-4 py-2">View all products</a>
            </div>

        </div>
    </section>

    {{-- 🛒 3. Best Selling PRODUCTS (DYNAMIC) --}}
    <section class="py-5 featured-products-section" style="background-color: var(--light)">
        <div class="container">

            {{-- Heading --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box">
                    <h2 class="m-0">Best Selling Products</h2>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="row g-4">

                @foreach ($bestSellingProducts as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card-minimal">

                            {{-- Image Area --}}
                            <div class="img-box">
                                @if ($product->discount > 0)
                                    <span class="badge bg-danger text-white position-absolute top-0 start-0 m-2 fw-bold"
                                        style="z-index: 2;">
                                        {{ $product->discount }}% OFF
                                    </span>
                                @endif
                                <button class="btn-wishlist">
                                    <i class="las la-heart"></i>
                                </button>
                                <a href="{{ route('product.detail', $product->slug) }}">
                                    <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}">
                                </a>
                            </div>

                            {{-- Details Area --}}
                            <div class="product-details text-start">
                                <a href="{{ route('product.detail', $product->slug) }}" class="product-title">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="d-flex align-items-center rating-row">
                                    <span class="stars text-warning">
                                        <i class="las la-star"></i><i class="las la-star"></i><i class="las la-star"></i><i
                                            class="las la-star"></i><i class="las la-star"></i>
                                    </span>
                                    <span class="review-count">(25)</span>
                                </div>

                                <div class="price-row">
                                    <span class="price-current">₹{{ number_format($product->price) }}</span>
                                    @if ($product->mrp_price > $product->price)
                                        <span class="price-old">₹{{ number_format($product->mrp_price) }}</span>
                                    @endif
                                </div>

                                {{-- Add to Cart --}}
                                <button class="btn btn-earthy" onclick="addToCart({{ $product->id }}, 1, 0, this)"
                                    data-id="{{ $product->id }}">
                                    Add to Cart
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach

                @if ($featuredProducts->count() == 0)
                    <div class="col-12 text-center text-muted">No featured products found.</div>
                @endif

            </div>

            {{-- View All --}}
            <div class="text-center mt-5">
                <a href="{{ url('products') }}" class="btn btn-view-all rounded-pill px-4 py-2">View all products</a>
            </div>

        </div>
    </section>

    {{-- 🛒 3. Best Selling PRODUCTS (DYNAMIC) --}}
    <section class="py-5 featured-products-section" style="background-color: var(--light)">
        <div class="container">

            {{-- Heading --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box">
                    <h2 class="m-0">our Products</h2>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="row g-4">

                @foreach ($products as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card-minimal">

                            {{-- Image Area --}}
                            <div class="img-box">
                                @if ($product->discount > 0)
                                    <span class="badge bg-danger text-white position-absolute top-0 start-0 m-2 fw-bold"
                                        style="z-index: 2;">
                                        {{ $product->discount }}% OFF
                                    </span>
                                @endif
                                <button class="btn-wishlist">
                                    <i class="las la-heart"></i>
                                </button>
                                <a href="{{ route('product.detail', $product->slug) }}">
                                    <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}">
                                </a>
                            </div>

                            {{-- Details Area --}}
                            <div class="product-details text-start">
                                <a href="{{ route('product.detail', $product->slug) }}" class="product-title">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                <div class="d-flex align-items-center rating-row">
                                    <span class="stars text-warning">
                                        <i class="las la-star"></i><i class="las la-star"></i><i
                                            class="las la-star"></i><i class="las la-star"></i><i
                                            class="las la-star"></i>
                                    </span>
                                    <span class="review-count">(25)</span>
                                </div>

                                <div class="price-row">
                                    <span class="price-current">₹{{ number_format($product->price) }}</span>
                                    @if ($product->mrp_price > $product->price)
                                        <span class="price-old">₹{{ number_format($product->mrp_price) }}</span>
                                    @endif
                                </div>

                                {{-- Add to Cart --}}
                                <button class="btn btn-earthy" onclick="addToCart({{ $product->id }}, 1, 0, this)"
                                    data-id="{{ $product->id }}">
                                    Add to Cart
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach

                @if ($featuredProducts->count() == 0)
                    <div class="col-12 text-center text-muted">No featured products found.</div>
                @endif

            </div>

            {{-- View All --}}
            <div class="text-center mt-5">
                <a href="{{ url('products') }}" class="btn btn-view-all rounded-pill px-4 py-2">View all products</a>
            </div>

        </div>
    </section>

    {{-- 🛒 4. video-feed-section (Placeholder for next section) --}}
    <section class="py-5 video-feed-section" style="background-color: #f7f1de;">
        <div class="container-fluid px-4">

            <div class="d-flex justify-content-center mb-4">
                <div class="fancy-heading-box">
                    <h2 class="m-0">Explore Feed</h2>
                </div>
            </div>

            <div class="video-slider-container">
                <div class="video-carousel">

                    {{-- 🟢 Check if videos exist --}}
                    @if (isset($videos) && $videos->count() > 0)

                        @foreach ($videos as $video)
                            <div class="px-2">
                                <div class="video-card">
                                    <div class="video-wrapper">

                                        {{-- ✅ VIDEO TAG --}}
                                        <video loop playsinline preload="none" muted class="the-video"
                                            poster="{{ asset($video->image) }}"> {{-- 🟢 Dynamic Poster --}}

                                            <source src="{{ asset($video->video) }}" type="video/mp4">
                                            {{-- 🟢 Dynamic Video --}}
                                        </video>

                                        {{-- Sound Toggle --}}
                                        <button class="btn-sound-toggle" type="button" title="Unmute">
                                            <i class="las la-volume-mute"></i>
                                        </button>

                                        {{-- Default Overlay --}}
                                        <div class="video-overlay-default">
                                            <div class="play-icon-circle">
                                                <i class="las la-play"></i>
                                            </div>
                                            <h5 class="video-title">{{ $video->title }}</h5> {{-- 🟢 Dynamic Title --}}
                                        </div>

                                        {{-- Buy Now Overlay --}}
                                        <div class="video-overlay-hover">
                                            {{-- 🟢 Dynamic Link (Handling Relative vs Absolute) --}}
                                            @php
                                                $link = $video->link;
                                                // If link doesn't start with http, wrap in url()
if (!Str::startsWith($link, ['http://', 'https://'])) {
                                                    $link = url($link);
                                                }
                                            @endphp

                                            <a href="{{ $link }}" class="btn btn-buy-now-video w-100">
                                                Buy Now <i class="las la-arrow-right ms-1"></i>
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Optional: Show nothing or a message if no videos --}}
                        <div class="text-center w-100">
                            <p class="text-muted">No videos available at the moment.</p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </section>


    <section class="py-5 favourites-section" style="background-color: #f7f1de;">
        <div class="container">

            {{-- 1. Fancy Heading --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box">
                    <h2 class="m-0">Suyagya Favourites</h2>
                </div>
            </div>

            {{-- 2. Masonry Grid Layout --}}
            <div class="favourites-grid">

                {{-- TOP ROW WRAPPER --}}
                <div class="row g-3">

                    {{-- LEFT COLUMN (Contains 1 Wide Image + 2 Small Images) --}}
                    <div class="col-lg-8">

                        {{-- 1. Wide Image (Top Left) --}}
                        <div class="fav-card wide mb-3">
                            <img src="https://prinjal.com/cdn/shop/files/02_Detail_copy.jpg?v=1711632938"
                                class="img-fluid" alt="Rudraksha Mala">
                            <div class="fav-content">
                                <h3>Rudraksha Mala</h3>
                                <a href="#" class="btn btn-fav-shop">Shop now</a>
                            </div>
                        </div>

                        {{-- Row for 2 Small Images --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                {{-- 2. Small Image (Middle Left 1) --}}
                                <div class="fav-card standard">
                                    <img src="https://prinjal.com/cdn/shop/files/SNA69239-min.jpg?v=1745405791"
                                        class="img-fluid" alt="Bracelets">
                                    <div class="fav-content">
                                        <h3>Rudraksha Bracelets</h3>
                                        <a href="#" class="btn btn-fav-shop">Shop now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- 3. Small Image (Middle Left 2) --}}
                                <div class="fav-card standard">
                                    <img src="https://prinjal.com/cdn/shop/files/SNA69051-min.jpg?v=1744891596"
                                        class="img-fluid" alt="Pendant">
                                    <div class="fav-content">
                                        <h3>Rudraksha Pendant</h3>
                                        <a href="#" class="btn btn-fav-shop">Shop now</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN (Contains 1 Tall Image) --}}
                    <div class="col-lg-4">
                        {{-- 4. Tall Image (Right Side) --}}
                        <div class="fav-card tall h-100">
                            <img src="https://prinjal.com/cdn/shop/files/Karungali_Beads_Silver_Mala.jpg?v=1750912937"
                                class="img-fluid" alt="Karungali Mala">
                            <div class="fav-content">
                                <h3>Karungali Mala</h3>
                                <a href="#" class="btn btn-fav-shop">Shop now</a>
                            </div>
                        </div>
                    </div>

                </div>
                {{-- END TOP ROW --}}

                {{-- BOTTOM ROW (Layout: 1 Small + 1 Wide) --}}
                <div class="row g-3 mt-0">

                    {{-- 1. Small Image (Left) --}}
                    <div class="col-md-4">
                        <div class="fav-card standard">
                            <img src="https://prinjal.com/cdn/shop/files/02_Detail_3172df1f-7ec7-447a-8345-581cdee4798f.jpg?v=1711690744"
                                class="img-fluid" alt="Adiyogi">
                            <div class="fav-content">
                                <h3>Adiyogi Pendant</h3>
                                <a href="#" class="btn btn-fav-shop">Shop now</a>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Merged Wide Image (Right - Replaces 2 small divs) --}}
                    <div class="col-md-8">
                        <div class="fav-card wide">
                            {{-- यहाँ अपनी पसंद की चौड़ी इमेज लगाएं --}}
                            <img src="https://prinjal.com/cdn/shop/files/HanumanSilverIdol.png?v=1744016339"
                                class="img-fluid" alt="Murti Collection">
                            <div class="fav-content">
                                <h3>Murti Collection</h3>
                                <a href="#" class="btn btn-fav-shop">Shop now</a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <section class="py-5 energy-section" style="background-color: #f7f1de;">
        <div class="container">

            {{-- 1. Fancy Heading --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box">
                    <h2 class="m-0">Choose Energy You Want to Attract</h2>
                </div>
            </div>

            {{-- 2. Energy Icons Grid --}}
            <div class="row g-4 justify-content-center">
                @php
                    // Helper array to map DB names to Icons
                    // Ensure keys match your DB values exactly (case-insensitive usually preferred)
                    $iconMap = [
                        'Wealth' => asset('assets/img/icons/wealth.png'),
                        'Love' => asset('assets/img/icons/love.png'),
                        'Health' => asset('assets/img/icons/health.png'),
                        'Luck' => asset('assets/img/icons/luck.png'),
                        'Protection' => asset('assets/img/icons/protection.png'),
                        'Peace' => asset('assets/img/icons/peace.png'),
                        'Courage' => asset('assets/img/icons/courage.png'),
                        'Balance' => asset('assets/img/icons/balance.png'),
                        // Add default fallback if needed
                    ];
                @endphp

                @foreach ($purposes as $purpose)
                    @php
                        // Get icon or default placeholder
                        $icon = $iconMap[$purpose->value] ?? asset('assets/img/icons/default.png');
                    @endphp

                    <div class="col-6 col-sm-4 col-md-3 col-lg-custom-8">
                        {{-- 🔗 Dynamic Link: Sends to Product Listing with ?purpose=Health --}}
                        <a href="{{ route('products.all_collection') }}?purpose={{ $purpose->value }}&sort_by=created-descending"
                            class="energy-card text-decoration-none d-block text-center">

                            <div class="icon-wrapper mb-3 mx-auto">
                                <img src="{{ $icon }}" alt="{{ $purpose->value }}" class="img-fluid">
                            </div>

                            <h5 class="energy-title">{{ $purpose->value }}</h5>
                        </a>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ✨ 6. DYNAMIC SUB-CATEGORY SHOWCASE SECTIONS ✨ --}}
    {{-- ✨ DYNAMIC CATEGORY SHOWCASE SECTIONS (Ring, Earring, Pendant) ✨ --}}
    {{-- ✨ DYNAMIC CATEGORY SHOWCASE SECTIONS ✨ --}}
    @if (isset($showcaseSections) && $showcaseSections->count() > 0)
        @foreach ($showcaseSections as $section)
            @if ($section->products->count() > 0)
                <section class="py-5 category-showcase-section" style="background-color: #f7f1de;">
                    <div class="container-fluid px-4">

                        {{-- 1. Heading --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h3 fw-bold text-dark m-0">
                                {{ $section->category->name }} {{ $section->name }}
                            </h2>
                            <a href="{{ route('products.subcategory', ['cat_slug' => $section->category->slug, 'sub_slug' => $section->slug]) }}"
                                class="btn btn-outline-dark rounded-pill px-4">
                                View all
                            </a>
                        </div>

                        <div class="row g-3">

                            {{-- 2. LEFT SIDE: CATEGORY BANNER --}}
                            <div class="col-lg-2 d-none d-lg-block">
                                <div class="category-banner-card h-100 position-relative overflow-hidden rounded-3"
                                    style="min-height: 300px; background-color: #e0d4c3;"> {{-- Added min-height & bg-color --}}

                                    {{-- Image Logic: Check if image exists, else show placeholder --}}
                                    @php
                                        $bannerImage = $section->image
                                            ? asset($section->image)
                                            : 'https://placehold.co/300x500/e0d4c3/555?text=' .
                                                urlencode($section->name);
                                    @endphp

                                    <img src="{{ $bannerImage }}" alt="{{ $section->name }}"
                                        class="img-fluid w-100 h-100 object-fit-cover banner-img">

                                    <div class="banner-content position-absolute bottom-0 start-0 p-3 w-100 text-white"
                                        style="background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);">
                                        <h3 class="h4 fw-bold mb-0">{{ $section->name }}<br>Collection</h3>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. RIGHT SIDE: PRODUCTS GRID --}}
                            <div class="col-lg-10 col-12">
                                {{-- Use 'row' for proper grid alignment of products --}}
                                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">

                                    @foreach ($section->products as $product)
                                        <div class="col"> {{-- Auto column width based on row-cols classes above --}}
                                            <div
                                                class="product-card-standard h-100 border rounded-3 overflow-hidden bg-white shadow-sm">

                                                {{-- Image --}}
                                                <div class="card-img-wrapper position-relative bg-light"
                                                    style="aspect-ratio: 1/1;">
                                                    @if ($product->discount > 0)
                                                        <span
                                                            class="badge bg-danger text-white position-absolute top-0 start-0 m-2 fw-bold"
                                                            style="z-index: 2;">
                                                            {{ $product->discount }}% OFF
                                                        </span>
                                                    @endif

                                                    <a href="{{ route('product.detail', $product->slug) }}"
                                                        class="d-block w-100 h-100">
                                                        <img src="{{ asset($product->main_image) }}"
                                                            alt="{{ $product->name }}"
                                                            class="w-100 h-100 object-fit-cover">
                                                    </a>
                                                </div>

                                                {{-- Info --}}
                                                <div class="p-3 text-start">
                                                    <h6 class="product-title mb-1 text-truncate fw-bold"
                                                        style="font-size: 14px;">
                                                        <a href="{{ route('product.detail', $product->slug) }}"
                                                            class="text-dark text-decoration-none">
                                                            {{ $product->name }}
                                                        </a>
                                                    </h6>

                                                    <div class="mb-2 text-warning small">
                                                        <i class="las la-star"></i><i class="las la-star"></i><i
                                                            class="las la-star"></i>
                                                        <i class="las la-star"></i><i class="las la-star"></i>
                                                        <span class="text-muted ms-1">(24)</span>
                                                    </div>

                                                    <div class="mb-2">
                                                        <span
                                                            class="fw-bold text-dark">₹{{ number_format($product->price) }}</span>
                                                        @if ($product->mrp_price > $product->price)
                                                            <span
                                                                class="text-muted text-decoration-line-through small ms-2">
                                                                ₹{{ number_format($product->mrp_price) }}
                                                            </span>
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
                            </div>

                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    @endif

    <section class="ratings-bar-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">

                    <div class="ratings-content py-4">
                        <h3 class="text-white fw-bold m-0 mb-2">Join Over 50,000 Happy Customers.</h3>

                        <div class="d-flex justify-content-center align-items-center gap-2">
                            {{-- Stars --}}
                            <div class="stars-row">
                                <i class="las la-star"></i>
                                <i class="las la-star"></i>
                                <i class="las la-star"></i>
                                <i class="las la-star"></i>
                                <i class="las la-star-half-alt"></i>
                            </div>

                            {{-- Text --}}
                            <span class="text-white fw-600 fs-16">Rated 4.7/5 1,500 Reviews</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- 🌟 CUSTOMER LOVE / REVIEWS SECTION --}}
    <section class="py-5 testimonial-section" style="background-color: #f7f1de;">
        <div class="container">

            {{-- 1. Heading --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box">
                    <h2 class="m-0">Customer Love</h2>
                </div>
            </div>

            {{-- 2. Testimonial Slider --}}
            <div class="testimonial-slider-container">
                <div class="testimonial-slider">

                    @if (isset($reviews) && $reviews->count() > 0)
                        @foreach ($reviews as $review)
                            <div class="px-3"> {{-- Spacing between cards --}}
                                <div class="testimonial-card">
                                    <div class="row g-0 h-100">

                                        {{-- Left: Text Content --}}
                                        <div
                                            class="col-md-7 col-12 d-flex flex-column justify-content-center p-4 text-content">

                                            {{-- Stars Dynamic Loop --}}
                                            <div class="mb-3 text-warning">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $review->rating)
                                                        <i class="las la-star"></i>
                                                    @else
                                                        <i class="lar la-star"></i>
                                                    @endif
                                                @endfor
                                            </div>

                                            {{-- Review Text --}}
                                            <p class="review-text mb-3">
                                                "{{ Str::limit($review->review, 150) }}"
                                            </p>

                                            {{-- Title & Name --}}
                                            @if ($review->title)
                                                <h6 class="fw-bold text-dark mb-1">{{ $review->title }}</h6>
                                            @endif

                                            <p class="text-muted small m-0 fw-600">- {{ $review->display_name }}</p>
                                        </div>

                                        {{-- Right: Image (Media) --}}
                                        <div class="col-md-5 col-12">
                                            <div class="review-img-wrapper h-100">
                                                @php
                                                    // Check if media exists (it's an array)
// If yes, take the first image. If no, use a placeholder.
$reviewImage =
    'https://placehold.co/400x400/e0d4c3/555?text=Happy+Customer';

if (
    !empty($review->media) &&
    is_array($review->media) &&
    count($review->media) > 0
) {
    // Check if file is image (simple check)
    $firstFile = $review->media[0];
    if (!Str::endsWith($firstFile, '.mp4')) {
                                                            $reviewImage = asset($firstFile);
                                                        }
                                                    }
                                                @endphp

                                                <img src="{{ $reviewImage }}" alt="{{ $review->display_name }}"
                                                    class="img-fluid w-100 h-100 object-fit-cover">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center text-muted">
                            <p>No reviews yet. Be the first to share your experience!</p>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </section>

    <section class="py-5 blog-section" style="background-color: #f7f1de;">
        <div class="container">

            {{-- 1. Heading --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box">
                    <h2 class="m-0">Blogs</h2>
                </div>
            </div>

            {{-- 2. Blog Cards Row --}}
            <div class="row g-4 justify-content-center">

                @php
                    // Demo Blog Data - REPLACE IMAGES WITH YOUR OWN
                    $blogs = [
                        [
                            'title' => 'The Divine Power of Lord Shiva',
                            'tag' => 'Spiritual Knowledge',
                            // 👇 अपनी शिव जी की इमेज का लिंक यहाँ डालें
                            'img' => 'https://placehold.co/600x400/E8E8E8/333333?text=Lord+Shiva+Temple',
                            'desc' =>
                                'Discover the immense power and symbolism behind Lord Shiva, the destroyer and transformer in the Holy Trinity. Understand his role in the cosmic cycle.',
                        ],
                        [
                            'title' => 'Why We Worship Lord Ganesha First',
                            'tag' => 'Vedic Rituals',
                            // 👇 अपनी गणेश जी की इमेज का लिंक यहाँ डालें
                            'img' => 'https://placehold.co/600x400/E8E8E8/333333?text=Lord+Ganesha+Idol',
                            'desc' =>
                                'Understand the significance of invoking Lord Ganesha, the remover of obstacles, before any new beginning to ensure success and prosperity.',
                        ],
                        [
                            'title' => 'The Significance of Navratri & Maa Durga',
                            'tag' => 'Festivals & Deities',
                            // 👇 अपनी माँ दुर्गा की इमेज का लिंक यहाँ डालें
                            'img' => 'https://placehold.co/600x400/E8E8E8/333333?text=Maa+Durga',
                            'desc' =>
                                'Explore the nine divine forms of Goddess Durga worshipped during Navratri and their unique spiritual significance in empowering the soul.',
                        ],
                    ];
                @endphp

                @foreach ($blogs as $blog)
                    <div class="col-md-6 col-lg-4">
                        <div class="blog-card h-100">

                            {{-- Image & Tag Wrapper --}}
                            <div class="blog-img-wrapper">
                                <span class="blog-tag">{{ $blog['tag'] }}</span>
                                <a href="#" class="d-block h-100">
                                    <img src="{{ $blog['img'] }}" alt="{{ $blog['title'] }}" class="img-fluid">
                                </a>
                            </div>

                            {{-- Card Content --}}
                            <div class="blog-content">
                                <h3 class="blog-title">
                                    <a href="#" class="text-decoration-none text-dark">{{ $blog['title'] }}</a>
                                </h3>
                                <p class="blog-desc">{{ $blog['desc'] }}</p>
                                <a href="#" class="read-more-btn">
                                    Read more <i class="las la-arrow-right ms-1"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    <section class="py-5 faq-section" style="background-color: #f7f1de;"> {{-- Earthy Background --}}
        <div class="container">

            {{-- 1. Fancy Heading (Light Box on Dark BG) --}}
            <div class="d-flex justify-content-center mb-5">
                <div class="fancy-heading-box" style="background-color: #FFFBF2;">
                    <h2 class="m-0">FAQs</h2>
                </div>
            </div>

            <div class="row">
                @php
                    $faqs = [
                        [
                            'q' => 'What makes Suyagya jewelry unique?',
                            'a' =>
                                'Our jewelry is handcrafted using authentic beads and 92.5 sterling silver, ensuring spiritual energy and durability.',
                        ],
                        [
                            'q' => 'Are Suyagya Rudraksha beads genuine and certified?',
                            'a' =>
                                'Yes, every Rudraksha bead is lab-tested and comes with an authenticity certificate.',
                        ],
                        [
                            'q' => 'Can I buy jewelry for kids and women too?',
                            'a' =>
                                'Absolutely! We have a wide range of lightweight and adjustable designs suitable for everyone.',
                        ],
                        [
                            'q' => 'Why does the color of Rudraksha & Silver change?',
                            'a' =>
                                'Silver naturally oxidizes over time, and Rudraksha may darken due to body oils, which is a natural process.',
                        ],
                        [
                            'q' => 'Is Suyagya’s silver capping made of genuine silver?',
                            'a' => 'Yes, we strictly use 92.5 Sterling Silver for all our capping and chains.',
                        ],
                        [
                            'q' => 'Why is Suyagya better than other brands?',
                            'a' =>
                                'We prioritize spiritual authenticity, premium craftsmanship, and verified materials over mass production.',
                        ],
                        [
                            'q' => 'How do Karungali and Black Rudraksha differ?',
                            'a' =>
                                'While both Karungali and Black Rudraksha have protective spiritual qualities, Karungali is a type of sacred wood, offering durability and natural energy, whereas Black Rudraksha is a bead from the Rudraksha tree, prized for its unique metaphysical benefits.',
                        ],
                        [
                            'q' => 'Why wear Karungali by Suyagya?',
                            'a' =>
                                'Our Karungali is sourced from mature ebony trees and crafted to retain its natural electromagnetic properties.',
                        ],
                    ];
                @endphp

                {{-- Left Column (First Half) --}}
                <div class="col-lg-6">
                    <div class="accordion" id="faqAccordionLeft">
                        @foreach (array_slice($faqs, 0, 4) as $key => $faq)
                            <div class="faq-item mb-3">
                                <h2 class="accordion-header" id="headingL{{ $key }}">
                                    <button class="accordion-button collapsed faq-btn" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseL{{ $key }}"
                                        aria-expanded="false">
                                        {{ $faq['q'] }}
                                    </button>
                                </h2>
                                <div id="collapseL{{ $key }}" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordionLeft">
                                    <div class="accordion-body faq-answer">
                                        {{ $faq['a'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column (Second Half) --}}
                <div class="col-lg-6">
                    <div class="accordion" id="faqAccordionRight">
                        @foreach (array_slice($faqs, 4) as $key => $faq)
                            <div class="faq-item mb-3">
                                <h2 class="accordion-header" id="headingR{{ $key }}">
                                    <button class="accordion-button collapsed faq-btn" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseR{{ $key }}"
                                        aria-expanded="false">
                                        {{ $faq['q'] }}
                                    </button>
                                </h2>
                                <div id="collapseR{{ $key }}" class="accordion-collapse collapse"
                                    data-bs-parent="#faqAccordionRight">
                                    <div class="accordion-body faq-answer">
                                        {{ $faq['a'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </section>

    <section class="py-5 brand-story-section" style="background-color: #f7f1de;">
        <div class="container">

            <div class="accordion" id="brandStoryAccordion">
                <div class="accordion-item bg-transparent border-0 border-bottom border-dark">

                    {{-- 1. The Clickable Header --}}
                    <h2 class="accordion-header" id="headingStory">
                        <button class="accordion-button collapsed bg-transparent shadow-none text-dark fw-bold fs-5 px-0"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseStory"
                            aria-expanded="false" aria-controls="collapseStory">
                            Suyagya - India's Best Spiritual Jewelry Brand
                        </button>
                    </h2>

                    {{-- 2. The Expandable Content (Text from Image 1) --}}
                    <div id="collapseStory" class="accordion-collapse collapse" aria-labelledby="headingStory"
                        data-bs-parent="#brandStoryAccordion">
                        <div class="accordion-body px-0 pt-4 brand-story-content text-secondary">

                            <p>At Suyagya, we celebrate the age-old art of jewelry-making while interweaving it with
                                contemporary designs that resonate with today's generation. Our collections are a medley of
                                tradition, spirituality, and modernity.</p>

                            <h4 class="mt-4 text-dark fw-bold">1. Men Jewelry Collection</h4>
                            <p>For the modern man who values tradition, our Men Jewelry Collection strikes the perfect
                                balance between style and spirituality.</p>
                            <ul>
                                <li><strong>Rudraksha Mala:</strong> Embrace the spiritual essence with our authentic
                                    Rudraksha Malas.</li>
                                <li><strong>Rudraksha Pendant:</strong> A symbol of spirituality and wellbeing, our
                                    Rudraksha Pendants meld authenticity with style.</li>
                                <li><strong>Adiyogi Pendant:</strong> Celebrate the essence of spiritual awakening with our
                                    intricately designed Adiyogi Pendants.</li>
                                <li><strong>Rudraksha Bracelet:</strong> Infuse your everyday style with a touch of divinity
                                    with our range of Rudraksha bracelets.</li>
                            </ul>

                            <h4 class="mt-4 text-dark fw-bold">2. Women Jewelry Collection</h4>
                            <p>Elegance, tradition, and style converge in our Women Jewelry Collection, catering to the
                                multifaceted women of today.</p>
                            <ul>
                                <li><strong>Necklace Set for Women:</strong> From ornate sets for special occasions to
                                    minimalistic designs for daily wear.</li>
                                <li><strong>Women Mangalsutra:</strong> A symbol of marital bliss, our Mangalsutras blend
                                    tradition with modern designs.</li>
                                <li><strong>Women Bracelets:</strong> A melange of tradition and contemporary designs,
                                    perfect for gracing a woman's delicate wrist.</li>
                                <li><strong>Anklets for Women:</strong> Adorn your feet with our range of silver anklets,
                                    from traditional ghungroo designs to contemporary styles.</li>
                            </ul>

                            <h4 class="mt-4 text-dark fw-bold">3. Kids Jewelry Collection</h4>
                            <p>Cherish the innocent milestones of childhood with our endearing Kids Jewelry Collection.</p>
                            <ul>
                                <li><strong>Baby Bracelet:</strong> Gentle, safe, and crafted with love, our baby bracelets
                                    are perfect keepsakes.</li>
                                <li><strong>Kids Nazariya:</strong> Let every tiny step jingle with joy with our traditional
                                    and skin-friendly Nazariyas.</li>
                            </ul>

                            <h4 class="mt-4 text-dark fw-bold">4. Stone Malas & Bracelets</h4>
                            <p>Discover the natural beauty and craftsmanship of our Stone Mala Collection, featuring
                                intricately designed malas crafted from high-quality natural stones.</p>
                            <ul>
                                <li><strong>Karungali Stone Mala:</strong> Made from Ebony Wood (Karungali), these malas
                                    exude bold elegance.</li>
                                <li><strong>Sphatik Stone Mala:</strong> Featuring Crystal Beads (Sphatik), these malas
                                    offer a sleek and polished look.</li>
                            </ul>

                            <h3 class="mt-5 text-dark fw-bold">The Suyagya Promise: Unwavering Quality, Authenticity, and
                                Trust</h3>

                            <h5 class="mt-3 text-dark fw-bold">1. Uncompromised Quality:</h5>
                            <p>Every jewelry piece at Suyagya undergoes rigorous quality checks to ensure it stands true to
                                the high standards we've set for ourselves.</p>

                            <h5 class="mt-3 text-dark fw-bold">2. Authenticity Assured:</h5>
                            <p>With the flood of counterfeit products in the market, we understand the concerns about
                                authenticity. At Suyagya, our promise is genuine, and so are our products.</p>

                            <h5 class="mt-3 text-dark fw-bold">3. Features Tailored for You:</h5>
                            <p>At Suyagya, customization is at the heart of what we do. Recognizing the uniqueness of every
                                individual.</p>

                            <h5 class="mt-3 text-dark fw-bold">4. Building Trust, One Piece at a Time:</h5>
                            <p>Trust is the cornerstone of Suyagya's ethos. And we strive, day in and day out, to fortify
                                this trust.</p>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>


@endsection
