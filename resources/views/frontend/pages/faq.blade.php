@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="font-heading fw-bold">Help & FAQs</h1>
        <p class="text-muted">Find answers to your questions related to products, shipping, and more.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- 1. GENERAL FAQS (Shipping, Returns, etc.) --}}
            @if($generalFaqs->count() > 0)
                <div class="mb-5">
                    <h4 class="fw-bold mb-3 text-dark border-bottom pb-2">General Queries</h4>
                    @include('frontend.includes.faq_accordion', ['faqs' => $generalFaqs, 'idSuffix' => 'gen'])
                </div>
            @endif

            {{-- 2. BRAND SPECIFIC FAQS (From Home Settings) --}}
            @if(!empty($homeSettings->faq_content))
                <div class="mb-5">
                    <h4 class="fw-bold mb-3 text-dark border-bottom pb-2">About Suyagya Products</h4>
                    @include('frontend.includes.faq_accordion', ['faqs' => $homeSettings->faq_content, 'idSuffix' => 'brand'])
                </div>
            @endif

            {{-- 3. PRODUCT SPECIFIC FAQS --}}
            @if($productsWithFaqs->count() > 0)
                <div class="mt-5 pt-4 border-top">
                    <h4 class="fw-bold mb-4 text-center text-dark">Product Specific Questions</h4>

                    @foreach($productsWithFaqs as $product)
                        <div class="card mb-4 border rounded-0" style="background-color: transparent;">
                            {{-- Product Header --}}
                            <div class="card-header d-flex align-items-center gap-3">
                                <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}"
                                     class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">

                                <h6 class="mb-0 fw-bold">
                                    <a href="{{ route('product.detail', $product->slug) }}" class="text-dark text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </h6>
                            </div>

                            {{-- Product FAQs Accordion --}}
                            <div class="card-body p-0">
                                {{--
                                    Hume 'idSuffix' unique dena padega taaki IDs clash na karein.
                                    Isliye hum 'prod_' + product ID use kar rahe hain.
                                --}}
                                @include('frontend.includes.faq_accordion', [
                                    'faqs' => $product->faq_content,
                                    'idSuffix' => 'prod_' . $product->id
                                ])
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
