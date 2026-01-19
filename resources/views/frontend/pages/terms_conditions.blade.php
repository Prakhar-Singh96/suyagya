@extends('frontend.layouts.app')

@section('title', 'Terms & Conditions | Suyagya')

@section('styles')
<style>
    /* ✨ Terms & Conditions Page Styles */
    .terms-header {
        background-color: #f7f1de; /* Cream Background matching your theme */
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
    }

    .terms-header h1 {
        font-family: 'Merriweather', serif;
        font-weight: 700;
        color: #3e2723;
        margin-bottom: 10px;
    }

    .terms-content {
        max-width: 1000px;
        margin: 0 auto;
    }

    .terms-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        transition: transform 0.2s ease;
    }

    .terms-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.05);
        border-color: #d4af37; /* Gold Border on Hover */
    }

    .section-title {
        font-family: 'Merriweather', serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 2px solid #f7f1de;
        padding-bottom: 10px;
    }

    .section-title i {
        color: #d4af37; /* Gold Icon */
        font-size: 1.4rem;
    }

    .terms-text {
        color: #555;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    .terms-text ul {
        padding-left: 20px;
        margin-top: 10px;
    }

    .terms-text li {
        margin-bottom: 8px;
    }

    .contact-box {
        background-color: #3e2723;
        color: #fff;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        margin-top: 40px;
    }

    .contact-box h3 {
        font-family: 'Merriweather', serif;
        margin-bottom: 20px;
    }

    .contact-box a {
        color: #f7f1de;
        text-decoration: underline;
        font-weight: bold;
    }

    .last-updated {
        font-size: 0.85rem;
        color: #888;
        text-align: center;
        margin-top: -30px;
        margin-bottom: 30px;
    }
</style>
@endsection

@section('content')

{{-- 📜 Header Section --}}
<section class="terms-header">
    <div class="container">
        <h1>Terms & Conditions</h1>
        <p class="text-muted">Please read these terms carefully before using our services.</p>
    </div>
</section>

