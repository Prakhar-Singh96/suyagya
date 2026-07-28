<!DOCTYPE html>
<html lang="en">

<head>
    {{-- ✅ 1. Google Tag Manager (Script) - <head> के सबसे ऊपर --}}
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-MLSST65C');
    </script>
    {{-- End Google Tag Manager --}}

    <!-- Pixel Code -->
    <script>
        ! function(e, s, t, i, p, c, n) {
            e.hspixel || ((p = e.hspixel = function() {
                    p.process ?
                        p.process.apply(p, arguments) : p.queue.push(arguments)
                }).queue = [],
                p.t = 1 * new Date, (c = s.createElement("script")).async = 1,
                c.src = "https://hspx.hotstar.com/static/pixel/hspixel.js",
                (n = s.getElementsByTagName("script")[0]).parentNode.insertBefore(c, n)
            )
        }(window, document),
        hspixel("init", "SS_13029_Suyagya"),
            hspixel("track", "PageView");
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://hspx.hotstar.com/v1/events/track/cp_page_view?pi=SS_13029_Suyagya">
    </noscript>
    <!-- End of Pixel Code -->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="is-logged-in" content="{{ Auth::check() ? '1' : '0' }}">
    <meta name="user-wallet" content="{{ Auth::check() ? Auth::user()->wallet_balance : 0 }}">

    {{-- ⚡ SPEED OPTIMIZATION STEP 1: PRECONNECT CRITICAL ORIGINS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://connect.facebook.net">

    {{-- 🔥 DYNAMIC SEO LOGIC START 🔥 --}}
    @php
        $metaTitle = 'Suyagya - Authentic Stone Jewelry & Rudraksha';
        $metaDesc =
            'Shop genuine Rudraksha, Gemstones, and spiritual jewelry at Suyagya. Certified products with lab reports.';
        $metaKeys = 'rudraksha, gemstones, spiritual jewelry, mala, suyagya';
        $ogImage = asset('img/default-og.jpg');
        $currentUrl = url()->current();

        if (Route::is('product.detail') && !empty($product)) {
            $metaTitle = !empty($product->meta_title) ? $product->meta_title : $product->name . ' | Suyagya';
            $metaDesc = !empty($product->meta_description)
                ? $product->meta_description
                : Str::limit(strip_tags($product->description), 160);
            $metaKeys = $product->meta_keywords ?? $metaKeys;
            $ogImage = !empty($product->og_image)
                ? asset($product->og_image)
                : (!empty($product->product_main_image)
                    ? asset($product->product_main_image)
                    : asset('og-images/default-og.jpg'));
        } elseif (Route::is('products.category') && !empty($category)) {
            $metaTitle = !empty($category->meta_title)
                ? $category->meta_title
                : $category->name . ' Collection | Suyagya';
            $metaDesc = !empty($category->meta_description)
                ? $category->meta_description
                : 'Explore our exclusive collection of ' . $category->name;
            $metaKeys = $category->meta_keywords ?? $metaKeys;
            if ($category->og_image) {
                $ogImage = asset($category->og_image);
            }
        } elseif (Route::is('products.subcategory') && !empty($subCategory)) {
            $metaTitle = !empty($subCategory->meta_title)
                ? $subCategory->meta_title
                : $subCategory->name . ' | Suyagya';
            $metaDesc = !empty($subCategory->meta_description)
                ? $subCategory->meta_description
                : 'Best quality ' . $subCategory->name . ' available online.';
            $metaKeys = $subCategory->meta_keywords ?? $metaKeys;
        } elseif (Route::is('blogs.index')) {
            $metaTitle = 'Our Blogs - Spiritual Knowledge & Insights | Suyagya';
            $metaDesc =
                'Read latest articles on Rudraksha, Gemstones, and spirituality. Gain knowledge and insights from our experts.';
        } elseif (Route::is('blogs.show') && !empty($blog)) {
            $metaTitle = !empty($blog->meta_title) ? $blog->meta_title : $blog->title . ' | Suyagya';
            $metaDesc = !empty($blog->meta_description)
                ? $blog->meta_description
                : Str::limit(strip_tags($blog->content), 160);
            $metaKeys = !empty($blog->meta_keywords) ? $blog->meta_keywords : $metaKeys;
            if (!empty($blog->main_image)) {
                $ogImage = asset($blog->main_image);
            } elseif (!empty($blog->og_image)) {
                $ogImage = asset($blog->og_image);
            }
        } elseif (Route::is('terms.conditions')) {
            $metaTitle = 'Terms & Conditions | Suyagya';
            $metaDesc = 'Understand our policies regarding usage, orders, and services.';
        } elseif (Route::is('privacy.policy')) {
            $metaTitle = 'Privacy Policy | Suyagya';
            $metaDesc = 'Learn how Suyagya collects, uses, and protects your personal data.';
        } elseif (Route::is('refund.policy')) {
            $metaTitle = 'Return & Refund Policy | Suyagya';
            $metaDesc = 'Understand our return and refund process.';
        } elseif (Route::is('support.policy')) {
            $metaTitle = 'Support Policy | Suyagya';
            $metaDesc = 'Contact Suyagya support team for assistance.';
        } elseif (Route::is('frontend.faq')) {
            $metaTitle = 'Rudraksha, Rashi Bracelet & Gemstone FAQs | Suyagya';
            $metaDesc =
                'Rudraksha pehenne ke niyam, rashi bracelet selection, gemstone care, shipping, authenticity check — Suyagya ke sabse common sawaalon ke seedhe jawab. Lab certified original products.';
            $metaKeys =
                'rudraksha FAQ, rashi bracelet sawaal, gemstone bracelet care, karungali bracelet FAQ, suyagya products original, rudraksha pehenne ke niyam, spiritual jewelry india';
        } elseif (Route::is('about')) {
            $metaTitle = 'About us | Suyagya';
            $metaDesc = 'How Suyagya Was Born.';
        } elseif (Route::is('contact')) {
            $metaTitle = 'Contact us | Suyagya';
            $metaDesc = 'For business related bulk orders or queries, please contact us here.';
        } elseif (Route::is('track.order')) {
            $metaTitle = 'Track Order | Suyagya';
            $metaDesc = 'Track Your Order Here.';
        } elseif (Request::path() == '/' || Route::is('home') || Route::is('frontend.home')) {
            if (isset($homeSettings) && !empty($homeSettings)) {
                $metaTitle = $homeSettings->meta_title ?? $metaTitle;
                $metaDesc = $homeSettings->meta_description ?? $metaDesc;
                $metaKeys = $homeSettings->meta_keywords ?? $metaKeys;
                if ($homeSettings->og_image) {
                    $ogImage = asset($homeSettings->og_image);
                }
            }
        }
    @endphp
    {{-- 🔥 DYNAMIC SEO LOGIC END 🔥 --}}

    @pwaHead
    <link rel="apple-touch-icon" href="https://suyagya.com/apple-touch-icon-only.png">
    <link rel="apple-touch-icon" sizes="152x152" href="https://suyagya.com/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="https://suyagya.com/icon-192x192.png">
    <meta name="apple-mobile-web-app-title" content="Suyagya">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="keywords" content="{{ $metaKeys }}">
    <meta name="author" content="Suyagya">
    <link rel="canonical" href="{{ $currentUrl }}{{ request()->has('page') ? '?page=' . request()->page : '' }}" />

    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDesc }}" />
    <meta property="og:url" content="{{ $currentUrl }}" />
    <meta property="og:site_name" content="Suyagya" />
    <meta property="og:image" content="{{ $ogImage }}" />
    <meta property="og:image:secure_url" content="{{ $ogImage }}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDesc }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    @include('frontend.includes.schema')
    <meta name="p:domain_verify" content="da11f887c65754b1b5b976de4e3e4fbd" />

    <!-- Meta Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '799436573082052');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=799436573082052&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">

    {{-- Google फोंट्स विथ display=swap --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap"
        rel="stylesheet">

    {{-- बूटस्ट्रैप 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- 🚀 CRITICAL SLIDER FIX: गैलरी न टूटे, इसके लिए स्लिक की तीनों फाइलें बिना किसी रुकावट के रेंडर होंगी --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />

    {{-- बाकी Non-critical CSS फ़ाइलों को Asynchronous रहने दें (ताकि मोबाइल स्पीड 90+ भागे) --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css"
        media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css"
        media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"
        media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" media="print"
        onload="this.media='all'">

    {{-- Premium Custom Styles --}}
    <link rel="stylesheet"
        href="{{ asset('assets/css/custom.css') }}?v={{ filemtime(public_path('assets/css/custom.css')) }}">

    @yield('styles')
</head>

<body>
    <div class="aiz-main-wrapper d-flex flex-column" style="background-color: var(--light) !important;">

        {{-- ⬆️ Header --}}
        @include('frontend.includes.header')

        {{-- 🏠 Page Content --}}
        <main class="flex-grow-1" style="background-color: #f7f1de;">
            @yield('content')
        </main>

        <template id="tpl-side-cart">
            @include('frontend.includes.side_cart')
        </template>
        <template id="tpl-checkout-modal">
            @include('frontend.modals.checkout_modal')
        </template>
        <template id="tpl-wishlist-modal">
            @include('frontend.modals.wishlist_modal')
        </template>

        <div id="modals-mount-point"></div>

        {{-- ⬇️ Footer --}}
        @include('frontend.includes.footer')

        {{-- PWA Popup --}}
        <div class="pwa-popup-container" id="pwa-install-popup" style="display: none;">
            <div class="pwa-popup-content">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('icon-96x96.png') }}" alt="Suyagya Logo" class="pwa-app-icon" width="48"
                        height="48" loading="lazy">
                    <div class="ms-3 flex-grow-1">
                        <h6 class="mb-0 fw-bold">Suyagya App</h6>
                        <p class="mb-0 small text-muted">Install for better experience</p>
                    </div>
                    <div class="pwa-action-btns">
                        <button onclick="hidePwaPopup()"
                            class="btn btn-link text-muted text-decoration-none small">Later</button>
                        <button onclick="triggerInstall()"
                            class="btn btn-warning btn-sm fw-bold px-3 rounded-pill ms-2">Install</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast Container --}}
        <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 1060;">
            <div id="liveToast" class="toast align-items-center text-white bg-dark border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toast-message">Item added to wishlist!</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Astro AI Launcher Widget --}}
    <div id="chat-launcher" onclick="toggleChat()" class="astro-bounce"
        style="position:fixed; bottom:90px; right:20px; width:75px; height:75px; cursor:pointer; z-index:99;">
        <div style="position: relative; width: 100%; height: 100%;">
            <div
                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px; background: radial-gradient(circle, #ff9800 0%, rgba(255,152,0,0) 70%); border-radius: 50%; z-index: -1; animation: pulse-glow 2s infinite;">
            </div>
            <img src="{{ asset('assets/img/astropandit-icon.webp') }}" alt="Astro Pandit" width="75"
                height="75"
                style="width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3));"
                loading="lazy">
            <span
                style="position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%); background: #673ab7; color: white; font-size: 10px; padding: 2px 10px; border-radius: 10px; white-space: nowrap; font-weight: bold; box-shadow: 0 4px 8px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.3);">Astro
                AI</span>
        </div>
    </div>

    {{-- Astro Chat Window --}}
    <div id="astro-chat-window"
        style="position:fixed; bottom:100px; right:25px; width:350px; max-height:550px; background:white; border-radius:15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display:none; flex-direction:column; z-index:10000; border: 1px solid #e0e0e0; overflow:hidden;">
        <div
            style="background:#673ab7; color:white; padding:15px; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:bold;"><i class="las la-stars"></i> Suyagya Astro AI</span>
            <span onclick="toggleChat()" style="cursor:pointer; font-size:20px;">&times;</span>
        </div>
        <div id="chat-content"
            style="padding:15px; overflow-y:auto; flex-grow:1; background:#f9f9f9; max-height:400px;">
            <div id="chat-messages-container">
                <div class="bot-msg"
                    style="background:#eee; padding:10px; border-radius:10px; margin-bottom:15px; font-size:14px;">
                    Namaste! 🙏 Main aapka digital jyotish hoon. Details bharein:
                </div>
                <div id="ai-response-text" style="white-space: pre-line; font-size: 14px; line-height: 1.6;"></div>
            </div>
            <div id="astro-form">
                <div class="mb-2"><input type="text" id="user_name" class="form-control"
                        placeholder="Aapka Naam"></div>
                <div class="mb-2"><label class="astro-label">📅 Janam Tareekh</label><input type="date"
                        id="dob" class="form-control"></div>
                <div class="mb-2"><label class="astro-label">⏰ Janam Samay</label><input type="time"
                        id="tob" class="form-control"></div>
                <div class="mb-2 position-relative">
                    <label class="astro-label">🏙️ Birth City & State</label>
                    <input type="text" id="birth_city" class="form-control" placeholder="Type your city"
                        oninput="searchCity(this.value)" autocomplete="off">
                    <div id="city-suggestions" class="list-group position-absolute w-100"
                        style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></div>
                </div>
                <input type="hidden" id="lat"><input type="hidden" id="lng">
                <button onclick="processAstroRequest()" id="submit-btn" class="btn btn-primary w-100"
                    style="background:#673ab7; border:none; height: 45px; font-weight: bold;">Kundali Analysis Karein
                    ✨</button>
            </div>
            <div id="chat-loader" style="display:none; text-align:center; padding:20px;">
                <div class="spinner-border text-primary" role="status"></div>
                <p style="font-size:12px; margin-top:10px;">Grahon ki ganana ho rahi hai...</p>
            </div>
            <div id="ai-result-area" style="display:none; margin-top:10px;">
                <div class="input-group mb-2">
                    <input type="text" id="user-followup-msg" class="form-control"
                        placeholder="Kuch aur puchein...">
                    <button onclick="sendFollowup()" class="btn btn-primary" style="background:#673ab7;"><i
                            class="las la-paper-plane"></i></button>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="shareOnWhatsApp()" class="btn btn-success btn-sm flex-grow-1">WhatsApp
                        Share</button>
                    <button onclick="resetChat()" class="btn btn-outline-secondary btn-sm">Reset</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Widget Styles Component --}}
    <style>
        #ai-response-text img {
            max-width: 150px;
            border-radius: 10px;
            margin: 10px 0;
            display: block;
            border: 1px solid #ddd;
        }

        #ai-response-text h3 {
            font-size: 16px;
            color: #673ab7;
            margin-top: 15px;
            font-weight: bold;
        }

        .buy-btn {
            display: inline-block;
            background: #673ab7;
            color: white !important;
            padding: 6px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            margin-top: 5px;
        }

        #chat-content::-webkit-scrollbar {
            width: 4px;
        }

        #chat-content::-webkit-scrollbar-thumb {
            background: #673ab7;
            border-radius: 10px;
        }

        @keyframes astro-bounce {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-12px) scale(1.05);
            }
        }

        .astro-bounce {
            animation: astro-bounce 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                transform: translate(-50%, -50%) scale(0.7);
                opacity: 0.4;
            }

            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 0.9;
            }
        }

        #chat-launcher:hover {
            animation-play-state: paused;
            transform: scale(1.15) !important;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .jump-anim {
            animation: jump 1.5s infinite;
        }

        @keyframes jump {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .lucky-float-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 99;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #d4af37, #f7f1de);
            border: 2px solid #fff;
            border-radius: 50%;
            box-shadow: 2px 2px 3px #999;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: transform 0.3s ease;
            animation: floatIcon 3s ease-in-out infinite;
        }

        .lucky-float-btn[style*="display: block"] {
            display: flex !important;
        }

        .lucky-float-btn:hover {
            transform: scale(1.1);
        }

        .lucky-float-btn i {
            font-size: 24px;
            color: #333;
            margin-bottom: 2px;
            line-height: 1;
        }

        .lucky-float-btn .lucky-text {
            font-size: 8px;
            font-weight: 800;
            color: #333;
            text-transform: uppercase;
            line-height: 1.2;
        }

        @keyframes floatIcon {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 20px;
            right: 25px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 35px;
            box-shadow: 2px 2px 3px #999;
            z-index: 99;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none !important;
            animation: pulse-green 2s infinite;
        }

        .whatsapp-float:hover {
            background-color: #1ebe57;
            transform: scale(1.1);
            color: #fff;
        }

        @keyframes pulse-green {
            0% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
            }
        }

        @media (max-width: 768px) {
            .lucky-float-btn {
                bottom: 25px;
                right: 15px;
                width: 55px;
                height: 55px;
            }

            .lucky-float-btn i {
                font-size: 20px;
            }

            .lucky-float-btn .lucky-text {
                font-size: 7px;
            }

            .whatsapp-float {
                width: 60px;
                height: 60px;
                bottom: 20px;
                right: 25px;
                font-size: 28px;
            }
        }
    </style>

    {{-- ✅ WHATSAPP FLOATING BUTTON --}}
    <a href="https://wa.me/918920471151?text=Hi%20Suyagya%20Team,%20I%20need%20help%20with%20a%20product."
        class="whatsapp-float" target="_blank" rel="noopener noreferrer">
        <i class="lab la-whatsapp"></i>
    </a>

    {{-- Google Tag Manager (noscript) - <body> के तुरंत बाद --}}
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MLSST65C" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>

    {{-- ⚙️ SCRIPTS CORE LOADER: DEFER ATTACHED FOR NON-BLOCKING INITIAL RENDER --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    {{-- 🚀 OPTIMIZED SCRIPTS DELAYED LOADER (GTM & Razorpay) --}}
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                // 1. Load Razorpay Checkout
                var scriptRazor = document.createElement('script');
                scriptRazor.src = "https://checkout.razorpay.com/v1/checkout.js";
                document.body.appendChild(scriptRazor);

                // 2. Load GTM / Analytics (Jo Head se hataya tha)
                var scriptGTM = document.createElement('script');
                scriptGTM.async = true;
                scriptGTM.src = "https://www.googletagmanager.com/gtag/js?id=G-6ECDBEM0VJ";
                document.head.appendChild(scriptGTM);

                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', 'G-6ECDBEM0VJ');
            }, 2500); // 🚀 2.5 Second Delay for Performance Boom!
        });
    </script>

    @yield('scripts')
    <script src="{{ asset('assets/js/custom.js') }}?v={{ filemtime(public_path('assets/js/custom.js')) }}" defer></script>

    {{-- Menu & Lucky Draw Scripts --}}
    <script>
        // Execution directly linked to optimization
        document.addEventListener("DOMContentLoaded", function() {
            init__megaMenu();
            initFooterAccordion();
        });

        function init__megaMenu() {
            const mm = document.querySelector('aside#mega-menu--mobile');
            if (mm) {
                const mm_container = mm.querySelector('.mega__container');
                const mm_screens = mm.querySelectorAll('.mega__screen');
                const mm_subIcons = mm.querySelectorAll('a.btn .btn__icon');
                const mm_subLinks = mm.querySelectorAll('a.btn[aria-label]');
                const mm_subLinks_icon =
                    `<svg width="4" height="7" viewBox="0 0 4 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.88255 3.2234C4.03915 3.37573 4.03915 3.62204 3.88255 3.77275L0.683882 6.88575C0.52728 7.03808 0.274052 7.03808 0.119117 6.88575C-0.0358184 6.73343 -0.0374844 6.48711 0.119117 6.3364L3.03457 3.50051L0.117451 0.662992C-0.0391504 0.510664 -0.0391504 0.264347 0.117451 0.113639C0.274052 -0.0370684 0.52728 -0.0386889 0.682216 0.113639L3.88255 3.2234Z" fill="#221F20"/></svg>`;

                let mm_active_depth = parseInt(mm_container.dataset.activeDepth);
                mm_screens[0].dataset.activeMenu = true;

                mm_subLinks.forEach(item => {
                    const iconSpan = item.querySelector('.btn__icon');
                    if (iconSpan) iconSpan.insertAdjacentHTML("afterbegin", mm_subLinks_icon);
                });

                const screenBackBtns = mm.querySelectorAll('.screen-back-btn');
                screenBackBtns.forEach(backBtn => {
                    backBtn.addEventListener('click', (e) => {
                        if (mm_active_depth > 1) sub__handleActiveDepth(mm_screens, e, mm_container);
                    });
                });

                mm_subIcons.forEach(icon => {
                    icon.addEventListener('click', (e) => sub__handleActiveDepth(mm_screens, e, mm_container));
                });

                mm_subLinks.forEach(link => {
                    link.addEventListener('click', (e) => sub__handleActiveDepth(mm_screens, e, mm_container));
                });

                function sub__handleActiveDepth(screens, event, container) {
                    const target = event.currentTarget || event.target;
                    if (target.classList.contains('screen-back-btn') || target.id == "menu-back") {
                        mm_active_depth -= 1;
                        mm_container.dataset.activeDepth = mm_active_depth;
                        mm_screens.forEach(screen => {
                            let dft_screen_depth = parseInt(screen.dataset.menuDepth);
                            screen.dataset.activeMenu = false;
                            dft_screen_depth >= mm_active_depth ? screen.classList.remove('stacked') : null;
                            dft_screen_depth == mm_active_depth ? screen.dataset.activeMenu = true : null;
                        });
                    } else {
                        event.preventDefault();
                        event.stopPropagation();
                        mm_active_depth += 1;
                        mm_container.dataset.activeDepth = mm_active_depth;

                        mm_screens.forEach(screen => {
                            let dft_screen_depth = parseInt(screen.dataset.menuDepth);
                            screen.dataset.activeMenu = false;
                            dft_screen_depth < mm_active_depth ? screen.classList.add('stacked') : null;
                            dft_screen_depth == mm_active_depth ? screen.dataset.activeMenu = true : null;
                        });

                        let link = target.closest('a.btn') || target;
                        let link_menu = link.getAttribute('aria-label');
                        container.dataset.activeNav = link_menu;

                        let dft_active_screen = container.querySelector('.mega__screen[data-active-menu="true"]');
                        let dft_active_screen__navs = dft_active_screen.querySelectorAll('nav');

                        dft_active_screen__navs.forEach(nav => {
                            nav.classList.add('hidden');
                            nav.style.display = 'none';
                        });

                        let dft_active_nav = dft_active_screen.querySelector(`nav[aria-labelledby="${link_menu}"]`);
                        if (dft_active_nav) {
                            dft_active_nav.classList.remove('hidden');
                            dft_active_nav.style.display = 'block';
                        }
                    }
                }
            }
        }

        const menuButton = document.getElementById('menuButton');
        const megaMenu = document.getElementById('mega-menu--mobile');
        if (menuButton) {
            menuButton.addEventListener('click', () => {
                megaMenu.classList.toggle('active');
                menuButton.classList.toggle('active');
            });
        }

        function initFooterAccordion() {
            const headings = document.querySelectorAll('h4.footer-heading');
            headings.forEach(heading => {
                heading.addEventListener('click', () => {
                    if (window.innerWidth > 767) return;
                    const list = heading.nextElementSibling;
                    if (!list || !list.classList.contains('footer-list')) return;
                    heading.classList.toggle('active');
                    list.classList.toggle('active');
                });
            });
        }

        window.appRoutes = {
            getCoupons: "{{ route('get.coupons') }}",
            applyCoupon: "{{ route('apply.coupon') }}"
        };
        window.csrfToken = "{{ csrf_token() }}";
    </script>

    {{-- LUCKY DRAW INITIALIZATION --}}
    <script>
        $(document).ready(function() {
                @auth
                let dbHasActiveCoupon =
                    {{ \App\Models\UserCoupon::where('user_id', Auth::id())->where('is_used', 0)->exists() ? 'true' : 'false' }};
                if (!dbHasActiveCoupon) {
                    if (localStorage.getItem('gaming_coupon_amount')) {
                        localStorage.removeItem('gaming_coupon_amount');
                        localStorage.removeItem('gaming_coupon_code');
                    }
                }
            @endauth
            let userAlreadyPlayed =
                {{ Auth::check() && \App\Models\UserCoupon::where('user_id', Auth::id())->where('is_used', 0)->exists() ? 'true' : 'false' }};
            let shouldOpenGame = localStorage.getItem('openGameAfterLogin');
            let gameClosedByUser = localStorage.getItem('luckyDrawClosed');

            if (gameClosedByUser === 'true' && !userAlreadyPlayed) {
                $('#luckyFloatingIcon').fadeIn();
            } else if (shouldOpenGame === 'true') {
                @auth $('#gameModal').modal('show');
                localStorage.removeItem('openGameAfterLogin');
            @endauth
        }
        else {
            if (!userAlreadyPlayed) {
                setTimeout(() => {
                    $('#gameModal').modal('show');
                }, 3000);
            }
        }
        });

        function closeLuckyDraw() {
            $('#gameModal').modal('hide');
            localStorage.setItem('luckyDrawClosed', 'true');
            $('#luckyFloatingIcon').fadeIn();
        }

        function reopenLuckyDraw() {
            $('#gameModal').modal('show');
            $('#luckyFloatingIcon').fadeOut();
        }

        function playGuest() {
            $('#guest-view .chit-card i').addClass('d-none');
            $('#guest-result').removeClass('d-none');
            setTimeout(() => {
                $('#guest-msg').removeClass('d-none');
            }, 600);
        }

        function openLoginForGame() {
            $('#gameModal').modal('hide');
            localStorage.setItem('openGameAfterLogin', 'true');
            $('#login_modal').modal('show');
        }

        let playing = false;

        function playUser(element) {
            if (playing) return;
            playing = true;
            $(element).css('transform', 'scale(0.9)');
            $.ajax({
                url: "{{ route('game.play') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status === 'success' || res.status === 'already_played') {
                        localStorage.removeItem('luckyDrawClosed');
                        $('#luckyFloatingIcon').hide();
                        $(element).find('.chit-icon').addClass('d-none');
                        $(element).find('.prize-amt').text('₹' + res.amount);
                        $(element).find('.chit-result').removeClass('d-none');
                        $(element).css({
                            'transform': 'scale(1.1)',
                            'border': '2px solid #28a745',
                            'background': '#e8f5e9'
                        });
                        localStorage.setItem('gaming_coupon_amount', res.amount);
                        localStorage.setItem('gaming_coupon_code', res.code);
                        setTimeout(() => {
                            $('#final-amt').text(res.amount);
                            $('#win-msg').removeClass('d-none');
                            setTimeout(() => {
                                $('#gameModal').modal('hide');
                            }, 2500);
                        }, 600);
                        $('.chit-card').not(element).css('opacity', 0.3).attr('onclick', '');
                    }
                }
            });
        }
    </script>

    {{-- URL parameter cleanup --}}
    <script>
        (function() {
            var url = new URL(window.location.href);
            if (url.searchParams.has('srsltid')) {
                url.searchParams.delete('srsltid');
                var cleanUrl = url.pathname + (url.search ? url.search : '');
                window.history.replaceState({}, document.title, cleanUrl);
            }
        })();
    </script>

    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
                console.log('Suyagya PWA ServiceWorker registered!');
            });
        }
    </script>

    {{-- PWA Install Popup --}}
    <script>
        let deferredPrompt;
        const pwaPopup = document.getElementById('pwa-install-popup');

        function isMobileUser() {
            return /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const dismissed = sessionStorage.getItem('pwa-popup-dismissed');
            if (isMobileUser() && !window.matchMedia('(display-mode: standalone)').matches && dismissed !==
                'true') {
                setTimeout(() => {
                    if (pwaPopup) pwaPopup.style.display = 'block';
                }, 3000);
            }
        });

        function triggerInstall() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        hidePwaPopup();
                    }
                    deferredPrompt = null;
                });
            } else {
                alert(
                    "Suyagya ऐप इंस्टॉल करने के लिए ब्राउज़र के 'Share' बटन पर क्लिक करें और 'Add to Home Screen' चुनें।"
                );
            }
        }

        function hidePwaPopup() {
            if (pwaPopup) pwaPopup.style.display = 'none';
            sessionStorage.setItem('pwa-popup-dismissed', 'true');
        }
    </script>
    @include('frontend.includes.offer_popup')
</body>

</html>
