// Open Modal
function showLoginModal() {
    $('#login_modal').modal('show');
}
var iti;

$(document).ready(function () {
    // 👇 Ye line Modal ko Header se nikaal kar Body me move kar degi
    if ($('#login_modal').length) {
        $('#login_modal').appendTo("body");
    }

    var $catSlider = $('#categoryScroll');

    if ($catSlider.length) {
        $catSlider.on('init', function (event, slick) {
            // Show the slider once initialized to prevent the 1px glitch
            $(this).css({ 'visibility': 'visible', 'opacity': '1' });
        });

        $catSlider.slick({
            dots: false,
            infinite: true,
            speed: 300,
            slidesToShow: 7, // Default for large screens
            slidesToScroll: 1,
            autoplay: false,
            arrows: true, // Show arrows
            prevArrow: '<button type="button" class="slick-prev text-dark"><i class="las la-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next text-dark"><i class="las la-angle-right"></i></button>',
            responsive: [
                {
                    breakpoint: 1200,
                    settings: { slidesToShow: 6 }
                },
                {
                    breakpoint: 992,
                    settings: { slidesToShow: 5 }
                },
                {
                    breakpoint: 768,
                    settings: { slidesToShow: 4 }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 3,
                        arrows: false // Hide arrows on mobile if preferred
                    }
                }
            ]
        });
    }

    if ($('#heroSlider').length) {
        $('#heroSlider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 1500,
            infinite: true,
            arrows: false, // Hide Previous & Next buttons
            dots: false
        });
    }
});

// Initialize intl-tel-input
var input = document.querySelector("#phone_input");
if (input) {
    iti = window.intlTelInput(input, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        initialCountry: "auto",
        separateDialCode: true, // Shows country code next to flag
        geoIpLookup: function (callback) {
            $.get('https://ipinfo.io', function () { }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "in"; // Default to India
                callback(countryCode);
            });
        },
        preferredCountries: ['in', 'us', 'ae', 'gb']
    });
}

// 1. SEND OTP
function sendOtp() {
    var btn = $('#btn-get-otp');
    var errorMsg = $('#phone_error');

    // Reset error
    errorMsg.text('');

    // Check if empty
    if (!iti.getNumber()) {
        errorMsg.text('Please enter a mobile number');
        return;
    }

    // Validate using the library
    if (!iti.isValidNumber()) {
        errorMsg.text('Invalid number');
        return;
    }

    // Get the full international number (e.g., +919876543210)
    var fullPhoneNumber = iti.getNumber();

    btn.prop('disabled', true).text('Sending...');

    // AJAX Request
    $.ajax({
        url: "/send-otp",
        type: "POST",
        data: {
            phone: fullPhoneNumber, // Sending the full E.164 number
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            // Switch to OTP Screen
            $('#step-phone-container').hide();
            $('#step-otp-container').fadeIn();
            $('#display_phone').text(fullPhoneNumber); // Display full number
            startTimer();
        },
        error: function (xhr) {
            var err = JSON.parse(xhr.responseText);
            // Handle specific validation errors from backend if any
            var msg = err.message || 'Something went wrong. Try again.';
            if (err.errors && err.errors.phone) {
                msg = err.errors.phone[0];
            }
            errorMsg.text(msg);
            btn.prop('disabled', false).text('GET OTP');
        }
    });
}

// 2. VERIFY OTP & LOGIN (Updated to use full number)
function verifyOtp() {
    // We use the number from the instance to ensure consistency
    var fullPhoneNumber = iti.getNumber();
    var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val();
    var btn = $('#btn-verify');

    if (otp.length < 4) {
        $('#otp_error').text('Please enter complete OTP');
        return;
    }

    btn.prop('disabled', true).text('Verifying...');

    $.ajax({
        url: "/login-with-otp",
        type: "POST",
        data: {
            phone: fullPhoneNumber,
            otp: otp,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status) {
                location.reload();
            }
        },
        error: function (xhr) {
            var err = JSON.parse(xhr.responseText);
            $('#otp_error').text(err.message || 'Invalid OTP');
            btn.prop('disabled', false).text('LOGIN');
        }
    });
}

// Helper: Auto Focus Next Input
function moveToNext(elem, nextFieldID) {
    if (elem.value.length >= 1) {
        if (nextFieldID === 'submitOtp') {
            verifyOtp(); // Auto submit on last digit
        } else {
            document.getElementById(nextFieldID).focus();
        }
    }
}

// Helper: Edit Phone
function editPhone() {
    $('#step-otp-container').hide();
    $('#step-phone-container').fadeIn();
    $('#btn-get-otp').prop('disabled', false).text('GET OTP');
    $('#otp1, #otp2, #otp3, #otp4').val(''); // Clear OTP
}

// Helper: Timer
function startTimer() {
    var timeLeft = 30;
    var elem = document.getElementById('timer');
    var timerId = setInterval(countdown, 1000);
    function countdown() {
        if (timeLeft == -1) {
            clearTimeout(timerId);
            // Enable Resend Logic here if needed
        } else {
            elem.innerHTML = timeLeft;
            timeLeft--;
        }
    }
}

// Simple function to toggle a search bar (you may need to adapt this)
function toggleSearch() {
    // Select the universal search bar
    var searchBar = $('.header-search-bar');

    if (searchBar.is(':visible')) {
        searchBar.fadeOut(200);
    } else {
        searchBar.fadeIn(200);
        // Focus input for immediate typing
        setTimeout(function () {
            $('#live-search-input').focus();
        }, 100);
    }
}

$(document).ready(function () {
    let timeout = null;

    $('#live-search-input').on('keyup', function () {
        let query = $(this).val();
        let resultBox = $('#search-results-box');

        // Clear previous timeout (Debouncing)
        clearTimeout(timeout);

        if (query.length > 1) {
            // Wait 300ms before searching to avoid too many requests
            timeout = setTimeout(function () {
                $.ajax({
                    url: "/ajax-search", // Route URL
                    method: "GET",
                    data: { q: query },
                    beforeSend: function () {
                        resultBox.html('<div class="text-center py-5"><div class="spinner-border text-dark" role="status"></div></div>');
                    },
                    success: function (data) {
                        resultBox.html(data);
                    }
                });
            }, 300);
        } else {
            resultBox.html(''); // Clear results if empty
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {

    // 1. Initialize Slick Slider for Video Feed
    // Ensure jQuery is loaded before this runs
    if ($('.video-carousel').length) {
        $('.video-carousel').slick({
            dots: false,
            infinite: false, /* Stop at end so user knows */
            speed: 300,
            slidesToShow: 6, /* Desktop: 6 items */
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1400,
                    settings: { slidesToShow: 5 }
                },
                {
                    breakpoint: 1200,
                    settings: { slidesToShow: 4 }
                },
                {
                    breakpoint: 992,
                    settings: { slidesToShow: 3 }
                },
                {
                    breakpoint: 768,
                    settings: { slidesToShow: 2 }
                }
            ]
        });
    }

    // 2. Video Hover & Sound Logic
    const videoCards = document.querySelectorAll('.video-card');

    videoCards.forEach(card => {
        const video = card.querySelector('video');
        const soundBtn = card.querySelector('.btn-sound-toggle');
        const icon = soundBtn.querySelector('i');

        // MOUSE ENTER: Play Video (Muted)
        card.addEventListener('mouseenter', () => {
            if (video.paused) {
                var playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => { console.log("Autoplay prevented"); });
                }
            }
        });

        // MOUSE LEAVE: Pause & Reset
        card.addEventListener('mouseleave', () => {
            video.pause();
            // video.currentTime = 0; // Optional: Reset to start

            // Auto-mute when leaving
            video.muted = true;
            icon.classList.remove('la-volume-up');
            icon.classList.add('la-volume-mute');
        });

        // SOUND TOGGLE
        if (soundBtn) {
            soundBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (video.muted) {
                    video.muted = false;
                    icon.classList.remove('la-volume-mute');
                    icon.classList.add('la-volume-up');
                } else {
                    video.muted = true;
                    icon.classList.remove('la-volume-up');
                    icon.classList.add('la-volume-mute');
                }
            });
        }
    });

    if ($('.category-product-slider').length) {
        $('.category-product-slider').slick({
            dots: false,
            infinite: false, /* Loop band kar diya */
            speed: 300,
            slidesToShow: 5, /* 👈 यहाँ हमने 5 कर दिया है */
            slidesToScroll: 1,
            arrows: true,
            responsive: [
                {
                    breakpoint: 1600,
                    settings: { slidesToShow: 5 }
                },
                {
                    breakpoint: 1400,
                    settings: { slidesToShow: 4 }
                },
                {
                    breakpoint: 992,
                    settings: { slidesToShow: 3 }
                },
                {
                    breakpoint: 768,
                    settings: { slidesToShow: 2, arrows: false }
                }
            ]
        });
    }

    if ($('.testimonial-slider').length) {
        $('.testimonial-slider').slick({
            dots: true,          /* Show dots below */
            arrows: false,       /* Hide arrows for cleaner look */
            infinite: true,      /* Loop forever */
            speed: 800,          /* Transition speed */
            slidesToShow: 2,     /* Show 2 reviews at once on PC */
            slidesToScroll: 1,
            autoplay: true,      /* ✅ Automatic Sliding */
            autoplaySpeed: 4000, /* ✅ Changes every 4 seconds */
            responsive: [
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 1 /* Show 1 review on mobile/tablet */
                    }
                }
            ]
        });
    }
});

