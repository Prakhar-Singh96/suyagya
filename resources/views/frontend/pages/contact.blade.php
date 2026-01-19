@extends('frontend.layouts.app')

@section('content')

{{-- 1. HERO BANNER --}}
<div class="position-relative w-100">
    {{-- Replace with a Banner Image --}}
    <img src="{{ asset('assets/img/contact.webp') }}" alt="Contact Banner" class="w-100 object-fit-cover" style="height: 600px; object-position: center top;">
    <div class="position-absolute bottom-0 end-0 p-5 mb-4 text-end">
        {{-- <h1 class="display-2 fw-bold text-white" style="font-family: 'Merriweather', serif; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">CONTACT US</h1> --}}
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">

        {{-- 2. LEFT: ADDRESS INFO --}}
        <div class="col-lg-6">
            <h3 class="mb-4" style="font-family: 'Merriweather', serif;">Reach Us Directly</h3>

            <div class="mb-4">
                <h6 class="fw-bold text-uppercase small text-muted mb-1">Suyagya</h6>
                <p class="text-muted mb-1">J-3/356, DDA,</p>
                <p class="text-muted mb-1">Kalkaji, India</p>
                <p class="text-muted mb-1">New Delhi - 110019.</p>
            </div>

            <div class="mb-4">
                <p class="mb-1"><strong class="text-dark">Email Id:</strong> <a href="mailto:info@suyagya.com" class="text-decoration-none text-muted">info@suyagya.com</a></p>
                <p class="mb-1"><strong class="text-dark">Phone no:</strong> <a href="tel:+917692005006" class="text-decoration-none text-muted">+91 7692 005 006</a></p>
            </div>
        </div>

        {{-- 3. RIGHT: IMAGE --}}
        <div class="col-lg-6">
            <div class="text-center">
                {{-- Replace with a square product image --}}
                <img src="{{ asset('assets/img/contact-us.webp') }}" alt="Contact Visual" class="img-fluid shadow-sm" style="max-height: 350px; border-radius: 4px;">
            </div>
        </div>
    </div>

    {{-- 4. CONTACT FORM --}}
    <div class="row mt-5">
        <div class="col-12 text-center mb-4">
            <p class="text-muted small">For business related bulk orders or queries, please contact us here.</p>
            <h4 style="font-family: 'Merriweather', serif;">Send a message</h4>
        </div>

        <div class="col-lg-8 mx-auto">
            <form action="#" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control rounded-0 p-3" placeholder="Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="email" class="form-control rounded-0 p-3" placeholder="Email" required>
                    </div>
                    <div class="col-12">
                        <input type="tel" class="form-control rounded-0 p-3" placeholder="Phone number">
                    </div>
                    <div class="col-12">
                        <textarea class="form-control rounded-0 p-3" rows="5" placeholder="Message"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark rounded-0 px-5 py-2 text-uppercase" style="letter-spacing: 1px;">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
