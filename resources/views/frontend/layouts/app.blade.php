<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="is-logged-in" content="{{ Auth::check() ? '1' : '0' }}">

    {{-- 🚀 PERFORMANCE: GTM Script yahan se hata kar niche Footer me daal diya hai --}}

    {{-- 🔥 DYNAMIC SEO LOGIC START 🔥 --}}
    @php
        // 1. Default Values (Fallback)
        $metaTitle = 'Suyagya - Authentic Stone Jewelry & Rudraksha';
        $metaDesc = 'Shop genuine Rudraksha, Gemstones, and spiritual jewelry at Suyagya. Certified products with lab reports.';
        $metaKeys = 'rudraksha, gemstones, spiritual jewelry, mala, suyagya';
        $ogImage = asset('og-images/default-og.jpg');
        $currentUrl = url()->current();

        // 2. PRODUCT Detail Page
        if (Route::is('product.detail') && !empty($product)) {
            $metaTitle = !empty($product->meta_title) ? $product->meta_title : $product->name . ' | Suyagya';
            $metaDesc = !empty($product->meta_description) ? $product->meta_description : Str::limit(strip_tags($product->description), 160);
            $metaKeys = $product->meta_keywords ?? $metaKeys;
            if (!empty($product->og_image)) {
                $ogImage = asset($product->og_image);
            } elseif (!empty($product->product_main_image)) {
                $ogImage = asset($product->product_main_image);
            } else {
                $ogImage = asset('og-images/default-og.jpg');
            }
        }
        // 3. CATEGORY Page
        elseif (Route::is('products.category') && !empty($category)) {
            $metaTitle = !empty($category->meta_title) ? $category->meta_title : $category->name . ' Collection | Suyagya';
            $metaDesc = !empty($category->meta_description) ? $category->meta_description : 'Explore our exclusive collection of ' . $category->name;
            $metaKeys = $category->meta_keywords ?? $metaKeys;
            if ($category->og_image) {
                $ogImage = asset($category->og_image);
            }
        }
        // 4. SUB-CATEGORY Page
        elseif (Route::is('products.subcategory') && !empty($subCategory)) {
            $metaTitle = !empty($subCategory->meta_title) ? $subCategory->meta_title : $subCategory->name . ' | Suyagya';
            $metaDesc = !empty($subCategory->meta_description) ? $subCategory->meta_description : 'Best quality ' . $subCategory->name . ' available online.';
            $metaKeys = $subCategory->meta_keywords ?? $metaKeys;
        }
        // ✅ 5. BLOG PAGES (Dynamic SEO)
        elseif (Route::is('blogs.index')) {
            $metaTitle = 'Our Blogs - Spiritual Knowledge & Insights | Suyagya';
            $metaDesc = 'Read latest articles on Rudraksha, Gemstones, and spirituality. Gain knowledge and insights from our experts.';
        } elseif (Route::is('blogs.show') && !empty($blog)) {
            $metaTitle = !empty($blog->meta_title) ? $blog->meta_title : $blog->title . ' | Suyagya';
            $metaDesc = !empty($blog->meta_description) ? $blog->meta_description : Str::limit(strip_tags($blog->content), 160);
            $metaKeys = !empty($blog->meta_keywords) ? $blog->meta_keywords : $metaKeys;
            if (!empty($blog->main_image)) {
                $ogImage = asset($blog->main_image);
            } elseif (!empty($blog->og_image)) {
                $ogImage = asset($blog->og_image);
            }
        }
        // ✅ 6. STATIC PAGES
        elseif (Route::is('terms.conditions')) {
            $metaTitle = 'Terms & Conditions | Suyagya';
            $metaDesc = 'Read the Terms and Conditions of Suyagya. Understand our policies regarding usage, orders, and services.';
        } elseif (Route::is('privacy.policy')) {
            $metaTitle = 'Privacy Policy | Suyagya';
            $metaDesc = 'Your privacy is important to us. Learn how Suyagya collects, uses, and protects your personal data.';
        } elseif (Route::is('refund.policy')) {
            $metaTitle = 'Return & Refund Policy | Suyagya';
            $metaDesc = 'Understand our return and refund process. We ensure customer satisfaction with transparent policies.';
        } elseif (Route::is('support.policy')) {
            $metaTitle = 'Support Policy | Suyagya';
            $metaDesc = 'Need help? Contact Suyagya support team for assistance with orders, products, and services.';
        } elseif (Route::is('frontend.faq')) {
            $metaTitle = 'Frequently Asked Questions | Suyagya';
            $metaDesc = 'Find answers to your questions related to products, shipping, and more.';
        } elseif (Route::is('about')) {
            $metaTitle = 'About us | Suyagya';
            $metaDesc = 'How Suyagya Was Born.';
        } elseif (Route::is('contact')) {
            $metaTitle = 'Contact us | Suyagya';
            $metaDesc = 'For business related bulk orders or queries, please contact us here.';
        } elseif (Route::is('track.order')) {
            $metaTitle = 'Track Order | Suyagya';
            $metaDesc = 'Track Your Order Here.';
        }
        // 7. HOME Page
        elseif (isset($homeSettings) && !empty($homeSettings)) {
            $metaTitle = $homeSettings->meta_title ?? $metaTitle;
            $metaDesc = $homeSettings->meta_description ?? $metaDesc;
            $metaKeys = $homeSettings->meta_keywords ?? $metaKeys;
            if ($homeSettings->og_image) {
                $ogImage = asset($homeSettings->og_image);
            }
        }
    @endphp
    {{-- 🔥 DYNAMIC SEO LOGIC END 🔥 --}}

    {{-- ✅ PRIMARY META TAGS --}}
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="keywords" content="{{ $metaKeys }}">
    <meta name="author" content="Suyagya">
    <link rel="canonical" href="{{ $currentUrl }}" />

    {{-- ✅ OPEN GRAPH / FACEBOOK / WHATSAPP --}}
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDesc }}" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta property="og:url" content="{{ $currentUrl }}" />
    <meta property="og:site_name" content="Suyagya" />

    {{-- ✅ TWITTER CARD --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- ✅ 3. SCHEMA MARKUP --}}
    @include('frontend.includes.schema')

    {{-- ✅ FAVICON --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">

    {{-- 🔗 CSS Files --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- ⚡ PERFORMANCE: Added display=swap --}}
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">

    {{-- 💡 BOOTSTRAP 5 CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    {{-- 💡 Premium Custom Styles --}}
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v={{ filemtime(public_path('assets/css/custom.css')) }}">

    @yield('styles')

    {{-- 🚀 PERFORMANCE: Razorpay Affordability Script ko yahan se hata kar Footer me daala hai --}}
</head>

{{-- 🎨 Body --}}
<body>
    <div class="aiz-main-wrapper d-flex flex-column" style="background-color: var(--light) !important;">

        {{-- ⬆️ Header --}}
        @include('frontend.includes.header')

        {{-- 🏠 Page Content --}}
        <main class="flex-grow-1" style="background-color: #f7f1de;">
            @yield('content')
        </main>

        {{-- 🛒 Include Side Cart --}}
        @include('frontend.includes.side_cart')

        @include('frontend.modals.checkout_modal')
        @include('frontend.modals.wishlist_modal')

        {{-- ⬇️ Footer --}}
        @include('frontend.includes.footer')

        {{-- 🍞 Toast Container --}}
        <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 1060;">
            <div id="liveToast" class="toast align-items-center text-white bg-dark border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toast-message">
                        Item added to wishlist!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🎉 GAMIFICATION MODAL --}}
    @if (Cookie::get('lucky_draw_played') === null)
        @include('frontend.modals.game_modal')
    @endif

    {{-- 🎁 FLOATING LUCKY DRAW ICON --}}
    <div id="luckyFloatingIcon" class="lucky-float-btn" onclick="reopenLuckyDraw()" style="display: none;">
        <div class="icon-pulse">
            <i class="las la-gift"></i>
        </div>
        <span class="lucky-text">Win Prize</span>
    </div>

    {{-- ✅ WHATSAPP FLOATING BUTTON START --}}
    <a href="https://wa.me/917692005006?text=Hi%20Suyagya%20Team,%20I%20need%20help%20with%20a%20product."
        class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <i class="lab la-whatsapp"></i>
    </a>
    {{-- ❌ WHATSAPP FLOATING BUTTON END --}}

    {{-- CSS for Floating Buttons (Game & Whatsapp) --}}
    <style>
        .jump-anim { animation: jump 1.5s infinite; }
        @keyframes jump { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

        /* 🎁 Floating Game Icon */
        .lucky-float-btn {
            position: fixed;
            bottom: 100px; /* Above Whatsapp */
            right: 20px;
            z-index: 99;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #d4af37, #f7f1de);
            border: 2px solid #fff;
            border-radius: 50%;
            box-shadow: 2px 2px 3px #999;
            cursor: pointer;
            display: none; /* Hidden by default */
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: transform 0.3s ease;
            animation: floatIcon 3s ease-in-out infinite;
        }
        .lucky-float-btn[style*="display: block"] { display: flex !important; }
        .lucky-float-btn:hover { transform: scale(1.1); }
        .lucky-float-btn i { font-size: 24px; color: #333; margin-bottom: 2px; line-height: 1; }
        .lucky-float-btn .lucky-text { font-size: 8px; font-weight: 800; color: #333; text-transform: uppercase; line-height: 1.2; }
        @keyframes floatIcon { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-5px); } }

        /* 🟢 WhatsApp Button */
        .whatsapp-float {
            position: fixed; width: 60px; height: 60px; bottom: 25px; right: 25px;
            background-color: #25d366; color: #FFF; border-radius: 50px; text-align: center;
            font-size: 35px; box-shadow: 2px 2px 3px #999; z-index: 99;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.3s ease; text-decoration: none !important;
            animation: pulse-green 2s infinite;
        }
        .whatsapp-float:hover { background-color: #1ebe57; transform: scale(1.1); color: #fff; }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }

        /* Mobile Fixes */
        @media (max-width: 768px) {
            .lucky-float-btn { bottom: 90px; right: 15px; width: 55px; height: 55px; }
            .lucky-float-btn i { font-size: 20px; }
            .lucky-float-btn .lucky-text { font-size: 7px; }
            .whatsapp-float { width: 50px; height: 50px; bottom: 20px; right: 20px; font-size: 28px; }
        }
    </style>

    {{-- ⚙️ SCRIPTS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    {{-- 🚀 OPTIMIZED SCRIPTS LOADER (GTM & Razorpay) --}}
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                // 1. Load Razorpay Checkout
                var scriptRazor = document.createElement('script');
                scriptRazor.src = "https://checkout.razorpay.com/v1/checkout.js";
                document.body.appendChild(scriptRazor);

                // 2. Load Razorpay Affordability (Jo Head se hataya tha)
                var scriptAfford = document.createElement('script');
                scriptAfford.src = "https://cdn.razorpay.com/widgets/affordability/affordability.js";
                document.body.appendChild(scriptAfford);

                // 3. Load GTM / Analytics (Jo Head se hataya tha)
                var scriptGTM = document.createElement('script');
                scriptGTM.async = true;
                scriptGTM.src = "https://www.googletagmanager.com/gtag/js?id=G-6ECDBEM0VJ";
                document.head.appendChild(scriptGTM);

                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-6ECDBEM0VJ');

            }, 3000); // 3-4 Second Delay for Speed
        });
    </script>

    @yield('scripts')
    <script src="{{ asset('assets/js/custom.js') }}?v={{ filemtime(public_path('assets/js/custom.js')) }}"></script>

    {{-- Menu & Lucky Draw Scripts --}}
    <script>
        // ... (Apka Mega Menu Code Same Rahega) ...
        init__megaMenu();
        function init__megaMenu() {
            const mm = document.querySelector('aside#mega-menu--mobile');
            if (mm) {
                const mm_container = mm.querySelector('.mega__container');
                const mm_screens = mm.querySelectorAll('.mega__screen');
                const mm_subIcons = mm.querySelectorAll('a.btn .btn__icon');
                const mm_subLinks = mm.querySelectorAll('a.btn[aria-label]');
                const mm_subLinks_icon = `<svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.88255 3.2234C4.03915 3.37573 4.03915 3.62204 3.88255 3.77275L0.683882 6.88575C0.52728 7.03808 0.274052 7.03808 0.119117 6.88575C-0.0358184 6.73343 -0.0374844 6.48711 0.119117 6.3364L3.03457 3.50051L0.117451 0.662992C-0.0391504 0.510664 -0.0391504 0.264347 0.117451 0.113639C0.274052 -0.0370684 0.52728 -0.0386889 0.682216 0.113639L3.88255 3.2234Z" fill="#221F20"/></svg>`;
                let mm_active_depth = parseInt(mm_container.dataset.activeDepth);
                mm_screens[0].dataset.activeMenu = true;
                mm_subLinks.forEach(item => { const iconSpan = item.querySelector('.btn__icon'); if (iconSpan) iconSpan.insertAdjacentHTML("afterbegin", mm_subLinks_icon); });
                const screenBackBtns = mm.querySelectorAll('.screen-back-btn');
                screenBackBtns.forEach(backBtn => { backBtn.addEventListener('click', (e) => { if (mm_active_depth > 1) sub__handleActiveDepth(mm_screens, e, mm_container); }); });
                mm_subIcons.forEach(icon => { icon.addEventListener('click', (e) => sub__handleActiveDepth(mm_screens, e, mm_container)); });
                mm_subLinks.forEach(link => { link.addEventListener('click', (e) => sub__handleActiveDepth(mm_screens, e, mm_container)); });
                function sub__handleActiveDepth(screens, event, container) {
                    const target = event.currentTarget || event.target;
                    if (target.classList.contains('screen-back-btn') || target.id == "menu-back") {
                        mm_active_depth -= 1; mm_container.dataset.activeDepth = mm_active_depth;
                        mm_screens.forEach(screen => { let dft_screen_depth = parseInt(screen.dataset.menuDepth); screen.dataset.activeMenu = false; dft_screen_depth >= mm_active_depth ? screen.classList.remove('stacked') : null; dft_screen_depth == mm_active_depth ? screen.dataset.activeMenu = true : null; });
                    } else {
                        event.preventDefault(); event.stopPropagation();
                        mm_active_depth += 1; mm_container.dataset.activeDepth = mm_active_depth;
                        mm_screens.forEach(screen => { let dft_screen_depth = parseInt(screen.dataset.menuDepth); screen.dataset.activeMenu = false; dft_screen_depth < mm_active_depth ? screen.classList.add('stacked') : null; dft_screen_depth == mm_active_depth ? screen.dataset.activeMenu = true : null; });
                        let link = target.closest('a.btn') || target; let link_menu = link.getAttribute('aria-label'); container.dataset.activeNav = link_menu;
                        let dft_active_screen = container.querySelector('.mega__screen[data-active-menu="true"]'); let dft_active_screen__navs = dft_active_screen.querySelectorAll('nav'); dft_active_screen__navs.forEach(nav => { nav.classList.add('hidden'); });
                        let dft_active_nav = dft_active_screen.querySelector(`nav[aria-labelledby="${link_menu}"]`); if (dft_active_nav) dft_active_nav.classList.remove('hidden');
                    }
                }
            }
        }

        const menuButton = document.getElementById('menuButton');
        const megaMenu = document.getElementById('mega-menu--mobile');
        if(menuButton){
             menuButton.addEventListener('click', () => { megaMenu.classList.toggle('active'); menuButton.classList.toggle('active'); });
        }

        function initFooterAccordion() {
            const headings = document.querySelectorAll('h4.footer-heading');
            headings.forEach(heading => {
                heading.addEventListener('click', () => {
                    if (window.innerWidth > 767) return;
                    const list = heading.nextElementSibling;
                    if (!list || !list.classList.contains('footer-list')) return;
                    heading.classList.toggle('active'); list.classList.toggle('active');
                });
            });
        }
        initFooterAccordion();

        window.appRoutes = { getCoupons: "{{ route('get.coupons') }}", applyCoupon: "{{ route('apply.coupon') }}" };
        window.csrfToken = "{{ csrf_token() }}";
    </script>

    {{-- LUCKY DRAW LOGIC --}}
    <script>
        $(document).ready(function() {
            @auth
                let dbHasActiveCoupon = {{ \App\Models\UserCoupon::where('user_id', Auth::id())->where('is_used', 0)->exists() ? 'true' : 'false' }};
                if (!dbHasActiveCoupon) { if (localStorage.getItem('gaming_coupon_amount')) { localStorage.removeItem('gaming_coupon_amount'); localStorage.removeItem('gaming_coupon_code'); console.log("🧹 Sync: Database empty, removed fake coupon."); } }
            @endauth
            let userAlreadyPlayed = {{ Auth::check() && \App\Models\UserCoupon::where('user_id', Auth::id())->where('is_used', 0)->exists() ? 'true' : 'false' }};
            let shouldOpenGame = localStorage.getItem('openGameAfterLogin');
            let gameClosedByUser = localStorage.getItem('luckyDrawClosed');

            if (gameClosedByUser === 'true' && !userAlreadyPlayed) { $('#luckyFloatingIcon').fadeIn(); }
            else if (shouldOpenGame === 'true') { @auth $('#gameModal').modal('show'); localStorage.removeItem('openGameAfterLogin'); @endauth }
            else { if (!userAlreadyPlayed) { setTimeout(() => { $('#gameModal').modal('show'); }, 3000); } }
        });

        function closeLuckyDraw() { $('#gameModal').modal('hide'); localStorage.setItem('luckyDrawClosed', 'true'); $('#luckyFloatingIcon').fadeIn(); }
        function reopenLuckyDraw() { $('#gameModal').modal('show'); $('#luckyFloatingIcon').fadeOut(); }
        function playGuest() { $('#guest-view .chit-card i').addClass('d-none'); $('#guest-result').removeClass('d-none'); setTimeout(() => { $('#guest-msg').removeClass('d-none'); }, 600); }
        function openLoginForGame() { $('#gameModal').modal('hide'); localStorage.setItem('openGameAfterLogin', 'true'); $('#login_modal').modal('show'); }
        let playing = false;
        function playUser(element) {
            if (playing) return; playing = true; $(element).css('transform', 'scale(0.9)');
            $.ajax({
                url: "{{ route('game.play') }}", type: "POST", data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    if (res.status === 'success' || res.status === 'already_played') {
                        localStorage.removeItem('luckyDrawClosed'); $('#luckyFloatingIcon').hide();
                        $(element).find('.chit-icon').addClass('d-none'); $(element).find('.prize-amt').text('₹' + res.amount); $(element).find('.chit-result').removeClass('d-none');
                        $(element).css({ 'transform': 'scale(1.1)', 'border': '2px solid #28a745', 'background': '#e8f5e9' });
                        localStorage.setItem('gaming_coupon_amount', res.amount); localStorage.setItem('gaming_coupon_code', res.code);
                        setTimeout(() => { $('#final-amt').text(res.amount); $('#win-msg').removeClass('d-none'); setTimeout(() => { $('#gameModal').modal('hide'); }, 2500); }, 600);
                        $('.chit-card').not(element).css('opacity', 0.3).attr('onclick', '');
                    }
                }
            });
        }
    </script>
</body>
</html>
