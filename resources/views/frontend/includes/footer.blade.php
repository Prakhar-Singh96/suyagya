{{-- Footer Section (Divine Hindu Style) --}}
<style>
    /* Custom CSS for Footer Page */

    /* 1. Footer Background Color (Deep Maroon/Brown) */
    .footer-main-section {
        background-color: #6e1d0b !important;
        color: #FFFFFF;
        /* Default text color is white */
        padding-top: 50px;
        padding-bottom: 50px;
        border-top: 1px solid #999;
        /* Subtle separator */
    }

    /* 2. Heading and Text Colors */
    /* .footer-main-section h4 {
        color: #FFFFFF !important;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 1.1rem;
        text-transform: uppercase;
    } */

    /* 3. Link Styling (Quick Links, Policies) */
    .footer-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-list li {
        margin-bottom: 8px;
    }

    .footer-list a {
        color: #FFFFFF !important;
        /* White color for all links */
        text-decoration: none;
        font-size: 0.95rem;
        opacity: 0.85;
        /* Soft white look like the example */
        transition: opacity 0.2s;
    }

    .footer-list a:hover {
        opacity: 1;
        color: var(--secondary-base) !important;
        /* Optional: Gold hover */
    }

    /* 4. Logo/Brand and Contact Info Styling */
    .footer-brand-info p,
    .footer-brand-info address {
        color: #FFFFFF;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 5px;
        opacity: 0.85;
    }

    /* 5. Input Field Styling (Exclusive Offers) */
    .footer-input-group input {
        background-color: var(--light);
        /* Darker red input field */
        border: none;
        color: #FFFFFF;
        border-radius: 4px 0 0 4px;
        height: 40px;
        padding: 0 10px;
        /* border-bottom: 1px solid var(--secondary-base); Gold line at bottom */
    }

    .footer-input-group button {
        /* background-color: var(--secondary-base); Gold button */
        color: var(--primary);
        /* Maroon text on button */
        border-radius: 0 4px 4px 0;
        width: 40px;
        height: 40px;
    }

    /* 6. Social Icons */
    .footer-social-icons {
        margin-top: 20px;
    }

    .footer-social-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: transparent;
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: #FFFFFF;
        font-size: 1rem;
        margin-right: 10px;
    }

    .footer-social-icons a:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* 7. Copyright Bar */
    .footer-copyright-bar {
        background-color: #2e120f;
        /* Even darker color */
        color: rgba(255, 255, 255, 0.6);
        padding: 15px 0;
        font-size: 0.85rem;
    }

    .footer-social-icons li {
        display: inline-block;
        margin-right: 10px;
        /* Reset list style that may appear from previous attempts */
        list-style: none;
    }

    .footer-social-icons {
        padding-left: 0;
    }

    /* Social Icon Button Styling */
    .social-icon-btn {
        /* Ensure the <a> tag looks like a button */
        display: flex !important;
        /* Overriding previous inline-flex */
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: #FFFFFF;
        font-size: 1rem;
    }

    .social-icon-btn svg {
        /* Ensure SVG fills the space */
        width: 60%;
        height: 60%;
        fill: currentColor;
        /* Allows color property to set the SVG color */
    }

    .footer-heading {
        padding-top: 45px;
    }
</style>


{{-- Note: CSS style block should be placed in public/assets/css/custom.css --}}
<section class="py-4" style="background-color: #f7f1de;"> {{-- Matches your theme BG --}}
    <div class="container">
        <div class="row text-center g-4">

            {{-- 1. Free Shipping --}}
            <div class="col-6 col-md-3">
                <div class="mb-2">
                    {{-- Use LasIcon or SVG/Image --}}
                    <i class="las la-shipping-fast text-dark" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Free Shipping</h6>
                <small class="text-muted">Free Shipping all over India.</small>
            </div>

            {{-- 2. 24/7 Support --}}
            <div class="col-6 col-md-3">
                <div class="mb-2">
                    <i class="las la-clock text-dark" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">24/7 Support</h6>
                <small class="text-muted">Available 24 x 7</small>
            </div>

            {{-- 3. Secure Payments --}}
            <div class="col-6 col-md-3">
                <div class="mb-2">
                    <i class="las la-credit-card text-dark" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">100% Secure payments</h6>
                <small class="text-muted">COD/UPI/CARDS</small>
            </div>

            {{-- 4. Made In India --}}
            <div class="col-6 col-md-3">
                <div class="mb-2">
                    {{-- Flag Image (Replace URL if needed) --}}
                    <img src="https://upload.wikimedia.org/wikipedia/en/4/41/Flag_of_India.svg"
                         alt="India" style="width: 40px; height: 40px; border: 1px solid #eee;">
                </div>
                <h6 class="fw-bold text-dark mb-1">MADE IN INDIA</h6>
                <small class="text-muted">Proudly Made in India</small>
            </div>

        </div>
    </div>
