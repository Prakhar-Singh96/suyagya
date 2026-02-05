<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\AstroChatController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\FilterController;
use App\Http\Controllers\Frontend\FaqController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\GameController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\LogisticController;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Admin\GeneralFaqController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\FilterValueController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Frontend\Auth\OtpController;
use App\Http\Controllers\Frontend\BlogPageController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\HomePageSettingController;
use App\Http\Controllers\Frontend\ProductListingController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Seller\Auth\LoginController as SellerLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/fix-astro-data', function () {
    $astroMapping = [
        '1 Mukhi' => ['planet' => 'Sun (Surya)', 'rashi' => 'Leo (Simha)', 'benefits' => 'Self-confidence, leadership, and power.'],
        '2 Mukhi' => ['planet' => 'Moon (Chandra)', 'rashi' => 'Cancer (Karka)', 'benefits' => 'Emotional stability and harmony in relationships.'],
        '3 Mukhi' => ['planet' => 'Mars (Mangal)', 'rashi' => 'Aries, Scorpio', 'benefits' => 'Success and energy, removes laziness.'],
        '4 Mukhi' => ['planet' => 'Mercury (Budh)', 'rashi' => 'Gemini, Virgo', 'benefits' => 'Communication, memory, and intelligence.'],
        '5 Mukhi' => ['planet' => 'Jupiter (Guru)', 'rashi' => 'Sagittarius, Pisces', 'benefits' => 'Good health, peace, and spiritual growth.'],
        '6 Mukhi' => ['planet' => 'Venus (Shukra)', 'rashi' => 'Taurus, Libra', 'benefits' => 'Focus, willpower, and artistic success.'],
        '7 Mukhi' => ['planet' => 'Saturn (Shani)', 'rashi' => 'Capricorn, Aquarius', 'benefits' => 'Wealth, prosperity, and career growth.'],
        'Yellow Sapphire' => ['planet' => 'Jupiter (Guru)', 'rashi' => 'Sagittarius, Pisces', 'benefits' => 'Wisdom, fortune, and marital bliss.'],
        'Blue Sapphire' => ['planet' => 'Saturn (Shani)', 'rashi' => 'Capricorn, Aquarius', 'benefits' => 'Protection from evil and rapid success.'],
        'Ruby' => ['planet' => 'Sun (Surya)', 'rashi' => 'Leo (Simha)', 'benefits' => 'Vitality, leadership, and professional success.'],
        'Coral' => ['planet' => 'Mars (Mangal)', 'rashi' => 'Aries, Scorpio', 'benefits' => 'Courage, physical strength, and overcoming obstacles.'],
        'Emerald' => ['planet' => 'Mercury (Budh)', 'rashi' => 'Gemini, Virgo', 'benefits' => 'Business growth and clear communication.'],
        'Tiger Eye' => ['planet' => 'Sun & Mars', 'rashi' => 'Leo, Aries', 'benefits' => 'Protection and courage.'],
    ];

    foreach (\App\Models\Product::all() as $product) {
        foreach ($astroMapping as $keyword => $data) {
            if (str_contains(strtolower($product->name), strtolower($keyword))) {
                $product->update([
                    'astro_planet'   => $data['planet'],
                    'astro_rashi'    => $data['rashi'],
                    'astro_benefits' => $data['benefits']
                ]);
            }
        }
    }
    return "Products Checked & Updated!";
});

Route::get('/', [HomeController::class, 'index']);

// Product Listing Pages
// 1. Category Page (e.g. /category/rudraksha)
Route::get('/category/{slug}', [ProductListingController::class, 'categoryProducts'])->name('products.category');

// 2. SubCategory Page (e.g. /category/rudraksha/mala)
Route::get('/category/{cat_slug}/{sub_slug}', [ProductListingController::class, 'subCategoryProducts'])->name('products.subcategory');

// 3. Shop by Purpose/Collection (e.g. /collection/wealth)
Route::get('/collections/all', [ProductListingController::class, 'showAllCollection'])->name('products.all_collection');
// Route::get('/collections/{slug}', [ProductListingController::class, 'showCollection'])->name('collections.show');

Route::get('/product/{slug}', [ProductListingController::class, 'productDetail'])->name('product.detail');

