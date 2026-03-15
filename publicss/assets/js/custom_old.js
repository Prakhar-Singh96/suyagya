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
        $catSlider.on('init', function(event, slick){
            // Show the slider once initialized to prevent the 1px glitch
            $(this).css({'visibility': 'visible', 'opacity': '1'});
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
            geoIpLookup: function(callback) {
                $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
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
            if(err.errors && err.errors.phone) {
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
        setTimeout(function() {
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
    // Close Drawer
    var sideCartEl = document.getElementById('sideCart');
    var sideCart = bootstrap.Offcanvas.getInstance(sideCartEl);
    sideCart.hide();

    // Open Main Checkout Modal
    $('#final_buy_mode').val('cart');

    // Summary Update for Cart
    $('#summ_img').attr('src', 'https://cdn-icons-png.flaticon.com/512/2543/2543369.png'); // Cart Icon
    $('#summ_name').text('Cart Checkout');
    $('#summ_qty').text($('#side_cart_count').text() + ' Items');
    $('#summ_total').text($('#cart_subtotal').text());

    $('#checkoutModal').modal('show');

    // Auth Check Logic
    const isLoggedIn = $('meta[name="is-logged-in"]').attr('content') === '1';
    if (isLoggedIn) showStep('address');
    else showStep('login');
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
function openDirectCheckout(btn) {
    // 1. Collect Data
    var prodId = $(btn).data('id');
    var qty = $('#qty_input').val() || 1;
    var isSiddh = $('#input_is_siddh').val() || 0;

    // Summary Data
    var img = $('#mainImage').attr('src');
    var title = $('h1.font-heading').text().trim();
    var price = $('#display_price').text();

    // Populate Modal
    $('#summ_img').attr('src', img);
    $('#summ_name').text(title);
    $('#summ_qty').text('Qty: ' + qty);
    $('#summ_total').text('₹' + price);

    // Hidden Inputs Fill
    $('#final_buy_mode').val('direct');
    $('#final_product_id').val(prodId);
    $('#final_quantity').val(qty);
    $('#final_is_siddh').val(isSiddh);

    // Show Modal
    $('#checkoutModal').modal('show');

   // Auth Check Logic
    const isLoggedIn = $('meta[name="is-logged-in"]').attr('content') === '1';
    if (isLoggedIn) showStep('address');
    else showStep('login');
}

// Helper: Switch Steps
function showStep(step) {
    $('#step_login, #step_address, #step_payment').hide();
    if (step === 'address') {
        $('#step_address').fadeIn();

        // Check if saved address list exists and has items
        // We check if the radio buttons for addresses exist
        if ($('.saved-addr-radio').length > 0) {
            $('#saved_address_list').show();
            $('#new_address_form').hide();

            // Auto-select the first address if none is selected
            if (!$('input[name="selected_address"]:checked').val()) {
                $('.saved-addr-radio').first().prop('checked', true);
            }
        } else {
            // No saved addresses, show new form
            $('#saved_address_list').hide();
            $('#new_address_form').show();
        }
    } else {
        $('#step_' + step).fadeIn();
    }
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
            geoIpLookup: function(callback) {
                $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
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
        if(res.status) {
            $('#chk_otp_box').slideDown();
            btn.hide();
            // Optional: User ko dikhane ke liye format kar sakte hain
            // $('#chk_mobile').val(fullPhone);
        } else {
            errorMsg.text(res.message || 'Failed to send OTP');
            btn.text('CONTINUE').prop('disabled', false);
        }
    }).fail(function() {
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
            $.get("/checkout/get-user-data", function(data) {

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
             } catch(e) {}
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

    $.post("/checkout/save-address-ajax", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        name: $('#chk_name').val(),
        phone: phoneInput,
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
function processPayment() {
    var btn = $('#btn_place_order');
    btn.prop('disabled', true).text('Processing...');

    // Form Data Collect
    var formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        payment_method: $('input[name="payment_method"]:checked').val(),
        buy_mode: $('#final_buy_mode').val(),
        product_id: $('#final_product_id').val(),
        quantity: $('#final_quantity').val(),
        is_siddh: $('#final_is_siddh').val(),
        address_id: $('#final_address_id').val()
    };

    $.post("/checkout/place-order", formData, function (res) {

        if (res.status === 'razorpay') {
            // 🟣 OPEN RAZORPAY MODAL
            var options = {
                "key": res.key,
                "amount": res.amount,
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
                        btn.prop('disabled', false).text('Place Order');
                        alert('Payment Cancelled');
                    }
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();

        } else if (res.status === 'success') {
            // 🟢 COD Success
            window.location.href = "/orders"; // Redirect to Orders Page
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

    // Debugging ke liye (Console check karein)
    console.log("Verifying Payment:", paymentData, localOrderId);

    $.post("/checkout/verify-payment", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        razorpay_payment_id: paymentData.razorpay_payment_id,
        razorpay_order_id: paymentData.razorpay_order_id,
        razorpay_signature: paymentData.razorpay_signature,
        order_id: localOrderId
    }, function (res) {
        if (res.status) {
            alert('Payment Successful!'); // Optional
            window.location.href = "/orders";
        } else {
            // Yahan server ka asli error dikhayein
            alert(res.message);
            console.error(res.message);
        }
    }).fail(function (xhr) {
        alert('Verification Server Error: ' + xhr.responseText);
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

let currentCartTotal = 0; // Isko modal open hote waqt set karna padega
let appliedCouponCode = null;

// 1. Fetch Coupons (View All Click)
function fetchCoupons() {
    $('#coupon_list_box').slideToggle();

    $.ajax({
        url: "{{ route('get.coupons') }}",
        type: "GET",
        success: function(response) {
            let html = '';
            if(response.length > 0) {
                response.forEach(c => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center bg-white border rounded p-2 mb-2">
                            <div>
                                <strong class="text-uppercase text-primary border border-primary px-2 rounded small me-2">${c.code}</strong>
                                <small class="text-muted d-block mt-1" style="font-size:10px;">${c.type == 'fixed' ? 'Flat ₹'+c.value+' OFF' : c.value+'% OFF'}</small>
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
        success: function(res) {
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
        error: function(err) {
            msg.text('Something went wrong').addClass('text-danger');
        }
    });
}

// 3. Remove Coupon
function removeCoupon() {
    $('#coupon_code').val('');
    $('#coupon_applied_box').slideUp();
    $('.input-group').slideDown();

    // Reset Bill
    $('#bill_discount').text('- ₹0');
    $('#bill_total').text('₹' + currentCartTotal);
    $('#coupon_msg').text('');

    appliedCouponCode = null;
}

// 🔥 IMPORTANT: Jab Modal Open ho tab ye value set karein
// Ye function aapke 'Buy Now' button par call hona chahiye
function openCheckoutModal(price, productId) {
    currentCartTotal = parseFloat(price); // Global Variable Set

    // UI Update
    $('#bill_subtotal').text('₹' + currentCartTotal);
    $('#bill_total').text('₹' + currentCartTotal);

    // Reset Old State
    removeCoupon();

    $('#checkoutModal').modal('show');
}

// 1. Toggle Coupon List & Fetch Data
function toggleCouponList() {
    let box = $('#coupon_list_box');

    if (box.is(':visible')) {
        box.slideUp();
    } else {
        box.slideDown();
        // Fetch Coupons only if empty
        // if (box.html().includes('Loading')) {
            $.get("{{ route('get.coupons') }}", function(data) {
                let html = '';
                if(data.length > 0) {
                    data.forEach(c => {
                        let desc = c.type === 'fixed' ? 'Flat ₹'+c.value+' OFF' : c.value+'% OFF';
                        html += `
                            <div class="coupon-card">
                                <div>
                                    <span class="coupon-code-box">${c.code}</span>
                                    <div class="small text-muted mt-1">${desc}</div>
                                </div>
                                <button class="btn-apply-coupon" onclick="applyCouponDirect('${c.code}')">APPLY</button>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-center small text-muted py-2">No coupons available</div>';
                }
                box.html(html);
            });
        // }
    }
}

// 2. Apply Coupon (Manual Input)
function applyCouponManual() {
    let code = $('#coupon_code').val();
    if(!code) {
        $('#coupon_msg').text('Please enter a code').show();
        return;
    }
    applyCouponDirect(code);
}

// 3. Main Apply Function
function applyCouponDirect(code) {
    $('#coupon_msg').hide(); // Hide old errors

    $.post("{{ route('apply.coupon') }}", {
        code: code,
        cart_total: currentCartTotal, // Global variable from modal open
        _token: "{{ csrf_token() }}"
    }, function(res) {
        if(res.status) {
            // Success UI
            $('#coupon_input_group').hide(); // Input chupao
            $('#coupon_list_box').slideUp(); // List band karo

            $('#applied_code_text').text(code);
            $('#saved_amount_text').text('₹' + res.discount);
            $('#coupon_applied_box').fadeIn(); // Green box dikhao

            // Bill Update
            $('#bill_discount').text('- ₹' + res.discount);
            $('#bill_total').text('₹' + res.new_total);

        } else {
            // Error
            $('#coupon_msg').text(res.message).addClass('text-danger').show();
        }
    }).fail(function() {
        $('#coupon_msg').text('Something went wrong').show();
    });
}

// 4. Remove Coupon
function removeCoupon() {
    $('#coupon_applied_box').hide();
    $('#coupon_input_group').fadeIn();
    $('#coupon_code').val('');

    // Reset Bill
    $('#bill_discount').text('- ₹0');
    $('#bill_total').text('₹' + currentCartTotal);
}
