<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema; // ✅ यह लाइन पक्का होनी चाहिए
use App\Models\Category;
use App\Models\Cart;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Header ke liye categories globally available
        View::composer('frontend.includes.header', function ($view) {
            $headerCategories = collect(); // ✅ डिफॉल्ट खाली कलेक्शन

            // 🟢 FIX: चेक करें कि क्या 'categories' टेबल मौजूद है
            if (Schema::hasTable('categories')) {
                // 1. Pehle Categories aur SubCategories load karein
                $headerCategories = Category::where('status', 1)
                    ->with(['subCategories' => function($q) {
                        $q->where('status', 1);
                    }])
                    ->orderBy('id', 'asc')
                    ->get();

                // 2. Har Category ke liye manually 4 latest products load karein
                foreach ($headerCategories as $category) {
                    // पक्का करें कि products टेबल भी हो (रिलेशन के लिए)
                    if (Schema::hasTable('products')) {
                        $latestProducts = $category->products()
                            ->where('status', 1)
                            ->latest()
                            ->take(4)
                            ->get();

                        $category->setRelation('products', $latestProducts);
                    }
                }
            }

            $view->with('headerCategories', $headerCategories);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 👇 CART COUNT GLOBAL LOGIC 👇
        View::composer('*', function ($view) {
            $cartGlobalCount = 0;

            // ✅ चेक करें कि क्या 'carts' टेबल मौजूद है
            if (Schema::hasTable('carts')) {
                $sessionId = Session::getId();
                $userId = Auth::id();

                if ($sessionId) {
                    $cartGlobalCount = Cart::where(function($q) use ($sessionId, $userId) {
                        if ($userId) {
                            $q->where('user_id', $userId);
                        } else {
                            $q->where('session_id', $sessionId);
                        }
                    })->count();
                }
            }

            $view->with('cartGlobalCount', $cartGlobalCount);
        });
    }
}
