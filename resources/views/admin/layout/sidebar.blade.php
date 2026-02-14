<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('admin/assets/img/logo/suyagya.webp') }}" alt="Logo" width="200">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        {{-- Dashboard --}}
        <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Store Management</span>
        </li>

        {{-- Products --}}
        <li class="menu-item {{ request()->is('admin/products*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-box"></i>
                <div>Products</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.products.index') }}" class="menu-link">
                        Product List
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.products.create') }}" class="menu-link">
                        Add Product
                    </a>
                </li>
            </ul>
        </li>

        {{-- Categories & Sub Categories --}}
        <li
            class="menu-item {{ request()->is('admin/categories*') || request()->is('admin/subcategories*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div>Categories</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.categories.index') }}" class="menu-link">
                        Category List
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.categories.create') }}" class="menu-link">
                        Add Category
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.subcategories.index') }}" class="menu-link">
                        Sub-Category List
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.subcategories.create') }}" class="menu-link">
                        Add Sub-Category
                    </a>
                </li>
            </ul>
        </li>

        {{-- Filters --}}
        <li
            class="menu-item {{ request()->is('admin/filters*') || request()->is('admin/filter-values*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-filter-alt"></i>
                <div>Filters</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.filters.index') }}" class="menu-link">
                        Filters List
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.filters.create') }}" class="menu-link">
                        Add Filter
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.filter-values.index') }}" class="menu-link">
                        Filter Values
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.filter-values.create') }}" class="menu-link">
                        Add Filter Value
                    </a>
                </li>
            </ul>
        </li>

        {{-- Orders --}}
        <li class="menu-item {{ request()->is('admin/orders*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-cart"></i>
                <div>Orders</div>
            </a>
            <ul class="menu-sub">
                {{-- List Page is enough --}}
                <li class="menu-item">
                    <a href="{{ route('admin.orders.index') }}" class="menu-link">
                        Order List
                    </a>
                </li>

                {{-- ❌ Remove this block 👇 --}}
                {{-- <li class="menu-item">
                    <a href="{{ route('admin.orders.show') }}" class="menu-link">
                        Order show
                    </a>
                </li> --}}
            </ul>
        </li>

        {{-- 💰 WALLET & CASHBACK MODULE --}}
        <li class="menu-item {{ request()->is('admin/wallet*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>Wallet & Cashback</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.wallet.pending') ? 'active' : '' }}">
                    <a href="{{ route('admin.wallet.pending') }}" class="menu-link">
                        <div>Pending Reels</div>
                        {{-- Optional: Yahan Badge dikha sakte hain agar count pass karein --}}
                    </a>
                </li>
            </ul>
        </li>

        {{-- ✨ NEW LOGISTIC MODULE HERE ✨ --}}
        <li class="menu-item {{ request()->is('admin/logistic*') ? 'active' : '' }}">
            <a href="{{ route('admin.logistic.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div>Logistics (Ship)</div>
            </a>
        </li>

        {{-- Customers --}}
        <li class="menu-item {{ request()->is('admin/customers*') ? 'active' : '' }}">
            <a href="{{ route('admin.customers.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Customers</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/payment*') ? 'active' : '' }}">
            <a href="{{ route('admin.payment.settings') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Payment Settings</div>
            </a>
        </li>

        {{-- 🎟️ COUPONS MODULE --}}
        <li class="menu-item {{ request()->is('admin/coupons*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                {{-- Icon for Coupon (Tag) --}}
                <i class="menu-icon tf-icons bx bx-purchase-tag-alt"></i>
                <div>Coupons</div>
            </a>
            <ul class="menu-sub">
                {{-- List --}}
                <li class="menu-item {{ request()->routeIs('admin.coupons.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.coupons.index') }}" class="menu-link">
                        <div>Coupon List</div>
                    </a>
                </li>

                {{-- Create --}}
                <li class="menu-item {{ request()->routeIs('admin.coupons.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.coupons.create') }}" class="menu-link">
                        <div>Add Coupon</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- video feed --}}
        <li class="menu-item {{ request()->is('admin/videos*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                {{-- Video Icon --}}
                <i class="menu-icon tf-icons bx bx-video"></i>
                <div>Video Feed</div>
            </a>
            <ul class="menu-sub">
                {{-- Video List --}}
                <li class="menu-item {{ request()->routeIs('admin.videos.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.videos.index') }}" class="menu-link">
                        <div>Video List</div>
                    </a>
                </li>

                {{-- Add Video --}}
                <li class="menu-item {{ request()->routeIs('admin.videos.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.videos.create') }}" class="menu-link">
                        <div>Add Video</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('admin/reviews*') ? 'active' : '' }}">
            <a href="{{ route('admin.reviews.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-star"></i>
                <div>Reviews</div>
            </a>
        </li>

        {{-- Home Banners --}}
        <li class="menu-item {{ request()->is('admin/banners*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-images"></i>
                <div>Home Banners</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.banners.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.banners.index') }}" class="menu-link">
                        <div>Banner List</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.banners.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.banners.create') }}" class="menu-link">
                        <div>Add Banner</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- 👇👇 YAHAN ADD KAREIN 👇👇 --}}
        <li class="menu-item {{ request()->routeIs('admin.home.settings') ? 'active' : '' }}">
            <a href="{{ route('admin.home.settings') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i> {{-- Cog Icon for Settings --}}
                <div data-i18n="Home Settings">Home Page Settings</div>
            </a>
        </li>

        <li
            class="menu-item {{ request()->is('admin/general-faqs*') || request()->is('admin/index*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-filter-alt"></i>
                <div>General FAQs</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin.general-faqs.index') }}" class="menu-link">
                        General FAQs List
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{ route('admin.general-faqs.create') }}" class="menu-link">
                        Add General FAQs
                    </a>
                </li>
            </ul>
        </li>

        {{-- 📝 BLOGS MODULE --}}
        <li class="menu-item {{ request()->is('admin/blogs*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-news"></i> {{-- News Icon --}}
                <div data-i18n="Blogs">Blogs</div>
            </a>

            <ul class="menu-sub">
                {{-- List --}}
                <li class="menu-item {{ Request::routeIs('admin.blogs.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.blogs.index') }}" class="menu-link">
                        <div data-i18n="List">All Blogs</div>
                    </a>
                </li>

                {{-- Add New --}}
                <li class="menu-item {{ Request::routeIs('admin.blogs.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.blogs.create') }}" class="menu-link">
                        <div data-i18n="Add">Add New</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->routeIs('admin.redirects.*') ? 'active' : '' }}">
            <a href="{{ route('admin.redirects.index') }}" class="menu-link">
                {{-- Sneat Theme ka Icon --}}
                <i class="menu-icon tf-icons bx bx-link"></i>
                <div>Redirects (Fix Links)</div>
            </a>
        </li>

    </ul>
</aside>
