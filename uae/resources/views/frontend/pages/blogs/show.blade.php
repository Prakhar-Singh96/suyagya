@extends('frontend.layouts.app')

{{-- SEO Meta Tags --}}
@section('title', $meta['title'])
@section('meta_description', $meta['description'])
@section('meta_keywords', $meta['keywords'])
@section('og_image', $meta['image'])

@section('content')

    <div class="container py-5">
        <div class="row g-5">

            {{-- LEFT COLUMN: Main Blog Content --}}
            <div class="col-lg-8">
                <article class="p-0">

                    {{-- Heading --}}
                    <h1 class="fw-bold font-heading mb-3 text-dark">{{ $blog->title }}</h1>

                    {{-- 🔥 MAIN CONTENT (HTML) --}}
                    <div class="blog-content text-dark" style="line-height: 1.8; font-size: 1.1rem;">
                        {!! $blog->content !!}
                    </div>

                    {{-- Share Buttons --}}
                    <div class="d-flex align-items-center">
                        <span class="fw-bold me-3">Share:</span>
                        <a href="https://api.whatsapp.com/send?text={{ $blog->title }} {{ url()->current() }}"
                            target="_blank" class="text-success fs-3 me-3"><i class="lab la-whatsapp"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank"
                            class="text-primary fs-3 me-3"><i class="lab la-facebook"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $blog->title }}"
                            target="_blank" class="text-info fs-3"><i class="lab la-twitter"></i></a>
                    </div>

                </article>
            </div>

            {{-- RIGHT COLUMN: Sidebar --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="z-index:10;">

                    {{-- Recent Blogs Widget --}}
                    <div class="card border rounded shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Recent Articles</h6>
                        </div>
                        <div class="card-body">
                            @foreach ($recentBlogs as $recent)
                                <div class="d-flex align-items-center mb-3">
                                    <a href="{{ route('blogs.show', $recent->slug) }}" class="flex-shrink-0">
                                        <img src="{{ asset($recent->main_image) }}" class="rounded object-fit-cover"
                                            width="70" height="70" alt="{{ $recent->title }}">
                                    </a>
                                    <div class="ms-3">
                                        <h6 class="mb-1" style="font-size: 14px; line-height: 1.4;">
                                            <a href="{{ route('blogs.show', $recent->slug) }}"
                                                class="text-dark text-decoration-none hover-primary">
                                                {{ Str::limit($recent->title, 45) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted"
                                            style="font-size: 11px;">{{ $recent->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Optional: Shop Banner --}}
                    <div class="rounded overflow-hidden shadow-sm mt-4 position-relative">

                        {{-- Link Wrapper --}}
                        <a href="{{ route('products.category', 'rudraksh') }}"
                            class="d-block text-decoration-none text-dark">

                            {{-- Image --}}
                            <img src="{{ asset('uploads/categories/icons/1764926950_6932a5e63a67b.webp') }}"
                                alt="Shop Rudraksha" class="img-fluid w-100 transition-zoom">

                            {{-- 🔥 Shop Now Button Overlay --}}
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-center"
                                style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">

                                <span class="btn fw-bold text-uppercase px-4 py-2 shadow-sm"
                                    style="font-size: 0.85rem; letter-spacing: 1px; background-color: #c09867;">
                                    Shop Now <i class="las la-arrow-right ms-1"></i>
                                </span>

                            </div>
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Custom CSS for Content Formatting --}}
    <style>
        /* Make images inside content responsive */
        .blog-content img {
            max-width: 100%;
            height: auto !important;
            border-radius: 8px;
            margin: 20px 0;
        }

        .blog-content h2,
        .blog-content h3 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
            font-weight: bold;
        }

        .hover-primary:hover {
            color: #ff6f00 !important;
        }
    </style>

@endsection
