{{-- ============================================================== --}}
{{-- 🎨 INTERNAL CSS FOR SEARCH BAR (Mobile vs Desktop) --}}
{{-- ============================================================== --}}
<style>
    /* 📱 MOBILE STYLE (Max-width 991px) */
    @media (max-width: 991px) {
        .header-search-bar {
            display: none;
            position: fixed !important;
            /* Header ki height ke barabar niche (approx 60px-70px) */
            top: 65px !important;
            left: 0;
            width: 100%;
            /* ✅ Auto Height: Taaki pura page na dhake */
            height: auto !important;
            max-height: 80vh;
            /* Screen ka 80% hi use kare */
            z-index: 990;
            /* Header (z-1020) ke niche, content ke upar */
            background-color: #fff;
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #f1f1f1;
        }
    }

    /* 💻 DESKTOP STYLE (Min-width 992px) */
    @media (min-width: 992px) {
        .header-search-bar {
            display: none;
            position: fixed !important;
            /* Desktop Header Height Adjustment */
            top: 106px !important;
            left: 0;
            width: 100%;
            height: auto !important;
            max-height: 70vh;
            z-index: 1010;
            border-top: 1px solid #eee;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            overflow-y: auto;
        }

    }

    /* ========================================= */
    /* 💻 LAPTOP & SMALL DESKTOP FIXES (992px - 1400px) */
    /* ========================================= */
    @media (min-width: 992px) and (max-width: 1400px) {

        /* 1. Container ki padding kam karein taki jagah mile */
        header .container-fluid {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        /* 2. Menu Items ke beech ka gap kam karein */
        #mainMenu .navbar-nav {
            gap: 15px !important;
            /* Kam space */
        }

        /* 3. Font Size Chhota karein taki fit aaye */
        #mainMenu .nav-link {
            font-size: 11px !important;
            /* Thoda chhota font */
            padding-left: 0 !important;
            padding-right: 0 !important;
            letter-spacing: 0.3px !important;
        }

        /* 4. Icons ka size adjust karein */
        .nav-action-icons i,
        .nav-action-icons .las,
        .nav-action-icons .lar {
            font-size: 22px !important;
        }
    }

    /* ✅ COMMON FIX: Text kabhi 2 line me na toote */
    #mainMenu .nav-link {
        white-space: nowrap !important;
        /* Force text in one line */
    }
</style>