<div class="container pb-5">

    <div class="last-updated">
        Last Updated: {{ date('F d, Y') }}
    </div>

    <div class="terms-content">

        {{-- Intro --}}
        <div class="mb-5 text-center px-lg-5">
            <p class="lead text-dark">
                Welcome to <strong>Suyagya.com</strong>. By accessing or using our website, you agree to be bound by the following Terms and Conditions. We represent authenticity and devotion in every product we offer.
            </p>
        </div>

        {{-- 1. About Us --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-info-circle"></i> 1. About Us</h2>
            <div class="terms-text">
                <p><strong>Website:</strong> <a href="https://suyagya.com" class="text-dark fw-bold">https://suyagya.com</a></p>
                <p><strong>Operated & Managed By:</strong> Suyagya Admin</p>
                <p><strong>Email:</strong> info@suyagya.com</p>
                <p><strong>Address:</strong> J-3/356, DDA Flats, Kalkaji, Delhi, India 110019</p>
            </div>
        </div>

        {{-- 2. Products & Services --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-om"></i> 2. Products & Services</h2>
            <div class="terms-text">
                <p>We offer a curated selection of spiritual and religious products designed to bring peace and positivity to your life. Our collection includes, but is not limited to:</p>
                <ul>
                    <li>Rudraksha (Authentic beads)</li>
                    <li>Yantras (Geometry of the Divine)</li>
                    <li>Rashi Bracelets (Zodiac specific)</li>
                    <li>Pooja items & Spiritual gifts</li>
                </ul>
                <p class="small text-muted mt-2">
                    <em>Note: All product descriptions, images, and pricing are displayed as accurately as possible. However, we do not guarantee that all product details will be completely error-free.</em>
                </p>
            </div>
        </div>

        {{-- 3. Orders & Payments --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-wallet"></i> 3. Orders & Payments</h2>
            <div class="terms-text">
                <ul>
                    <li>Orders can be securely placed via our checkout system.</li>
                    <li>We accept payments via trusted third-party gateways like <strong>Razorpay, UPI, Debit/Credit Card, and Net Banking</strong>.</li>
                    <li><strong>Security:</strong> We do not store your payment or card details on our servers.</li>
                    <li>Once an order is placed, it cannot be modified. Please review your cart carefully before checkout.</li>
                </ul>
            </div>
        </div>

        {{-- 4. Shipping & Delivery --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-shipping-fast"></i> 4. Shipping & Delivery</h2>
            <div class="terms-text">
                <p>We proudly deliver across India.</p>
                <ul>
                    <li><strong>Standard Delivery Time:</strong> 3–10 working days (depending on your location).</li>
                    <li><strong>Tracking:</strong> You will receive tracking details via email/SMS once your order is dispatched.</li>
                    <li>Delays caused by natural calamities, strikes, or courier partner issues are beyond our control, though we will assist you in tracking your package.</li>
                </ul>
            </div>
        </div>

        {{-- 5. Return & Replacement Policy --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-exchange-alt"></i> 5. Return & Replacement Policy</h2>
            <div class="terms-text">
                <p>We stand by the quality of our products. However, if issues arise:</p>
                <ul>
                    <li>We accept returns/replacements <strong>only for damaged or wrong items</strong> reported within <strong>48 hours</strong> of delivery.</li>
                    <li>To raise a request, email us at <a href="mailto:info@suyagya.com">info@suyagya.com</a> with your Order Number and clear photos of the product.</li>
                    <li>Products used, altered, or returned without original packaging are <strong>not eligible</strong> for return.</li>
                    <li>Refunds (if applicable) will be processed within <strong>7 working days</strong> to the original payment method.</li>
                </ul>
            </div>
        </div>

        {{-- 6. Cancellation Policy --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-ban"></i> 6. Cancellation Policy</h2>
            <div class="terms-text">
                <ul>
                    <li>Orders once processed or shipped cannot be cancelled.</li>
                    <li>If you wish to cancel an unprocessed order, please contact us within <strong>1 hour</strong> of placing the order.</li>
                </ul>
            </div>
        </div>

        {{-- 7. Privacy --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-user-shield"></i> 7. Privacy</h2>
            <div class="terms-text">
                <p>Your privacy is important to us. Your use of the website is governed by our <a href="{{ url('privacy-policy') }}" class="text-primary">Privacy Policy</a>, which explains how we collect, use, and protect your personal data securely.</p>
            </div>
        </div>

        {{-- 8. Spiritual Belief Disclaimer --}}
        <div class="terms-card border-warning" style="background-color: #fffbf2;">
            <h2 class="section-title text-warning"><i class="las la-praying-hands"></i> 8. Spiritual Belief Disclaimer</h2>
            <div class="terms-text">
                <p>The spiritual benefits of products like Rudraksha, Yantra, etc., are based on ancient traditional beliefs, scriptures, and user faith. <strong>Suyagya does not claim or guarantee specific outcomes, miracles, or effects</strong> from using any of the spiritual items sold. Results may vary from person to person.</p>
            </div>
        </div>

        {{-- 9. Website Use & Restrictions --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-exclamation-triangle"></i> 9. Website Use & Restrictions</h2>
            <div class="terms-text">
                <ul>
                    <li>You must not misuse the website by knowingly introducing viruses or harmful technologies.</li>
                    <li>You agree not to use content, images, or product listings without written permission from us.</li>
                    <li>We reserve the right to refuse service or block access to users who violate our policies.</li>
                </ul>
            </div>
        </div>

        {{-- 10. Limitation of Liability --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-balance-scale"></i> 10. Limitation of Liability</h2>
            <div class="terms-text">
                <p>We shall not be liable for any indirect, incidental, or consequential damages arising out of your use of the website or products purchased. Our liability, in any case, is strictly limited to the <strong>value of the product purchased</strong>.</p>
            </div>
        </div>

        {{-- 11. Changes to Terms --}}
        <div class="terms-card">
            <h2 class="section-title"><i class="las la-sync"></i> 11. Changes to Terms</h2>
            <div class="terms-text">
                <p>We reserve the right to modify these terms at any time. Any changes will be updated on this page with the "Last Updated" date. It is your responsibility to review them periodically.</p>
            </div>
        </div>

        {{-- Contact Box --}}
        <div class="contact-box">
            <h3>Have Questions?</h3>
            <p class="mb-4">We are here to help you on your spiritual journey.</p>
            <div class="row justify-content-center">
                <div class="col-md-5 mb-3">
                    <i class="las la-envelope fs-2 mb-2"></i><br>
                    <a href="mailto:info@suyagya.com">info@suyagya.com</a>
                </div>
                <div class="col-md-5 mb-3">
                    <i class="las la-map-marker fs-2 mb-2"></i><br>
                    <span>J-3/356, DDA, Kalkaji,<br>New Delhi - 110019, India</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-top border-secondary">
                <small>Thank you for visiting <strong>Suyagya.com</strong> — we’re blessed to serve you!</small>
            </div>
        </div>

    </div>
</div>

@endsection
