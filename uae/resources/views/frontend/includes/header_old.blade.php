<header class="sticky-top z-1020 shadow-sm" style="background-color: var(--light) !important;">

    {{-- ⭐️ Top Bar - Language, Currency, Seller Links --}}
    {{-- Top bar background color changed to match the light tone in the example --}}
    <div class="top-navbar d-none d-lg-block border-bottom" style="background-color: var(--light) !important;">
        <div class="container-fluid px-3">
            <div class="d-flex justify-content-between align-items-center py-1">

                <div class="d-flex align-items-center">
                    {{-- Language switcher --}}
                    <div class="dropdown me-3" id="lang-change">
                        {{-- Text color is dark, not light --}}
                        <a href="javascript:void(0)" class="dropdown-toggle text-dark small" data-bs-toggle="dropdown"
                            data-bs-display="static">
                            <span style="color: var(--dark);">English</span>
                        </a>
                        {{-- ... (Dropdown Content) ... --}}
                    </div>

                    {{-- Currency Switcher --}}
                    <div class="dropdown" id="currency-change">
                        <a href="javascript:void(0)" class="dropdown-toggle text-dark small" data-bs-toggle="dropdown"
                            data-bs-display="static">
                            <span style="color: var(--dark);">Currency</span>
                        </a>
                        {{-- ... (Dropdown Content) ... --}}
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <a href="{{ url('shops/create') }}" class="text-dark small pe-3 border-end">Become a seller!</a>
                    <a href="{{ url('seller/login') }}" class="text-dark small ps-3">Login to Seller</a>
                </div>
            </div>
        </div>
    </div>

    {{-- 🏠 Logo and Main Nav Bar --}}
    {{-- Golden Border is correctly placed at the bottom of the whole header, handled by CSS --}}
    <nav class="navbar navbar-expand-lg py-0">
        <div class="container-fluid px-3">

            {{-- Logo --}}
            <a class="navbar-brand py-2 me-lg-5" href="{{ url('/') }}">
                <img src="https://suyagya.com/public/uploads/all/ackLS169wFEfhj8jfhnb0SGblGIHug1XfDCg7WIs.webp"
                    alt="Suyagya" height="50">
            </a>

            {{-- Collapse/Main Menu Links --}}
            <div class="collapse navbar-collapse justify-content-start" id="mainMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 main-nav-list align-items-center">

                    <li class="nav-item dropdown mega-parent me-3">
                        <a class="nav-link text-dark dropdown-toggle" href="#" id="productsDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Products
                        </a>
                        {{-- 🔴 Empty Dropdown: As requested, we will leave the content empty here --}}
                        <div class="dropdown-menu mega-menu border-0 shadow w-100" aria-labelledby="productsDropdown">
                            <div class="mega-bg container-fluid px-5 py-3"
                                style="background-color: var(--light) !important;">
                                <div class="row text-center mega-grid">

                                    @foreach ($headerCategories as $category)
                                        <div class="col-3 mb-2">
                                            <a href="{{ route('products.category', $category->slug) }}"
                                                class="d-flex flex-column align-items-center text-decoration-none">

                                                <div class="mega-icon">
                                                    <img src="{{ asset($category->icon_image) }}"
                                                        alt="{{ $category->name }}" class="rounded-circle"
                                                        onerror="this.src='{{ asset('assets/img/placeholder.jpg') }}'">
                                                </div>

                                                <span class="small fw-bold text-dark mt-2">
                                                    {{ $category->name }}
                                                </span>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- 🟢 DYNAMIC CATEGORIES LOOP (JAPAM STYLE) --}}
                    @foreach ($headerCategories as $category)

                        @if ($category->subCategories->count() > 0)
                            <li class="nav-item dropdown hover-dropdown me-3">
                                <a class="nav-link text-dark dropdown-toggle fw-bold"
                                    href="{{ route('products.category', $category->slug) }}"
                                    id="catDrop{{ $category->id }}" role="button" aria-expanded="false">
                                    {{ $category->name }}
                                </a>

                                {{-- ✨ JAPAM STYLE MEGA MENU ✨ --}}
                                <div class="dropdown-menu japam-mega-menu shadow-lg border-0"
                                    aria-labelledby="catDrop{{ $category->id }}">
                                    <div class="row g-0">

                                        {{-- LEFT COL: Sub Categories List --}}
                                        <div class="col-4 col-lg-3">
                                            <div class="japam-sc-list">
                                                @foreach ($category->subCategories as $sub)
                                                    <a href="{{ route('products.subcategory', [$category->slug, $sub->slug]) }}"
                                                        class="japam-sc-item">
                                                        {{ $sub->name }}
                                                        <i class="las la-angle-right"></i>
                                                    </a>
                                                @endforeach

                                                <a href="{{ route('products.category', $category->slug) }}"
                                                    class="japam-sc-item text-primary fw-bold">
                                                    View All {{ $category->name }}
                                                    <i class="las la-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>

                                        {{-- RIGHT COL: Product Images / Featured Items --}}
                                        <div class="col-8 col-lg-9">
                                            <div class="japam-prod-grid h-100">
                                                <div class="row g-3">
                                                    @if ($category->products->count() > 0)
                                                        @foreach ($category->products as $product)
                                                            <div class="col-3">
                                                                <a href="#" class="japam-prod-card">
                                                                    <img src="{{ asset($product->main_image) }}"
                                                                        class="japam-prod-img"
                                                                        alt="{{ $product->name }}">
                                                                    <span
                                                                        class="japam-prod-title">{{ $product->name }}</span>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        {{-- Fallback if no products --}}
                                                        <div class="col-12 text-center py-5 text-muted">
                                                            <i class="las la-box-open fs-1 mb-2"></i>
                                                            <p>Explore our {{ $category->name }} collection</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </li>
                        @else
                            <li class="nav-item me-3">
                                <a href="{{ route('products.category', $category->slug) }}"
                                    class="nav-link text-dark fw-bold">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endif

                    @endforeach

                </ul>
            </div>

            {{-- 3. ICON ACTION BLOCK (Astrotalk Style) --}}
            <div class="d-flex align-items-center nav-action-icons ms-auto">

                {{-- 🔥 1. Search Icon --}}
                <div class="nav-search-icon ms-2">
                    <a href="javascript:void(0);" onclick="toggleSearch()" title="Search">
                        <i class="las la-search"></i>
                    </a>
                </div>

                {{-- 👤 2. Account Icon --}}
                <div class="nav-user-auth ms-4">
                    @auth
                        {{-- ✅ LOGGED IN USER (Dropdown) --}}
                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-dark text-decoration-none"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="las la-user-circle fs-2"></i>
                                {{-- Optional: Show First Name --}}
                                {{-- <span class="ms-2 small fw-bold d-none d-md-block">{{ Auth::user()->name }}</span> --}}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-3"
                                style="min-width: 200px;">
                                <li class="px-3 py-2 border-bottom">
                                    <span class="small text-muted d-block">Welcome,</span>
                                    <span class="fw-bold text-dark">{{ Auth::user()->name ?? 'User' }}</span>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ url('/orders') }}">
                                        <i class="las la-box me-2"></i> Order History
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ url('/profile') }}">
                                        <i class="las la-user-cog me-2"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="las la-sign-out-alt me-2"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        {{-- ❌ GUEST USER (Login Modal Trigger) --}}
                        <a href="javascript:void(0);" onclick="showLoginModal()" title="Login / Signup">
                            <i class="las la-user-circle fs-2"></i>
                        </a>
                    @endauth
                </div>

                {{-- ⭐ 3. Wishlist/Favorite Icon --}}
                <div class="nav-wishlist-icon ms-4">
                    <a href="{{ url('/wishlists') }}" title="Wishlist" class="position-relative">
                        <i class="las la-heart"></i>
                    </a>
                </div>

                {{-- 🛒 4. Cart Icon (Side Cart Trigger) --}}
                <div class="nav-cart-box ms-4 position-relative">
                    <a href="javascript:void(0);" onclick="openSideCart()" title="Cart" class="text-dark">
                        <i class="las la-shopping-bag" style="font-size: 28px;"></i>

                        {{-- Badge --}}
                        <span id="cart-badge"
                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size: 10px; {{ (isset($cartGlobalCount) && $cartGlobalCount > 0) ? '' : 'display: none;' }}">
                            {{ $cartGlobalCount ?? 0 }}
                        </span>
                    </a>
                </div>

            </div>

            {{-- 👇 Search Bar Section --}}
            {{-- 👇 LIVE SEARCH BAR SECTION --}}
            <div class="header-search-bar bg-white border-bottom shadow-lg"
                style="display: none; position: absolute; top: 100%; left: 0; width: 100%; z-index: 1100; max-height: 80vh; overflow-y: auto;">

                <div class="container py-3">
                    {{-- Input Field (No Button) --}}
                    <div class="position-relative">
                        <i
                            class="las la-search position-absolute top-50 start-0 translate-middle-y ms-3 fs-4 text-muted"></i>
                        <input type="text"
                            class="form-control border-0 bg-light py-3 ps-5 rounded-pill fs-6 fw-bold"
                            id="live-search-input" placeholder="Search for products, categories..."
                            autocomplete="off">
                        {{-- Close Icon --}}
                        <i class="las la-times position-absolute top-50 end-0 translate-middle-y me-3 fs-4 cursor-pointer"
                            onclick="toggleSearch()"></i>
                    </div>
                </div>

                {{-- 🟢 AJAX RESULTS CONTAINER --}}
                <div id="search-results-box" class="bg-white">
                    {{-- Results will load here via JS --}}
                </div>

            </div>

            <div class="modal fade" id="login_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered login-modal-dialog">
                    <div class="modal-content login-modal-content">
                        <div class="row g-0">

                            {{-- LEFT SIDE: BRANDING --}}
                            <div class="col-md-5 login-left-panel d-none d-md-flex">
                                <div class="login-logo">
                                    <img src="https://suyagya.com/public/uploads/all/ackLS169wFEfhj8jfhnb0SGblGIHug1XfDCg7WIs.webp"
                                        alt="Logo">
                                </div>
                                <h4 class="login-offer-text">Login Now & avail best offers!</h4>

                                <div class="feature-box">
                                    <div class="feature-icon"><i class="las la-star"></i></div>
                                    <div class="feature-title">100% Authentic Products</div>
                                    <div class="feature-desc">Lab certified & verified items</div>
                                </div>

                                <div class="feature-box">
                                    <div class="feature-icon"><i class="las la-gift"></i></div>
                                    <div class="feature-title">Exclusive Discounts</div>
                                    <div class="feature-desc">Best prices for registered users</div>
                                </div>
                            </div>

                            {{-- RIGHT SIDE: FORM --}}
                            <div class="col-md-7 login-right-panel">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                                    data-bs-dismiss="modal" aria-label="Close"></button>

                                {{-- STEP 1: PHONE NUMBER INPUT --}}
                                <div id="step-phone-container">
                                    <h3 class="login-title">Get Started</h3>
                                    <p class="text-center text-muted small mb-4">Enter your mobile number to login/sign
                                        up</p>

                                    <div class="mb-4">
                                        <label class="fw-bold small mb-2">Mobile Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">+91</span>
                                            <input type="number" id="phone_input"
                                                class="form-control border-start-0 ps-0" placeholder="Enter Number">
                                        </div>
                                        <small id="phone_error" class="text-danger"></small>
                                    </div>

                                    <button onclick="sendOtp()" id="btn-get-otp" class="btn-login-action">GET
                                        OTP</button>
                                </div>

                                {{-- STEP 2: OTP VERIFICATION (Initially Hidden) --}}
                                <div id="step-otp-container" style="display: none;">
                                    <h3 class="login-title">OTP Verification</h3>
                                    <p class="text-center text-muted small mb-3">
                                        OTP sent to <span id="display_phone" class="fw-bold text-dark"></span>
                                        <a href="#" onclick="editPhone()" class="edit-number-btn">Edit</a>
                                    </p>

                                    {{-- 4 Digit Boxes --}}
                                    <div class="otp-boxes">
                                        <input type="text" class="otp-input" maxlength="1"
                                            oninput="moveToNext(this, 'otp2')" id="otp1">
                                        <input type="text" class="otp-input" maxlength="1"
                                            oninput="moveToNext(this, 'otp3')" id="otp2">
                                        <input type="text" class="otp-input" maxlength="1"
                                            oninput="moveToNext(this, 'otp4')" id="otp3">
                                        <input type="text" class="otp-input" maxlength="1"
                                            oninput="moveToNext(this, 'submitOtp')" id="otp4">
                                    </div>
                                    <small id="otp_error" class="text-danger text-center d-block mb-2"></small>

                                    <div class="text-center mb-3">
                                        <small class="text-muted"><i class="las la-clock"></i> Resend OTP in <span
                                                id="timer">30</span> Sec</small>
                                    </div>

                                    <button onclick="verifyOtp()" id="btn-verify"
                                        class="btn-login-action">LOGIN</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Toggle Button (Hidden on Desktop) --}}
            <button class="navbar-toggler p-0 d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
</header>