// ---------------------------------------------------
// 🛒 1. SAFE SIDE CART INSTANCE HELPER
// ---------------------------------------------------
function getSideCartInstance() {
    var el = document.getElementById('sideCart');
    // Check if instance exists, otherwise create new
    return bootstrap.Offcanvas.getInstance(el) || new bootstrap.Offcanvas(el);
}

// ---------------------------------------------------
// 🔄 2. REFRESH CART DATA (WITHOUT OPENING DRAWER)
// ---------------------------------------------------
function refreshSideCartData() {
    $.get("/cart/side-cart-html", function (res) {
        // HTML Update
        $('#side_cart_body').html(res.html);
        $('#side_cart_count').text(res.count);
        $('#cart_subtotal').text('₹' + res.subtotal);
        $('#cart_savings').text('₹' + res.savings);

        // Header Badge Update
        if (res.count > 0) {
            $('#cart-badge').text(res.count).show();
            $('#side_cart_footer').fadeIn();
        } else {
            $('#cart-badge').hide();
            $('#side_cart_footer').hide();
        }
    });
}

// ---------------------------------------------------
// 📂 3. OPEN SIDE CART (AND FETCH DATA)
// ---------------------------------------------------
function openSideCart() {
    var sideCart = getSideCartInstance();
    sideCart.show(); // Sirf tab call karein jab kholna ho
    refreshSideCartData(); // Data load karein
}

// ---------------------------------------------------
// 🛒 4. ADD TO CART (Open Drawer after adding)
// ---------------------------------------------------
function addToCart(productId, quantity, isSiddh, btnElement) {
    var btn = $(btnElement);
    var originalText = btn.html();

    if (!productId) {
        productId = $(btn).data('id');
        quantity = $('#qty_input').val();
        isSiddh = $('#input_is_siddh').val();
    }

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        url: "/add-to-cart",
        type: "POST",
        data: {
            product_id: productId,
            quantity: quantity,
            is_siddh: isSiddh,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            if (res.status) {
                // ✅ Success
                openSideCart();
                btn.html('Added ✔').removeClass('btn-outline-dark').addClass('btn-success');
                setTimeout(() => {
                    btn.html(originalText).prop('disabled', false).removeClass('btn-success').addClass('btn-outline-dark');
                }, 2000);
            } else {
                // ❌ Access Denied or Error
                alert(res.message); // Yahan Admin wala error msg dikhega
                btn.html(originalText).prop('disabled', false);
            }
        },
        error: function () {
            alert('Error adding to cart');
            btn.html(originalText).prop('disabled', false);
        }
    });
}

// ---------------------------------------------------
// ➕ 5. UPDATE QTY (ONLY REFRESH DATA - NO FADE ISSUE)
// ---------------------------------------------------
function updateSideCartQty(cartId, action) {
    // ⚠️ IMPORTANT: Yahan hum 'openSideCart()' call nahi karenge
    // Sirf data refresh karenge taaki screen black na ho

    $.post("/cart/update-quantity-side", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        cart_id: cartId,
        action: action
    }, function (res) {
        if (res.status) {
            refreshSideCartData(); // ✅ Sirf Content Update Hoga
        } else {
            alert(res.message);
        }
    });
}

// ---------------------------------------------------
// 🗑️ 6. REMOVE ITEM
// ---------------------------------------------------
function removeFromSideCart(id) {
    $.get("/cart/remove/" + id, function () {
        refreshSideCartData(); // ✅ Sirf Content Update Hoga
    });
}

// 🚀 5. CHECKOUT FROM SIDE CART
function initiateCartCheckout() {
    // 1. Close Side Drawer
    var sideCartEl = document.getElementById('sideCart');
    var sideCart = bootstrap.Offcanvas.getInstance(sideCartEl);
    if (sideCart) sideCart.hide();

    // 2. Get Values from Side Cart HTML
    // Text Example: "₹ 1,000"
    let subtotalText = $('#cart_subtotal').text();
    let savingsText = $('#cart_savings').text(); // Ye side cart me hota hai

    // 3. Convert to Numbers
    let subtotal = parseFloat(subtotalText.replace(/[^\d.]/g, '')) || 0;
    let savings = parseFloat(savingsText.replace(/[^\d.]/g, '')) || 0;

    // MRP = Selling Price + Savings
    let mrp = subtotal + savings;

    // 4. Call Modal with ALL Data
    openCheckoutModal(subtotal, mrp, savings);
}

// 🛒 DETAIL PAGE HELPER (Values collect karne ke liye)
function addToCartFromDetail(btn) {
    // Detail page se values uthao
    var productId = $('#base_price').next('span').next('input').val(); // Hacky? No, let's use a cleaner way.
    // Better: Blade se ID pass karein button me (niche dekhein)

    // Values from Inputs
    var qty = $('#qty_input').val();
    var isSiddh = $('#input_is_siddh').val();
    var prodId = $(btn).data('id'); // Data attribute se ID lenge

    addToCart(prodId, qty, isSiddh, btn);
}

// 🟢 GLOBAL AUTH STATUS (Meta tag se value lenge)
// Layout file ke <head> me ye zaroor ho: <meta name="is-logged-in" content="{{ Auth::check() ? '1' : '0' }}">

// 🛒 1. OPEN CHECKOUT MODAL
// function openDirectCheckout(btn) {
//     // ... (Data collection logic - same as before) ...
//     var prodId = $(btn).data('id');
//     var qty = $('#qty_input').val() || 1;
//     var priceText = $('#display_price').text().replace(/,/g, '');
//     var price = parseFloat(priceText);
//     var mrpText = $('#display_mrp').text().replace(/,/g, '');
//     var mrp = parseFloat(mrpText) || price;

//     var img = $('.product-main-slider .slick-current img').attr('src');
//     if (!img) img = $('.product-main-slider img').first().attr('src');
//     var title = $('h1.font-heading').text().trim();

//     currentCartTotal = price * qty;
//     var totalMrp = mrp * qty;
//     var productDiscount = totalMrp - currentCartTotal;
//     currentProductId = prodId;

//     // UI Updates
//     $('#summ_img').attr('src', img);
//     $('#summ_name').text(title);
//     $('#summ_qty').text('Qty: ' + qty);
//     $('#summ_total').text('₹' + currentCartTotal.toLocaleString('en-IN'));

//     // --- Price Breakdown Logic ---
//     if (productDiscount > 0) {
//         $('#summ_mrp_display').text('₹' + totalMrp.toLocaleString('en-IN')).show();

//         // 🔥 FIX: Simple .show() use karein
//         $('#row_mrp_total').show();
//         $('#bill_mrp').text('₹' + totalMrp.toLocaleString('en-IN'));

//         // 🔥 FIX: Simple .show() use karein
//         $('#row_product_discount').show();
//         $('#bill_product_discount').text('- ₹' + productDiscount.toLocaleString('en-IN'));
//     } else {
//         $('#summ_mrp_display').hide();
//         $('#row_mrp_total').hide();
//         $('#row_product_discount').hide();
//     }

//     // Totals
//     $('#bill_subtotal').text('₹' + currentCartTotal.toLocaleString('en-IN'));
//     $('#bill_final_total').text('₹' + currentCartTotal.toLocaleString('en-IN'));
//     $('#btn_pay_amount').text('₹' + currentCartTotal.toLocaleString('en-IN'));

//     // ✅ FORCE RESET COUPON UI
//     $('#row_coupon_discount').hide();
//     $('#bill_coupon_discount').text('- ₹0');

//     $('#coupon_applied_box').hide();
//     $('#coupon_input_group').show();
//     $('#coupon_code').val('');
//     $('#coupon_msg').hide();
//     $('#coupon_list_box').hide();

//     // Hidden Inputs
//     $('#final_buy_mode').val('direct');
//     $('#final_product_id').val(prodId);
//     $('#final_quantity').val(qty);
//     $('#final_is_siddh').val($('#input_is_siddh').val());
//     $('#final_coupon_code').val('');

//     // Open Modal (jQuery)
//     $('#checkoutModal').modal('show');

//     // ... Login check logic (same as before) ...
//     const isLoggedIn = $('meta[name="is-logged-in"]').attr('content') === '1';
//     if (isLoggedIn) {
//         $.get("/checkout/get-user-data", function(data) {
//              if (data.has_address) {
//                  $('#saved_address_list').html(data.html).show();
//                  $('#new_address_form').hide();
//              } else {
//                  $('#saved_address_list').hide();
//                  $('#new_address_form').show();
//              }
//              $('#user_phone_display').text(data.user_phone);
//              showStep('address');
//         });
//     } else {
//         showStep('login');
//     }
// }

