@extends('frontend.layouts.app')

@section('title', 'Return & Refund Policy | Suyagya')

@section('styles')
<style>
    /* ✨ Refund Policy Page Styles */
    .policy-header {
        background-color: #f7f1de;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
        border-bottom: 4px solid #d4af37;
    }

    .policy-header h1 {
        font-family: 'Merriweather', serif;
        font-weight: 700;
        color: #3e2723;
    }

    .policy-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 30px;
        height: 100%; /* For equal height */
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        transition: transform 0.3s ease;
    }

    .policy-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        border-color: #d4af37;
    }

    .card-title {
        font-family: 'Merriweather', serif;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.25rem;
    }

    /* Green Title for Eligible */
    .text-success-custom { color: #2e7d32; }
    /* Red Title for Non-Eligible */
    .text-danger-custom { color: #c62828; }

    .custom-list {
        list-style: none;
        padding-left: 0;
    }

    .custom-list li {
        position: relative;
        padding-left: 30px;
        margin-bottom: 12px;
        color: #555;
        line-height: 1.6;
    }

    /* Custom Check/Cross Icons */
    .list-check li::before {
        content: '\f00c'; /* Check Icon */
        font-family: 'Line Awesome Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        top: 2px;
        color: #2e7d32;
        font-size: 18px;
    }

    .list-cross li::before {
        content: '\f00d'; /* Cross Icon */
        font-family: 'Line Awesome Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        top: 2px;
        color: #c62828;
        font-size: 18px;
    }

    .list-arrow li::before {
        content: '\f105'; /* Arrow Icon */
        font-family: 'Line Awesome Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        top: 2px;
        color: #d4af37;
        font-size: 18px;
    }

    .step-box {
        background-color: #fffbf2;
        border: 1px dashed #d4af37;
        padding: 20px;
        border-radius: 8px;
    }

    .contact-footer {
        background-color: #3e2723;
        color: #fff;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        margin-top: 40px;
    }

    .contact-footer a { color: #f7f1de; text-decoration: underline; }
</style>
@endsection

@section('content')

{{-- 📜 Header --}}
<section class="policy-header">
    <div class="container">
        <h1>Return & Refund Policy</h1>
        <p class="text-muted mt-2">Authenticity & Customer Satisfaction is our priority.</p>
    </div>
</section>

<div class="container pb-5">

    {{-- Intro Text --}}
    <div class="text-center mb-5 px-lg-5">
        <p class="lead text-dark">
            At <strong>Suyagya.com</strong>, we offer spiritual and religious products with care and authenticity. However, if there’s an issue with your order, please review our terms below.
        </p>
    </div>

    {{-- ✅ Eligible vs ❌ Non-Eligible (Side by Side) --}}
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="policy-card border-top border-success border-4">
                <h3 class="card-title text-success-custom">
                    <i class="las la-check-circle fs-2"></i> Eligible Returns
                </h3>
                <p class="small text-muted mb-3">You may request a return/replacement ONLY if:</p>
                <ul class="custom-list list-check">
                    <li>You received a <strong>damaged, defective, or wrong</strong> product.</li>
                    <li>The request is raised within <strong>48 hours</strong> of receiving the order.</li>
                    <li>The item is <strong>unused</strong>, in original condition with tags/packaging intact.</li>
                    <li><strong>Unboxing video & photos</strong> are provided for verification (Highly Recommended).</li>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="policy-card border-top border-danger border-4">
                <h3 class="card-title text-danger-custom">
                    <i class="las la-ban fs-2"></i> Non-Returnable Items
                </h3>
                <p class="small text-muted mb-3">The following items cannot be returned:</p>
                <ul class="custom-list list-cross">
                    <li>Used or worn spiritual items (due to sanctity reasons).</li>
                    <li>Opened Rudraksha, Yantras, or energized products.</li>
                    <li><strong>Custom or personalized orders</strong> (e.g., name-based astrology items).</li>
                    <li>Free gifts or promotional items.</li>
                    <li>Products returned without original packaging.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- 🔄 Replacement & Refund Process --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-4">
            <div class="policy-card">
                <h3 class="card-title text-dark"><i class="las la-sync-alt text-warning"></i> Replacement</h3>
                <p>If your item qualifies for a return, we will offer:</p>
                <ul class="custom-list list-arrow">
                    <li>A replacement of the same item (subject to stock).</li>
                    <li>An exchange with another product of similar value.</li>
                    <li>Refund to original method (only in rare cases).</li>
                </ul>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="policy-card">
                <h3 class="card-title text-dark"><i class="las la-wallet text-warning"></i> Refund Process</h3>
                <ul class="custom-list list-arrow">
                    <li>Refunds are initiated within <strong>7 working days</strong> of approval.</li>
                    <li>Amount is credited to the <strong>original payment method</strong>.</li>
                    <li>For <strong>COD orders</strong>, we may issue store credit or request bank details for transfer.</li>
                </ul>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="policy-card">
                <h3 class="card-title text-dark"><i class="las la-truck text-warning"></i> Return Pickup</h3>
                <ul class="custom-list list-arrow">
                    <li>We arrange pickup via our courier partners in serviceable areas.</li>
                    <li>If pickup isn't available, you may be asked to <strong>self-ship</strong>.</li>
                    <li>Shipping costs for valid returns (damaged/wrong items) will be reimbursed.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- 📧 How to Request --}}
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="step-box shadow-sm">
                <h3 class="text-center fw-bold mb-4 font-heading" style="font-family: 'Merriweather', serif;">
                    <i class="las la-envelope-open-text text-warning"></i> How to Request a Return?
                </h3>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="fw-bold fs-5 mb-1">Step 1</div>
                        <p class="text-muted">Email us at <a href="mailto:info@suyagya.com" class="fw-bold text-dark">info@suyagya.com</a></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="fw-bold fs-5 mb-1">Step 2</div>
                        <p class="text-muted">Include <strong>Order ID</strong> & Attach <strong>Photos/Video</strong> of the issue.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="fw-bold fs-5 mb-1">Step 3</div>
                        <p class="text-muted">Our team will respond within <strong>2 business days</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 🚫 Cancellation Policy --}}
    <div class="mb-5">
        <h3 class="font-heading fw-bold mb-3"><i class="las la-times-circle text-danger"></i> Cancellation Policy</h3>
        <div class="bg-light p-4 rounded border-start border-danger border-4">
            <ul class="custom-list list-arrow mb-0">
                <li>Orders can be cancelled <strong>only before they are shipped</strong>.</li>
                <li>Once the order is dispatched/shipped, it <strong>cannot be cancelled</strong>.</li>
                <li>Refunds for cancelled prepaid orders will be processed within <strong>5–7 working days</strong>.</li>
            </ul>
        </div>
    </div>

    {{-- ⚠️ Disclaimer Note --}}
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="las la-exclamation-triangle fs-1 me-3"></i>
        <div>
            <strong>Note:</strong> Suyagya reserves the right to approve or reject any return/refund request at its sole discretion. Since our products are spiritual in nature, we request customers to read product details carefully before purchasing.
        </div>
    </div>

    {{-- 📞 Contact Footer --}}
    <div class="contact-footer">
        <h3 style="font-family: 'Merriweather', serif;">Still have questions?</h3>
        <p class="mb-4">We are here to help you.</p>
        <div class="row justify-content-center">
            <div class="col-md-5">
                <i class="las la-envelope fs-3 mb-2"></i><br>
                <a href="mailto:info@suyagya.com" class="fs-5">info@suyagya.com</a>
            </div>
            <div class="col-md-5">
                <i class="las la-map-marker fs-3 mb-2"></i><br>
                <span>J-3/356, DDA, Kalkaji, New Delhi - 110019</span>
            </div>
        </div>
        <div class="mt-4 pt-3 border-top border-secondary small">
            Thank you for trusting <strong>Suyagya.com</strong>
        </div>
    </div>

</div>

@endsection
