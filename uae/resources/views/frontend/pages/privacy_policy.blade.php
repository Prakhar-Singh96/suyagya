@extends('frontend.layouts.app')

@section('styles')
<style>
    /* ✨ Privacy Policy Page Styles */
    .privacy-header {
        background-color: #f7f1de;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
        border-bottom: 4px solid #d4af37;
    }

    .privacy-header h1 {
        font-family: 'Merriweather', serif;
        font-weight: 700;
        color: #3e2723;
    }

    /* Card Styling */
    .policy-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        transition: transform 0.3s ease;
    }

    .policy-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        border-color: #d4af37;
    }

    .card-title {
        font-family: 'Merriweather', serif;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1.2rem;
        color: #333;
    }

    .card-title i {
        color: #d4af37;
        font-size: 1.5rem;
    }

    /* List Styles */
    .data-list {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* 2 Column Auto Grid */
        gap: 15px;
    }

    .data-list li {
        background-color: #f9f9f9;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #555;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 3px solid #d4af37;
    }

    .data-list li i {
        font-size: 1.2rem;
        color: #3e2723;
    }

    /* Highlight Box (What We Do) */
    .highlight-box {
        background-color: #e3f2fd; /* Light Blue for Info */
        border: 1px dashed #2196f3;
        padding: 20px;
        border-radius: 8px;
        color: #0d47a1;
    }

    /* Security Box */
    .security-box {
        background-color: #e8f5e9; /* Light Green */
        border: 1px solid #c8e6c9;
        padding: 25px;
        border-radius: 12px;
        text-align: center;
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
<section class="privacy-header">
    <div class="container">
        <h1>Privacy Policy</h1>
        <p class="text-muted mt-2">Your trust and privacy are our sacred responsibility.</p>
    </div>
</section>

<div class="container pb-5">

    {{-- Last Updated --}}
    <div class="text-center text-muted small mb-5">
        Last Updated: {{ date('F d, Y') }}
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Intro --}}
            <div class="mb-5 text-center">
                <p class="lead text-dark">
                    Welcome to <strong>Suyagya.com</strong> – your trusted online store for spiritual and religious products. This website is managed and operated by <strong>Ram Ram Pandit Ji</strong>, with a commitment to protecting your privacy and personal data.
                </p>
            </div>

            {{-- 1. Who We Are --}}
            <div class="policy-card">
                <h3 class="card-title"><i class="las la-user-tie"></i> Who We Are</h3>
                <div class="row">
                    <div class="col-md-6 mb-2"><strong>Website Name:</strong> Suyagya</div>
                    <div class="col-md-6 mb-2"><strong>Address:</strong> <a href="https://suyagya.com" class="text-primary">https://suyagya.com</a></div>
                    <div class="col-md-6 mb-2"><strong>Managed By:</strong> Suyagya Admin</div>
                    <div class="col-md-6 mb-2"><strong>Email:</strong> <a href="mailto:mysuyagya@gmail.com" class="text-dark">info@suyagya.com</a></div>
                    <div class="col-12 mt-2">
                        <strong>Business Address:</strong> J-3/356, DDA Flats, Kalkaji, Delhi, India 110019
                    </div>
                </div>
            </div>

            {{-- 2. What We Do (Highlighted) --}}
            <div class="policy-card">
                <h3 class="card-title"><i class="las la-store"></i> What We Do</h3>
                <p class="text-muted mb-3">Suyagya.com is an e-commerce platform that sells spiritual and devotional products.</p>

                <div class="highlight-box">
                    <i class="las la-info-circle fs-4 me-2 align-middle"></i>
                    <strong>Important Note:</strong> This site does not offer any pooja or religious services directly. Those services are exclusively available through our separate platform:
                    <a href="https://ramrampanditji.com" target="_blank" class="fw-bold text-decoration-underline">ramrampanditji.com</a>
                </div>
            </div>

            {{-- 3. Information We Collect --}}
            <div class="policy-card">
                <h3 class="card-title"><i class="las la-database"></i> Information We Collect</h3>
                <p class="text-muted mb-3">We may collect the following information when you visit or shop on our website:</p>
                <ul class="data-list">
                    <li><i class="las la-user"></i> Full Name</li>
                    <li><i class="las la-phone"></i> Contact Number</li>
                    <li><i class="las la-envelope"></i> Email Address</li>
                    <li><i class="las la-map-marker"></i> Shipping & Billing Address</li>
                    <li><i class="las la-credit-card"></i> Payment Details (Securely processed)</li>
                    <li><i class="las la-history"></i> Order History</li>
                    <li><i class="las la-laptop"></i> Browser & Device Info</li>
                    <li><i class="las la-cookie"></i> Cookies & Usage Data</li>
                </ul>
            </div>

            {{-- 4. How We Use Data --}}
            <div class="policy-card">
                <h3 class="card-title"><i class="las la-tasks"></i> How We Use Your Data</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 ps-0"><i class="las la-check text-success me-2"></i> Process and fulfill your orders efficiently.</li>
                    <li class="list-group-item border-0 ps-0"><i class="las la-check text-success me-2"></i> Send order confirmations, invoices, and shipping updates.</li>
                    <li class="list-group-item border-0 ps-0"><i class="las la-check text-success me-2"></i> Provide customer support and resolve queries.</li>
                    <li class="list-group-item border-0 ps-0"><i class="las la-check text-success me-2"></i> Improve website functionality and user experience.</li>
                    <li class="list-group-item border-0 ps-0"><i class="las la-check text-success me-2"></i> Share product-related offers and updates (only if you opt-in).</li>
                </ul>
            </div>

            {{-- 5. Data Security (Highlighted) --}}
            <div class="policy-card">
                <div class="security-box">
                    <i class="las la-shield-alt text-success display-4 mb-3"></i>
                    <h4 class="fw-bold text-success">100% Data Security</h4>
                    <p class="text-dark m-0">
                        Your data is protected using secure technologies. We <strong>never store your full payment details</strong> (Credit/Debit Card numbers) on our servers. All payments are handled via trusted third-party gateways (like Razorpay) which follow the highest security standards.
                    </p>
                </div>
            </div>

            {{-- 6. Cookies & Sharing --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="policy-card h-100">
                        <h3 class="card-title"><i class="las la-cookie-bite"></i> Use of Cookies</h3>
                        <p class="text-muted small">Suyagya.com uses cookies to:</p>
                        <ul class="ps-3 small text-muted">
                            <li>Remember your cart items.</li>
                            <li>Keep you logged in securely.</li>
                            <li>Track website performance and analytics.</li>
                        </ul>
                        <p class="small text-muted mt-2">You can disable cookies via your browser settings at any time.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="policy-card h-100">
                        <h3 class="card-title"><i class="las la-share-alt"></i> Sharing Information</h3>
                        <p class="text-muted small">We share data <strong>only</strong> with essential partners:</p>
                        <ul class="ps-3 small text-muted">
                            <li>Trusted Delivery Partners (to ship your order).</li>
                            <li>Payment Gateways (to process payments).</li>
                            <li>Email Service Providers (for updates).</li>
                        </ul>
                        <p class="fw-bold small mt-2 text-dark"><i class="las la-ban text-danger"></i> We NEVER sell or rent your data.</p>
                    </div>
                </div>
            </div>

            {{-- 7. Your Rights --}}
            <div class="policy-card">
                <h3 class="card-title"><i class="las la-user-shield"></i> Your Rights & Retention</h3>
                <p class="text-muted">We retain your data only as long as necessary to fulfill orders and comply with legal requirements.</p>
                <p class="fw-bold mt-3">You have the right to:</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge bg-light text-dark border p-2">Access your data</span>
                    <span class="badge bg-light text-dark border p-2">Correct/Update details</span>
                    <span class="badge bg-light text-dark border p-2">Request Deletion</span>
                    <span class="badge bg-light text-dark border p-2">Unsubscribe</span>
                </div>
                <p class="mt-3 small">To make any request, contact us at: <a href="mailto:info@suyagya.com" class="fw-bold text-dark">info@suyagya.com</a></p>
            </div>

            {{-- 8. Changes to Policy --}}
            <div class="policy-card bg-light border-0">
                <h3 class="card-title text-muted" style="font-size: 1rem;"><i class="las la-sync"></i> Changes to This Policy</h3>
                <p class="small text-muted m-0">We may update this Privacy Policy from time to time. All changes will be posted on this page, and the "Last Updated" date will be modified accordingly.</p>
            </div>

            {{-- Contact Footer --}}
            <div class="contact-footer">
                <h3 style="font-family: 'Merriweather', serif;">Questions about Privacy?</h3>
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
                    Thank you for trusting <strong>Suyagya</strong>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
