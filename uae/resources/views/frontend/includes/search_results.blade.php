<div class="container-fluid px-0">

    {{-- ============================================================== --}}
    {{-- 🖥️ DESKTOP VIEW (Visible ONLY on Large Screens > 992px) --}}
    {{-- ============================================================== --}}
    <div class="d-none d-lg-block">
        <div class="row g-0">
            {{-- LEFT COLUMN: TABS & CONTENT --}}
            <div class="col-lg-8 border-end position-relative">

                {{-- 1. TABS HEADER --}}
                <div class="d-flex border-bottom px-4 pt-3 pb-0 mb-3" id="search-tabs">
                    <div class="pb-2 border-bottom border-dark border-2 fw-bold text-dark me-4 cursor-pointer tab-btn active"
                        data-target="#tab-products">
                        Products ({{ $products->count() }})
                    </div>
                    <div class="pb-2 text-muted me-4 cursor-pointer tab-btn" data-target="#tab-collections">
                        Collections ({{ $collections->count() }})
                    </div>
                    {{-- <div class="pb-2 text-muted cursor-pointer tab-btn" data-target="#tab-pages">
                        Pages ({{ $pages->count() }})
                    </div> --}}
                </div>

                {{-- 2. TAB CONTENT AREA --}}
                <div class="px-4 pb-4" style="min-height: 250px;">
                    <div id="tab-products" class="search-tab-content">
                        @if ($products->count() > 0)
                            <div class="row g-3">
                                @foreach ($products as $product)
                                    <div class="col-12">
                                        <a href="{{ route('product.detail', $product->slug) }}"
                                            class="d-flex align-items-center text-decoration-none text-dark search-item-card p-2 rounded hover-bg-light">
                                            <div class="flex-shrink-0 me-3" style="width: 60px; height: 60px;">
                                                <img src="{{ asset($product->main_image) }}"
                                                    class="w-100 h-100 object-fit-cover rounded border">
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark small">{{ $product->name }}</h6>
                                                <div class="small">
                                                    <span class="fw-bold">₹{{ number_format($product->price) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mt-3">No products found.</p>
                        @endif
                    </div>

                    {{-- Collections Tab Content (Desktop) --}}
                    <div id="tab-collections" class="search-tab-content" style="display: none;">
                        @if ($collections->count() > 0)
                            <div class="row g-3">
                                @foreach ($collections as $item)
                                    <div class="col-12">
                                        <a href="{{ $item->url }}"
                                            class="d-flex align-items-center text-decoration-none text-dark search-item-card p-2 rounded hover-bg-light">
                                            <div class="flex-shrink-0 me-3"
                                                style="width: 50px; height: 50px; background: #f8f8f8; display: flex; align-items: center; justify-content: center; border-radius: 5px; border: 1px solid #eee;">
                                                @if ($item->image)
                                                    <img src="{{ asset($item->image) }}"
                                                        class="w-100 h-100 object-fit-cover rounded">
                                                @else
                                                    <i class="las la-tags fs-3 text-muted"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark small">{{ $item->name }}</h6>
                                                <span class="text-muted x-small text-uppercase"
                                                    style="font-size: 10px;">{{ $item->type }}</span>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mt-3">No collections found.</p>
                        @endif
                    </div>

                </div>

                <div class="px-4 pb-3">
                    <a href="{{ route('products.search_listing') }}?q={{ $query }}"
                        class="btn btn-outline-dark w-100 rounded-0 btn-sm">
                        View all search results <i class="las la-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            {{-- Right Column (Suggestions) --}}
            <div class="col-lg-4 bg-light">
                <div class="p-4 h-100">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 small">Suggestions</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach ($suggestions as $sug)
                            <li class="mb-2">
                                <a href="{{ route('products.search_listing') }}?q={{ $sug }}"
                                    class="text-decoration-none text-muted d-block py-1 hover-text-dark small">
                                    {!! preg_replace('/(' . $query . ')/i', '<strong class="text-dark">$1</strong>', $sug) !!}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- 📱 MOBILE VIEW (Visible ONLY on Small Screens < 992px) --}}
    {{-- ============================================================== --}}
    {{-- 🔥 Force Display Block on Mobile using custom style if Bootstrap fails --}}
    <div class="d-lg-none" style="display: block;">

        {{-- 1. SUGGESTIONS LIST (Top) --}}
        @if ($suggestions->count() > 0)
            <div class="bg-light border-bottom">
                <div class="p-3">
                    <h6 class="fw-bold text-dark small mb-2 text-uppercase"
                        style="font-size: 11px; letter-spacing: 1px; color: #888;">Suggestions</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach ($suggestions as $sug)
                            <li>
                                <a href="{{ route('products.search_listing') }}?q={{ $sug }}"
                                    class="text-decoration-none text-dark d-block py-2" style="font-size: 14px;">
                                    <i class="las la-search me-2 text-muted"></i>
                                    {!! preg_replace('/(' . $query . ')/i', '<strong>$1</strong>', $sug) !!}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- 2. PRODUCTS LIST HEADER --}}
        @if ($products->count() > 0)
            <div class="px-3 pt-3 pb-2 bg-white border-bottom">
                <h6 class="fw-bold text-dark m-0" style="font-size: 13px; text-transform: uppercase;">Products</h6>
            </div>

            {{-- 3. PRODUCTS LIST ITEMS --}}
            <div class="bg-white">
                @foreach ($products as $product)
                    <div class="border-bottom px-3 py-2">
                        <a href="{{ route('product.detail', $product->slug) }}"
                            class="d-flex align-items-center text-decoration-none text-dark py-2">
                            {{-- Image --}}
                            <div class="flex-shrink-0 me-3" style="width: 50px; height: 50px;">
                                <img src="{{ asset($product->main_image) }}"
                                    class="w-100 h-100 object-fit-cover rounded border" alt="{{ $product->name }}">
                            </div>
                            {{-- Content --}}
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark text-truncate"
                                    style="font-size: 14px; max-width: 200px;">{{ $product->name }}</h6>
                                <div class="small">
                                    <span class="fw-bold text-dark">₹{{ number_format($product->price) }}</span>
                                    @if ($product->mrp_price > $product->price)
                                        <span class="text-decoration-line-through text-muted ms-2"
                                            style="font-size: 11px;">₹{{ number_format($product->mrp_price) }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- Arrow Icon --}}
                            <i class="las la-angle-right text-muted"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($collections->count() > 0)
            <div class="px-3 pt-3 pb-2 bg-white border-bottom">
                <h6 class="fw-bold text-dark m-0" style="font-size: 13px; text-transform: uppercase;">Collections</h6>
            </div>
            <div class="row g-3">
                @foreach ($collections as $item)
                    <div class="col-12">
                        <a href="{{ $item->url }}"
                            class="d-flex align-items-center text-decoration-none text-dark search-item-card p-2 rounded hover-bg-light">
                            <div class="flex-shrink-0 me-3"
                                style="width: 50px; height: 50px; background: #f8f8f8; display: flex; align-items: center; justify-content: center; border-radius: 5px; border: 1px solid #eee;">
                                @if ($item->image)
                                    <img src="{{ asset($item->image) }}" class="w-100 h-100 object-fit-cover rounded">
                                @else
                                    <i class="las la-tags fs-3 text-muted"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark small">{{ $item->name }}</h6>
                                <span class="text-muted x-small text-uppercase"
                                    style="font-size: 10px;">{{ $item->type }}</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small mt-3">No collections found.</p>
        @endif

        {{-- 4. VIEW ALL BUTTON --}}
        <a href="{{ route('products.search_listing') }}?q={{ $query }}"
            class="d-block text-center py-3 bg-light text-primary text-decoration-none fw-bold small border-top">
            View all results for "{{ $query }}" <i class="las la-arrow-right ms-1"></i>
        </a>

    </div>

</div>

<script>
    // Tab Logic (Only runs on Desktop view since mobile has no tabs)
    $('.tab-btn').on('click', function() {
        if ($(window).width() >= 992) {
            $('.tab-btn').removeClass('border-bottom border-dark border-2 fw-bold text-dark active').addClass(
                'text-muted');
            $(this).removeClass('text-muted').addClass(
                'border-bottom border-dark border-2 fw-bold text-dark active');
            $('.search-tab-content').hide();
            $($(this).data('target')).show();
        }
    });
</script>

<script>
    // Simple Tab Switcher Logic inside the loaded view
    $('.tab-btn').on('click', function() {
        // Remove active class from all tabs
        $('.tab-btn').removeClass('border-bottom border-dark border-2 fw-bold text-dark active').addClass(
            'text-muted');

        // Add active class to clicked tab
        $(this).removeClass('text-muted').addClass(
            'border-bottom border-dark border-2 fw-bold text-dark active');

        // Hide all contents
        $('.search-tab-content').hide();

        // Show target content
        $($(this).data('target')).show();
    });
</script>
