@extends('frontend.layouts.app')

@section('content')

{{-- 1. HERO BANNER --}}
<div class="position-relative w-100">

    {{-- Dark Overlay for better text readability --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6)); z-index: 1;"></div>

    {{-- Banner Image --}}
    <img src="{{ asset('assets/img/about.webp') }}" alt="About Banner"
         class="w-100 object-fit-cover"
         style="height: 600px; object-position: center top;">
         {{-- Height 600px mobile/desktop dono ke liye balanced hai --}}

    {{-- Text Content --}}
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white w-100 px-3" style="z-index: 2;">

        {{-- Optional: Om Icon or Decoration --}}
        <div class="mb-3">
            <i class="las la-om text-warning" style="font-size: 3rem; opacity: 0.8;"></i>
        </div>

        <h1 class="display-4 fw-bold mb-4" style="font-family: 'Merriweather', serif; text-shadow: 2px 2px 8px rgba(0,0,0,0.6);">
            A Spiritual Calling: <br>
            <span style="color: #f8e5b8;">How Suyagya Was Born</span>
        </h1>

        <div class="mx-auto" style="max-width: 800px;">
            <p class="lead fs-5" style="line-height: 1.8; font-weight: 300; text-shadow: 1px 1px 4px rgba(0,0,0,0.5);">
                Behind every journey lies a profound moment of inspiration. For
                <strong class="fw-bold text-warning" style="letter-spacing: 0.5px;">Mr. Ram Mittal</strong>,
                that moment came during a visit to the
                <span class="text-white fw-bold">silent peaks of the Himalayas</span>.
                It was there, in the lap of nature and ancient divinity, that he felt a deep urge to reconnect the world with its spiritual roots—giving birth to
                <strong class="text-warning text-uppercase" style="text-decoration: underline; text-decoration-color: #fff;">Suyagya</strong> was born.
            </p>
        </div>

    </div>
</div>

{{-- 2. GOLD STRIP --}}
<div class="py-4 text-center text-white" style="background-color: #c09867;">
    <div class="container">
        <h3 class="mb-0 fw-dark" style="letter-spacing: 1px;">Suyagya: A Fusion of Love and Abundance</h3>
        <small>The name embodies the essence of who we are and what we stand for.</small>
    </div>
</div>

{{-- 3. VISION & SHOP IMAGE --}}
<div class="container py-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <h2 class="mb-4" style="color: #333; font-family: 'Merriweather', serif;">A Grand Vision Reimagined</h2>
            <p class="text-muted" style="line-height: 1.8;">
                Suyagya is the culmination of many stories, and it all began with a dream. We operate under values that prioritize purity and devotion above all else.
            </p>
            <p class="text-muted" style="line-height: 1.8;">
                Today, Suyagya stands as a testament to spiritual wellness. The group has ventured into diverse sectors, but our core remains rooted in providing authentic spiritual products like Rudraksha, Gemstones, and Pooja items.
            </p>
            <p class="text-muted" style="line-height: 1.8;">
                We are dedicated to building Suyagya into the most premium and trusted name in the realm of spiritual jewelry and wellness.
            </p>
        </div>
        <div class="col-lg-6">
            {{-- Replace with your Shop/Office Image --}}
            <img src="{{ asset('assets/img/shop-image.webp') }}" alt="Our Shop" class="img-fluid rounded shadow-sm w-100 grayscale-img">
        </div>
    </div>
</div>

{{-- 4. TEAM IMAGE --}}
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            {{-- Replace with Team Image --}}
            <img src="{{ asset('assets/img/team-image.png') }}" alt="Our Team" class="img-fluid rounded w-100">
        </div>
    </div>
</div>

{{-- 5. VISION & MISSION TEXT --}}
<div class="container py-5 text-center" style="max-width: 800px;">
    <div class="mb-5">
        <h3 class="mb-3" style="color: #333; font-family: 'Merriweather', serif;">Our Vision</h3>
        <p class="text-muted">
            We believe that jewelry is more than an accessory; it is a reflection of your innermost beliefs.
            Our vision is to create exquisite and spiritually inspired jewelry for people from all walks of life.
        </p>
    </div>

    <div class="mb-5">
        <h3 class="mb-3 text-uppercase" style="color: #333; font-family: 'Merriweather', serif;">Our Mission</h3>
        <p class="text-muted">
            At the core of our mission is the dream to shatter the myth that exquisite jewelry is to be reserved for special occasions.
            We believe every day is special, and your style must reflect that.
        </p>
    </div>
</div>

<style>
    .grayscale-img {
        filter: grayscale(100%);
        transition: filter 0.3s;
    }
    .grayscale-img:hover {
        filter: grayscale(0%);
    }
</style>

@endsection