</section>
<footer class="footer-main-section">
    <div class="container">
        <div class="row">

            {{-- 1. 🏢 Brand, Logo, and Contact Info --}}
            <div class="col-lg-3 col-md-6 mb-4 footer-col-info">
                {{-- Logo and Text Container (Aligns to the top-left) --}}
                <div class="d-flex flex-column">
                    <div class="footer-logo mb-3">
                        {{-- Logo Image --}}
                        <img src="{{ asset('assets/img/footerlogoimage.png') }}"
                            alt="Suyagya Logo" style="height: 60px;">
                    </div>
                    <div class="footer-brand-info">
                        {{-- Alignment Fix: Text starts directly below the logo --}}
                        <div class="footer-details mb-3">
                        <p>Discover authentic spiritual products rooted in ancient traditions at Suyagya.</p>
                        <address class="mb-1">
                            J-3/356, DDA, Kalkaji, New Delhi - 110019. India
                        </address>

                        <p class="mb-1">
                            <i class="las la-phone me-2" style="font-size:1.1rem;"></i>
                            +91 7692 005 006
                        </p>

                        <p class="mb-1">
                            <i class="las la-envelope me-2" style="font-size:1.1rem;"></i>
                            <a href="mailto:support@suyagya.com" class="text-white"
                                style="opacity: 1;">info@suyagya.com</a>
                        </p>

                        <p class="mt-2">
                            <span class="fw-bold">Working Hours:</span> Mon-Sat, 10 AM - 6 PM
                        </p>
                        </div>
                    </div>
                </div> {{-- End Logo and Text Container --}}
            </div>

            {{-- 2. 🔗 Quick Links --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-list">
                    {{-- <li><a href="{{ url('shops/create') }}">Become a Seller</a></li> --}}
                    <li><a href="{{ route('track.order') }}">Track Your Order</a></li>
                    {{-- <li><a href="{{ url('best-sellers') }}">Best Sellers</a></li> --}}
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('frontend.faq') }}">FAQs</a></li>
                </ul>
            </div>

            {{-- 3. 📜 Policies --}}
            <div class="col-lg-3 col-md-6 mb-4">
                <h4 class="footer-heading">Policies</h4>
                <ul class="footer-list">
                    <li><a href="{{ route('refund.policy') }}">Refund & Cancellation</a></li>
                    <li><a href="{{ route('terms.conditions') }}">Terms & Conditions</a></li>
                    <li><a href="{{ route('support.policy') }}">Support Policy</a></li>
                    <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                </ul>
            </div>

            {{-- 4. 💌 Offers & Social Media --}}
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-heading">Get our exclusive offers</h4>
                <p class="small mb-2">Get exclusive coupons in your mailbox.</p>

                {{-- Email Subscription Input --}}
                <div class="footer-input-group d-flex mb-4">
                    <input type="email" placeholder="Email" class="form-control" style="flex-grow: 1;">
                    <button type="submit" class="btn">
                        <i class="las la-arrow-right"></i>
                    </button>
                </div>

                {{-- ✅ FIX: SOCIAL ICONS USING SVG --}}
                <div class="footer-social-icons d-flex">

                    <a href="https://www.facebook.com/mysuyagya" target="_blank" aria-label="Facebook"
                        class="social-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M15.12,5.32H17V2.14A26.11,26.11,0,0,0,14.26,2C11.54,2,9.68,3.66,9.68,6.7V9.32H6.61v3.56H9.68V22h3.68V12.88h3.06l.46-3.56H13.36V7.05C13.36,6,13.64,5.32,15.12,5.32Z">
                            </path>
                        </svg>
                    </a>

                    <a href="https://www.instagram.com/mysuyagya/reels/" target="_blank" aria-label="Instagram"
                        class="social-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12,2A10,10,0,0,0,2,12a10,10,0,0,0,10,10,10,10,0,0,0,10-10A10,10,0,0,0,12,2Zm3.47,1.86a1.44,1.44,0,1,1-1.44,1.44,1.44,1.44,0,0,1,1.44-1.44ZM12,6.5A5.5,5.5,0,1,1,6.5,12,5.5,5.5,0,0,1,12,6.5ZM12,8.5a3.5,3.5,0,1,0,3.5,3.5A3.5,3.5,0,0,0,12,8.5Z">
                            </path>
                        </svg>
                    </a>

                    <a href="https://x.com/MySuyagya" target="_blank" aria-label="Twitter" class="social-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M22.46,6a8.59,8.59,0,0,1-2.4,0.64,4.3,4.3,0,0,0,1.88-2.31,8.6,8.6,0,0,1-2.65,1A4.27,4.27,0,0,0,12.72,3a4.29,4.29,0,0,0-4.3,4.29,4.29,4.29,0,0,0,.11,1,12.18,12.18,0,0,1-8.8-4.48,4.29,4.29,0,0,0,1.33,5.7A4.28,4.28,0,0,1,2.94,11.5v0.05a4.29,4.29,0,0,0,3.44,4.2A4.28,4.28,0,0,1,4,16.59a4.26,4.26,0,0,1-0.8-0.08,4.3,4.3,0,0,0,4,2.98A8.6,8.6,0,0,1,2,19.75a8.34,8.34,0,0,1-0.81-.05,12.1,12.1,0,0,0,6.56,1.92A12.15,12.15,0,0,0,21.5,8.19V7.83A8.76,8.76,0,0,0,22.46,6Z">
                            </path>
                        </svg>
                    </a>

                    <a href="https://www.youtube.com/@MySuyagya" target="_blank" aria-label="Youtube" class="social-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M21.5,8.43a2.59,2.59,0,0,0-1.83-1.83C18.66,6,12,6,12,6s-6.66,0-7.67,0.6A2.59,2.59,0,0,0,2.5,8.43,26.43,26.43,0,0,0,2,12a26.43,26.43,0,0,0,0.5,3.57,2.59,2.59,0,0,0,1.83,1.83C5.34,18,12,18,12,18s6.66,0,7.67-0.6a2.59,2.59,0,0,0,1.83-1.83A26.43,26.43,0,0,0,22,12a26.43,26.43,0,0,0-0.5-3.57ZM10,14V10l4,2Z">
                            </path>
                        </svg>
                    </a>

                    <a href="https://www.linkedin.com/company/suyagya/" target="_blank" aria-label="LinkedIn" class="social-icon-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M21.5,2H2.5A0.5,0.5,0,0,0,2,2.5v19A0.5,0.5,0,0,0,2.5,22h19A0.5,0.5,0,0,0,22,21.5V2.5A0.5,0.5,0,0,0,21.5,2ZM8,19H5V10H8ZM6.5,8.2A1.7,1.7,0,1,1,8.2,6.5,1.7,1.7,0,0,1,6.5,8.2ZM19,19H16V14.6c0-1.04-.3-1.74-1.29-1.74A1.33,1.33,0,0,0,13.43,14,1.4,1.4,0,0,0,13.36,15V19H10V10h3V11.2a4.42,4.42,0,0,1,3.95-2.1c2.89,0,5.05,1.72,5.05,5V19Z">
                            </path>
                        </svg>
                    </a>

                </div>
            </div>

        </div>
    </div>
</footer>

{{-- Footer Bottom: Copyright Bar --}}
<div class="footer-copyright-bar">
    <div class="container text-center">
        © 2025. Suyagya. All Copyrights Reserved to THE PALAK TRADING COMPANY
    </div>
</div>