// 🛒 1. OPEN DIRECT CHECKOUT (Buy Now Button) - Updated
// 🛒 1. OPEN DIRECT CHECKOUT (Buy Now Button) - Fixed Calculation
// 🛒 1. OPEN DIRECT CHECKOUT (Buy Now Button) - Fixed Calculation
function openDirectCheckout(btn) {
    // 1. Data Collection
    var prodId = $(btn).data('id');
    var qty = $('#qty_input').val() || 1;
    var isSiddh = $('#input_is_siddh').val();

    // 2. Price Calculation
    var priceText = $('#display_price').text().replace(/,/g, '');
    var price = parseFloat(priceText);
    var mrpText = $('#display_mrp').text().replace(/,/g, '');
    var mrp = parseFloat(mrpText) || price;

    var img = $('.product-main-slider .slick-current img').attr('src');
    if (!img) img = $('.product-main-slider img').first().attr('src');
    var title = $('h1.font-heading').text().trim();

    // 3. Set Global Variables
    currentCartTotal = price * qty;
    currentProductId = prodId;

    // 🔥 RESET ALL DISCOUNTS
    appliedCouponCode = null;
    appliedGamingCode = null;
    window.appliedGamingAmount = 0;

    var totalMrp = mrp * qty;
    var productDiscount = totalMrp - currentCartTotal;

    // 4. 🔥 LOAD GAME WINNINGS
    let gameAmt = localStorage.getItem('gaming_coupon_amount');
    let gameCode = localStorage.getItem('gaming_coupon_code');

    if (gameAmt && gameCode) {
        window.appliedGamingAmount = parseFloat(gameAmt);
        appliedGamingCode = gameCode;
        console.log("Game Coupon Applied:", window.appliedGamingAmount);
    }

    // 5. UI Updates
    $('#summ_img').attr('src', img);
    $('#summ_name').text(title);
    $('#summ_qty').text('Qty: ' + qty);

    let fmtBase = currentCartTotal.toLocaleString('en-IN');
    let fmtMrp = totalMrp.toLocaleString('en-IN');

    $('#summ_total').text('₹' + fmtBase);
    $('#bill_subtotal').text('₹' + fmtBase);

    // 6. Handle Product Discount Rows
    if (productDiscount > 0) {
        $('#summ_mrp_display').text('₹' + fmtMrp).show();
        $('#row_mrp_total').show();
        $('#bill_mrp').text('₹' + fmtMrp);
        $('#row_product_discount').show();
        $('#bill_product_discount').text('- ₹' + productDiscount.toLocaleString('en-IN'));
    } else {
        $('#summ_mrp_display').hide();
        $('#row_mrp_total').hide();
        $('#row_product_discount').hide();
    }

    // 7. 🔥 SHOW/HIDE GAMING ROW
    if (window.appliedGamingAmount > 0) {
        if ($('#row_gaming_discount').length === 0) {
            $('<div id="row_gaming_discount" class="d-flex justify-content-between mb-1 small text-primary fw-bold"><span><i class="las la-gamepad"></i> Game Reward</span><span id="bill_gaming_discount">- ₹0</span></div>').insertBefore('#row_coupon_discount');
        }
        $('#row_gaming_discount').show();
        $('#bill_gaming_discount').text('- ₹' + window.appliedGamingAmount);
    } else {
        $('#row_gaming_discount').hide();
    }

    // 8. Reset Admin Coupon UI
    $('#row_coupon_discount').hide();
    $('#bill_coupon_discount').text('- ₹0');
    $('#coupon_applied_box').hide();
    $('#coupon_input_group').show();
    $('#coupon_code').val('');
    $('#coupon_msg').hide();
    $('#coupon_list_box').hide();

    // 9. Hidden Inputs
    $('#final_buy_mode').val('direct');
    $('#final_product_id').val(prodId);
    $('#final_quantity').val(qty);
    $('#final_is_siddh').val(isSiddh);
    $('#final_coupon_code').val('');

    // 🔥 Inject Gaming Input
    if ($('#final_gaming_coupon_code').length === 0) {
        $('<input type="hidden" name="gaming_coupon_code" id="final_gaming_coupon_code">').appendTo('#finalPaymentForm');
    }
    $('#final_gaming_coupon_code').val(appliedGamingCode);

    // 10. 🔥 CALCULATE TOTAL
    calculateFinalTotal();

    // 11. Open Modal
    $('#checkoutModal').modal('show');

    // 12. Login Logic
    const isLoggedIn = $('meta[name="is-logged-in"]').attr('content') === '1';
    if (isLoggedIn) {
        $.get("/checkout/get-user-data", function (data) {
            if (data.has_address) {
                $('#saved_address_list').html(data.html).show();
                $('#new_address_form').hide();
            } else {
                $('#saved_address_list').hide();
                $('#new_address_form').show();
            }
            $('#user_phone_display').text(data.user_phone);
            showStep('address');
        });
    } else {
        showStep('login');
    }
}

// Helper: Switch Steps
function showStep(step) {
    $('#step_login, #step_address, #step_payment').hide();
    $('#step_' + step).fadeIn();
}

// Global variable for Checkout Modal Input
var itiCheckout;

// 1. Initialize Plugin on Document Ready
$(document).ready(function () {
    var inputChk = document.querySelector("#chk_mobile");

    if (inputChk) {
        itiCheckout = window.intlTelInput(inputChk, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            initialCountry: "auto",
            separateDialCode: true, // Flag alag, code alag
            geoIpLookup: function (callback) {
                $.get('https://ipinfo.io', function () { }, "jsonp").always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "in";
                    callback(countryCode);
                });
            },
            preferredCountries: ['in', 'us', 'ae', 'gb']
        });
    }
});


