@extends('frontend.layouts.app')

{{-- SEO Title --}}
@section('title', 'Page Not Found | Suyagya')

@section('content')
<section class="py-5 bg-light" style="min-height: 60vh; display: flex; align-items: center;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">

                {{-- 🖼️ Error Image --}}
                {{-- Make sure you have an image at public/assets/images/404.png --}}
                {{-- Agar image nahi hai to ye niche wala <img> hata dein aur sirf Icon use karein --}}
                <img src="{{ asset('assets/images/404-error.png') }}"
                     alt="Page Not Found"
                     class="img-fluid mb-4"
                     style="max-width: 250px;"
                     onerror="this.style.display='none'"> {{-- Agar img na mile to hide ho jaye --}}

                {{-- 🚫 Icon (Fallback) --}}
                <div class="mb-3 text-warning">
                    <i class="las la-exclamation-circle" style="font-size: 80px;"></i>
                </div>

                <h1 class="display-4 fw-bold text-dark">404</h1>
                <h2 class="h4 mb-3">Oops! Page Not Found</h2>

                <p class="text-muted mb-4">
                    The page you are looking for might have been removed, had its name changed,
                    or is temporarily unavailable.
                </p>

                <div class="d-flex justify-content-center gap-3">
                    {{-- 🔙 Back Button --}}
                    <a href="{{ url('/') }}" class="btn btn-earthy rounded-pill px-4 py-2">
                        <i class="las la-home me-2"></i> Back to Home
                    </a>

                    {{-- 📞 Contact Button --}}
                    <a href="{{ url('/contact-us') }}" class="btn btn-outline-dark rounded-pill px-4 py-2">
                        Contact Support
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
