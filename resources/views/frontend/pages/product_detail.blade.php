@extends('frontend.layouts.app')

@section('title', $product->name . ' | Suyagya')

@section('styles')
    <style>
        /* ✨ PREMIUM DESIGN STYLES */
        :root {
            --primary-orange: #ff6f00;
            --text-dark: #222;
            --bg-cream: #fffbf2;
        }

        /* 💎 GEMSTONE CONFIGURATOR STYLES (AstroTalk Style) */
        /* .gem-config-container {
                                border: 1px solid #eee;
                                padding: 15px;
                                border-radius: 8px;
                                margin-bottom: 20px;
                                background-color: #f7f1de;
                            } */

        .gem-option-group {
            margin-bottom: 15px;
        }

        .gem-option-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-family: 'Merriweather', serif;
        }

        .gem-btn-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .gem-btn {
            border: 1px solid #ddd;
            background: #fff;
            padding: 8px 16px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
            color: #555;
            min-width: 60px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .gem-btn:hover {
            border-color: #ff6f00;
            color: #ff6f00;
        }

        .gem-btn.active {
            border-color: #ff6f00;
            color: #ff6f00;
            background-color: #fffbf2;
            /* Light orange tint */
            font-weight: 600;
            box-shadow: 0 0 0 1px #ff6f00 inset;
            /* Thicker look */
        }

        /* Icon styling inside buttons */
        .gem-btn i {
            font-size: 16px;
        }

        /* Material Colors */
        .mat-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            display: inline-block;
            margin-right: 5px;
            border: 1px solid #ccc;
        }

        .bg-silver {
            background-color: #c0c0c0;
        }

        .bg-panch {
            background-color: #d4af37;
        }

        /* 📱 RESPONSIVE SLIDER FIX */
        .product-slider-container {
            height: 622px;
            /* Fixed height for Desktop */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            overflow: hidden;
            /* Prevent spillover */
        }

        .product-slider-container img,
        .product-slider-container video {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            /* Default for Desktop */
        }

        /* Mobile Adjustments */
        /* @media (max-width: 768px) {
                                                                .product-slider-container {
                                                                    height: 455px !important;
                                                                    aspect-ratio: 1 / 1;
                                                                    width: 100%;
                                                                }

                                                                .product-slider-container img,
                                                                .product-slider-container video {
                                                                    width: 100%;
                                                                    height: 100%;
                                                                    object-fit: cover;
                                                                }

                                                                .product-images {
                                                                    top: 0 !important;
                                                                }
                                                            } */
        /* 🔥 ZOOM STYLES */
        .product-slider-container {
            overflow: hidden;
            /* Bahar na nikle */
            cursor: zoom-in;
            /* Cursor change */
            position: relative;
        }

        .product-slider-container img {
            transition: transform 0.1s ease-out;
            /* Smooth movement */
            transform-origin: center center;
            will-change: transform;
        }
    </style>
@endsection