// 🔐 2. LOGIN LOGIC FOR CHECKOUT MODAL (Updated)
function sendCheckoutOtp() {
    var btn = $('#btn_send_otp');
    var errorMsg = $('#chk_mobile_error');

    // Clear previous errors
    errorMsg.text('');

    // Check if valid using library
    if (!itiCheckout.isValidNumber()) {
        errorMsg.text('Please enter a valid mobile number.');
        return;
    }

    // 🔥 Get Full Number with Country Code (e.g. +919876543210)
    var fullPhone = itiCheckout.getNumber();

    btn.text('Sending...').prop('disabled', true);

    $.post("/send-otp", {
        phone: fullPhone, // Sending full number
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function (res) {
        if (res.status) {
            $('#chk_otp_box').slideDown();
            btn.hide();
            // Optional: User ko dikhane ke liye format kar sakte hain
            // $('#chk_mobile').val(fullPhone);
        } else {
            errorMsg.text(res.message || 'Failed to send OTP');
            btn.text('CONTINUE').prop('disabled', false);
        }
    }).fail(function () {
        errorMsg.text('Error sending OTP. Please try again.');
        btn.text('CONTINUE').prop('disabled', false);
    });
}

// function verifyCheckoutOtp() {
//     // Verify ke liye bhi full number chahiye
//     var fullPhone = itiCheckout.getNumber();
//     var otp = $('#chk_otp').val();
//     var btn = $('#btn_verify_otp');

//     if (!otp || otp.length < 4) {
//         alert('Please enter valid OTP');
//         return;
//     }

//     btn.text('Verifying...').prop('disabled', true);

//     $.post("/login-with-otp", {
//         phone: fullPhone,
//         otp: otp,
//         _token: $('meta[name="csrf-token"]').attr('content')
//     }, function (res) {
//         if (res.status) {
//             // ✅ LOGIN SUCCESS
//             $('#step_login').hide();

//             $.get("/checkout/get-user-data", function(data) {
//                 $('#user_phone_display').text(data.user_phone);
//                 $('#chk_name').val(data.user_name);

//                 if (data.has_address) {
//                     $('#saved_address_list').html(data.html).show();
//                     $('#new_address_form').hide();
//                 } else {
//                     $('#saved_address_list').hide();
//                     $('#new_address_form').show();
//                 }

//                 $('#step_address').fadeIn();
//                 $('meta[name="is-logged-in"]').attr('content', '1');
//             });

//         } else {
//             alert('Invalid OTP');
//             btn.text('VERIFY OTP').prop('disabled', false);
//         }
//     }).fail(function () {
//         alert('Server Error during verification');
//         btn.text('VERIFY OTP').prop('disabled', false);
//     });
// }
// function sendCheckoutOtp() {
//     var phone = $('#chk_mobile').val();
//     if (phone.length != 10) { alert('Valid number enter karein'); return; }

//     $('#btn_send_otp').text('Sending...').prop('disabled', true);

//     $.post("/send-otp", { phone: phone, _token: $('meta[name="csrf-token"]').attr('content') }, function (res) {
//         $('#chk_otp_box').slideDown();
//         $('#btn_send_otp').hide();
//     });
// }

// function verifyCheckoutOtp() {
//     var phone = $('#chk_mobile').val();
//     var otp = $('#chk_otp').val();

//     $.post("/login-with-otp", { phone: phone, otp: otp, _token: $('meta[name="csrf-token"]').attr('content') }, function (res) {
//         if (res.status) {
//             // Login Success -> Step 2 (Address) par jao
//             window.location.reload();
//             $('#user_phone_display').text(phone); // Number update karo
//             showStep('address');
//         } else {
//             alert('Invalid OTP');
//         }
//     });
// }

// ⚡ 3. ADDRESS LOGIC (Pincode Fetch)
function fetchCheckoutCityState() {
    let pincode = $('#chk_pincode').val();
    if (pincode.length === 6) {
        $('#chk_pincode_msg').text('Checking...');

        $.get("https://api.postalpincode.in/pincode/" + pincode, function (data) {
            if (data[0].Status === 'Success') {
                let details = data[0].PostOffice[0];
                $('#chk_city').val(details.District);
                $('#chk_state').val(details.State);

                $('#chk_pincode_msg').text('✅ Verified').removeClass('text-danger').addClass('text-success');
                $('#address_expanded').slideDown();
            } else {
                $('#chk_pincode_msg').text('❌ Invalid').addClass('text-danger');
                $('#address_expanded').slideUp();
            }
        });
    }
}

// ✅ CORRECTED & IMPROVED verifyCheckoutOtp
function verifyCheckoutOtp() {
    // 1. Get Elements
    var otp = $('#chk_otp').val();
    var btn = $('#btn_verify_otp');

    // 2. Validate OTP Input
    if (!otp || otp.length < 4) {
        alert('Please enter valid OTP');
        return;
    }

    // 3. Get Phone Number (Handle both intl-input and standard input)
    var fullPhone = "";
    if (typeof itiCheckout !== 'undefined' && itiCheckout.isValidNumber()) {
        // Agar Library active hai to wahan se number lo
        fullPhone = itiCheckout.getNumber();
    } else {
        // Fallback: Agar library fail ho to direct value lo
        fullPhone = $('#chk_mobile').val();
    }

    // 4. Button Loading State
    btn.text('Verifying...').prop('disabled', true);

    // 5. AJAX Call
    $.post("/login-with-otp", {
        phone: fullPhone,
        otp: otp,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function (res) {
        // ✅ SUCCESS BLOCK (HTTP 200 OK)
        if (res.status) {

            // Hide Login Step
            $('#step_login').hide();

            // Fetch User Data & Address
            $.get("/checkout/get-user-data", function (data) {

                // Update UI
                $('#user_phone_display').text(data.user_phone);
                $('#chk_name').val(data.user_name);

                // Show Address List or Form
                if (data.has_address) {
                    $('#saved_address_list').html(data.html).show();
                    $('#new_address_form').hide();
                } else {
                    $('#saved_address_list').hide();
                    $('#new_address_form').show();
                }

                // Show Next Step
                $('#step_address').fadeIn();

                // Update Auth State
                $('meta[name="is-logged-in"]').attr('content', '1');
            });

        } else {
            // Logic handled here if server returns 200 but status false
            alert(res.message || 'Invalid OTP');
            btn.text('VERIFY OTP').prop('disabled', false);
        }

    }).fail(function (xhr) {
        // ❌ ERROR BLOCK (HTTP 401, 422, 500 etc.)

        var errorMessage = "Something went wrong";

        // Try to get message from JSON response
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        } else if (xhr.responseText) {
            // Fallback for simple text errors
            try {
                var err = JSON.parse(xhr.responseText);
                errorMessage = err.message || errorMessage;
            } catch (e) { }
        }

        // Show Actual Error (e.g., "Invalid OTP")
        alert(errorMessage);

        // Reset Button
        btn.text('VERIFY OTP').prop('disabled', false);
    });
}

// function verifyCheckoutOtp() {
//     var phone = $('#chk_mobile').val();
//     var otp = $('#chk_otp').val();
//     var btn = $('#btn-verify'); // Ensure button has this ID or pass 'event.target'

//     // Button loading state
//     $(btn).text('Verifying...').prop('disabled', true);

//     $.post("/login-with-otp", {
//         phone: phone,
//         otp: otp,
//         _token: $('meta[name="csrf-token"]').attr('content')
//     }, function (res) {
//         if (res.status) {

//             // ✅ LOGIN SUCCESS: AB PAGE RELOAD NAHI KARENGE
//             // Seedha User Data aur Address mangwayenge

//             $.get("/checkout/get-user-data", function(data) {

//                 // 1. Update User Phone on UI
//                 $('#user_phone_display').text(data.user_phone);
//                 $('#chk_name').val(data.user_name); // Auto fill name in new form

//                 // 2. Decide: Show List or New Form?
//                 if (data.has_address) {
//                     // Address hai -> List dikhao
//                     $('#saved_address_list').html(data.html).show();
//                     $('#new_address_form').hide();
//                 } else {
//                     // Address nahi hai -> Form dikhao
//                     $('#saved_address_list').hide();
//                     $('#new_address_form').show();
//                 }

//                 // 3. Move to Step 2 (Address)
//                 showStep('address');

//                 // 4. Update Auth Meta Tag (Optional but good)
//                 $('meta[name="is-logged-in"]').attr('content', '1');
//             });

//         } else {
//             alert('Invalid OTP');
//             $(btn).text('VERIFY OTP').prop('disabled', false);
//         }
//     }).fail(function () {
//         alert('Server Error');
//         $(btn).text('VERIFY OTP').prop('disabled', false);
//     });
// }

// 💾 4. SAVE & CONTINUE
function saveAndContinue() {
    if (!$('#chk_name').val() || !$('#chk_house').val()) { alert('Fill all fields'); return; }

    var btn = event.target;
    $(btn).text('Saving...').prop('disabled', true);

    var phoneInput = $('#chk_mobile').val();
    // 🔥 NEW: Get Email Value
    let email = $('#chk_email').val();

    $.post("/checkout/save-address-ajax", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        name: $('#chk_name').val(),
        phone: phoneInput,
        email: email,
        pincode: $('#chk_pincode').val(),
        city: $('#chk_city').val(),
        state: $('#chk_state').val(),
        address_line1: $('#chk_house').val() + ', ' + $('#chk_area').val(),
        type: $('input[name="addr_type"]:checked').val()
    }, function (res) {
        if (res.status) {
            $('#final_address_id').val(res.address_id);
            showStep('payment'); // Go to Payment
        }
    }).fail(function () {
        alert('Error saving address');
        $(btn).text('CONTINUE').prop('disabled', false);
    });
}

// 🏠 SELECT SAVED ADDRESS & CONTINUE
function useSavedAddress() {
    // Check which radio is selected
    var selectedId = $('input[name="selected_address"]:checked').val();

    if (!selectedId) {
        alert("Please select an address or add a new one.");
        return;
    }

    // Set Hidden Field ID
    $('#final_address_id').val(selectedId);

    // Switch to Payment
    $('#step_address').fadeOut(200, function () {
        $('#modalTitle').text('Make Payment');
        $('#step_payment').fadeIn(200).removeClass('d-none');
    });
}

// ⚡ SMART ADDRESS LOGIC (Auto-Fill if Exists)
function fetchCheckoutCityState() {
    let pincode = $('#chk_pincode').val();

    // Sirf tab chalega jab 6 digit ho
    if (pincode.length === 6) {

        $('#chk_pincode_msg').text('Checking records...').removeClass('text-danger text-success').addClass('text-muted');

        // 1. Apne Database me Check karo
        $.ajax({
            url: "/checkout/check-address/" + pincode,
            type: "GET",
            success: function (response) {

                if (response.found) {
                    // ✅ ADDRESS MIL GAYA -> Auto Fill Karo
                    let addr = response.data;

                    $('#chk_city').val(addr.city);
                    $('#chk_state').val(addr.state);
                    $('#chk_name').val(addr.name);

                    // Address Split (Agar comma se separate kiya tha)
                    // Ya seedha fill karein agar logic complex nahi hai
                    $('#chk_house').val(getHousePart(addr.address_line1));
                    $('#chk_area').val(getAreaPart(addr.address_line1));

                    // Type Select Karo
                    $(`input[name="addr_type"][value="${addr.type}"]`).prop('checked', true);

                    // Success Message
                    $('#chk_pincode_msg').text('✅ Saved Address Found!').removeClass('text-muted').addClass('text-success');

                    // Form Open Karo
                    $('#address_expanded').slideDown();

                } else {
                    // ❌ ADDRESS NAHI MILA -> External API Call Karo (New Address Logic)
                    fetchFromPostalApi(pincode);
                }
            },
            error: function () {
                // Agar error aaye to bhi External API try karo fallback ke liye
                fetchFromPostalApi(pincode);
            }
        });
    }
}

// 🌍 Helper: External API Call
function fetchFromPostalApi(pincode) {
    $('#chk_pincode_msg').text('Fetching City/State...');

    $.get("https://api.postalpincode.in/pincode/" + pincode, function (data) {
        if (data[0].Status === 'Success') {
            let details = data[0].PostOffice[0];

            // Fill City State
            $('#chk_city').val(details.District);
            $('#chk_state').val(details.State);

            // Clear other fields (Kyuki ye naya address hai)
            $('#chk_house').val('');
            $('#chk_area').val('');
            // Name wahi rehne do jo Auth user ka hai

            $('#chk_pincode_msg').text('✅ New Location Detected').addClass('text-success');
            $('#address_expanded').slideDown();
        } else {
            $('#chk_pincode_msg').text('❌ Invalid Pincode').addClass('text-danger');
            $('#address_expanded').slideUp();
        }
    });
}

