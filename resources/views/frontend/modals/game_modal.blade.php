<div class="modal fade" id="gameModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 text-center p-4"
            style="background: #fffbf2; border: 3px solid #ff6f00; background-image: url('https://www.transparenttextures.com/patterns/cubes.png');">

            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal"></button>

            <h3 class="fw-bold font-heading mb-1">🎰 Lucky Draw</h3>
            <p class="text-muted small mb-4">Pick a lucky chit to win up to <strong>₹ 100!</strong></p>

            {{-- 1. GUEST VIEW --}}
            @guest
                <div id="guest-view">
                    <div class="chit-card mx-auto my-3 anim-float" onclick="playGuest()"
                        style="width: 120px; height: 120px;">
                        <i class="las la-scroll text-warning" style="font-size: 3.5rem;"></i>
                        <div id="guest-result" class="d-none animate__animated animate__zoomIn">
                            <h1 class="text-success fw-bold display-4 m-0">₹ 10</h1>
                        </div>
                    </div>
                    <div id="guest-msg" class="d-none text-center">

                        {{-- Message Text --}}
                        <p class="mb-2 text-muted" style="font-size: 14px; line-height: 1.4;">
                            <span class="text-danger fw-bold">Oops!</span> Aapko kam amount mila. 😢 <br>
                            If you want to win up to <span class="text-dark fw-bold">₹100</span>, then please Login.
                        </p>

                        {{-- Login Button --}}
                        <button class="btn btn-dark w-100 shadow-sm" onclick="openLoginForGame()">
                            Login to Win More! <i class="las la-sign-in-alt"></i>
                        </button>

                    </div>
                </div>
            @endguest

            {{-- 2. LOGGED IN USER VIEW --}}
            @auth
                <div id="user-view">
                    @php
                        // Check Active (Unused) Coupon
                        $hasActiveCoupon = \App\Models\UserCoupon::where('user_id', Auth::id())
                            ->where('is_used', 0)
                            ->exists();
                    @endphp

                    @if ($hasActiveCoupon)
                        <div class="py-3">
                            <div class="mb-3">
                                <span class="badge bg-success fs-5 px-4 py-2 rounded-pill">Winner! 🏆</span>
                            </div>
                            <h4 class="fw-bold">You have an Active Coupon!</h4>
                            <p class="text-muted small">Use it at checkout to get discount.</p>
                            <button class="btn btn-warning w-100 fw-bold shadow" data-bs-dismiss="modal">Shop
                                Now</button>
                        </div>
                    @else
                        {{-- 🎲 PARCHI GAME GRID --}}
                        <div class="row g-3 justify-content-center">
                            @for ($i = 1; $i <= 6; $i++)
                                <div class="col-4 chit-wrapper" style="animation-delay: {{ $i * 0.1 }}s">
                                    <div class="chit-card p-2 shadow-sm border" onclick="playUser(this)">

                                        {{-- Icon (Closed Parchi) --}}
                                        <div class="chit-icon">
                                            <i class="las la-scroll text-warning" style="font-size: 3.5rem;"></i>
                                            <small class="d-block text-muted fw-bold" style="font-size: 10px;">PICK
                                                ME</small>
                                        </div>

                                        {{-- Result (Hidden) --}}
                                        <div class="chit-result d-none animate__animated animate__flipInX">
                                            <span class="d-block fw-bold text-success fs-4 prize-amt">...</span>
                                            <small class="text-dark x-small">OFF</small>
                                        </div>

                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div id="win-msg" class="d-none mt-4 animate__animated animate__fadeInUp">
                            <h3 class="fw-bold text-danger m-0">🎉 ₹ <span id="final-amt"></span> OFF!</h3>
                            <small class="text-muted">Coupon Auto-Applied</small>
                        </div>
                    @endif
                </div>
            @endauth

        </div>
    </div>
</div>
