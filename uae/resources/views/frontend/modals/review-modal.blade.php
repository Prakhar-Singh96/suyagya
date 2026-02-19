<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center pt-0">
                <h5 class="fw-bold mb-3">How do you like this item?</h5>
                
                {{-- Product Thumbnail --}}
                <div class="mb-3">
                    <img src="{{ asset($product->main_image) }}" class="rounded" width="80" height="80" style="object-fit: cover;">
                    <p class="small text-muted mt-1">{{ $product->name }}</p>
                </div>

                {{-- ⭐ Star Input (Clickable) --}}
                <div class="rating-input mb-2 fs-1 text-warning cursor-pointer">
                    <i class="lar la-star" data-val="1" onclick="setRating(1)"></i>
                    <i class="lar la-star" data-val="2" onclick="setRating(2)"></i>
                    <i class="lar la-star" data-val="3" onclick="setRating(3)"></i>
                    <i class="lar la-star" data-val="4" onclick="setRating(4)"></i>
                    <i class="lar la-star" data-val="5" onclick="setRating(5)"></i>
                </div>
                <h6 id="rating-text" class="fw-bold text-dark mb-4">Select Rating</h6>

                {{-- 📝 Review Form --}}
                <form action="{{ route('reviews.store') }}" method="POST" id="reviewForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="rating" id="rating_value" value="0">

                    {{-- Review Title --}}
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Review Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Give your review a title" required>
                    </div>

                    {{-- Review Content --}}
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Review Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="review" rows="3" placeholder="Start writing here..." required></textarea>
                    </div>

                    {{-- Image & Video Upload --}}
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Picture/Video (optional)</label>
                        <input type="file" class="form-control" name="media[]" multiple accept="image/*,video/mp4,video/x-m4v,video/*">
                        <div class="form-text small">Max 5 files (Images or Video)</div>
                    </div>

                    {{-- Display Name --}}
                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Display Name (publicly like John Smith) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="display_name" placeholder="Display name" required>
                    </div>

                    {{-- Email Address --}}
                    <div class="mb-4 text-start">
                        <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="Your email address" required>
                    </div>

                    <div class="d-flex gap-2">
                         <button type="button" class="btn btn-outline-secondary w-50 py-2 fw-bold" data-bs-dismiss="modal">Cancel Review</button>
                         <button type="submit" class="btn btn-dark w-50 py-2 text-uppercase fw-bold">Submit Review</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>