// Helper to extract House/Area (Optional - Simple splitting)
function getHousePart(fullAddr) {
    if (!fullAddr) return '';
    return fullAddr.split(',')[0]; // Comma se pehle wala House
}
function getAreaPart(fullAddr) {
    if (!fullAddr) return '';
    let parts = fullAddr.split(',');
    parts.shift(); // Pehla hissa hata do
    return parts.join(',').trim(); // Baaki sab Area
}

// 💳 PROCESS PAYMENT (Place Order)
// 💳 PROCESS PAYMENT (Place Order)
function processPayment() {
    var btn = $('#btn_place_order');
    btn.prop('disabled', true).text('Processing...');

    // 1. Hidden inputs update (Optional, but good for sync)
    $('#final_coupon_code').val(appliedCouponCode);
    $('#final_gaming_coupon_code').val(appliedGamingCode);

    // 2. Form Data Collect
    var formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        payment_method: $('input[name="payment_method"]:checked').val(),
        buy_mode: $('#final_buy_mode').val(),
        product_id: $('#final_product_id').val(),
        quantity: $('#final_quantity').val(),
        is_siddh: $('#final_is_siddh').val(),
        address_id: $('#final_address_id').val(),

        // ✅ Send BOTH Coupon Codes
        coupon_code: appliedCouponCode,          // Admin Coupon
        gaming_coupon_code: appliedGamingCode    // 🔥 Game Coupon (Ye add kiya hai)
    };

    $.post("/checkout/place-order", formData, function (res) {

        if (res.status === 'razorpay') {
            // 🟣 OPEN RAZORPAY MODAL
            var options = {
                "key": res.key,
                "amount": res.amount, // Backend se ab sahi (discounted) amount aayega
                "currency": "INR",
                "name": res.name,
                "description": res.description,
                "image": res.image,
                "order_id": res.rzp_order_id,
                "handler": function (response) {
                    // Payment Success -> Verify on Server
                    verifyServerPayment(response, res.order_id);
                },
                "prefill": res.prefill,
                "theme": { "color": "#ff6f00" },
                "modal": {
                    "ondismiss": function () {
                        console.log('Razorpay Modal Closed. Starting Cancel Process...');

                        // Disable button again
                        btn.prop('disabled', true).text('Cancelling...');

                        // 🔥 Updated Code with Error Handling
                        $.ajax({
                            url: "/checkout/cancel-order",
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                order_id: res.order_id
                            },
                            success: function (response) {
                                console.log('Server Response:', response);
                                window.location.href = "/orders";
                            },
                            error: function (xhr, status, error) {
                                // 🔥 Agar error aya to yahan dikhega
                                console.error("Cancel Failed:", error);
                                console.error("Response:", xhr.responseText);

                                alert("Error cancelling order: " + xhr.status + " " + error);

                                // Error ke baad bhi redirect kar do taaki user phase na rahe
                                window.location.href = "/orders";
                            }
                        });
                    }
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();

        } else if (res.status === 'success') {
            // 🟢 COD Success
            window.location.href = "/orders";
        } else {
            alert(res.message);
            btn.prop('disabled', false).text('Place Order');
        }

    }).fail(function () {
        alert('Server Error');
        btn.prop('disabled', false).text('Place Order');
    });
}

// 🔐 VERIFY PAYMENT ON SERVER
function verifyServerPayment(paymentData, localOrderId) {

    console.log("Verifying Payment:", paymentData, localOrderId);

    // 1. 🔥 UI UPDATE: Modal ka content badal kar SUCCESS dikha do
    // Isse user ko turant pata chal jayega ki payment ho gayi hai
    let successHtml = `
        <div class="modal-body text-center py-5">
            <div class="mb-3">
                <i class="las la-check-circle text-success" style="font-size: 6rem; animation: zoomIn 0.5s;"></i>
            </div>
            <h2 class="fw-bold text-success">Payment Successful!</h2>
            <p class="text-muted mb-4">Please wait, we are confirming your order...</p>

            <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    // Checkout Modal ke andar ka HTML replace kar do
    $('#checkoutModal .modal-content').html(successHtml);

    // 2. Backend Call (Background me chalega)
    $.post("/checkout/verify-payment", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        razorpay_payment_id: paymentData.razorpay_payment_id,
        razorpay_order_id: paymentData.razorpay_order_id,
        razorpay_signature: paymentData.razorpay_signature,
        order_id: localOrderId
    }, function (res) {
        if (res.status) {
            // ✅ Success - Redirect Logic
            setTimeout(function () {
                window.location.replace("/orders");
            }, 1000); // 1 sec dikha kar redirect kar do
        } else {
            // Error handling
            alert(res.message);
            window.location.reload();
        }
    }).fail(function (xhr) {
        alert('Verification Server Error: ' + xhr.responseText);
        window.location.reload();
    });
}


// Open Modal
function openReviewModal() {
    var myModal = new bootstrap.Modal(document.getElementById('reviewModal'));
    myModal.show();
}

// Handle Star Rating Click inside Modal
function setRating(val) {
    // Set Hidden Input Value
    document.getElementById('rating_value').value = val;

    // Update Star Icons
    const stars = document.querySelectorAll('.rating-input i');
    stars.forEach((star, index) => {
        if (index < val) {
            star.classList.remove('lar'); // Remove Empty
            star.classList.add('las');    // Add Filled
        } else {
            star.classList.remove('las'); // Remove Filled
            star.classList.add('lar');    // Add Empty
        }
    });

    // Update Text
    const texts = ["Terrible", "Bad", "Average", "Good", "Excellent"];
    document.getElementById('rating-text').innerText = texts[val - 1];
}

function filterReviews(productId) {
    let sort = document.getElementById('reviewSort').value;
    let container = document.getElementById('reviewListContainer');

    // Show loading
    container.style.opacity = '0.5';

    $.ajax({
        url: "/reviews/filter",
        type: "GET",
        data: {
            product_id: productId,
            sort: sort
        },
        success: function (response) {
            container.innerHTML = response.html;
            container.style.opacity = '1';
        },
        error: function () {
            alert('Error loading reviews');
            container.style.opacity = '1';
        }
    });
}

// Global Variables for Checkout
let currentCartTotal = 0;
let currentProductId = 0;
let appliedCouponCode = null;

// 🔥 NEW VARIABLES FOR GAMING COUPON
let appliedGamingCode = null;
let appliedGamingAmount = 0;

// 1. Fetch Coupons (View All Click)
function fetchCoupons() {
    $('#coupon_list_box').slideToggle();

    $.ajax({
        url: "{{ route('get.coupons') }}",
        type: "GET",
        success: function (response) {
            let html = '';
            if (response.length > 0) {
                response.forEach(c => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center bg-white border rounded p-2 mb-2">
                            <div>
                                <strong class="text-uppercase text-primary border border-primary px-2 rounded small me-2">${c.code}</strong>
                                <small class="text-muted d-block mt-1" style="font-size:10px;">${c.type == 'fixed' ? 'Flat ₹' + c.value + ' OFF' : c.value + '% OFF'}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-dark py-0" onclick="$('#coupon_code').val('${c.code}'); applyCoupon();">Apply</button>
                        </div>
                    `;
                });
            } else {
                html = '<p class="text-center small text-muted">No coupons available.</p>';
            }
            $('#coupon_list_box').html(html);
        }
    });
}

// 2. Apply Coupon Logic
function applyCoupon() {
    let code = $('#coupon_code').val();
    let msg = $('#coupon_msg');

    // Reset Message
    msg.text('').removeClass('text-success text-danger');

    if (!code) {
        msg.text('Please enter a coupon code').addClass('text-danger');
        return;
    }

    // Get current total (Parse from UI or Variable)
    // Note: Ensure currentCartTotal is set when modal opens
    // e.g. currentCartTotal = 1500;

    $.ajax({
        url: "{{ route('apply.coupon') }}",
        type: "POST",
        data: {
            code: code,
            cart_total: currentCartTotal,
            _token: "{{ csrf_token() }}"
        },
        success: function (res) {
            if (res.status) {
                // Success
                $('#coupon_applied_box').slideDown();
                $('#applied_code').text(code);
                $('#saved_amount').text('₹' + res.discount);

                // Update Bill UI
                $('#bill_discount').text('- ₹' + res.discount);
                $('#bill_total').text('₹' + res.new_total);

                // Hide input box to prevent re-apply
                $('.input-group').slideUp();
                $('#coupon_list_box').slideUp();

                appliedCouponCode = code;

                msg.text(res.message).addClass('text-success');
            } else {
                // Error
                msg.text(res.message).addClass('text-danger');
            }
        },
        error: function (err) {
            msg.text('Something went wrong').addClass('text-danger');
        }
    });
}


// 🔥 IMPORTANT: Jab Modal Open ho tab ye value set karein
// Ye function aapke 'Buy Now' button par call hona chahiye
// function openCheckoutModal(price, mrpTotal = 0, discountTotal = 0) {
//     console.log("Opening Cart Checkout...", price, mrpTotal, discountTotal);

