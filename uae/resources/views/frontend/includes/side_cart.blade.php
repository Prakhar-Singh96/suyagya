{{-- 🛒 SIDE CART DRAWER (Offcanvas) --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="sideCart" aria-labelledby="sideCartLabel" style="width: 400px;">

    {{-- Header --}}
    <div class="offcanvas-header bg-white border-bottom">
        <h6 class="offcanvas-title fw-bold" id="sideCartLabel">
            <i class="las la-shopping-bag text-warning"></i> Your Cart (<span id="side_cart_count">0</span>)
        </h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    {{-- Body (Items Load Here via AJAX) --}}
    <div class="offcanvas-body p-0" id="side_cart_body">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-warning" role="status"></div>
        </div>
    </div>

    {{-- Footer (Checkout Button) --}}
    <div class="offcanvas-footer p-3 border-top bg-white" id="side_cart_footer" style="display: none;">

        {{-- Savings Badge --}}
        <div class="alert alert-success py-1 px-2 mb-2 x-small text-center fw-bold rounded-1">
            🎉 You are saving <span id="cart_savings">₹0</span> on this order!
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">Subtotal:</span>
            <span class="fw-bold fs-5 text-dark" id="cart_subtotal">₹0</span>
        </div>

        {{-- Checkout Trigger --}}
        <button class="btn btn-dark w-100 py-3 fw-bold text-uppercase d-flex justify-content-between align-items-center"
                onclick="initiateCartCheckout()">
            <span>PROCEED TO CHECKOUT</span>
            <span><i class="las la-arrow-right"></i></span>
        </button>
    </div>
</div>
