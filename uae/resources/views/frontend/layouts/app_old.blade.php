<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="is-logged-in" content="{{ Auth::check() ? '1' : '0' }}">
    <title>@yield('title', 'Suyagya | Authentic Spiritual Products')</title>

    {{-- Favicon, Meta Tags, etc. --}}
    {{-- ... (Keep existing meta tags from original HTML) ... --}}

    {{-- 🔗 CSS Files --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Ensure Merriweather is loaded for premium look --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap"
        rel="stylesheet">

    {{-- 💡 BOOTSTRAP 5 CSS CDN (MANDATORY) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css" />


    {{-- 💡 Premium Custom Styles --}}
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @yield('styles')
</head>

{{-- 🎨 Body uses the creamy background set in custom.css --}}

<body>
    <div class="aiz-main-wrapper d-flex flex-column" style="background-color: var(--light) !important;">

        {{-- ⬆️ Header --}}
        @include('frontend.includes.header')

        {{-- 🏠 Page Content --}}
        <main class="flex-grow-1"> {{-- <<< KEY FIX: flex-grow-1 added to <main> --}}
            @yield('content')
        </main>

        {{-- 🛒 Include Side Cart --}}
        @include('frontend.includes.side_cart')

        {{-- Footer ke upar ya body tag band hone se pehle --}}
        @include('frontend.modals.checkout_modal')


        {{-- ⬇️ Footer --}}
        @include('frontend.includes.footer')

    </div>

    {{-- ⚙️ SCRIPTS --}}
    {{-- 1. jQuery (Must be first) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- 2. Bootstrap Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    {{-- 3. Slick Slider (Depends on jQuery) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    {{-- 4. Page Specific Scripts --}}
    @yield('scripts')

    {{-- 5. Custom JS (Main Logic) --}}
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>

</html>