//     // 1. Set Global Total
//     if (typeof price === 'string') price = parseFloat(price.replace(/[^\d.]/g, ''));
//     currentCartTotal = price;

//     // 2. Format Values
//     let formattedPrice = currentCartTotal.toLocaleString('en-IN');
//     let formattedMrp = mrpTotal.toLocaleString('en-IN');
//     let formattedDisc = discountTotal.toLocaleString('en-IN');

//     // 3. Header & Images
//     $('#summ_name').text('Cart Checkout');
//     $('#summ_qty').text($('#side_cart_count').text() + ' Items');
//     $('#summ_img').attr('src', 'https://cdn-icons-png.flaticon.com/512/1170/1170678.png'); // Cart Icon

//     // 4. Update Prices
//     $('#summ_total').text('₹' + formattedPrice);       // Top Right
//     $('#bill_subtotal').text('₹' + formattedPrice);    // Subtotal Row
//     $('#bill_final_total').text('₹' + formattedPrice); // Final Total Row
//     $('#btn_pay_amount').text('₹' + formattedPrice);   // Button

//     // 5. 🔥 HANDLE MRP & DISCOUNT ROWS (The Fix)
//     if (discountTotal > 0) {
//         // Show Breakdown with values
//         $('#row_mrp_total').show();
//         $('#bill_mrp').text('₹' + formattedMrp);

//         $('#row_product_discount').show();
//         $('#bill_product_discount').text('- ₹' + formattedDisc);

//         // Optional: Top MRP Strikethrough
//         $('#summ_mrp_display').text('₹' + formattedMrp).show();
//     } else {
//         // Hide if no discount
//         $('#row_mrp_total').hide();
//         $('#row_product_discount').hide();
//         $('#summ_mrp_display').hide();
//     }

//     // 6. 🔥 FORCE RESET COUPON UI (Important)
//     $('#row_coupon_discount').css('display', 'none');
//     $('#bill_coupon_discount').text('- ₹0');

//     $('#coupon_applied_wrapper').hide();
//     $('#coupon_applied_box').hide();
//     $('#coupon_input_group').show();
//     $('#coupon_code').val('');
//     $('#coupon_msg').hide();
//     $('#coupon_list_box').hide();

//     // 7. Hidden Inputs
//     $('#final_buy_mode').val('cart'); // Mode is Cart
//     $('#final_coupon_code').val('');

//     // 8. Open Modal
//     $('#checkoutModal').modal('show');

//     // Login Check Logic
//     const isLoggedIn = $('meta[name="is-logged-in"]').attr('content') === '1';
//     if (isLoggedIn) {
//         $.get("/checkout/get-user-data", function(data) {
//              if (data.has_address) {
//                  $('#saved_address_list').html(data.html).show();
//                  $('#new_address_form').hide();
//              } else {
//                  $('#saved_address_list').hide();
//                  $('#new_address_form').show();
//              }
//              $('#user_phone_display').text(data.user_phone);
//              showStep('address');
//         });
//     } else {
//         showStep('login');
//     }
// }

// 🛒 OPEN CHECKOUT MODAL (Updated with Gaming Logic)
// 🛒 1. OPEN CHECKOUT MODAL (With Gaming Logic)
// function openCheckoutModal(price, mrpTotal = 0, discountTotal = 0) {
//     console.log("Opening Cart Checkout...", price, mrpTotal, discountTotal);

//     // 1. Set Global Total
//     if (typeof price === 'string') price = parseFloat(price.replace(/[^\d.]/g, ''));
//     currentCartTotal = price;

//     // 2. 🔥 RESET COUPON VARIABLES
//     appliedCouponCode = null; // Admin Coupon Reset
//     appliedGamingCode = null; // Game Coupon Reset
//     appliedGamingAmount = 0;  // Game Amount Reset

//     // 3. 🔥 CHECK LOCAL STORAGE (Yahan se value aayegi)
//     let gameAmt = localStorage.getItem('gaming_coupon_amount');
//     let gameCode = localStorage.getItem('gaming_coupon_code');

//     // Debugging ke liye (Console me check karein)
//     console.log("Game Data Found:", gameAmt, gameCode);

//     if (gameAmt && gameCode) {
//         appliedGamingAmount = parseFloat(gameAmt);
//         appliedGamingCode = gameCode;
//     }

//     // 4. Format Base Values
//     let formattedPrice = currentCartTotal.toLocaleString('en-IN');
//     let formattedMrp = mrpTotal.toLocaleString('en-IN');
//     let formattedDisc = discountTotal.toLocaleString('en-IN');

//     // 5. Header & Images
//     $('#summ_name').text('Cart Checkout');
//     $('#summ_qty').text($('#side_cart_count').text() + ' Items');
//     $('#summ_img').attr('src', 'https://cdn-icons-png.flaticon.com/512/1170/1170678.png');

//     // 6. Update Subtotal
//     $('#summ_total').text('₹' + formattedPrice);
//     $('#bill_subtotal').text('₹' + formattedPrice);

//     // 7. Product Discount Rows
//     if (discountTotal > 0) {
//         $('#row_mrp_total').show();
//         $('#bill_mrp').text('₹' + formattedMrp);
//         $('#row_product_discount').show();
//         $('#bill_product_discount').text('- ₹' + formattedDisc);
//         $('#summ_mrp_display').text('₹' + formattedMrp).show();
//     } else {
//         $('#row_mrp_total').hide();
//         $('#row_product_discount').hide();
//         $('#summ_mrp_display').hide();
//     }

//     // 8. 🔥 SHOW GAMING DISCOUNT ROW (Yahan ₹0 ki jagah asli amount aayega)
//     if (appliedGamingAmount > 0) {
//         $('#row_gaming_discount').show(); // Row dikhao
//         $('#bill_gaming_discount').text('- ₹' + appliedGamingAmount); // Amount update karo
//     } else {
//         $('#row_gaming_discount').hide(); // Agar nahi jeeta to chupao
//     }

//     // 9. Reset Admin Coupon UI
//     $('#row_coupon_discount').hide();
//     $('#bill_coupon_discount').text('- ₹0');
//     $('#coupon_applied_wrapper').hide();
//     $('#coupon_applied_box').hide();
//     $('#coupon_input_group').show();
//     $('#coupon_code').val('');
//     $('#coupon_msg').hide();
//     $('#coupon_list_box').hide();

//     // 10. 🔥 CALCULATE FINAL PAYABLE
//     let finalPayable = currentCartTotal - appliedGamingAmount;
//     if (finalPayable < 0) finalPayable = 0;

//     // 11. Update Final Amount UI
//     $('#bill_final_total').text('₹' + finalPayable.toLocaleString('en-IN'));
//     $('#btn_pay_amount').text('₹' + finalPayable.toLocaleString('en-IN'));

//     // 12. Set Hidden Inputs (Backend ke liye)
//     $('#final_buy_mode').val('cart');
//     $('#final_coupon_code').val('');

//     // 🔥 Gaming Coupon Hidden Input (Form me inject karo)
//     if ($('#final_gaming_coupon_code').length === 0) {
//         $('<input type="hidden" name="gaming_coupon_code" id="final_gaming_coupon_code">').appendTo('#finalPaymentForm');
//     }
//     $('#final_gaming_coupon_code').val(appliedGamingCode);

//     // 13. Open Modal
//     $('#checkoutModal').modal('show');

//     // 14. Login Check
//     const isLoggedIn = $('meta[name="is-logged-in"]').attr('content') === '1';
//     if (isLoggedIn) {
//         $.get("/checkout/get-user-data", function(data) {
//              if (data.has_address) {
//                  $('#saved_address_list').html(data.html).show();
//                  $('#new_address_form').hide();
//              } else {
//                  $('#saved_address_list').hide();
//                  $('#new_address_form').show();
//              }
//              $('#user_phone_display').text(data.user_phone);
//              showStep('address');
//         });
//     } else {
//         showStep('login');
//     }
// }