@section('content')

    {{-- 🍞 2. OPTIMIZED BREADCRUMBS (Home > Category > Product) --}}
    <div class="py-2 border-bottom mb-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a>
                    </li>

                    @if ($product->category)
                        <li class="breadcrumb-item">
                            <a href="{{ route('products.category', $product->category->slug) }}"
                                class="text-muted text-decoration-none">
                                {{ $product->category->name }}
                            </a>
                        </li>
                    @endif

                    @if ($product->subCategory)
                        <li class="breadcrumb-item">
                            <a href="{{ route('products.subcategory', ['cat_slug' => $product->category->slug, 'sub_slug' => $product->subCategory->slug]) }}"
                                class="text-muted text-decoration-none">
                                {{ $product->subCategory->name }}
                            </a>
                        </li>
                    @endif

                    <li class="breadcrumb-item active text-dark" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small text-start">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container pb-5">
        <div class="row g-lg-5">

            {{-- 🖼️ LEFT SIDE: IMAGE GALLERY --}}


            {{-- 🖼️ LEFT SIDE: IMAGE GALLERY --}}
            <div class="col-lg-6 mb-4">
                <div class="product-images" style="position: sticky; top: 20px; z-index: 10;">

                    {{-- 1. MAIN BIG SLIDER --}}
                    {{-- 1. MAIN BIG SLIDER --}}
                    <div class="product-main-slider mb-3">

                        {{-- A. Main Image (First Slide) --}}
                        <div class="product-slider-container zoom-container">
                            <a href="{{ asset($product->product_main_image) }}" class="glightbox"
                                data-gallery="product-gallery">
                                <img src="{{ asset($product->product_main_image) }}"
                                    class="img-fluid w-100 h-100 object-fit-contain zoom-img"
                                    alt="{{ $product->product_main_image_alt ?? $product->name }}">
                            </a>
                        </div>

                        {{-- B. Gallery Loop --}}
                        @if ($product->images->count() > 0)
                            @foreach ($product->images as $img)
                                @php
                                    $extension = pathinfo($img->image, PATHINFO_EXTENSION);
                                    $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'webm']);
                                @endphp

                                <div class="product-slider-container {{ $isVideo ? '' : 'zoom-container' }}">
                                    @if ($isVideo)
                                        {{-- Video (No Zoom, No Lightbox on click usually, or specific lightbox type) --}}
                                        <a href="{{ asset($img->image) }}" class="glightbox"
                                            data-gallery="product-gallery">
                                            <video width="100%" height="100%"
                                                style="object-fit: contain; max-height: 100%;">
                                                <source src="{{ asset($img->image) }}" type="video/{{ $extension }}">
                                            </video>
                                            {{-- Fake overlay to catch click for lightbox --}}
                                            <div class="position-absolute top-0 start-0 w-100 h-100"></div>
                                        </a>
                                    @else
                                        {{-- Image (Zoom + Lightbox) --}}
                                        <a href="{{ asset($img->image) }}" class="glightbox"
                                            data-gallery="product-gallery">
                                            <img src="{{ asset($img->image) }}"
                                                class="img-fluid w-100 h-100 object-fit-contain zoom-img"
                                                alt="{{ $img->alt ?? $product->name }}">
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- 2. THUMBNAIL SLIDER --}}
                    <div class="product-thumb-slider px-4">
                        {{-- Main Image Thumb --}}
                        <div class="mx-1">
                            <div class="border rounded overflow-hidden" style="height: 80px; cursor: pointer;">
                                <img src="{{ asset($product->product_main_image) }}" class="w-100 h-100 object-fit-cover"
                                    alt="{{ $product->product_main_image_alt ?? $product->name }} thumbnail">
                            </div>
                        </div>

                        {{-- Gallery Thumbs --}}
                        @if ($product->images->count() > 0)
                            @foreach ($product->images as $img)
                                @php
                                    $extension = pathinfo($img->image, PATHINFO_EXTENSION);
                                    $isVideo = in_array(strtolower($extension), ['mp4', 'mov', 'avi', 'webm']);
                                @endphp

                                <div class="mx-1">
                                    <div class="border rounded overflow-hidden position-relative d-flex align-items-center justify-content-center bg-light"
                                        style="height: 80px; cursor: pointer;">

                                        @if ($isVideo)
                                            {{-- 🎥 VIDEO THUMBNAIL --}}
                                            {{-- Use a video tag without controls for the thumbnail look --}}
                                            <video src="{{ asset($img->image) }}" class="w-100 h-100 object-fit-cover"
                                                muted playsinline onmouseover="this.play()"
                                                onmouseout="this.pause();this.currentTime=0;"></video>

                                            {{-- Optional: Overlay Play Icon for better UX --}}
                                            <div
                                                class="position-absolute top-50 start-50 translate-middle pointer-events-none">
                                                <i class="las la-play-circle text-white"
                                                    style="font-size: 30px; opacity: 0.8; text-shadow: 0 0 5px rgba(0,0,0,0.5);"></i>
                                            </div>
                                        @else
                                            {{-- 🖼️ Image Thumb --}}
                                            <img src="{{ asset($img->image) }}" class="w-100 h-100 object-fit-cover"
                                                alt="{{ $img->alt ?? $product->name }} thumbnail">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- 📝 RIGHT SIDE: PRODUCT INFO --}}
            <div class="col-lg-6">

                <h1 class="fw-bold font-heading mb-2 text-dark" style="font-size: 1.8rem; line-height: 1.3;">
                    {{ $product->name }}
                </h1>

                {{-- Rating --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning small me-2">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= round($averageRating))
                                <i class="las la-star"></i>
                            @elseif($i - 0.5 <= $averageRating)
                                <i class="las la-star-half-alt"></i>
                            @else
                                <i class="lar la-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted small border-start ps-2">{{ number_format($averageRating, 1) }}
                        ({{ $totalReviews }} Reviews)</span>
                </div>

                {{-- 💰 PRICE SECTION (FIXED FOR MRP UPDATE) --}}
                <div class="mb-3 d-flex align-items-baseline">
                    <input type="hidden" id="base_price" value="{{ $product->price }}">

                    <span class="fs-2 fw-bold text-dark me-2">
                        ₹<span id="display_price">{{ number_format($product->price) }}</span>
                    </span>

                    {{-- 🔥 FIX: HTML structure update taaki JS hamesha MRP dhoond sake --}}
                    {{-- Agar MRP bada hai to dikhao, nahi to 'd-none' class se chupao --}}
                    <span id="mrp_container"
                        class="text-decoration-line-through text-muted fs-5 {{ $product->mrp_price > $product->price ? '' : 'd-none' }}">
                        ₹<span id="display_mrp">{{ number_format($product->mrp_price) }}</span>
                    </span>

                    <span id="discount_container"
                        class="text-danger fw-bold ms-3 bg-danger-subtle px-2 py-1 rounded small {{ $product->mrp_price > $product->price ? '' : 'd-none' }}">
                        <span id="display_discount">{{ round($product->discount) }}</span>% OFF
                    </span>
                </div>

                {{-- Timer --}}
                <div class="offer-timer-box mb-4 p-2 border border-danger rounded d-inline-block bg-light">
                    <span class="text-danger fw-bold small me-2">Offer ends in:</span>
                    <span id="countdown" class="fw-bold text-dark"
                        style="min-width: 100px; display: inline-block;">Loading...</span>
                </div>

                @if ($product->is_gemstone && $product->gemstoneVariants->count() > 0)

                    {{-- 🔥 SMART PHP: Check what actually exists --}}
                    @php
                        $gemVariants = $product->gemstoneVariants;
                        $availTypes = $gemVariants->pluck('type')->unique()->toArray();
                        $availMats = $gemVariants->pluck('material')->unique()->filter()->toArray();

                        // Default Selection (Pick the first available variant)
                        $firstVar = $gemVariants->first();
                        $defType = $firstVar->type;
                        $defRatti = $firstVar->ratti_size;
                        $defMat = $firstVar->material ?? 'silver';
                    @endphp

                    {{-- Hidden Data --}}
                    <div id="gem_data" style="display:none;">{{ json_encode($gemVariants) }}</div>
                    <input type="hidden" name="variant_id" id="selected_variant_id" value="{{ $firstVar->id }}">

                    <div class="gem-config-container">

                        {{-- 1. TYPE SELECTOR (Show only available types) --}}
                        @if (count($availTypes) > 1)
                            <div class="gem-option-group">
                                <span class="gem-option-title">Type</span>
                                <div class="gem-btn-wrapper">
                                    @if (in_array('loose', $availTypes))
                                        <div class="gem-btn {{ $defType == 'loose' ? 'active' : '' }} gem-type-btn"
                                            onclick="updateGemState('type', 'loose', this)">
                                            <i class="las la-gem"></i> Gemstone
                                        </div>
                                    @endif
                                    @if (in_array('ring', $availTypes))
                                        <div class="gem-btn {{ $defType == 'ring' ? 'active' : '' }} gem-type-btn"
                                            onclick="updateGemState('type', 'ring', this)">
                                            <i class="las la-ring"></i> Ring
                                        </div>
                                    @endif
                                    @if (in_array('pendant', $availTypes))
                                        <div class="gem-btn {{ $defType == 'pendant' ? 'active' : '' }} gem-type-btn"
                                            onclick="updateGemState('type', 'pendant', this)">
                                            <i class="las la-medal"></i> Pendant
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <input type="hidden" id="sel_type" value="{{ $defType }}">

                        {{-- 2. SIZE (RATTI) - Fixed to show ALL available sizes --}}
                        {{-- <div class="gem-option-group">
                            <span class="gem-option-title">Size (Ratti)</span>
                            <div class="gem-btn-wrapper" id="ratti_group">
                                {{-- 🔥 FIX: Removed 'where type loose'. Now shows unique sizes from ALL variants
                                @foreach ($gemVariants->unique('ratti_size')->sortBy('ratti_size') as $gv)
                                    <div class="gem-btn gem-ratti-btn {{ (string) $gv->ratti_size == (string) $defRatti ? 'active' : '' }}"
                                        onclick="updateGemState('ratti', '{{ $gv->ratti_size }}', this)">
                                        {{ $gv->ratti_size }} Ratti
                                    </div>
                                @endforeach
                            </div>
                        </div> --}}
                        {{-- <input type="hidden" id="sel_ratti" value="{{ $defRatti }}"> --}}

                        {{-- 3. MATERIAL (Dynamic Visibility) --}}
                        {{-- <div class="gem-option-group" id="material_section"
                            style="display: {{ $defType == 'loose' ? 'none' : 'block' }};">
                            <span class="gem-option-title">Material</span>
                            <div class="gem-btn-wrapper">
                                @if (in_array('silver', $availMats))
                                    <div class="gem-btn {{ $defMat == 'silver' ? 'active' : '' }} gem-mat-btn"
                                        onclick="updateGemState('material', 'silver', this)">
                                        <span class="mat-color bg-silver"></span> Silver
                                    </div>
                                @endif
                                @if (in_array('panchdhatu', $availMats))
                                    <div class="gem-btn {{ $defMat == 'panchdhatu' ? 'active' : '' }} gem-mat-btn"
                                        onclick="updateGemState('material', 'panchdhatu', this)">
                                        <span class="mat-color bg-panch"></span> Panchdhatu
                                    </div>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" id="sel_mat" value="{{ $defMat }}"> --}}

                        {{-- 4. RING SIZE (Only show if Type is Ring) --}}
                        <div class="gem-option-group" id="ring_size_section"
                            style="display: {{ $defType == 'ring' ? 'block' : 'none' }};">
                            <span class="gem-option-title">Ring Size</span>
                            <div class="d-flex align-items-center">
                                <select class="form-select w-auto" name="ring_size" style="min-width: 200px;">
                                    <option value="">Select a Ring Size</option>
                                    @for ($i = 10; $i <= 30; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                    <option value="adjustable">Free/Adjustable</option>
                                </select>
                                {{-- Updated Link with Icon --}}
                                <a href="{{ route('ring.size.guide') }}"
                                    class="small text-primary text-decoration-none ms-3 fw-bold d-flex align-items-center hover-underline">
                                    <i class="las la-ruler-combined me-1" style="font-size: 1.2rem;"></i>
                                    Find Your Size
                                </a>
                            </div>
                        </div>

                    </div>
                @elseif ($product->variants->count() > 0)
                    {{-- STANDARD WEIGHT DROPDOWN (No Change) --}}
                    <div class="mb-4 bg-light p-2 rounded border" style="max-width: 250px;">
                        <label class="fw-bold small mb-1 d-block text-dark">Select Weight:</label>
                        <select class="form-select form-select-sm border-secondary fw-bold text-dark" id="variant_select"
                            name="variant_id">
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->id }}" data-price="{{ $variant->selling_price }}"
                                    data-mrp="{{ $variant->mrp_price }}" data-stock="{{ $variant->quantity }}">
                                    {{ $variant->weight }}g
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                {{-- 👆👆 CONFIG END 👆👆 --}}


                {{-- EMI Widget --}}
                {{-- @if ($product->emi_available)

                    <div class="emi-box border rounded p-2 mb-4 align-items-center bg-white"
                        style="max-width: 400px; display: {{ $product->price > 1100 ? 'flex' : 'none' }};">

                        <span class="badge bg-success me-2" style="font-size: 10px;">NEW</span>
                        <div class="flex-grow-1" style="font-size: 13px;">
                            or <strong>₹<span id="emi_amount">{{ ceil($product->price / 3) }}</span>/month</strong> (3
                            months)
                            <span class="badge bg-warning text-dark ms-1" style="font-size: 10px;">0% Interest</span>
                            <div class="text-muted" style="font-size: 11px;">UPI & Cards Accepted | No Extra Cost</div>
                        </div>
                    </div>
                @endif --}}

                <div id="razorpay-affordability-widget"></div>

                {{-- Siddh Checkbox --}}
                @if ($product->is_siddh_enabled)
                    <div class="siddh-box p-3 border rounded mb-4"
                        style="background-color: #fcf8f2; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1)">
                        <div class="form-check d-flex align-items-center">
                            <input class="form-check-input me-3" type="checkbox" id="siddh_check"
                                style="width: 25px; height: 25px; cursor: pointer;">
                            <div>
                                <label class="form-check-label fw-bold text-dark cursor-pointer" for="siddh_check">
                                    Get Siddh Product for Just ₹{{ number_format($product->siddh_price, 0) }}
                                </label>
                                <small class="d-block text-muted">Energized with mantras for better results.</small>
                            </div>
                        </div>
                    </div>
                @endif
                <input type="hidden" name="is_siddh" id="input_is_siddh" value="0">

                {{-- Buttons --}}
                @if ($product->quantity > 0)
                    <div class="mb-4">
                        <label class="fw-bold small mb-2 d-block">Quantity</label>
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-secondary btn-sm rounded-0" onclick="updateQty('minus')"><i
                                    class="las la-minus"></i></button>
                            <input type="text" id="qty_input" name="quantity"
                                class="form-control text-center border-secondary fs-6 fw-bold" value="1"
                                min="1" max="{{ $product->quantity }}" readonly>
                            <button class="btn btn-outline-secondary btn-sm rounded-0" onclick="updateQty('plus')"><i
                                    class="las la-plus"></i></button>
                        </div>
                        @if ($product->quantity < 5)
                            <small class="text-danger fw-bold mt-1 d-block"><i class="las la-exclamation-circle"></i> Only
                                {{ $product->quantity }} left in stock!</small>
                        @endif
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <button class="btn btn-warning w-50 py-3 fw-bold text-dark text-uppercase shadow-sm fs-6"
                            style="border: 2px solid #ffc107;" data-id="{{ $product->id }}"
                            onclick="addToCartFromDetail(this)">Add to Cart</button>
                        <button class="btn btn-dark w-50 py-3 fw-bold text-uppercase shadow-sm fs-6"
                            data-id="{{ $product->id }}" onclick="openDirectCheckout(this)">Buy Now</button>
                    </div>
                @else
                    <div class="alert alert-danger border-0 d-flex align-items-center mb-4"
                        style="background-color: #ffe5e5; color: #cc0000;">
                        <i class="las la-ban fs-3 me-2"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Out of Stock</h6><small>This item is currently unavailable.</small>
                        </div>
                    </div>
                @endif

                {{-- 🏷️ RAZORPAY & BANK OFFERS SECTION --}}
                {{-- 🏷️ RAZORPAY & BANK OFFERS SECTION --}}
                <div class="offers-box mb-4 p-3 border rounded"
                    style="background-color: #fcf8f5; font-family: 'Merriweather', serif;">

                    <h6 class="fw-bold text-dark mb-3"
                        style="font-size: 16px; border-bottom: 1px dashed #ccc; padding-bottom: 10px;">
                        <img src="https://razorpay.com/favicon.png" width="18" class="me-2"
                            style="vertical-align: sub;">
                        Best Offers for You
                    </h6>

                    <div class="d-flex flex-column gap-3">

                        {{-- 1. Tata Neu UPI --}}
                        <div>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-start">
                                    <i class="las la-credit-card text-primary mt-1 me-2 fs-5"></i>
                                    <div style="line-height: 1.5;">
                                        <strong class="text-dark d-block" style="font-size: 14px;">Tata NeuCard UPI
                                            Offer</strong>
                                        <span class="text-muted small">Upto 1.5% savings with NeuCard UPI txns.</span>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="$('#tc_neu_upi').slideToggle()"
                                    class="fw-bold text-primary small text-decoration-none"
                                    style="white-space: nowrap;">T&C</a>
                            </div>
                            <div id="tc_neu_upi" class="mt-2 p-2 bg-white border rounded text-muted"
                                style="display:none; font-size: 12px; line-height: 1.6;">
                                <strong>Terms and Conditions:</strong><br>
                                (1) 1.5% back as NeuCoins with Tata Neu HDFC Bank Infinity credit card on CC on UPI
                                transactions done using Tata Neu App.<br>
                                (2) 1% back with Plus credit card on Tata Neu App.<br>
                                (3) 0.5% back with Infinity card on 3rd party apps (PhonePe, GPay etc).<br>
                                (4) 0.25% back with Plus card on 3rd party apps.<br>
                                (5) 1 NeuCoin = ₹1 redeemable on TATA Brands.<br>
                                (6) Capped to 500 NeuCoins per month.<br>
                                Exclusions: Fuel, Cash Advances, EMI, Rental, Govt, Education payments via 3rd party apps.
                            </div>
                        </div>

                        {{-- 2. Tata Neu EMI/Non-EMI --}}
                        <div>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-start">
                                    <i class="las la-credit-card text-primary mt-1 me-2 fs-5"></i>
                                    <div style="line-height: 1.5;">
                                        <strong class="text-dark d-block" style="font-size: 14px;">Tata NeuCard EMI
                                            Offer</strong>
                                        <span class="text-muted small">Upto 1.5% savings with NeuCard on EMI/non-EMI
                                            txns.</span>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="$('#tc_neu_emi').slideToggle()"
                                    class="fw-bold text-primary small text-decoration-none"
                                    style="white-space: nowrap;">T&C</a>
                            </div>
                            <div id="tc_neu_emi" class="mt-2 p-2 bg-white border rounded text-muted"
                                style="display:none; font-size: 12px; line-height: 1.6;">
                                <strong>Terms and Conditions:</strong><br>
                                1. 1.5% back as NeuCoins with Tata Neu Infinity credit card on EMI/non-EMI spends.<br>
                                2. 1% back with Tata Neu Plus credit card.<br>
                                3. 1 NeuCoin = ₹1 redeemable on TATA Brands.<br>
                                4. Governed by Fair Usage Policy (hdfcbank.com).<br>
                                Exclusions: Fuel, Wallet loads, Cash Advances, Rental, Govt, Education payments via 3rd
                                party apps.
                            </div>
                        </div>

                        {{-- 3. Navi UPI --}}
                        <div>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-start">
                                    <i class="las la-wallet text-success mt-1 me-2 fs-5"></i>
                                    <div style="line-height: 1.5;">
                                        <strong class="text-dark d-block" style="font-size: 14px;">Navi UPI
                                            Cashback</strong>
                                        <span class="text-muted small">Win up to ₹100 cashback on every transaction on Navi
                                            UPI.</span>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="$('#tc_navi').slideToggle()"
                                    class="fw-bold text-primary small text-decoration-none"
                                    style="white-space: nowrap;">T&C</a>
                            </div>
                            <div id="tc_navi" class="mt-2 p-2 bg-white border rounded text-muted"
                                style="display:none; font-size: 12px; line-height: 1.6;">
                                - Offer valid on payment via Navi UPI.<br>
                                - Rewards will be credited to Navi account and can be redeemed to cash.<br>
                                - Reward issuance is solely at Navi's discretion.
                            </div>
                        </div>

                        {{-- 4. Amazon Pay --}}
                        <div>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-start">
                                    <i class="lab la-amazon text-warning mt-1 me-2 fs-5"></i>
                                    <div style="line-height: 1.5;">
                                        <strong class="text-dark d-block" style="font-size: 14px;">Amazon Pay
                                            Balance</strong>
                                        <span class="text-muted small">Win upto ₹300 back across 4 transactions.</span>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="$('#tc_amazon').slideToggle()"
                                    class="fw-bold text-primary small text-decoration-none"
                                    style="white-space: nowrap;">T&C</a>
                            </div>
                            <div id="tc_amazon" class="mt-2 p-2 bg-white border rounded text-muted"
                                style="display:none; font-size: 12px; line-height: 1.6;">
                                1) Min Order Value: ₹100.<br>
                                2) Assured cashback upto ₹75 on each transaction (max 4 times).<br>
                                3) Cashback given as Scratch Card in Amazon Pay Rewards section within 24 hours.<br>
                                4) Scratch card valid till end of this month.
                            </div>
                        </div>

                        {{-- 5. CRED UPI --}}
                        <div>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-start">
                                    <i class="las la-shield-alt text-dark mt-1 me-2 fs-5"></i>
                                    <div style="line-height: 1.5;">
                                        <strong class="text-dark d-block" style="font-size: 14px;">CRED UPI Offer</strong>
                                        <span class="text-muted small">Win assured cashback upto ₹50 via CRED UPI.</span>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="$('#tc_cred').slideToggle()"
                                    class="fw-bold text-primary small text-decoration-none"
                                    style="white-space: nowrap;">T&C</a>
                            </div>
                            <div id="tc_cred" class="mt-2 p-2 bg-white border rounded text-muted"
                                style="display:none; font-size: 12px; line-height: 1.6;">
                                - Cashback will be available on CRED app to claim within 7 days of transaction.<br>
                                - Other T&C may apply.
                            </div>
                        </div>

                        {{-- 6. No Cost EMI --}}
                        <div>
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="d-flex align-items-start">
                                    <i class="las la-percentage text-danger mt-1 me-2 fs-5"></i>
                                    <div style="line-height: 1.5;">
                                        <strong class="text-dark d-block" style="font-size: 14px;">No Cost EMI</strong>
                                        <span class="text-muted small">0% Interest on UPI & Cards Accepted | No Extra
                                            Cost.</span>
                                    </div>
                                </div>
                                <a href="javascript:void(0)" onclick="$('#tc_no_cost').slideToggle()"
                                    class="fw-bold text-primary small text-decoration-none"
                                    style="white-space: nowrap;">T&C</a>
                            </div>
                            <div id="tc_no_cost" class="mt-2 p-2 bg-white border rounded text-muted"
                                style="display:none; font-size: 12px; line-height: 1.6;">
                                1. The interest charged by the bank will be given as an upfront discount on the payment
                                page, making it effectively No Cost EMI.<br>
                                2. This offer is valid only on selected Bank Credit Cards.<br>
                                3. Banks may charge a nominal processing fee.
                            </div>
                        </div>

                    </div>
                </div>

                {{-- 🚚 DELIVERY CHECKER --}}
                <div class="delivery-check-box mb-3">
                    <h6 class="fw-bold small mb-2 d-flex align-items-center"><i
                            class="las la-truck fs-4 me-2 text-danger"></i> <span style="color: #000;">Get estimated
                            delivery date</span></h6>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="las la-map-marker text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 border-secondary ps-0"
                            placeholder="Enter your pincode" id="pincodeInput" maxlength="6" style="box-shadow: none;">
                        <button class="btn text-white fw-bold px-4" style="background-color: #198754;"
                            onclick="checkDelivery()">Check</button>
                    </div>
                    <div id="deliveryResult" class="small fw-bold mt-2 mb-2" style="display:none;"></div>
                </div>

                {{-- Trust Badge --}}
                <div class="secure-box d-flex align-items-center justify-content-between p-3 rounded mb-3"
                    style="background-color: #e8f5e9; border: 1px solid #c8e6c9;">
                    <div class="d-flex align-items-center"><i class="las la-check-circle fs-3 text-success me-2"></i>
                        <div style="line-height: 1.2;">
                            <div class="fw-bold text-dark" style="font-size: 13px;">100% Secure</div>
                            <div class="text-dark" style="font-size: 12px;">Payment Guarantee</div>
                        </div>
                    </div>
                    <div class="payment-icons d-flex align-items-center gap-4 flex-wrap justify-content-end">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Google_Pay_Logo.svg" height="20"
                            alt="GPay">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/PhonePe_Logo.svg" height="20"
                            alt="PhonePe">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo-vector.svg" height="20"
                            alt="UPI">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" height="20"
                            alt="Mastercard">
                    </div>
                </div>

                {{-- Accordions --}}
                <div class="accordion accordion-flush mt-4" id="productDetailsAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseDescription" aria-expanded="true">
                                <i class="las la-leaf me-2 fs-5"></i> Description
                            </button>
                        </h2>

                        {{-- ✅ CHANGE 2: 'show' class add kar di --}}
                        <div id="collapseDescription" class="accordion-collapse collapse show"
                            data-bs-parent="#productDetailsAccordion">
                            <div class="accordion-body text-muted small" style="line-height: 1.6;">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header"><button class="accordion-button collapsed fw-bold text-dark"
                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseHowToWear"><i
                                    class="las la-hand-holding-heart me-2 fs-5"></i> How To Wear & Recharge</button></h2>
                        <div id="collapseHowToWear" class="accordion-collapse collapse"
                            data-bs-parent="#productDetailsAccordion">
                            <div class="accordion-body text-muted small" style="line-height: 1.6;">
                                <p>Wear on DOMINANT HAND.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header"><button class="accordion-button collapsed fw-bold text-dark"
                                type="button" data-bs-toggle="collapse" data-bs-target="#collapseDelivery"><i
                                    class="las la-truck me-2 fs-5"></i> Delivery</button></h2>
                        <div id="collapseDelivery" class="accordion-collapse collapse"
                            data-bs-parent="#productDetailsAccordion">
                            <div class="accordion-body text-muted small" style="line-height: 1.6;">
                                <p>Free delivery on orders above ₹299.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <section class="py-5" style="background-color: #D32F2F; color: white;">
        <div class="container text-center">
            <h2 class="font-heading fw-bold mb-3" style="font-family: 'Playfair Display', serif;">Made In India</h2>
            <p class="mx-auto" style="max-width: 800px; font-size: 1.1rem; line-height: 1.6;">
                All our jewellery is handmade by Indian craftsmen and women - largely from villages. In this way, we are
                able to play our part in supporting and growing the local Indian economy.
            </p>
        </div>
    </section>

    <section class="py-5 bg-light" id="review-section" style="--bs-bg-opacity: 0;">
        <div class="container">

            <div class="card border-0 shadow-sm p-4 mb-4">
                <div class="row align-items-center">

                    {{-- 📊 Left: Rating Stats --}}
                    <div class="col-md-4 text-center border-end">
                        <h2 class="display-3 fw-bold text-dark mb-0">{{ number_format($averageRating, 1) }}</h2>
                        <div class="text-warning fs-4 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($averageRating))
                                    <i class="las la-star"></i>
                                @elseif($i - 0.5 <= $averageRating)
                                    <i class="las la-star-half-alt"></i>
                                @else
                                    <i class="lar la-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-muted small">Based on {{ $totalReviews }} reviews</p>
                    </div>

                    {{-- 📉 Middle: Progress Bars --}}
                    <div class="col-md-5 px-4">
                        @foreach ([5, 4, 3, 2, 1] as $star)
                            @php
                                $count = $starCounts[$star] ?? 0;
                                $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-2">
                                <span class="small me-2">{{ $star }} <i
                                        class="las la-star text-warning"></i></span>
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-dark" role="progressbar"
                                        style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="small ms-2 text-muted">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- ✍️ Right: Write Review --}}
                    <div class="col-md-3 text-center">
                        <div class="p-3 border rounded bg-white">
                            <h6 class="fw-bold mb-3">Click to review</h6>
                            <div class="review-trigger-stars fs-2 text-secondary cursor-pointer"
                                onclick="openReviewModal()">
                                <i class="lar la-star" data-value="1"></i>
                                <i class="lar la-star" data-value="2"></i>
                                <i class="lar la-star" data-value="3"></i>
                                <i class="lar la-star" data-value="4"></i>
                                <i class="lar la-star" data-value="5"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 🔽 Filters --}}
            <div class="d-flex align-items-center mb-3">
                <select class="form-select w-auto me-2" id="reviewSort" onchange="filterReviews({{ $product->id }})">
                    <option value="recent">Recent</option>
                    <option value="highest">Highest Rating</option>
                    <option value="lowest">Lowest Rating</option>
                    <option value="media">Reviews with Media</option>
                </select>
            </div>

            {{-- 💬 Review List Container --}}
            <div class="row g-3" id="reviewListContainer">
                @include('frontend.includes.review_list', ['reviews' => $reviews])
            </div>

        </div>
    </section>

    {{-- ✅ Include Review Modal Here --}}
    @include('frontend.modals.review-modal')

    {{-- ======================================= --}}
    {{-- 🛍️ YOU MAY ALSO LIKE (RELATED PRODUCTS) --}}
    {{-- ======================================= --}}
    @if ($relatedProducts->count() > 0)
        <section class="py-5 bg-white" style="--bs-bg-opacity: 0 !important; background-color: #f7f1de !important;">
            <div class="container">

                {{-- Heading --}}
                <h3 class="fw-bold font-heading mb-4 text-dark position-relative d-inline-block">
                    You may also like
                    <span class="position-absolute start-0 bottom-0 w-50 border-bottom border-2 border"
                        style="width: 100% !important; border-color:#FFCC66 !important;"></span>
                </h3>

                {{-- Product Grid --}}
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4">

                    @foreach ($relatedProducts as $related)
                        <div class="col">
                            <div
                                class="card h-100 border rounded-0 shadow-sm position-relative product-card overflow-hidden">

                                {{-- Discount Badge (Optional) --}}
                                @if ($related->mrp_price > $related->price)
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2 rounded-0 small">
                                        <i class="las la-tag"></i>
                                        {{ round((($related->mrp_price - $related->price) / $related->mrp_price) * 100) }}%
                                        off
                                    </span>
                                @endif

                                {{-- Product Image --}}
                                <a href="{{ url('product/' . $related->slug) }}"
                                    class="d-block overflow-hidden bg-light ratio ratio-1x1">
                                    <img src="{{ asset($related->main_image) }}"
                                        class="card-img-top w-100 h-100 object-fit-cover product-img-hover"
                                        alt="{{ $related->main_image_alt ?? $related->name }}">
                                </a>

                                {{-- Card Body --}}
                                <div class="card-body p-3 d-flex flex-column">

                                    {{-- Title --}}
                                    <h6 class="card-title mb-2"
                                        style="font-size: 0.95rem; line-height: 1.4; min-height: 2.8em;">
                                        <a href="{{ url('product/' . $related->slug) }}"
                                            class="text-dark text-decoration-none fw-semibold stretched-link">
                                            {{ Str::limit($related->name, 50) }}
                                        </a>
                                    </h6>

                                    {{-- ⭐ Dynamic Rating Logic ⭐ --}}
                                    <div class="mb-2 small text-warning">
                                        @php
                                            // Rating ko round figure me convert karein (e.g. 4.5 -> 5)
                                            $avgStar = round($related->reviews_avg_rating ?? 0);
                                        @endphp

                                        {{-- 1 se 5 tak loop chalayen --}}
                                        @foreach (range(1, 5) as $i)
                                            @if ($avgStar >= $i)
                                                {{-- Bhara hua sitara --}}
                                                <i class="las la-star"></i>
                                            @else
                                                {{-- Khali sitara --}}
                                                <i class="lar la-star"></i>
                                            @endif
                                        @endforeach

                                        {{-- Review Count --}}
                                        <span class="text-muted ms-1" style="font-size: 0.75rem;">
                                            ({{ $related->reviews_count ?? 0 }})
                                        </span>
                                    </div>

                                    {{-- Price --}}
                                    <div class="mt-auto">
                                        <span class="fw-bold text-dark fs-6">₹{{ number_format($related->price) }}</span>
                                        @if ($related->mrp_price > $related->price)
                                            <small class="text-decoration-line-through text-muted ms-1"
                                                style="font-size: 0.8rem;">
                                                ₹{{ number_format($related->mrp_price) }}
                                            </small>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>
        </section>
    @endif

    <section class="py-5 bg-white shadow-sm" style="--bs-bg-opacity: 0 !important; background-color: #f7f1de !important;">
        <div class="container" style="width: 80%;">
            <h4 class="text-dark fw-bold mb-4 ps-3">Shop by Category</h4>
            <!-- FIXED Category Carousel -->
            <div class="w-100 position-relative">
                <div id="categoryScroll" class="category-slider d-flex align-items-center">
                    @foreach ($categories as $category)
                        <div class="carousel-box px-2">
                            <div class="category-scroll-item text-center">
                                <a class="d-block" href="{{ url('category/' . $category['slug']) }}">
                                    <div class="mega-icon mx-auto mb-2">
                                        <img src="{{ asset($category->icon_image) }}" class="img-fluid"
                                            alt="{{ $category['icon_alt'] ?? $category['name'] }}">
                                    </div>
                                    <span class="small fw-semibold text-dark">{{ $category['name'] }}</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- END FIXED Carousel -->
        </div>
    </section>

    @php
        // Product Specific FAQs
        $pFaqs = $product->faq_content ?? [];

        // Split into 2 columns
        $pChunks = [];
        if (count($pFaqs) > 0) {
            $pChunks = array_chunk($pFaqs, ceil(count($pFaqs) / 2));
        }

        $pLeft = $pChunks[0] ?? [];
        $pRight = $pChunks[1] ?? [];
    @endphp

    @if (count($pFaqs) > 0)
        <section class="py-5 faq-section mt-4" style="background-color: #f7f1de;">
            <div class="container">

                {{-- Heading --}}
                <div class="d-flex justify-content-center mb-5">
                    <div class="fancy-heading-box"
                        style="background-color: #FFFBF2; padding: 10px 30px; border: 1px solid #ddd;">
                        <h2 class="m-0 font-heading fw-bold">Product FAQs</h2>
                    </div>
                </div>

                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        @include('frontend.includes.faq_accordion', [
                            'faqs' => $pLeft,
                            'idSuffix' => 'prod_left',
                        ])
                    </div>

                    {{-- Right Column --}}
                    <div class="col-lg-6">
                        @include('frontend.includes.faq_accordion', [
                            'faqs' => $pRight,
                            'idSuffix' => 'prod_right',
                        ])
                    </div>
                </div>

            </div>
        </section>
    @endif

    @php
        $pTitle = $product->story_title;
        $pContent = $product->story_content;

        // Fallback: Agar product me khali hai to Category wala utha lo
        if (empty($pTitle) && $product->category) {
            $pTitle = $product->category->story_title;
            $pContent = $product->category->story_content;
        }
    @endphp

    {{-- Include Partial --}}
    @include('frontend.includes.brand_story', [
        'storyTitle' => $pTitle,
        'storyContent' => $pContent,
    ])