// Full Search Results Page (Product Listing jaisa)
Route::get('/search', [ProductListingController::class, 'searchListing'])->name('products.search_listing');

Route::get('/ajax-search', [SearchController::class, 'ajaxSearch'])->name('search.ajax');

Route::get('/check-pincode-delivery/{pincode}', [ProductListingController::class, 'checkPincode']);

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.update.qty');
// Side Cart Quantity Update
Route::post('/cart/update-quantity-side', [CartController::class, 'updateSideCartQty']);
Route::get('/cart/side-cart-html', [CartController::class, 'getSideCartHtml']);

Route::post('/reviews/submit', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/reviews/filter', [ReviewController::class, 'filterReviews'])->name('reviews.filter');

// OTP Login Routes
Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send.otp');
Route::post('/login-with-otp', [OtpController::class, 'loginWithOtp'])->name('login.otp');

Route::get('/track-order', [TrackingController::class, 'index'])->name('track.order');
Route::post('/track-order', [TrackingController::class, 'track'])->name('track.order.submit');

// Coupon Routes
Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('apply.coupon');
Route::get('/get-coupons', [CheckoutController::class, 'getCoupons'])->name('get.coupons'); // Coupon List ke liye

// Game coupon Route
Route::post('/play-sticker-game', [GameController::class, 'playGame'])->name('game.play');


Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/wishlist/fetch', [WishlistController::class, 'fetchWishlist'])->name('wishlist.fetch');
Route::get('/faqs', [FaqController::class, 'index'])->name('frontend.faq');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/terms-conditions', [PageController::class, 'terms'])->name('terms.conditions');
Route::get('/refund-policy', [PageController::class, 'refund_policy'])->name('refund.policy');
Route::get('/privacy-policy', [PageController::class, 'privacy_policy'])->name('privacy.policy');
Route::get('/support-policy', [PageController::class, 'support_policy'])->name('support.policy');
// Blog Routes
Route::get('/blogs', [BlogPageController::class, 'index'])->name('blogs.index');
Route::get('/blog/{slug}', [BlogPageController::class, 'show'])->name('blogs.show');

// --- AUTHENTICATED USER ROUTES ---
Route::middleware(['auth'])->group(function () {

    // 1. Logout
    Route::post('/logout', [OtpController::class, 'logout'])->name('logout');

    // 2. User Profile & Orders
    Route::get('/orders', [App\Http\Controllers\Frontend\UserController::class, 'orders'])->name('user.orders');

    // 🟢 New Route for Order Details
    Route::get('/orders/{id}', [App\Http\Controllers\Frontend\UserController::class, 'orderDetails'])->name('user.order_details');

    // Route::get('/profile', [App\Http\Controllers\Frontend\UserController::class, 'profile'])->name('user.profile');

    Route::post('/checkout/cancel-order', [CheckoutController::class, 'cancelOrder'])->name('checkout.cancel');

    // 3. Checkout Actions
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/save-address', [CheckoutController::class, 'saveAddress'])->name('checkout.save_address');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place_order');
    Route::post('/checkout/save-address-ajax', [CheckoutController::class, 'saveAddressAjax'])->name('checkout.save_address_ajax');
    // Check if user already has address with this pincode
    Route::get('/checkout/check-address/{pincode}', [CheckoutController::class, 'checkAddressByPincode']);
    Route::post('/checkout/verify-payment', [CheckoutController::class, 'verifyPayment'])->name('checkout.verify_payment');
    Route::get('/checkout/get-user-data', [CheckoutController::class, 'getUserCheckoutData'])->name('checkout.user_data');
});