{{-- 🟢 DESKTOP HEADER (Fully Responsive) --}}
<header class="sticky-top z-1020 shadow-sm" style="background-color: #fff; border-bottom: 1px solid #f0f0f0;">

    <nav class="navbar navbar-expand-lg py-2">

        {{-- ✅ px-4 px-xl-5: Laptop par kam padding, bade screen par jyada --}}
        <div class="container-fluid px-3 px-lg-4 px-xl-5">

            {{-- 1. LOGO --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/suyagyalogomobile.webp') }}" alt="Suyagya"
                    style="height: 50px; width: auto; object-fit: contain;">
            </a>

            <button class="navbar-toggler p-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- 2. MAIN MENU --}}
            <div class="collapse navbar-collapse justify-content-center" id="mainMenu">
                {{-- flex-nowrap: Items ek hi line me rahenge --}}
                <ul class="navbar-nav mb-2 mb-lg-0 align-items-center gap-3 gap-xl-4 flex-nowrap">

                    @foreach ($headerCategories as $category)
                        <li class="nav-item dropdown hover-dropdown">
                            @if ($category->subCategories->count() > 0)
                                <a class="nav-link text-dark fw-bold text-uppercase d-flex align-items-center"
                                    style="font-size: 13px; letter-spacing: 0.5px;"
                                    href="{{ route('products.category', $category->slug) }}"
                                    id="catDrop{{ $category->id }}" role="button" aria-expanded="false">
                                    {{ $category->name }} <i class="las la-angle-down small ms-1"
                                        style="font-size: 10px;"></i>
                                </a>
                                {{-- Mega Menu Code Same Rahega --}}
                                <div class="dropdown-menu japam-mega-menu shadow-lg border-0"
                                    aria-labelledby="catDrop{{ $category->id }}">
                                    <div class="row g-0">
                                        <div class="col-4 col-lg-3">
                                            <div class="japam-sc-list">
                                                @foreach ($category->subCategories as $sub)
                                                    <a href="{{ route('products.subcategory', [$category->slug, $sub->slug]) }}"
                                                        class="japam-sc-item">
                                                        {{ $sub->name }} <i class="las la-angle-right"></i>
                                                    </a>
                                                @endforeach
                                                <a href="{{ route('products.category', $category->slug) }}"
                                                    class="japam-sc-item text-primary fw-bold">
                                                    View All {{ $category->name }} <i class="las la-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-8 col-lg-9">
                                            <div class="japam-prod-grid h-100">
                                                <div class="row g-3">
                                                    @if ($category->products->count() > 0)
                                                        @foreach ($category->products as $product)
                                                            <div class="col-3">
                                                                <a href="{{ route('product.detail', $product->slug) }}"
                                                                    class="japam-prod-card">
                                                                    <img src="{{ asset($product->main_image) }}"
                                                                        class="japam-prod-img"
                                                                        alt="{{ $product->name }}">
                                                                    <span
                                                                        class="japam-prod-title">{{ $product->name }}</span>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @else
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
                            @else
                                <a href="{{ route('products.category', $category->slug) }}"
                                    class="nav-link text-dark fw-bold text-uppercase"
                                    style="font-size: 13px; letter-spacing: 0.5px;">
                                    {{ $category->name }}
                                </a>
                            @endif
                        </li>
                    @endforeach

                </ul>
            </div>

            {{-- 3. ICONS (Right Side) --}}
            <div class="d-flex align-items-center gap-3 ms-auto nav-action-icons">

                {{-- Search --}}
                <a href="javascript:void(0);" onclick="toggleSearch()" class="text-dark" title="Search">
                    <i class="las la-search" style="font-size: 24px;"></i>
                </a>

                {{-- User --}}
                <div class="nav-user-auth">
                    @auth
                        <div class="dropdown">
                            <a href="#" class="text-dark d-flex align-items-center" role="button"
                                data-bs-toggle="dropdown">
                                <i class="las la-user-circle" style="font-size: 26px;"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                                <li><a class="dropdown-item small" href="{{ url('/orders') }}">My Orders</a></li>
                                <li><a class="dropdown-item small text-danger" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                                </li>
                            </ul>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    @else
                        <a href="javascript:void(0);" onclick="showLoginModal()" class="text-dark" title="Login">
                            <i class="las la-user-circle" style="font-size: 26px;"></i>
                        </a>
                    @endauth
                </div>

                {{-- Wishlist --}}
                <a href="javascript:void(0)" onclick="openWishlistModal()" class="text-dark position-relative">
                    <i class="lar la-heart" style="font-size: 26px;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger p-1"
                        style="font-size: 10px; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center;">
                        {{ Auth::check() ? \App\Models\Wishlist::where('user_id', Auth::id())->count() : count(session('guest_wishlist', [])) }}
                    </span>
                </a>

                {{-- Cart --}}
                <a href="javascript:void(0);" onclick="openSideCart()" class="text-dark position-relative">
                    <i class="las la-shopping-bag" style="font-size: 26px;"></i>
                    <span id="cart-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger p-1"
                        style="font-size: 10px; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; {{ isset($cartGlobalCount) && $cartGlobalCount > 0 ? '' : 'display: none;' }}">
                        {{ $cartGlobalCount ?? 0 }}
                    </span>
                </a>

            </div>

        </div>
    </nav>
</header>


{{-- Mobile Header (Separate Block) --}}
<div class="Mobile-Header sticky-top" style="background: #fff; z-index: 1020;">
    {{-- ✅ NEW TOP INFO BAR (Mobile - Fixed with Marquee Tag) --}}
    <div class="mobileHeader">
        <div class="HeaderLeft">
            <button id="menuButton">
                <span class="icon-menu"> <svg viewBox="0 0 100 80" width="20" height="20"
                        class="icon-hover-classes" data-aid="hamburger-menu" style="grid-area:nav;"
                        data-armada-selector="mobile-menu-open-icon">
                        <rect width="100" height="10" class="fill-current"></rect>
                        <rect y="30" width="100" height="10" class="fill-current"></rect>
                        <rect y="60" width="100" height="10" class="fill-current"></rect>
                    </svg></span>
                <span class="icon-close"><i class="la la-close" data-v-141adee1=""></i></span>
            </button>
            <div class="nav-search-icon">
                <a href="javascript:void(0);" onclick="toggleSearch()" title="Search">
                    <i class="las la-search"></i>
                </a>
            </div>

        </div>
        <div class="HeaderCenter">
            <a class="navbar-brand py-2 me-lg-5" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/suyagyalogomobile.webp') }}" alt="Suyagya" height="50">
            </a>
        </div>
        <div class="HeaderRight">
            {{-- 👤 2. Account Icon --}}
            <div class="nav-user-auth ml-0">
                @auth
                    {{-- ✅ LOGGED IN USER (Dropdown) --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-dark text-decoration-none" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="las la-user-circle fs-2"></i>
                            {{-- Optional: Show First Name --}}
                            {{-- <span class="ms-2 small fw-bold d-none d-md-block">{{ Auth::user()->name }}</span> --}}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-3"
                            style="min-width: 200px;">
                            {{-- <li class="px-3 py-2 border-bottom">
                                <span class="small text-muted d-block">Welcome,</span>
                                <span class="fw-bold text-dark">{{ Auth::user()->name ?? 'User' }}</span>
                            </li> --}}
                            <li>
                                <a class="dropdown-item py-2" href="{{ url('/orders') }}">
                                    <i class="las la-box me-2"></i> Order History
                                </a>
                            </li>
                            {{-- <li>
                                <a class="dropdown-item py-2" href="{{ url('/profile') }}">
                                    <i class="las la-user-cog me-2"></i> My Profile
                                </a>
                            </li> --}}
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="las la-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
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
                <a href="javascript:void(0)" onclick="openWishlistModal()" class="position-relative ...">
                    <i class="lar la-heart fs-4"></i>
                    <span
                        class="wishlist-count position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 10px;">
                        {{ Auth::check() ? \App\Models\Wishlist::where('user_id', Auth::id())->count() : count(session('guest_wishlist', [])) }}
                    </span>
                </a>
            </div>

            {{-- 🛒 4. Cart Icon (Side Cart Trigger) --}}
            <div class="nav-cart-box ms-4 position-relative">
                <a href="javascript:void(0);" onclick="openSideCart()" title="Cart" class="text-dark">
                    <i class="las la-shopping-bag" style="font-size: 28px;"></i>

                    {{-- Badge --}}
                    <span id="cart-badge"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 10px; {{ isset($cartGlobalCount) && $cartGlobalCount > 0 ? '' : 'display: none;' }}">
                        {{ $cartGlobalCount ?? 0 }}
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- 📱 MOBILE MEGA MENU (Dynamic) --}}
    <aside class="mega-menu" id="mega-menu--mobile">
        <div class="mega__container" data-active-depth="1" data-active-nav="">

            <div class="mega__body">

                {{-- 🟢 LEVEL 1: MAIN MENU --}}
                <div class="mega__screen" data-menu-depth="1" data-active-menu="true">
                    <nav class="navigation">

                        {{-- 1. Static Home --}}
                        <a href="{{ url('/') }}" class="btn">
                            <span class="btn__label">Home</span>
                        </a>

                        {{-- 2. Dynamic Categories (Triggers) --}}
                        @foreach ($headerCategories as $category)
                            @if ($category->subCategories->count() > 0)
                                {{-- 🔻 CASE A: Has Subcategories --}}
                                {{-- IMPORTANT: aria-label matches the ID in Level 2 --}}
                                <a href="#" class="btn" aria-label="mobile-cat-{{ $category->id }}">
                                    <span class="btn__label">{{ $category->name }}</span>
                                    <span class="btn__icon"></span> {{-- Arrow Icon --}}
                                </a>
                            @else
                                {{-- 🔗 CASE B: Direct Link (No Subcategories) --}}
                                <a href="{{ route('products.category', $category->slug) }}" class="btn">
                                    <span class="btn__label">{{ $category->name }}</span>
                                </a>
                            @endif
                        @endforeach

                        {{-- 3. Static Links --}}
                        <a href="{{ url('/about') }}" class="btn">
                            <span class="btn__label">About</span>
                        </a>
                        <a href="{{ url('/contact') }}" class="btn">
                            <span class="btn__label">Contact</span>
                        </a>
                        <a href="{{ url('shops/create') }}" class="btn">
                            <span class="btn__label text-primary">Become a Seller</span>
                        </a>

                    </nav>
                </div>

                {{-- 🟢 LEVEL 2: SUBCATEGORIES CONTAINER (Single Screen) --}}
                {{-- Note: Only ONE 'mega__screen' div for Depth 2 --}}
                <div class="mega__screen" data-menu-depth="2" data-active-menu="false">

                    {{-- Header with Back Button --}}
                    <div class="categoriesHeading">
                        <button class="screen-back-btn">
                            <span class="btn__icon">
                                <svg width="20" height="20" viewBox="0 0 4 7" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0.11745 3.7766C-0.0391497 3.62427 -0.0391497 3.37796 0.11745 3.22725L3.31612 0.11425C3.47272 -0.0380799 3.72595 -0.0380798 3.88088 0.11425C4.03582 0.26657 4.03748 0.51289 3.88088 0.6636L0.96543 3.49949L3.88255 6.33701C4.03915 6.48934 4.03915 6.73565 3.88255 6.88636C3.72595 7.03707 3.47272 7.03869 3.31778 6.88636L0.11745 3.7766Z"
                                        fill="#221F20" />
                                </svg>
                            </span>
                        </button>
                        <h5>Categories</h5> {{-- Common Title --}}
                    </div>

                    {{-- Loop to create hidden NAV blocks --}}
                    @foreach ($headerCategories as $category)
                        @if ($category->subCategories->count() > 0)
                            {{-- This NAV opens ONLY when aria-label matches --}}
                            <nav class="navigation" aria-labelledby="mobile-cat-{{ $category->id }}">

                                {{-- View All Link --}}
                                <a href="{{ route('products.category', $category->slug) }}"
                                    class="btn fw-bold text-primary">
                                    <span class="btn__label">View All {{ $category->name }}</span>
                                    <span class="btn__icon"><i class="las la-arrow-right"></i></span>
                                </a>

                                {{-- Subcategories List --}}
                                @foreach ($category->subCategories as $sub)
                                    <a href="{{ route('products.subcategory', [$category->slug, $sub->slug]) }}"
                                        class="btn">
                                        <span class="btn__label">{{ $sub->name }}</span>
                                    </a>
                                @endforeach

                            </nav>
                        @endif
                    @endforeach

                </div>
                {{-- End Level 2 Screen --}}

            </div>
        </div>
    </aside>

</div>

{{-- ✅✅✅ UNIVERSAL SEARCH BAR (PLACED AT BOTTOM OF FILE) ✅✅✅ --}}
{{-- ✅✅✅ UNIVERSAL SEARCH BAR (BOTTOM OF FILE) ✅✅✅ --}}
<div class="header-search-bar bg-white border-bottom shadow-lg">
    <div class="container py-3">
        <div class="position-relative">
            <i class="las la-search position-absolute top-50 start-0 translate-middle-y ms-3 fs-4 text-muted"></i>

            <input type="text" class="form-control border-0 bg-light py-3 ps-5 rounded-pill fs-6 fw-bold"
                id="live-search-input" placeholder="  Search for products..." autocomplete="off">

            {{-- Close Icon (Visible on both now for ease) --}}
            <i class="las la-times position-absolute top-50 end-0 translate-middle-y me-3 fs-4 cursor-pointer"
                onclick="toggleSearch()"></i>
        </div>
    </div>

    {{-- Result Box --}}
    <div id="search-results-box" class="bg-white"></div>
</div>