// 🛒 1. OPEN CHECKOUT MODAL (Updated for Cart + Game Logic)
function openCheckoutModal(price, mrpTotal = 0, discountTotal = 0) {
    console.log("Opening Cart Checkout...", price, mrpTotal, discountTotal);

    // 1. Set Global Total
    if (typeof price === 'string') price = parseFloat(price.replace(/[^\d.]/g, ''));
    currentCartTotal = price;

    // 2. 🔥 RESET GLOBAL VARIABLES (Critical Step)
    appliedCouponCode = null;       // Admin Coupon Reset
    appliedGamingCode = null;       // Game Coupon Reset
    window.appliedGamingAmount = 0; // Game Amount Reset (Window scope)

    // 3. 🔥 CHECK LOCAL STORAGE
    let gameAmt = localStorage.getItem('gaming_coupon_amount');
    let gameCode = localStorage.getItem('gaming_coupon_code');

    if (gameAmt && gameCode) {
        window.appliedGamingAmount = parseFloat(gameAmt); // Global set karo
        appliedGamingCode = gameCode;
        console.log("Cart Checkout - Game Data Found:", window.appliedGamingAmount);
    }

    // 4. Format Base Values
    let formattedPrice = currentCartTotal.toLocaleString('en-IN');
    let formattedMrp = mrpTotal.toLocaleString('en-IN');
    let formattedDisc = discountTotal.toLocaleString('en-IN');

    // 5. Header & Images
    $('#summ_name').text('Cart Checkout');
    $('#summ_qty').text($('#side_cart_count').text() + ' Items');
    $('#summ_img').attr('src', 'https://cdn-icons-png.flaticon.com/512/1170/1170678.png'); // Cart Icon

    // 6. Update Subtotal (Base Price)
    $('#summ_total').text('₹' + formattedPrice);
    $('#bill_subtotal').text('₹' + formattedPrice);

    // 7. Product Discount Rows (MRP vs Selling Price)
    if (discountTotal > 0) {
        $('#row_mrp_total').show();
        $('#bill_mrp').text('₹' + formattedMrp);
        $('#row_product_discount').show();
        $('#bill_product_discount').text('- ₹' + formattedDisc);
        $('#summ_mrp_display').text('₹' + formattedMrp).show();
    } else {
        $('#row_mrp_total').hide();
        $('#row_product_discount').hide();
        $('#summ_mrp_display').hide();
    }

    // 8. 🔥 SHOW GAMING DISCOUNT ROW
    if (window.appliedGamingAmount > 0) {
        // Inject Row if missing
        if ($('#row_gaming_discount').length === 0) {
            $('<div id="row_gaming_discount" class="d-flex justify-content-between mb-1 small text-primary fw-bold"><span><i class="las la-gamepad"></i> Game Reward</span><span id="bill_gaming_discount">- ₹0</span></div>').insertBefore('#row_coupon_discount');
        }
        $('#row_gaming_discount').show();
        $('#bill_gaming_discount').text('- ₹' + window.appliedGamingAmount);
    } else {
        $('#row_gaming_discount').hide();
    }

    // 9. Reset Admin Coupon UI
    $('#row_coupon_discount').hide();
    $('#bill_coupon_discount').text('- ₹0');
    $('#coupon_applied_wrapper').hide();
    $('#coupon_applied_box').hide();
    $('#coupon_input_group').show();
    $('#coupon_code').val('');
    $('#coupon_msg').hide();
    $('#coupon_list_box').hide();

    // 10. Hidden Inputs (Backend ke liye)
    $('#final_buy_mode').val('cart');
    $('#final_coupon_code').val('');

    // 🔥 Inject Gaming Coupon Hidden Input
    if ($('#final_gaming_coupon_code').length === 0) {
        $('<input type="hidden" name="gaming_coupon_code" id="final_gaming_coupon_code">').appendTo('#finalPaymentForm');
    }
    $('#final_gaming_coupon_code').val(appliedGamingCode);

    // 11. 🔥 FINAL CALCULATION (Use Master Function)
    // Ye line sabse zaruri hai taki 'To Pay' update ho jaye
    calculateFinalTotal();

    // 12. Open Modal
    $('#checkoutModal').modal('show');

    // 13. Login Logic
    const isLoggedIn = $('meta[name="is-logged-in"]').attr('content') === '1';
    if (isLoggedIn) {
        $.get("/checkout/get-user-data", function (data) {
            if (data.has_address) {
                $('#saved_address_list').html(data.html).show();
                $('#new_address_form').hide();
            } else {
                $('#saved_address_list').hide();
                $('#new_address_form').show();
            }
            $('#user_phone_display').text(data.user_phone);
            showStep('address');
        });
    } else {
        showStep('login');
    }
}

// 1. Toggle Coupon List & Fetch Data
function toggleCouponList() {
    let box = $('#coupon_list_box');

    if (box.is(':visible')) {
        box.slideUp();
    } else {
        box.slideDown();

        // 🔥 FIX: Use window.appRoutes instead of {{ route }}
        $.get(window.appRoutes.getCoupons, function (data) {
            let html = '';
            if (data.length > 0) {
                data.forEach(c => {
                    let val = parseFloat(c.value);

                    // Logic to show text properly
                    let isPercent = c.type.toLowerCase().includes('percent') || c.type.includes('%');
                    let desc = isPercent ? val + '% OFF' : 'Flat ₹' + val + ' OFF';

                    html += `
                        <div class="d-flex justify-content-between align-items-center bg-white border rounded p-2 mb-2">
                            <div>
                                <span class="badge bg-light text-dark border border-secondary mb-1 text-uppercase">${c.code}</span>
                                <div class="small text-muted" style="font-size: 11px;">${desc}</div>
                            </div>
                            <button class="btn btn-sm btn-outline-dark fw-bold py-1 px-3" style="font-size: 11px;" onclick="applyCouponDirect('${c.code}')">APPLY</button>
                        </div>
                    `;
                });
            } else {
                html = '<div class="text-center small text-muted py-2">No coupons available</div>';
            }
            box.html(html);
        });
    }
}

// 2. Apply Coupon (Manual Input)
function applyCouponManual() {
    let code = $('#coupon_code').val();
    if (!code) return;
    applyCouponDirect(code);
}

// 3. Main Apply Function
// 3. Main Apply Function
function applyCouponDirect(code) {
    $('#coupon_msg').hide();
    $('#coupon_code').val(code);

    // Ensure route is correct
    // If using Blade: let url = "{{ route('apply.coupon') }}";
    // If external JS: let url = "/checkout/apply-coupon";
    let url = window.appRoutes ? window.appRoutes.applyCoupon : "/checkout/apply-coupon";

    $.post(url, {
        code: code,
        cart_total: currentCartTotal,
        _token: $('meta[name="csrf-token"]').attr('content') // Ensure this selector is correct
    }, function (res) {
        if (res.status) {
            // UI Show
            $('#coupon_input_group').hide();
            $('#coupon_list_box').slideUp();
            $('#coupon_applied_box').fadeIn();
            $('#row_coupon_discount').fadeIn();

            // 1. Text Update (For calculator to read)
            $('#saved_amount_text').text('₹' + res.discount);
            $('#bill_coupon_discount').text('- ₹' + res.discount);

            // 2. Set Variables
            appliedCouponCode = code;
            $('#final_coupon_code').val(code);

            // 3. 🔥 RECALCULATE (Game + Admin)
            calculateFinalTotal();

            $('#coupon_msg').text(res.message).addClass('text-success').show();
        } else {
            $('#coupon_msg').text(res.message).addClass('text-danger').show();
        }
    }).fail(function () {
        $('#coupon_msg').text('Error applying coupon').show();
    });
}

// // 2. Remove Coupon Logic
function removeCoupon() {
    // 1. UI Reset
    $('#coupon_applied_box').hide();
    $('#coupon_input_group').fadeIn();
    $('#coupon_code').val('');
    $('#coupon_msg').hide();
    $('#row_coupon_discount').hide();

    // 2. Text Reset (Crucial for calculator)
    $('#saved_amount_text').text('₹0');

    // 3. Reset Variables
    appliedCouponCode = null;
    $('#final_coupon_code').val('');

    // 4. 🔥 RECALCULATE (Only Game remains)
    calculateFinalTotal();
}

function toggleWishlist(productId, btnElement) {
    $.ajax({
        url: '/wishlist/toggle',
        type: 'POST',
        data: {
            product_id: productId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status) {
                var icon = $(btnElement).find('i');

                // 1. Icon Change
                if (response.action === 'added') {
                    icon.removeClass('lar la-heart').addClass('las la-heart text-danger');
                } else {
                    icon.removeClass('las la-heart text-danger').addClass('lar la-heart');
                }

                // 2. Count Update
                $('.wishlist-count').text(response.count);

                // 3. 🟢 SHOW TOAST (Alert Hata Diya)
                showToast(response.message);

            }
        },
        error: function (xhr) {
            console.log('Error:', xhr.responseText);
        }
    });
}

