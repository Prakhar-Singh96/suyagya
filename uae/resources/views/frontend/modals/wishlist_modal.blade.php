<div class="modal fade" id="wishlistModal" tabindex="-1" aria-labelledby="wishlistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            {{-- 🟢 Modal Header --}}
            <div class="modal-header border-bottom-0 pb-0 bg-dark text-white">
                <h5 class="modal-title font-heading fw-bold text-white" id="wishlistModalLabel">My Wishlist</h5>
            </div>

            {{-- 🟢 Modal Body (Content AJAX se aayega) --}}
            <div class="modal-body">

                {{-- Loader (Initially Visible) --}}
                <div id="wishlist-loader" class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="text-muted mt-2 small">Loading your favorites...</p>
                </div>

                {{-- Content Container --}}
                <div id="wishlist-content" class="row g-3 d-none">
                    {{-- AJAX se items yahan inject honge --}}
                </div>

                {{-- Empty State (Hidden by default) --}}
                <div id="wishlist-empty" class="text-center py-5 d-none">
                    <i class="lar la-heart text-muted mb-3" style="font-size: 60px; opacity: 0.5;"></i>
                    <p class="text-muted">Your wishlist is empty.</p>
                    <button class="btn btn-dark btn-sm rounded-0" data-bs-dismiss="modal">Continue Shopping</button>
                </div>
            </div>

        </div>
    </div>
</div>