@endsection

@section('scripts')
    <script src="https://cdn.razorpay.com/widgets/affordability/affordability.js"></script>
    <script>
        // --- 🟢 RAZORPAY WIDGET CONFIGURATION ---
        const rzpKey = "rzp_live_S0zZ2YEhXKBKxb"; // Aapki Live Key

        // --- 🟢 WIDGET RENDER FUNCTION (FIXED) ---
        function renderRazorpayWidget(currentPrice) {
            const container = document.getElementById('razorpay-affordability-widget');

            // 1. Agar container nahi mila, toh error log karo
            if (!container) {
                console.error("Error: Widget Container nahi mila! HTML check karein.");
                return;
            }

            // 2. Container ko saaf karo
            container.innerHTML = '';
            container.removeAttribute('data-razorpay-rendered');

            // 3. SHOW / HIDE LOGIC
            // Agar price ₹1000 se kam hai, toh widget chhupa do aur return ho jao
            // (Aap is limit ko 1000 ki jagah 0 ya 3000 kar sakte hain)
            if (currentPrice < 1200) {
                container.style.display = 'none'; // ❌ HIDE
                console.log("Price low hai, widget hide kiya gaya.");
                return;
            } else {
                container.style.display = 'block'; // ✅ SHOW
            }

            console.log("Rendering Widget for Price:", currentPrice);

            const widgetConfig = {
                "key": rzpKey, // Ensure 'rzpKey' upar define ho
                "amount": Math.round(currentPrice * 100), // Paise convert
            };

            try {
                const rzpAffordabilitySuite = new RazorpayAffordabilitySuite(widgetConfig);
                rzpAffordabilitySuite.render();
            } catch (e) {
                console.error("Razorpay Widget Error:", e);
            }
        }

        // 2. Page Load Logic
        document.addEventListener('DOMContentLoaded', function() {
            // Start Price uthao
            let startPrice = parseFloat("{{ $product->price }}");

            // Widget chalao
            renderRazorpayWidget(startPrice);

            // Gemstone Logic Initialize
            if (document.getElementById('gem_data')) findGemPrice();

            // 🔥 FIX: Page load hote hi Variant ka Stock check karo
            const variantSelect = document.getElementById('variant_select');
            if (variantSelect) {
                variantSelect.dispatchEvent(new Event('change'));
            }
        });
        // 1. Slider Setup (Fixed)
        $('.product-main-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            fade: true,
            asNavFor: '.product-thumb-slider',
            prevArrow: '<button type="button" class="slick-prev custom-arrow main-prev"><i class="las la-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next custom-arrow main-next"><i class="las la-angle-right"></i></button>'
        });
        $('.product-thumb-slider').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: '.product-main-slider',
            dots: false,
            centerMode: false,
            focusOnSelect: true,
            arrows: false
        });

        // 2. Quantity Logic (Ab ye Updated Max Stock padhega)
        function updateQty(action) {
            const input = document.getElementById('qty_input');
            let currentVal = parseInt(input.value);
            // 🔥 Fix: Hamesha fresh max attribute uthao
            let maxStock = parseInt(input.getAttribute('max'));

            if (isNaN(maxStock)) maxStock = 100; // Fallback

            if (action === 'plus') {
                if (currentVal < maxStock) {
                    input.value = currentVal + 1;
                } else {
                    alert('Sorry, only ' + maxStock + ' items available in this size.');
                }
            } else if (action === 'minus') {
                if (currentVal > 1) input.value = currentVal - 1;
            }
        }

        // 3. Delivery Check (Corrected URL and Logic)
        function checkDelivery() {
            const pincode = document.getElementById('pincodeInput').value;
            const resultBox = document.getElementById('deliveryResult');

            if (pincode.length === 6) {
                resultBox.style.display = 'block';
                resultBox.className = "small text-muted fw-bold mt-2";
                resultBox.innerText = "Checking...";

                // Use jQuery AJAX since you have Slick slider (jQuery is present)
                $.ajax({
                    url: "/check-pincode-delivery/" + pincode,
                    type: "GET",
                    success: function(response) {
                        if (response.status) {
                            resultBox.className = "small text-success fw-bold mt-2";
                            resultBox.innerHTML = "Free Delivery by " + response.date;
                        } else {
                            resultBox.className = "small text-danger fw-bold mt-2";
                            resultBox.innerText = response.message || "Not available";
                        }
                    },
                    error: function() {
                        resultBox.className = "small text-danger fw-bold mt-2";
                        resultBox.innerText = "Unable to fetch delivery date.";
                    }
                });
            } else {
                alert('Please enter valid 6 digit pincode');
                resultBox.style.display = 'none';
            }
        }

        // 4. Gemstone Logic
        function updateGemState(key, value, btn) {
            let group = btn.parentElement;
            group.querySelectorAll('.gem-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            if (key === 'type') document.getElementById('sel_type').value = value;
            if (key === 'ratti') document.getElementById('sel_ratti').value = value;
            if (key === 'material') document.getElementById('sel_mat').value = value;

            if (key === 'type') {
                let mat = document.getElementById('material_section');
                let ring = document.getElementById('ring_size_section');
                if (value === 'loose') {
                    mat.style.display = 'none';
                    ring.style.display = 'none';
                } else if (value === 'ring') {
                    mat.style.display = 'block';
                    ring.style.display = 'block';
                } else if (value === 'pendant') {
                    mat.style.display = 'block';
                    ring.style.display = 'none';
                }
            }
            findGemPrice();
        }

        function findGemPrice() {
            const type = document.getElementById('sel_type').value;
            const ratti = document.getElementById('sel_ratti').value;
            const mat = document.getElementById('sel_mat').value;
            const dataDiv = document.getElementById('gem_data');
            if (!dataDiv) return;
            const variants = JSON.parse(dataDiv.innerText);

            const match = variants.find(v => {
                let isMatch = (v.type === type && v.ratti_size == ratti);
                if (type !== 'loose') isMatch = isMatch && (v.material === mat);
                return isMatch;
            });

            if (match) {
                updatePrices(match.price, match.mrp);
                document.getElementById('selected_variant_id').value = match.id;
            }
        }

        // =========================================================
        // 🔥 5. WEIGHT VARIANT LOGIC (MAIN FIX HERE) 🔥
        // =========================================================
        const variantSelect = document.getElementById('variant_select');
        if (variantSelect) {
            variantSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];

                // Get Data
                const price = opt.getAttribute('data-price');
                const mrp = opt.getAttribute('data-mrp');
                const stock = parseInt(opt.getAttribute('data-stock')); // Stock nikala

                // 🔥 UPDATE QUANTITY LIMITS
                const qtyInput = document.getElementById('qty_input');
                const cartBtn = document.querySelector('.btn-warning'); // Add to cart button

                if (qtyInput) {
                    qtyInput.setAttribute('max', stock); // Max limit set ki
                    qtyInput.value = 1; // Reset value to 1
                }

                // 🔥 HANDLE OUT OF STOCK
                if (stock < 1) {
                    if (qtyInput) qtyInput.value = 0;
                    if (cartBtn) {
                        cartBtn.disabled = true;
                        cartBtn.innerText = "Out of Stock";
                        cartBtn.style.opacity = "0.6";
                    }
                } else {
                    if (cartBtn) {
                        cartBtn.disabled = false;
                        cartBtn.innerText = "ADD TO CART";
                        cartBtn.style.opacity = "1";
                    }
                }

                updatePrices(price, mrp);
            });
        }

        // 6. Common Price Update (FIXED)
        function updatePrices(price, mrp) {
            price = parseFloat(price);
            mrp = parseFloat(mrp);

            // Update Base Price hidden input
            const baseInput = document.getElementById('base_price');
            if (baseInput) baseInput.value = price;

            // Update Selling Price
            document.getElementById('display_price').innerText = price.toLocaleString('en-IN');

            // Handle MRP and Discount Visibility
            const mrpContainer = document.getElementById('mrp_container');
            const discContainer = document.getElementById('discount_container');

            if (mrp > price) {
                // Show MRP and Discount
                if (mrpContainer) {
                    mrpContainer.classList.remove('d-none');
                    document.getElementById('display_mrp').innerText = mrp.toLocaleString('en-IN');
                }
                if (discContainer) {
                    discContainer.classList.remove('d-none');
                    const disc = Math.round(((mrp - price) / mrp) * 100);
                    document.getElementById('display_discount').innerText = disc;
                }
            } else {
                // Hide MRP and Discount if no discount
                if (mrpContainer) mrpContainer.classList.add('d-none');
                if (discContainer) discContainer.classList.add('d-none');
            }

            // // Trigger Siddh Recalc (This handles Siddh + EMI together)
            // const siddhCheck = document.getElementById('siddh_check');
            // if (siddhCheck && siddhCheck.checked) {
            //     // Trigger change event to re-calculate Siddh Price + EMI
            //     siddhCheck.dispatchEvent(new Event('change'));
            // } else {
            //     // 🔥 Direct EMI Update if Siddh is NOT checked
            //     calculateEMI(price);
            // }

            // B. 🔥 RAZORPAY WIDGET UPDATE
            // Nayi price ke saath widget dubara banayein
            renderRazorpayWidget(price);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('gem_data')) findGemPrice();
        });

        // Siddh Checkbox
        const siddhCheck = document.getElementById('siddh_check');
        if (siddhCheck) {
            siddhCheck.addEventListener('change', function() {
                const currentBasePrice = parseFloat(document.getElementById('base_price').value) || 0;
                const siddhPrice = parseFloat("{{ $product->siddh_price ?? 0 }}");
                const displayPrice = document.getElementById('display_price');
                const inputSiddh = document.getElementById('input_is_siddh');

                let finalPrice = currentBasePrice;

                if (this.checked) {
                    finalPrice = currentBasePrice + siddhPrice;
                    inputSiddh.value = 1;
                } else {
                    inputSiddh.value = 0;
                }

                // Update Display
                displayPrice.innerText = finalPrice.toLocaleString('en-IN');

                // Update Razorpay
                renderRazorpayWidget(finalPrice);
            });
        }

        // 🕒 1. FAKE DAILY TIMER (Midnight Countdown)
        const timerDisplay = document.getElementById('countdown');

        if (timerDisplay) {
            function startDailyTimer() {
                // Abhi ka time lo
                const now = new Date();

                // Aaj raat 12 baje ka time set karo (End of Day)
                const midnight = new Date();
                midnight.setHours(24, 0, 0, 0);

                // Time difference nikalo
                let diff = midnight - now;

                // Agar calculation me koi gadbad ho to 12 ghante jod do (Safe side)
                if (diff < 0) {
                    diff = diff + (24 * 60 * 60 * 1000);
                }

                // Hours, Minutes, Seconds calculate karo
                const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
                const m = Math.floor((diff / (1000 * 60)) % 60);
                const s = Math.floor((diff / 1000) % 60);

                // Double digits me dikhao (e.g., 05 instead of 5)
                const hh = (h < 10) ? "0" + h : h;
                const mm = (m < 10) ? "0" + m : m;
                const ss = (s < 10) ? "0" + s : s;

                timerDisplay.innerHTML = `${hh} hr : ${mm} min : ${ss} sec`;
            }

            // Har second update karo
            setInterval(startDailyTimer, 1000);
            startDailyTimer(); // Page load hote hi run karo
        }

        // --------------------------------------------------------
        // 🏦 DYNAMIC EMI CALCULATOR (No Cost EMI Logic)
        // --------------------------------------------------------
        document.addEventListener('DOMContentLoaded', function() {
            calculateEMI(); // Page load hote hi calculate karein
        });

        // function calculateEMI() {
        //     // 1. Current Price uthao
        //     let priceElement = document.getElementById('display_price');
        //     if (!priceElement) return;

        //     // Comma hata kar number banao (e.g. "1,499" -> 1499)
        //     let currentPrice = parseFloat(priceElement.innerText.replace(/,/g, ''));

        //     // 2. Minimum Amount Check (Razorpay EMI usually starts from ₹3000 or ₹5000, check your setting)
        //     // Agar product sasta hai (e.g. ₹500), to EMI box hide kar do
        //     let emiContainer = document.querySelector('.emi-box');

        //     if (currentPrice < 1100) {
        //         // Agar price 3000 se kam hai to EMI box chupao (Optional logic)
        //         if (emiContainer) emiContainer.style.display = 'none';
        //         return;
        //     } else {
        //         if (emiContainer) emiContainer.style.display = 'flex';
        //     }

        //     // 3. Calculation (Price / 3 Months)
        //     // Math.ceil() use kiya taaki points na aayen (e.g. 499.33 -> 500)
        //     let emi3Months = Math.ceil(currentPrice / 3);

        //     // 4. Update HTML
        //     let emiText = document.getElementById('emi_amount'); // Apne HTML me ye ID add karna mat bhulna inside emi-box
        //     if (emiText) {
        //         emiText.innerText = emi3Months.toLocaleString('en-IN');
        //     } else {
        //         // Fallback: Agar ID nahi mili to console me batao
        //         console.warn('Element with id "emi_amount" not found inside EMI box.');
        //     }
        // }

        // 🔄 Jab bhi Price update ho (Variant/Siddh change), EMI bhi update karo
        // Hum purane 'updatePrices' function ko "Hook" kar rahe hain
        const originalUpdatePrices = window.updatePrices;

        window.updatePrices = function(price, mrp) {
            // Pehle original function chalne do
            if (typeof originalUpdatePrices === 'function') {
                originalUpdatePrices(price, mrp);
            }

            // Phir EMI calculate karo
            setTimeout(calculateEMI, 100);
        };

        // 1. Initialize Lightbox (Click to Enlarge)
        const lightbox = GLightbox({
            selector: '.glightbox', // Class name to target
            touchNavigation: true, // Mobile swipe support
            loop: true, // Infinite loop
            zoomable: true, // ✅ ZOOM ENABLED (Icon aayega top-right me)
            draggable: true, // ✅ DRAG ENABLED (Zoom hone par image move kar sakenge)
            dragAutoSnap: true, // Image wapas center me aayegi agar jyada drag kiya
            openEffect: 'zoom', // Opening animation
            closeEffect: 'zoom', // Closing animation
            slideEffect: 'slide' // Slide animation
        });

        // 2. Flipkart Style Hover Zoom Logic
        // Hum sabhi containers par loop lagayenge (kyunki slider me multiple images hain)
        const zoomContainers = document.querySelectorAll('.zoom-container');

        zoomContainers.forEach(container => {
            const img = container.querySelector('.zoom-img');

            if (img) {
                // Mouse Enter/Move
                container.addEventListener("mousemove", function(e) {
                    const rect = container.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;

                    const xPercent = (x / rect.width) * 100;
                    const yPercent = (y / rect.height) * 100;

                    img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
                    img.style.transform = "scale(2)"; // 2x Zoom on hover
                });

                // Mouse Leave
                container.addEventListener("mouseleave", function() {
                    img.style.transformOrigin = "center center";
                    img.style.transform = "scale(1)";
                });
            }
        });

        // 3. Fix for Slick Slider (Re-init zoom if slick changes DOM)
        // Agar slick slider swipe hone ke baad zoom band ho jaye, to ye zaroori hai
        $('.product-main-slider').on('afterChange', function(event, slick, currentSlide) {
            // Re-attach listeners is difficult, but standard CSS hover works best here.
            // Hamara upar wala JS logic static elements par hai, Slick clone karta hai.
            // Isliye behtar hai hum 'event delegation' use karein:
        });

        // 🔥 BETTER WAY FOR SLICK SLIDER (Event Delegation)
        // Ye code upar wale `forEach` ko replace karega taaki Slider ke cloned elements par bhi chale
        $(document).on('mousemove', '.zoom-container', function(e) {
            const container = this;
            const img = container.querySelector('.zoom-img');
            if (!img) return;

            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const xPercent = (x / rect.width) * 100;
            const yPercent = (y / rect.height) * 100;

            img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
            img.style.transform = "scale(2)";
        });

        $(document).on('mouseleave', '.zoom-container', function(e) {
            const img = this.querySelector('.zoom-img');
            if (img) {
                img.style.transformOrigin = "center center";
                img.style.transform = "scale(1)";
            }
        });
    </script>
@endsection
