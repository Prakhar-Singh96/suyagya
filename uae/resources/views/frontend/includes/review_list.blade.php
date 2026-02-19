{{-- Container से 'row' हटा दिया और अपनी क्लास लगाई --}}
<div class="review-masonry-grid" id="reviewListContainer">

    @forelse($reviews as $review)
        {{-- 'col-md-3' हटाकर 'review-masonry-item' लगाया --}}
        <div class="review-masonry-item">
            <div class="card border p-3 shadow-sm"> {{-- Shadow-sm for better look --}}

                {{-- Stars --}}
                <div class="text-warning small mb-2">
                    @for($i=1; $i<=5; $i++)
                        <i class="{{ $i <= $review->rating ? 'las la-star' : 'lar la-star' }}"></i>
                    @endfor
                </div>

                {{-- Name --}}
                <h6 class="fw-bold text-dark mb-2">
                    {{ $review->display_name }}
                    <i class="las la-check-circle text-success" title="Verified"></i>
                    <span class="text-muted small" style="font-size: 0.75rem;">Verified</span>
                </h6>

                {{-- Review Text --}}
                <p class="small text-muted mb-2" style="line-height: 1.5;">
                    {{ $review->review }}
                </p>

                {{-- Media Images --}}
                @if($review->media && count($review->media) > 0)
                    <div class="mt-2">
                        @foreach($review->media as $media)
                            @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $media))
                                {{-- Width 100% rakha taki wo card me fit ho jaye --}}
                                <img src="{{ asset($media) }}" class="rounded mb-2 border"
                                     style="width: 100%; height: auto; object-fit: cover; display: block;">
                            @endif
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            <p>No reviews yet. Be the first to review!</p>
        </div>
    @endforelse

</div>