function showToast(message) {
    // 1. Toast Element dhundo
    var toastEl = document.getElementById('liveToast');

    // 2. Message Body dhundo (Jahan text likha hai)
    var toastBody = document.getElementById('toast-message');

    if (toastEl && toastBody) {
        // ✅ Ye line Controller se aaye message ko HTML me daal degi
        toastBody.innerText = message;

        // Toast Show karo
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
}

// 1. Open Modal & Load Data
function openWishlistModal() {
    // Modal Show
    var myModal = new bootstrap.Modal(document.getElementById('wishlistModal'));
    myModal.show();

    // Show Loader, Hide Content
    $('#wishlist-loader').show();
    $('#wishlist-content').addClass('d-none').html('');
    $('#wishlist-empty').addClass('d-none');

    // AJAX Call to fetch items
    $.ajax({
        url: '/wishlist/fetch',
        type: 'GET',
        success: function (response) {
            $('#wishlist-loader').hide();

            if (response.empty) {
                $('#wishlist-empty').removeClass('d-none');
            } else {
                $('#wishlist-content').html(response.html).removeClass('d-none');
            }
        },
        error: function () {
            $('#wishlist-loader').hide();
            alert('Could not load wishlist.');
        }
    });
}

// 2. Remove Item Logic (For Popup)
function removeFromWishlist(productId) {
    // Toggle function hi reuse karenge remove ke liye
    toggleWishlist(productId, null); // 'null' kyunki button element ki zarurat nahi yahan

    // UI se remove karein with fade effect
    $('.wishlist-item-' + productId).fadeOut(300, function () {
        $(this).remove();
        // Agar sab remove ho gaya to empty state dikhao
        if ($('#wishlist-content').children().length <= 1) { // 1 because this one is removing
            $('#wishlist-content').addClass('d-none');
            $('#wishlist-empty').removeClass('d-none');
        }
    });
}

// 3. Move to Cart (Add to Cart + Remove from Wishlist)
function moveToCart(productId) {
    // 1. Sabse pehle Wishlist Modal ko dhoondh kar band karein
    var wishlistModalEl = document.getElementById('wishlistModal');
    var modal = bootstrap.Modal.getInstance(wishlistModalEl);

    if (modal) {
        modal.hide(); // ✅ Modal Close
    }

    // 2. Thoda wait karein (300ms) taaki animation smooth lage, phir Cart mein add karein
    setTimeout(function () {
        // Add to Cart Logic (Existing)
        var dummyBtn = document.createElement('button');
        addToCart(productId, 1, 0, dummyBtn);
    }, 300);

    // 3. Backend se Wishlist item remove karein
    // Hum 'toggleWishlist' direct call kar rahe hain taaki DB se hat jaye
    toggleWishlist(productId, null);

    // 4. Modal ke andar se bhi element hata dein (Taaki agli baar open karne par na dikhe)
    $('.wishlist-item-' + productId).remove();

    // Check karein agar wishlist empty ho gayi to empty state set karein
    if ($('#wishlist-content').children().length <= 1) {
        $('#wishlist-content').addClass('d-none');
        $('#wishlist-empty').removeClass('d-none');
    }
}

// 🔢 CALCULATE FINAL TOTAL (Central Logic)
function calculateFinalTotal() {
    // 1. Get Admin Discount (Read from UI)
    let adminDiscount = 0;

    // Check if Admin Coupon box is visible
    if ($('#coupon_applied_box').is(':visible')) {
        // Text ex: "₹ 125" -> Remove non-digits -> 125
        let text = $('#saved_amount_text').text().replace(/[^\d.]/g, '');
        adminDiscount = parseFloat(text) || 0;
    }

    // 2. Get Game Discount (From Global Variable)
    let gameDiscount = window.appliedGamingAmount || 0;

    // 3. Calculate Final
    let totalDiscount = gameDiscount + adminDiscount;
    let finalPayable = currentCartTotal - totalDiscount;

    // Safety Check
    if (finalPayable < 0) finalPayable = 0;

    // 4. Update UI
    let fmtTotal = finalPayable.toLocaleString('en-IN');
    $('#bill_final_total').text('₹' + fmtTotal);
    $('#btn_pay_amount').text('₹' + fmtTotal);
}

// 1. Chat window toggle logic
function toggleChat() {
    const chat = document.getElementById('astro-chat-window');
    chat.style.display = (chat.style.display === 'none' || chat.style.display === '') ? 'flex' : 'none';
}

// 2. City se Lat/Lng nikalne ke liye (onblur ke liye optimized)
async function getCoordinates() {
    const cityInput = document.getElementById('birth_city');
    const city = cityInput.value;
    if (!city) return false;

    // UI Feedback: Input border yellow karo jab tak fetch ho raha hai
    cityInput.style.borderColor = "#ffc107";

    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${city}`);
        const data = await res.json();
        if (data.length > 0) {
            document.getElementById('lat').value = data[0].lat;
            document.getElementById('lng').value = data[0].lon;
            cityInput.style.borderColor = "#28a745"; // Success Green
            console.log("Location Found:", data[0].display_name);
            return true;
        } else {
            cityInput.style.borderColor = "#dc3545"; // Error Red
            return false;
        }
    } catch (e) {
        console.error("Location error", e);
        return false;
    }
}

let chatMemory = []; // 🧠 Memory Array
let currentUserRashi = "";

async function processAstroRequest() {
    const name = document.getElementById('user_name').value.trim();
    const lat = document.getElementById('lat').value;

    if (!name || !lat) {
        alert("Please enter Name and City.");
        return;
    }

    document.getElementById('astro-form').style.display = 'none';
    document.getElementById('chat-loader').style.display = 'block';

    const payload = {
        name: name,
        dob: document.getElementById('dob').value,
        tob: document.getElementById('tob').value,
        lat: lat,
        lng: document.getElementById('lng').value,
        message: "Initial Kundali Request",
        history: chatMemory
    };

    await callAstroService(payload);
}

async function sendFollowup() {
    const input = document.getElementById('user-followup-msg');
    const msg = input.value.trim();
    if (!msg) return;

    // Display User Message
    document.getElementById('ai-response-text').innerHTML += `<div style="text-align:right; margin:10px; color:#673ab7;"><b>Aap:</b> ${msg}</div>`;
    input.value = "";

    const payload = {
        name: document.getElementById('user_name').value,
        message: msg,
        user_rashi: currentUserRashi,
        history: chatMemory
    };

    await callAstroService(payload);
}

async function callAstroService(payload) {
    document.getElementById('chat-loader').style.display = 'block';

    // लोडर दिखने के बाद ऑटो-स्क्रॉल करें ताकि यूज़र को पता चले कि काम हो रहा है
    scrollToBottom();

    try {
        const response = await fetch("/get-astro-advice", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();
        document.getElementById('chat-loader').style.display = 'none';
        document.getElementById('ai-result-area').style.display = 'block';

        if (result.status === 'success') {
            currentUserRashi = result.user_rashi;

            // Memory में डेटा जोड़ें
            chatMemory.push({ role: "user", content: payload.message });
            chatMemory.push({ role: "assistant", content: result.message });

            // AI रिस्पॉन्स रेंडर करें
            let formatted = formatAstroResponse(result.message);

            // नया मैसेज एक अलग div में डालें ताकि पहचानना आसान हो
            const responseContainer = document.getElementById('ai-response-text');
            responseContainer.innerHTML += `<div class="bot-msg-wrapper" style="margin-bottom:20px; border-left:4px solid #673ab7; padding-left:10px;">${formatted}</div>`;

            // 🚀 स्मूथ ऑटो-स्क्रॉल: मैसेज रेंडर होने के तुरंत बाद
            setTimeout(() => {
                scrollToBottom();
            }, 100);
        }
    } catch (e) {
        console.error(e);
        document.getElementById('chat-loader').style.display = 'none';
        alert("Technical issue! Please try again.");
    }
}

// 💡 स्क्रॉल के लिए अलग फंक्शन ताकि इसे कहीं भी इस्तेमाल कर सकें
function scrollToBottom() {
    const container = document.getElementById('chat-content');
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: 'smooth'
        });
    }
}

function formatAstroResponse(text) {
    if (!text) return "";
    return text
        .replace(/!\[.*?\]\((.*?)\)/g, '<img src="$1">') // Image Rendering
        .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" class="buy-btn">$1</a>') // Active Link
        .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>') // Bold
        .replace(/### (.*?)\n/g, '<h3>$1</h3>') // Headings
        .replace(/\n/g, '<br>'); // Line breaks
}

// WhatsApp Share & Reset Functions (No change needed here)
function shareOnWhatsApp() {
    const text = document.getElementById('ai-response-text').innerText;
    const name = document.getElementById('user_name').value;
    const shareText = `✨ *Suyagya Astro Report for ${name}* ✨\n\n${text}\n\nApni Kundali check karein: https://suyagya.com`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(shareText)}`, '_blank');
}

function resetChat() {
    document.getElementById('astro-form').style.display = 'block';
    document.getElementById('ai-result-area').style.display = 'none';
    document.getElementById('chat-loader').style.display = 'none';
    document.getElementById('lat').value = ""; // Reset Lat
}

async function searchCity(query) {
    const suggestionBox = document.getElementById('city-suggestions');
    if (query.length < 3) {
        suggestionBox.style.display = 'none';
        return;
    }

    // हम Nominatim (Free API) का इस्तेमाल कर रहे हैं
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${query}&addressdetails=1&limit=5`;

    try {
        const response = await fetch(url);
        const data = await response.json();

        suggestionBox.innerHTML = '';
        suggestionBox.style.display = 'block';

        data.forEach(place => {
            const cityName = place.address.city || place.address.town || place.address.village || place.display_name;
            const stateName = place.address.state || "";

            const item = document.createElement('button');
            item.className = 'list-group-item list-group-item-action text-start';
            item.style.fontSize = '12px';
            item.innerHTML = `<strong>${cityName}</strong>, ${stateName}`;

            // शहर चुनने पर क्या होगा
            item.onclick = () => {
                document.getElementById('birth_city').value = `${cityName}, ${stateName}`;
                document.getElementById('lat').value = place.lat;
                document.getElementById('lng').value = place.lon;
                suggestionBox.style.display = 'none';

                // बटन को एक्टिव करें
                document.getElementById('submit-btn').disabled = false;
            };
            suggestionBox.appendChild(item);
        });
    } catch (error) {
        console.error("City search error:", error);
    }
}




