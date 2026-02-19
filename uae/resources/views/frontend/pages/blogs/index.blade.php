@extends('frontend.layouts.app')

@section('title', 'Our Latest Blogs & Insights | Suyagya')

@section('content')

{{-- 1. Banner Section --}}
<div class="py-5 bg-dark text-white text-center" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ asset('assets/img/blog-banner.jpg') }}') center/cover;">
    <div class="container py-4">
        <h1 class="display-4 fw-bold font-heading">Spiritual Wisdom</h1>
        <p class="lead text-light">Explore articles on Rudraksha, Gemstones, and Vedic Rituals.</p>
    </div>
</div>

{{-- 2. Blog Grid --}}
<section class="py-5" style="background-color: #ffff;">
    <div class="container">
        <div class="row g-4">
            @foreach($blogs as $blog)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        {{-- Image --}}
                        <div class="overflow-hidden position-relative" style="height: 220px;">
                            <a href="{{ route('blogs.show', $blog->slug) }}">
                                <img src="{{ asset($blog->main_image) }}"
                                     alt="{{ $blog->img_alt ?? $blog->title }}"
                                     class="img-fluid w-100 h-100 object-fit-cover transition-zoom">
                            </a>
                        </div>

                        {{-- Content --}}
                        <div class="card-body p-4">
                            <div class="small text-muted mb-2">
                                <i class="las la-calendar"></i> {{ $blog->created_at->format('d M, Y') }}
                            </div>
                            <h5 class="card-title fw-bold font-heading">
                                <a href="{{ route('blogs.show', $blog->slug) }}" class="text-dark text-decoration-none">
                                    {{ Str::limit($blog->title, 55) }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small">
                                {{-- HTML strip karke sirf text dikhayenge --}}
                                {{ Str::limit(strip_tags($blog->content), 100) }}
                            </p>
                        </div>

                        {{-- Footer Button --}}
                        <div class="card-footer bg-white border-0 p-4 pt-0">
                            <a href="{{ route('blogs.show', $blog->slug) }}" class="text-primary fw-bold text-decoration-none text-uppercase small">
                                Read More <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination Links --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $blogs->links() }}
        </div>
    </div>
</section>

<style>
    .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .transition-zoom { transition: transform 0.5s ease; }
    .card:hover .transition-zoom { transform: scale(1.05); }
</style>

@endsection
