@extends('frontend.layouts.app')

@section('title', 'Support Policy | Suyagya')

@section('styles')
<style>
    /* ✨ Support Page Styles */
    .support-header {
        background-color: #f7f1de;
        padding: 60px 0;
        text-align: center;
        border-bottom: 4px solid #d4af37;
        margin-bottom: 40px;
    }

    .support-header h1 {
        font-family: 'Merriweather', serif;
        font-weight: 700;
        color: #3e2723;
    }

    /* Contact Cards */
    .contact-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        border-color: #d4af37;
    }

    .contact-icon-circle {
        width: 70px;
        height: 70px;
        background-color: #fffbf2;
        border: 2px dashed #d4af37;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px auto;
        font-size: 32px;
        color: #3e2723;
        transition: background 0.3s;
    }

    .contact-card:hover .contact-icon-circle {
        background-color: #d4af37;
        color: #fff;
        border-style: solid;
    }

    .contact-link {
        color: #333;
        font-weight: 600;
        text-decoration: none;
        font-size: 1.1rem;
    }

    .contact-link:hover {
        color: #d4af37;
    }

    /* Hours & Response Box */
    .info-box {
        background-color: #f9f9f9;
        border-left: 5px solid #d4af37;
        padding: 25px;
        border-radius: 8px;
        height: 100%;
    }

    /* Services List */
    .service-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #f0f0f0;
        transition: transform 0.2s;
    }

    .service-item:hover {
        transform: translateX(5px);
        border-color: #d4af37;
        background-color: #fffbf2;
    }

    .service-icon {
        color: #2e7d32; /* Green for service */
        font-size: 24px;
        background: #e8f5e9;
        padding: 8px;
        border-radius: 6px;
    }

    /* Commitment Section */
    .commitment-section {
        background-color: #3e2723;
        color: #fff;
        padding: 50px 0;
        text-align: center;
        margin-top: 50px;
        border-radius: 12px;
        background-image: url('https://www.transparenttextures.com/patterns/stardust.png');
    }
</style>
@endsection

@section('content')

{{-- 🎧 Header --}}
<section class="support-header">
    <div class="container">
        <h1>Support Policy</h1>
        <p class="text-muted mt-2">We are here to help you on your spiritual journey.</p>
    </div>
</section>

<div class="container pb-5">

    {{-- Intro --}}
    <div class="text-center mb-5 px-lg-5">
        <p class="lead text-dark">
            At <strong>Suyagya</strong>, your peace of mind and satisfaction are our top priorities. Our dedicated support team is here to assist you with any queries regarding your orders or experience on our platform.
        </p>
    </div>

    {{-- 📞 Contact Cards Row --}}
    <div class="row g-4 mb-5 justify-content-center">

        {{-- Email --}}
        <div class="col-md-4">
            <div class="contact-card">
                <div class="contact-icon-circle">
                    <i class="las la-envelope"></i>
                </div>
                <h5 class="fw-bold mb-2">Email Us</h5>
                <p class="text-muted small mb-2">For general queries & support</p>
                <a href="mailto:info@suyagya.com" class="contact-link">info@suyagya.com</a>
            </div>
        </div>

        {{-- Phone --}}
        <div class="col-md-4">
            <div class="contact-card">
                <div class="contact-icon-circle">
                    <i class="las la-phone-volume"></i>
                </div>
                <h5 class="fw-bold mb-2">Call Us</h5>
                <p class="text-muted small mb-2">Speak to our support team</p>
                <a href="tel:+917692005006" class="contact-link">+91 7692 005 006</a>
            </div>
        </div>

        {{-- Address --}}
        <div class="col-md-4">
            <div class="contact-card">
                <div class="contact-icon-circle">
                    <i class="las la-map-marked-alt"></i>
                </div>
                <h5 class="fw-bold mb-2">Visit Us</h5>
                <p class="text-muted small mb-2">Our physical location</p>
                <span class="text-dark">J-3/356, DDA Flats, Kalkaji,<br>Delhi - 110019</span>
            </div>
        </div>
    </div>

    {{-- ⏰ Hours & Response Time --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="info-box">
                <h4 class="fw-bold mb-3 font-heading" style="font-family: 'Merriweather', serif;">
                    <i class="las la-clock text-warning"></i> Support Hours
                </h4>
                <p class="text-dark mb-1"><strong>Monday to Saturday:</strong> 9:00 AM to 7:00 PM IST</p>
                <p class="text-danger small"><i class="las la-coffee"></i> We are closed on Sundays and Public Holidays.</p>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="info-box" style="border-left-color: #2e7d32;">
                <h4 class="fw-bold mb-3 font-heading" style="font-family: 'Merriweather', serif;">
                    <i class="las la-reply-all text-success"></i> Response Time
                </h4>
                <p class="mb-2">Our team strives to respond to all inquiries within <strong>2 business days</strong>.</p>
                <p class="small text-muted mb-0">
                    <i class="las la-exclamation-circle text-danger"></i> For urgent matters, please mention <strong>“URGENT”</strong> in your email subject line.
                </p>
            </div>
        </div>
    </div>

    {{-- 🛠️ Services We Offer --}}
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h3 class="text-center fw-bold mb-4 font-heading" style="font-family: 'Merriweather', serif;">
                Support Services We Offer
            </h3>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="service-item">
                        <div class="service-icon"><i class="las la-shipping-fast"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Order Tracking</h6>
                            <p class="small text-muted m-0">Updates on shipment status and delivery timelines.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="service-item">
                        <div class="service-icon"><i class="las la-edit"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Order Modification</h6>
                            <p class="small text-muted m-0">Help with placing or editing orders (within allowed time).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="service-item">
                        <div class="service-icon"><i class="las la-info-circle"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Product Guidance</h6>
                            <p class="small text-muted m-0">Information about authentic Rudraksha, Yantras, etc.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="service-item">
                        <div class="service-icon"><i class="las la-undo-alt"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1">Returns & Refunds</h6>
                            <p class="small text-muted m-0">Assistance with replacements and refund processing.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ❤️ Commitment Footer --}}
    <div class="commitment-section shadow">
        <div class="container">
            <i class="las la-hand-holding-heart display-4 mb-3 text-warning"></i>
            <h3 class="fw-bold mb-3">Our Commitment to You</h3>
            <p class="mx-auto" style="max-width: 700px; font-size: 1.1rem; opacity: 0.9;">
                "We believe in honest, respectful, and timely communication. Your feedback helps us improve continuously, so please feel free to share your suggestions or concerns."
            </p>
        </div>
    </div>

</div>

@endsection