// --- ADMIN ROUTES ---
Route::prefix('admin')->name('admin.')->group(function () {

    // ==== LOGIN ROUTES ====
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login-submit', [AdminLoginController::class, 'login'])->name('login.submit');


    // ==== PROTECTED ADMIN ROUTES (SUPERADMIN ONLY) ====
    Route::middleware(['admin'])->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('subcategories', SubCategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('filters', FilterController::class);
        Route::resource('orders', OrderController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('filter-values', FilterValueController::class);
        // AJAX Routes for Product Page
        Route::get('get-subcategories/{categoryId}', [ProductController::class, 'getSubCategories']);
        Route::delete('delete-gallery-image/{id}', [ProductController::class, 'deleteGalleryImage']);

        // 💳 Payment Settings Routes
        Route::get('/payment-settings', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payment.settings');
        Route::post('/payment-settings/update', [App\Http\Controllers\Admin\PaymentController::class, 'update'])->name('payment.update');
        Route::resource('/videos', VideoController::class);

        Route::resource('reviews', AdminReviewController::class);

        Route::resource('banners', BannerController::class);

        Route::post('product/upload-image', [ProductController::class, 'uploadCkImage'])->name('product.upload_image');

        // Quick Status Toggle Route
        Route::get('reviews/status/{id}', [AdminReviewController::class, 'toggleStatus'])->name('reviews.toggle');

        Route::resource('coupons', CouponController::class);

        Route::get('/home-page-settings', [HomePageSettingController::class, 'edit'])->name('home.settings');
        Route::post('/home-page-settings', [HomePageSettingController::class, 'update'])->name('home.settings.update');
        Route::resource('blogs', BlogController::class);

        // General FAQs CRUD
        Route::resource('/general-faqs', GeneralFaqController::class);

        Route::get('/redirects', [RedirectController::class, 'index'])->name('redirects.index');
        Route::post('/redirects/store', [RedirectController::class, 'store'])->name('redirects.store');
        Route::delete('/redirects/{id}', [RedirectController::class, 'destroy'])->name('redirects.destroy');

        // Admin Middleware Group ke andar
        Route::group(['prefix' => 'logistic', 'as' => 'logistic.'], function () {

            // Dashboard (Orders ready to ship)
            Route::get('/', [LogisticController::class, 'index'])->name('index');

            // Step 1: Weight Form
            Route::get('/ship/{id}', [LogisticController::class, 'prepareShipment'])->name('ship');

            // Step 2: Create Order & Show Rates
            Route::post('/create-order/{id}', [LogisticController::class, 'createAndFetchRates'])->name('create_order');

            // Step 3: Final Manifest
            Route::post('/manifest/{id}', [LogisticController::class, 'manifest'])->name('manifest');

            // Cancel
            Route::post('/cancel/{id}', [LogisticController::class, 'cancelShipment'])->name('cancel');
        });


        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
    });
});

// --- SELLER ROUTES ---
Route::prefix('seller')->name('seller.')->group(function () {

    // Login Page
    Route::get('/login', [SellerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login-submit', [SellerLoginController::class, 'login'])->name('login.submit');

    // Protected Routes (केवल Sellers के लिए)
    Route::middleware(['auth'])->group(function () {
        // यहाँ हम Controller के अंदर ही चेक कर रहे हैं, लेकिन Middleware भी लगा सकते हैं
        Route::get('/dashboard', function () {
            if (auth()->user()->user_type !== 'seller') {
                abort(403);
            }
            return view('seller.dashboard');
        })->name('dashboard');

        Route::post('/logout', [SellerLoginController::class, 'logout'])->name('logout');
    });
});

Route::get('/run-migration', function () {
    // Check karein ki URL mein secret password hai ya nahi
    if (request('key') != 'prakhar123') {
        return 'Access Denied!';
    }

    Artisan::call('migrate', ["--force" => true]);
    return 'Migration Completed Successfully!';
});

Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    return 'Cache Cleared!';
});

Route::get('/run-seeder', function () {
    // Security check (wahi purana key)
    if (request('key') != 'prakhar123') {
        return 'Access Denied!';
    }

    // Command run karein (force flag zaroori hai production ke liye)
    Artisan::call('db:seed', ["--force" => true]);

    return 'Seeder Run Successfully! Admin created.';
});

// 1. मेन इंडेक्स फाइल
Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');

// 2. अलग-अलग हिस्से
Route::get('sitemap/pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('sitemap/collections.xml', [SitemapController::class, 'collections'])->name('sitemap.collections');
Route::get('sitemap/categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('sitemap/products.xml', [SitemapController::class, 'products'])->name('sitemap.products');

Route::get('/sitemap/blogs.xml', [SitemapController::class, 'blogs'])->name('sitemap.blogs');

// Route::view('/ring-size-guide', 'frontend.pages.ring_size_guide')->name('ring.size.guide');


Route::post('/get-astro-advice', [AstroChatController::class, 'getAstroAdvice'])->name('astro.get-advice');
