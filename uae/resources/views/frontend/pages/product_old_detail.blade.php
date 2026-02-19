@extends('frontend.layouts.app')

@section('title', $product->name . ' | Suyagya')

@section('styles')
    <style>
        /* ✨ PREMIUM DESIGN STYLES (Your Existing) */
        :root { --primary-orange: #ff6f00; --text-dark: #222; --bg-cream: #fffbf2; }

        /* 💎 New Gemstone Config Styles */
        .gem-config-box { background: #fff; padding: 10px; border: 1px solid #eee; border-radius: 6px; margin-bottom: 20px; }
        .gem-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #555; display: block; margin-bottom: 5px; }
        .gem-btn-group { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
        .gem-btn {
            border: 1px solid #ddd; background: #fff; padding: 6px 12px; font-size: 12px; cursor: pointer; border-radius: 4px; transition: 0.2s;
        }
        .gem-btn:hover { background: #f9f9f9; border-color: #bbb; }
        .gem-btn.active { border-color: #000; background: #000; color: #fff; }
    </style>
@endsection

@section('content')

    {{-- Breadcrumb (Your Code) --}}
    <div class="py-2 border-bottom mb-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active text-dark">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0 small text-start">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="container pb-5">
        <div class="row g-lg-5">

            {{-- 🖼️ LEFT SIDE: IMAGE GALLERY (Your Code) --}}
            <div class="col-lg-6 mb-4">
                <div class="product-images" style="top: 100px; z-index: 1;">
                    <div class="product-main-slider">
                        <div><img src="{{ asset($product->main_image) }}"></div>
                        @if ($product->images->count() > 0)
                            @foreach ($product->images as $img) <div><img src="{{ asset($img->image) }}"></div> @endforeach
                        @endif
                    </div>
                    <div class="product-thumb-slider">
                        <div><img src="{{ asset($product->main_image) }}"></div>
                        @if ($product->images->count() > 0)
                            @foreach ($product->images as $img) <div><img src="{{ asset($img->image) }}"></div> @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- 📝 RIGHT SIDE: PRODUCT INFO --}}
            <div class="col-lg-6">

                <h1 class="fw-bold font-heading mb-2 text-dark" style="font-size: 1.8rem; line-height: 1.3;">{{ $product->name }}</h1>

                {{-- Rating --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning small me-2">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= round($averageRating)) <i class="las la-star"></i>
                            @elseif($i - 0.5 <= $averageRating) <i class="las la-star-half-alt"></i>
                            @else <i class="lar la-star"></i> @endif
                        @endfor
                    </div>
                    <span class="text-muted small border-start ps-2">{{ number_format($averageRating, 1) }} ({{ $totalReviews }} Reviews)</span>
                </div>

                {{-- Price --}}
                <div class="mb-3 d-flex align-items-baseline">
                    <input type="hidden" id="base_price" value="{{ $product->price }}">
                    <span class="fs-2 fw-bold text-dark me-2">₹<span id="display_price">{{ number_format($product->price) }}</span></span>
                    @if ($product->mrp_price > $product->price)
                        <span class="text-decoration-line-through text-muted fs-5">₹<span id="display_mrp">{{ number_format($product->mrp_price) }}</span></span>
                        <span class="text-danger fw-bold ms-3 bg-danger-subtle px-2 py-1 rounded small"><span id="display_discount">{{ $product->discount }}</span>% OFF</span>
                    @endif
                </div>

                {{-- Timer --}}
                <div class="offer-timer-box mb-4 p-2 border border-danger rounded d-inline-block bg-light">
                    <span class="text-danger fw-bold small me-2">Offer ends in:</span>
                    <span id="countdown" class="fw-bold text-dark" style="min-width: 100px; display: inline-block;">Loading...</span>
                </div>

                {{-- ================================================= --}}
                {{-- 🔄 DYNAMIC CONFIGURATION SECTION (Gemstone vs Weight) --}}
                {{-- ================================================= --}}

                @if ($product->is_gemstone && $product->gemstoneVariants->count() > 0)
                    {{-- 💎 GEMSTONE CONFIGURATOR --}}

                    {{-- Store Gem Data --}}
                    <div id="gem_data" style="display:none;">{{ json_encode($product->gemstoneVariants) }}</div>
                    <input type="hidden" name="variant_id" id="selected_variant_id" value="">

                    <div class="gem-config-box">

                        {{-- 1. TYPE --}}
                        <span class="gem-label">Type</span>
                        <div class="gem-btn-group">
                            <div class="gem-btn active" onclick="updateGemState('type', 'loose', this)">💎 Stone</div>
                            <div class="gem-btn" onclick="updateGemState('type', 'ring', this)">💍 Ring</div>
                            <div class="gem-btn" onclick="updateGemState('type', 'pendant', this)">🏅 Pendant</div>
                        </div>
                        <input type="hidden" id="sel_type" value="loose">

                        {{-- 2. SIZE (Ratti) --}}
                        <span class="gem-label">Size (Ratti)</span>
                        <div class="gem-btn-group" id="ratti_group">
                            @foreach($product->gemstoneVariants->where('type', 'loose')->unique('ratti_size') as $idx => $gv)
                                <div class="gem-btn {{ $idx === 0 ? 'active' : '' }}" onclick="updateGemState('ratti', '{{ $gv->ratti_size }}', this)">
                                    {{ $gv->ratti_size }}
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" id="sel_ratti" value="{{ $product->gemstoneVariants->first()->ratti_size ?? '' }}">

                        {{-- 3. MATERIAL (Hidden initially) --}}
                        <div id="material_section" style="display:none;">
                            <span class="gem-label">Material</span>
                            <div class="gem-btn-group">
                                <div class="gem-btn active" onclick="updateGemState('material', 'silver', this)">⚪ Silver</div>
                                <div class="gem-btn" onclick="updateGemState('material', 'panchdhatu', this)">🟡 Panchdhatu</div>
                            </div>
                        </div>
                        <input type="hidden" id="sel_mat" value="silver">

                        {{-- 4. Ring Size --}}
                         <div id="ring_size_section" style="display:none;" class="mt-2">
                            <span class="gem-label">Ring Size</span>
                            <select class="form-select form-select-sm w-auto" name="ring_size">
                                <option value="">Select Size</option>
                                @for($i=10; $i<=30; $i++) <option value="{{$i}}">{{$i}}</option> @endfor
                            </select>
                        </div>

                    </div>

                @elseif ($product->variants->count() > 0)
                    {{-- ⚖️ STANDARD WEIGHT DROPDOWN (Your Original Code) --}}
                    <div class="mb-4 bg-light p-2 rounded border" style="max-width: 250px;">
                        <label class="fw-bold small mb-1 d-block text-dark">Select Weight:</label>
                        <select class="form-select form-select-sm border-secondary fw-bold text-dark" id="variant_select" name="variant_id">
                            @foreach ($product->variants as $variant)
                                <option value="{{ $variant->id }}" data-price="{{ $variant->selling_price }}"
                                    data-mrp="{{ $variant->mrp_price }}" data-stock="{{ $variant->quantity }}">
                                    {{ $variant->weight }}g
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                {{-- ================================================= --}}


                {{-- EMI Widget --}}
                @if ($product->emi_available && $product->price > 500)
                    @php $emiPrice = ceil($product->price / 3); @endphp
                    <div class="emi-box border rounded p-2 mb-4 d-flex align-items-center bg-white" style="max-width: 400px;">
                        <span class="badge bg-success me-2" style="font-size: 10px;">NEW</span>
                        <div class="flex-grow-1" style="font-size: 13px;">
                            or <strong>₹{{ $emiPrice }}/month</strong> (3 months)
                            <span class="badge bg-warning text-dark ms-1" style="font-size: 10px;">0% Interest</span>
                        </div>
                    </div>
                @endif

                {{-- Siddh Checkbox --}}
                @if ($product->is_siddh_enabled)
                    <div class="siddh-box p-3 border rounded mb-4" style="background-color: #fcf8f2;">
                        <div class="form-check d-flex align-items-center">
                            <input class="form-check-input me-3" type="checkbox" id="siddh_check" style="width: 25px; height: 25px;">
                            <div>
                                <label class="form-check-label fw-bold text-dark cursor-pointer" for="siddh_check">
                                    Get Siddh Product (+₹{{ number_format($product->siddh_price, 0) }})
                                </label>
                                <small class="d-block text-muted">Energized with mantras.</small>
                            </div>
                        </div>
                    </div>
                @endif
                <input type="hidden" name="is_siddh" id="input_is_siddh" value="0">

                {{-- Buttons --}}
                @if ($product->quantity > 0)
                    <div class="mb-4">
                        <label class="fw-bold small mb-2 d-block">Quantity</label>
                        <div class="input-group" style="width: 140px;">
                            <button class="btn btn-outline-secondary btn-sm" onclick="updateQty('minus')"><i class="las la-minus"></i></button>
                            <input type="text" id="qty_input" name="quantity" class="form-control text-center fs-6 fw-bold" value="1" readonly>
                            <button class="btn btn-outline-secondary btn-sm" onclick="updateQty('plus')"><i class="las la-plus"></i></button>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <button class="btn btn-warning w-50 py-3 fw-bold text-uppercase shadow-sm fs-6" onclick="addToCartFromDetail(this)">Add to Cart</button>
                        <button class="btn btn-dark w-50 py-3 fw-bold text-uppercase shadow-sm fs-6" onclick="openDirectCheckout(this)">Buy Now</button>
                    </div>
                @else
                    <div class="alert alert-danger">Out of Stock</div>
                @endif

                {{-- Delivery Checker (Your Code) --}}
                <div class="delivery-check-box mb-3">
                    <h6 class="fw-bold small mb-2"><i class="las la-truck fs-4 me-2 text-danger"></i> Check Delivery</h6>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Enter pincode" id="pincodeInput" maxlength="6">
                        <button class="btn btn-success text-white fw-bold px-4" onclick="checkDelivery()">Check</button>
                    </div>
                    <div id="deliveryResult" class="small fw-bold mt-2 mb-2" style="display:none;"></div>
                </div>

                {{-- Trust Badge --}}
                <div class="secure-box d-flex align-items-center justify-content-between p-3 rounded mb-3" style="background-color: #e8f5e9;">
                    <div class="d-flex align-items-center">
                        <i class="las la-check-circle fs-3 text-success me-2"></i>
                        <div><div class="fw-bold text-dark">100% Secure</div><div class="text-dark" style="font-size: 12px;">Payment Guarantee</div></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Bottom Sections (Accordions, etc) --}}
    {{-- ... Keep your Accordion section here ... --}}

@endsection

@section('scripts')
<script>
    // 1. Slider Setup
    $('.product-main-slider').slick({ slidesToShow: 1, asNavFor: '.product-thumb-slider', arrows: true, prevArrow:'<button class="slick-prev"><</button>', nextArrow:'<button class="slick-next">></button>' });
    $('.product-thumb-slider').slick({ slidesToShow: 5, asNavFor: '.product-main-slider', focusOnSelect: true, arrows: false });

    // 2. Standard Weight Logic
    const variantSelect = document.getElementById('variant_select');
    if (variantSelect) {
        variantSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            updatePrices(opt.getAttribute('data-price'), opt.getAttribute('data-mrp'));
        });
    }

    // 3. Gemstone Logic (Find Match)
    function updateGemState(key, value, btn) {
        // UI Active
        btn.parentElement.querySelectorAll('.gem-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Update Hidden
        if(key === 'type') document.getElementById('sel_type').value = value;
        if(key === 'ratti') document.getElementById('sel_ratti').value = value;
        if(key === 'material') document.getElementById('sel_mat').value = value;

        // Visibility
        if(key === 'type') {
            const mat = document.getElementById('material_section');
            const ring = document.getElementById('ring_size_section');
            if(value === 'loose') { mat.style.display='none'; ring.style.display='none'; }
            else if(value === 'ring') { mat.style.display='block'; ring.style.display='block'; }
            else { mat.style.display='block'; ring.style.display='none'; }
        }

        findGemPrice();
    }

    function findGemPrice() {
        const type = document.getElementById('sel_type').value;
        const ratti = document.getElementById('sel_ratti').value;
        const mat = document.getElementById('sel_mat').value;
        const variants = JSON.parse(document.getElementById('gem_data').innerText);

        // Find Matching Variant
        const match = variants.find(v => {
            let isMatch = (v.type === type && v.ratti_size == ratti);
            if(type !== 'loose') isMatch = isMatch && (v.material === mat);
            return isMatch;
        });

        if(match) {
            updatePrices(match.price, match.mrp);
            document.getElementById('selected_variant_id').value = match.id;
        }
    }

    // Common Price Updater
    function updatePrices(price, mrp) {
        document.getElementById('base_price').value = price; // For Siddh calc
        document.getElementById('display_price').innerText = parseFloat(price).toLocaleString('en-IN');
        if(document.getElementById('display_mrp')) {
            document.getElementById('display_mrp').innerText = parseFloat(mrp).toLocaleString('en-IN');
            // Discount Logic
            const disc = Math.round(((mrp - price)/mrp)*100);
            document.getElementById('display_discount').innerText = disc;
        }
    }

    // Initialize Gemstone (if active)
    document.addEventListener('DOMContentLoaded', () => {
        if(document.getElementById('gem_data')) findGemPrice();
    });

    function checkDelivery() {
            const pincode = document.getElementById('pincodeInput').value;
            const resultBox = document.getElementById('deliveryResult');
            const dateSpan = document.getElementById('deliveryDate');

            if (pincode.length === 6) {

                // Show Loading State
                dateSpan.innerText = "Checking...";
                resultBox.style.display = 'block';
                resultBox.className = "small text-muted fw-bold mt-2";

                // 🔥 Call Backend Route (BigShip Integrated)
                $.ajax({
                    url: "/check-pincode-delivery/" + pincode,
                    type: "GET",
                    success: function(response) {

                        if (response.status) {
                            // ✅ Success: Show the Date from API
                            dateSpan.innerText = response.date;

                            // Green Style
                            resultBox.className = "small text-success fw-bold mt-2";

                            // Optional: Show "Fast" tag if delivery is within 3 days
                            if (response.days <= 3) {
                                dateSpan.innerHTML +=
                                    " <span class='badge bg-success ms-2' style='font-size:10px;'>⚡ FAST</span>";
                            }
                        } else {
                            // ❌ Error: Not Serviceable
                            resultBox.className = "small text-danger fw-bold mt-2";
                            resultBox.innerText = response.message; // "Service not available"
                        }
                    },
                    error: function() {
                        resultBox.className = "small text-danger fw-bold mt-2";
                        resultBox.innerText = "Unable to fetch delivery date.";
                    }
                });

            } else {
                alert('Please enter valid 6 digit pincode');
                resultBox.style.display = 'none';
            }
        }

        // 🕒 1. FAKE DAILY TIMER (Midnight Countdown)
        const timerDisplay = document.getElementById('countdown');

        if (timerDisplay) {
            function startDailyTimer() {
                // Abhi ka time lo
                const now = new Date();

                // Aaj raat 12 baje ka time set karo (End of Day)
                const midnight = new Date();
                midnight.setHours(24, 0, 0, 0);

                // Time difference nikalo
                let diff = midnight - now;

                // Agar calculation me koi gadbad ho to 12 ghante jod do (Safe side)
                if (diff < 0) {
                    diff = diff + (24 * 60 * 60 * 1000);
                }

                // Hours, Minutes, Seconds calculate karo
                const h = Math.floor((diff / (1000 * 60 * 60)) % 24);
                const m = Math.floor((diff / (1000 * 60)) % 60);
                const s = Math.floor((diff / 1000) % 60);

                // Double digits me dikhao (e.g., 05 instead of 5)
                const hh = (h < 10) ? "0" + h : h;
                const mm = (m < 10) ? "0" + m : m;
                const ss = (s < 10) ? "0" + s : s;

                timerDisplay.innerHTML = `${hh} hr : ${mm} min : ${ss} sec`;
            }

            // Har second update karo
            setInterval(startDailyTimer, 1000);
            startDailyTimer(); // Page load hote hi run karo
        }

        // 🕉️ 2. SIDDH PRICE UPDATE LOGIC
        const siddhCheck = document.getElementById('siddh_check');
        const displayPrice = document.getElementById('display_price');
        const siddhPrice = parseFloat("{{ $product->siddh_price ?? 0 }}");
        const inputSiddh = document.getElementById('input_is_siddh');

        if (siddhCheck) {
            siddhCheck.addEventListener('change', function() {

                // 🔥 MAIN FIX: Hamesha current hidden value uthao (Jo variant change hone par update hoti hai)
                const currentBasePrice = parseFloat(document.getElementById('base_price').value) || 0;

                if (this.checked) {
                    // Price badhao (Current Variant Price + Siddh Price)
                    let newPrice = currentBasePrice + siddhPrice;
                    displayPrice.innerText = newPrice.toLocaleString('en-IN');
                    inputSiddh.value = 1;
                } else {
                    // Price wapas normal (Sirf Current Variant Price)
                    displayPrice.innerText = currentBasePrice.toLocaleString('en-IN');
                    inputSiddh.value = 0;
                }
            });
        }

        // 📦 QUANTITY HANDLER
        function updateQty(action) {
            const input = document.getElementById('qty_input');
            let currentVal = parseInt(input.value);
            const maxStock = parseInt(input.getAttribute('max'));

            if (action === 'plus') {
                if (currentVal < maxStock) {
                    input.value = currentVal + 1;
                } else {
                    alert('Maximum stock limit reached!');
                }
            } else if (action === 'minus') {
                if (currentVal > 1) {
                    input.value = currentVal - 1;
                }
            }
        }
</script>
@endsection
