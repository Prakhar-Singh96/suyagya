<style>
    /* 🔥 Custom Scrollbar for Horizontal List */
    .horizontal-scroll-wrapper {
        display: flex;
        overflow-x: auto;
        gap: 15px;
        padding-bottom: 10px;
        scroll-behavior: smooth;
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .horizontal-scroll-wrapper::-webkit-scrollbar {
        display: none;
        /* Hide scrollbar for Chrome/Safari */
    }

    .recommendation-card {
        min-width: 260px;
        /* Card ki choudai */
        max-width: 260px;
        background: #f9f9f9;
        border-radius: 4px;
    }

    .rec-nav-btn {
        cursor: pointer;
        font-size: 18px;
        color: #666;
        user-select: none;
    }

    .rec-nav-btn:hover {
        color: #000;
    }
</style>

@if (isset($cartItems) && $cartItems->count() > 0)
    <div class="list-group list-group-flush">
        @foreach ($cartItems as $item)
            @php
                // 🚀 सुधार 1: मास्टर प्राइस लॉजिक (Variant Price को प्राथमिकता दें)
                // अगर वैरिएंट है तो उसकी कीमत लें, वरना प्रोडक्ट की बेस प्राइस
                $basePrice = $item->variant ? $item->variant->selling_price : $item->product->price;

                // सिद्धार्थ चार्ज जोड़ें (अगर लागू हो)
                $price = $basePrice + ($item->is_siddh ? $item->product->siddh_price : 0);
            @endphp
            <div class="list-group-item p-3 border-bottom-0 border-top">
                <div class="d-flex align-items-center">
                    {{-- Image --}}
                    <img src="{{ asset($item->product->main_image) }}" class="rounded border me-3" width="70"
                        height="70" style="object-fit: cover;">

                    {{-- Info --}}
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1 text-truncate small text-dark" style="max-width: 180px;">
                            {{ $item->product->name }}</h6>

                        @if ($item->is_siddh)
                            <span class="badge bg-warning text-dark x-small mb-1" style="font-size: 10px;">Siddh /
                                Energized</span>
                        @endif
                        {{-- 🚀 वजन और रिंग साइज यहाँ दिखाएं --}}
                        @if($item->variant)
                            <small class="text-muted d-block" style="font-size: 11px;">
                                <i class="las la-weight"></i> Weight: <b>{{ $item->variant->weight }}g</b>
                            </small>
                        @endif
                        @if($item->ring_size)
                            <small class="text-muted d-block" style="font-size: 11px;">
                                <i class="las la-ring"></i> Size: <b>{{ $item->ring_size }}</b>
                            </small>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold text-dark small">₹{{ number_format($price) }}</span>

                            {{-- Qty --}}
                            <div class="input-group input-group-sm" style="width: 80px;">
                                <button class="btn btn-outline-secondary px-1"
                                    onclick="updateSideCartQty({{ $item->id }}, 'minus')">-</button>
                                <input type="text" class="form-control text-center px-0 bg-white border-secondary"
                                    value="{{ $item->quantity }}" readonly style="font-size: 12px;">
                                <button class="btn btn-outline-secondary px-1"
                                    onclick="updateSideCartQty({{ $item->id }}, 'plus')">+</button>
                            </div>
                        </div>
                    </div>

                    {{-- Delete --}}
                    <button class="btn btn-link text-danger ms-1 p-0" onclick="removeFromSideCart({{ $item->id }})">
                        <i class="las la-trash-alt"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5 mt-5">
        <div class="mb-3">
            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" width="80" class="opacity-50">
        </div>
        <h6 class="text-muted fw-bold">Your Cart is Empty</h6>
        <p class="text-muted x-small mb-4">Looks like you haven't added anything yet.</p>
        <button class="btn btn-warning btn-sm fw-bold text-white" data-bs-dismiss="offcanvas"
            style="background-color: #ff6f00; border:none;">
            Start Shopping
        </button>
    </div>
@endif
{{-- =============================================== --}}
{{-- 🔥 SECTION 1: SUYAGYA BESTSELLERS (Dynamic) --}}
{{-- =============================================== --}}
@if (isset($bestSellers) && $bestSellers->count() > 0)
    <div class="border-top pt-3 pb-3 px-3 mt-2 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark m-0"
                style="font-family: 'Merriweather', 'serif'; letter-spacing: 0.5px; border-bottom: 2px solid #333; padding-bottom: 2px;">
                Suyagya Bestsellers
            </h6>
            <div class="text-muted">
                <i class="las la-angle-left rec-nav-btn" onclick="scrollSection('bestseller-scroll', 'left')"></i>
                <i class="las la-angle-right rec-nav-btn" onclick="scrollSection('bestseller-scroll', 'right')"></i>
            </div>
        </div>

        <div class="horizontal-scroll-wrapper" id="bestseller-scroll">
            @foreach ($bestSellers as $prod)
                <div class="recommendation-card p-2 border">
                    <div class="d-flex align-items-center">
                        <a href="{{ url('product/' . $prod->slug) }}" class="flex-shrink-0">
                            <img src="{{ asset($prod->main_image) }}" class="rounded" width="70" height="70"
                                style="object-fit: cover;">
                        </a>
                        <div class="ms-2 w-100">
                            <a href="{{ url('product/' . $prod->slug) }}" class="text-decoration-none text-dark">
                                <p class="mb-1 small fw-bold text-truncate" style="max-width: 150px;">
                                    {{ $prod->name }}</p>
                            </a>
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold small">₹{{ number_format($prod->price) }}</span>
                                @if ($prod->mrp_price > $prod->price)
                                    <small class="text-muted text-decoration-line-through ms-2"
                                        style="font-size: 10px;">₹{{ $prod->mrp_price }}</small>
                                @endif
                            </div>
                            <button class="btn btn-earthy btn-sm w-100 py-0" style="font-size: 11px; height: 26px;"
                                onclick="addToCart({{ $prod->id }}, 1, 0, this)">
                                Add to cart
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- =============================================== --}}
{{-- 🔥 SECTION 2: YOU MAY ALSO LIKE (Dynamic) --}}
{{-- =============================================== --}}
@if (isset($youMayLike) && $youMayLike->count() > 0)
    <div class="border-top pt-3 pb-3 px-3 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark m-0"
                style="font-family: 'Merriweather', 'serif'; letter-spacing: 0.5px; border-bottom: 2px solid #333; padding-bottom: 2px;">
                You may also like...
            </h6>
            <div class="text-muted">
                <i class="las la-angle-left rec-nav-btn" onclick="scrollSection('youmaylike-scroll', 'left')"></i>
                <i class="las la-angle-right rec-nav-btn" onclick="scrollSection('youmaylike-scroll', 'right')"></i>
            </div>
        </div>

        <div class="horizontal-scroll-wrapper" id="youmaylike-scroll">
            @foreach ($youMayLike as $prod)
                <div class="recommendation-card p-2 border bg-white">
                    <div class="d-flex align-items-center">
                        <a href="{{ url('product/' . $prod->slug) }}" class="flex-shrink-0">
                            <img src="{{ asset($prod->main_image) }}" class="rounded" width="70" height="70"
                                style="object-fit: cover;">
                        </a>
                        <div class="ms-2 w-100">
                            <a href="{{ url('product/' . $prod->slug) }}" class="text-decoration-none text-dark">
                                <p class="mb-1 small fw-bold text-truncate" style="max-width: 150px;">
                                    {{ $prod->name }}</p>
                            </a>
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold small">₹{{ number_format($prod->price) }}</span>
                                @if ($prod->mrp_price > $prod->price)
                                    <small class="text-muted text-decoration-line-through ms-2"
                                        style="font-size: 10px;">₹{{ $prod->mrp_price }}</small>
                                @endif
                            </div>
                            <button class="btn btn-earthy btn-sm w-100 py-0" style="font-size: 11px; height: 26px;"
                                onclick="addToCart({{ $prod->id }}, 1, 0, this)">
                                Add to cart
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<script>
    // 1. Horizontal Scroll Logic (Arrows ke liye)
    function scrollSection(id, direction) {
        const container = document.getElementById(id);
        const scrollAmount = 270; // Card width + gap
        if (direction === 'left') {
            container.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        } else {
            container.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        }
    }
</script>
