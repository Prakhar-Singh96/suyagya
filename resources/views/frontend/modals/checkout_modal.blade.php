<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 overflow-hidden" style="border-radius: 16px;">



            {{-- Header --}}

            <div class="modal-header border-bottom-0 pb-0 pt-3 px-4">

                <h6 class="modal-title fw-bold" style="font-size: 16px;">Secure Checkout</h6>

                <button type="button" class="btn-close bg-light rounded-circle p-2" data-bs-dismiss="modal"></button>

            </div>



            <div class="modal-body px-4 pb-4 pt-2">



                {{-- ✅ 1. PRODUCT SUMMARY & BILLING (Enhanced Layout) --}}

                <div class="bg-light p-3 rounded border mb-3">



                    {{-- Top: Product Info --}}

                    <div class="d-flex align-items-start mb-3">

                        <img id="summ_img" src="" width="60" height="60"
                            class="rounded border me-3 bg-white" style="object-fit: contain;">

                        <div class="flex-grow-1">

                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 14px; line-height: 1.4;"
                                id="summ_name">Loading...</h6>

                            <div class="d-flex justify-content-between align-items-center mt-1">

                                <small class="text-muted fw-bold" id="summ_qty">Qty: 1</small>

                                <div class="text-end">

                                    {{-- MRP (Strikethrough) --}}

                                    <span class="text-decoration-line-through text-muted small me-1"
                                        id="summ_mrp_display" style="display:none;">₹0</span>

                                    {{-- Selling Price --}}

                                    <span class="fw-bold text-dark fs-6" id="summ_total">₹0</span>

                                </div>

                            </div>

                        </div>

                    </div>



                    {{-- Bottom: Price Breakdown --}}

                    <div class="border-top pt-2">

                        {{-- MRP Row --}}

                        <div class="d-flex justify-content-between mb-1 small" id="row_mrp_total" style="display:none;">

                            <span class="text-muted">MRP Total</span>

                            <span id="bill_mrp">₹0</span>

                        </div>



                        {{-- Product Discount Row --}}

                        <div class="d-flex justify-content-between mb-1 small text-success" id="row_product_discount"
                            style="display:none;">

                            <span>Product Discount</span>

                            <span id="bill_product_discount">- ₹0</span>

                        </div>



                        {{-- Subtotal (After Product Discount) --}}

                        <div class="d-flex justify-content-between mb-1 small">

                            <span class="text-muted">Subtotal</span>

                            <span id="bill_subtotal" class="fw-bold">₹0</span>

                        </div>



                        {{-- Coupon Discount (Hidden initially) --}}

                        <div id="row_coupon_discount" style="display: none;">
                            <div class="d-flex justify-content-between mb-1 small text-success">
                                <span>Coupon Discount</span>
                                <span id="bill_coupon_discount">- ₹0</span>
                            </div>
                        </div>



                        {{-- Final Amount --}}

                        <div class="border-top my-2"></div>

                        <div class="d-flex justify-content-between fw-bold text-dark fs-6 align-items-center">

                            <span>To Pay</span>

                            <span id="bill_final_total" class="fs-5">₹0</span>

                        </div>

                    </div>

                </div>



                {{-- ✅ 2. COUPON SECTION (Always Visible) --}}

                <div class="mb-3">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <label class="fw-bold small m-0 text-dark"><i class="las la-percent text-warning fs-5 me-1"></i>

                            Offers</label>

                        <a href="javascript:void(0);" class="text-primary small fw-bold text-decoration-none"
                            onclick="toggleCouponList()">View All</a>

                    </div>



                    {{-- Input --}}

                    <div class="input-group mb-2 shadow-sm" id="coupon_input_group">

                        <input type="text" id="coupon_code" class="form-control border-end-0 ps-3"
                            placeholder="Enter coupon code">

                        <button class="btn border border-start-0 bg-white text-dark fw-bold" type="button"
                            onclick="applyCouponManual()">APPLY</button>

                    </div>

                    <small id="coupon_msg" class="fw-bold d-block mb-2 text-danger"></small>



                    {{-- Success Box --}}

                    <div id="coupon_applied_box" style="display: none;">

                        <div
                            class="p-2 rounded mb-3 border border-success bg-success-subtle d-flex justify-content-between align-items-center">

                            <div>

                                <span class="d-block fw-bold text-success small">✅ Coupon Applied</span>

                                <small class="text-dark">You saved <span id="saved_amount_text"
                                        class="fw-bold">₹0</span></small>

                            </div>

                            <button class="btn btn-sm btn-link text-danger fw-bold text-decoration-none"
                                onclick="removeCoupon()">Remove</button>

                        </div>

                    </div>



                    {{-- Coupon List --}}

                    <div id="coupon_list_box" class="mt-2 border rounded bg-white shadow-sm p-2"
                        style="display: none; max-height: 200px; overflow-y: auto;"></div>

                </div>





                <hr>



                {{-- 🔴 STEP 1: LOGIN --}}

                <div id="step_login" style="display: none;">

                    <div class="text-center mb-3">

                        <h5 class="fw-bold mb-1">Login Required</h5>

                        <p class="text-muted small">Please login to proceed.</p>

                    </div>

                    <div class="mb-3">

                        <input type="tel" id="chk_mobile" class="form-control rounded-3" style="width: 100%;"
                            placeholder="Mobile Number">

                        <small id="chk_mobile_error" class="text-danger"></small>

                    </div>

                    <div id="chk_otp_box" style="display: none;">

                        <input type="text" id="chk_otp"
                            class="form-control rounded-3 mb-3 text-center letter-spacing-2" placeholder="Enter OTP">

                        <button type="button" id="btn_verify_otp" class="btn btn-dark w-100 py-2 rounded-3 fw-bold"
                            onclick="verifyCheckoutOtp()">VERIFY OTP</button>

                    </div>

                    <button type="button" id="btn_send_otp"
                        class="btn btn-warning w-100 py-2 rounded-3 fw-bold text-white"
                        style="background-color: #ff6f00; border: none;" onclick="sendCheckoutOtp()">CONTINUE</button>

                </div>



                {{-- 🟢 STEP 2: ADDRESS --}}

                <div id="step_address" style="display: none;">

                    {{-- User Info Header --}}
                    <div class="bg-light p-2 rounded-3 mb-4 d-flex align-items-center justify-content-between border">
                        <div class="d-flex align-items-center">
                            <div class="bg-white p-2 rounded-circle border me-2">
                                <i class="las la-user text-muted"></i>
                            </div>
                            <div style="line-height: 1.2;">
                                <small class="text-muted x-small d-block">LOGGED IN AS</small>
                                <span class="fw-bold text-dark small" id="user_phone_display">
                                    {{ Auth::user()->phone ?? 'User' }}
                                </span>
                            </div>
                        </div>
                        <i class="las la-check-circle text-success fs-4"></i>
                    </div>

                    {{-- A. Saved Address List --}}
                    <div id="saved_address_list"
                        style="{{ Auth::check() && Auth::user()->addresses->count() > 0 ? '' : 'display:none;' }}">

                        {{-- ✅ Yahan Include karein taaki Page Load par bhi dikhe --}}
                        @if (Auth::check())
                            @include('frontend.includes.checkout_address_list', [
                                'addresses' => Auth::user()->addresses,
                            ])
                        @endif

                    </div>

                    {{-- B. New Address Form (Agar koi address nahi hai to ye by default dikhega) --}}
                    <div id="new_address_form"
                        style="{{ Auth::check() && Auth::user()->addresses->count() > 0 ? 'display:none;' : '' }}">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Add New Address</h6>
                            @if (Auth::check() && Auth::user()->addresses->count() > 0)
                                <button type="button" class="btn btn-sm btn-link text-decoration-none"
                                    onclick="$('#new_address_form').slideUp(); $('#saved_address_list').slideDown();">
                                    Cancel
                                </button>
                            @endif
                        </div>

                        {{-- Pincode --}}
                        <div class="form-floating mb-3">
                            <input type="tel" id="chk_pincode" class="form-control rounded-3 fw-bold"
                                placeholder="Pincode" maxlength="6" onkeyup="fetchCheckoutCityState()">
                            <label for="chk_pincode">Pincode *</label>
                            <small id="chk_pincode_msg"
                                class="position-absolute top-50 end-0 translate-middle-y me-3 fw-bold x-small"></small>
                        </div>

                        {{-- Expanded Fields --}}
                        <div id="address_expanded" style="display: none;">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <input type="text" id="chk_city" class="form-control bg-light border-0 small"
                                        placeholder="City" readonly>
                                </div>
                                <div class="col-6">
                                    <input type="text" id="chk_state" class="form-control bg-light border-0 small"
                                        placeholder="State" readonly>
                                </div>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" id="chk_house" class="form-control rounded-3"
                                    placeholder="House No">
                                <label>Flat, House no., Building *</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" id="chk_area" class="form-control rounded-3"
                                    placeholder="Area">
                                <label>Area, Street, Sector *</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" id="chk_name" class="form-control rounded-3"
                                    placeholder="Name" value="{{ Auth::user()->name ?? '' }}">
                                <label>Full Name *</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" id="chk_email" class="form-control  rounded-3" placeholder="Email"
                                    value="{{ Auth::user()->email ?? '' }}">
                                <label>Email ID (Required for Bill) *</label>
                            </div>

                            <div class="mb-4">
                                <label class="d-block x-small fw-bold text-muted text-uppercase mb-2">Save As</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="addr_type" id="home"
                                        value="home" checked>
                                    <label class="btn btn-outline-secondary btn-sm px-3 rounded-pill"
                                        for="home">Home</label>

                                    <input type="radio" class="btn-check" name="addr_type" id="work"
                                        value="work">
                                    <label class="btn btn-outline-secondary btn-sm px-3 rounded-pill"
                                        for="work">Work</label>
                                </div>
                            </div>

                            <button type="button" class="btn btn-warning w-100 py-3 rounded-3 fw-bold text-white"
                                style="background-color: #ff6f00; border: none;" onclick="saveAndContinue()">
                                SAVE & CONTINUE
                            </button>
                        </div>
                    </div>

                </div>



                {{-- 💰 STEP 3: PAYMENT --}}

                <div id="step_payment" style="display: none;">

                    <h6 class="fw-bold mb-3">Select Payment Method</h6>

                    <form id="finalPaymentForm" onsubmit="event.preventDefault(); processPayment();">

                        @csrf

                        <input type="hidden" name="buy_mode" id="final_buy_mode">

                        <input type="hidden" name="product_id" id="final_product_id">

                        <input type="hidden" name="quantity" id="final_quantity">

                        <input type="hidden" name="is_siddh" id="final_is_siddh">

                        <input type="hidden" name="address_id" id="final_address_id">

                        <input type="hidden" name="coupon_code" id="final_coupon_code">



                        <label class="d-flex align-items-center p-3 mb-2 border rounded-3 cursor-pointer bg-white"
                            onclick="$('.pay-radio').prop('checked', false); $('#rzp').prop('checked', true);">

                            <input type="radio" class="form-check-input me-3 pay-radio" name="payment_method"
                                id="rzp" value="RAZORPAY" checked>

                            <div class="flex-grow-1">

                                <span class="fw-bold d-block small">UPI / Cards / Netbanking</span>

                                <small class="text-success x-small fw-bold">Extra 10% OFF</small>

                            </div>

                            <img src="https://cdn.razorpay.com/static/assets/logo/payment.svg" height="16">

                        </label>



                        <label class="d-flex align-items-center p-3 mb-4 border rounded-3 cursor-pointer bg-white"
                            onclick="$('.pay-radio').prop('checked', false); $('#cod').prop('checked', true);">

                            <input type="radio" class="form-check-input me-3 pay-radio" name="payment_method"
                                id="cod" value="COD">

                            <div class="flex-grow-1">

                                <span class="fw-bold d-block small">Cash on Delivery</span>

                            </div>

                        </label>



                        <button type="submit" id="btn_place_order"
                            class="btn btn-dark w-100 py-3 rounded-3 fw-bold text-uppercase">

                            Pay <span id="btn_pay_amount">₹0</span>

                        </button>

                    </form>

                </div>



            </div>

        </div>

    </div>

</div>
