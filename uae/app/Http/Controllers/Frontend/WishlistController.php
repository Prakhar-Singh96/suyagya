<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // 1. Wishlist Page dikhana
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with('product') // Product data sath laye
            ->latest()
            ->get();

        return view('frontend.pages.wishlist', compact('wishlists'));
    }

    // 2. Add or Remove (Toggle) Logic
    public function toggle(Request $request)
    {
        $productId = $request->product_id;
        $action = '';
        $message = '';
        $isGuest = false;

        // ✅ CASE 1: LOGGED IN USER
        if (Auth::check()) {
            $userId = Auth::id();
            $exists = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

            if ($exists) {
                $exists->delete();
                $action = 'removed';
                $message = 'Item removed from wishlist.';
            } else {
                Wishlist::create([
                    'user_id' => $userId,
                    'product_id' => $productId
                ]);
                $action = 'added';
                // 👇 Yahan User wala message set karein
                $message = 'Item added to wishlist!';
            }
            $count = Wishlist::where('user_id', $userId)->count();
        }

        // ✅ CASE 2: GUEST USER
        else {
            $isGuest = true;
            $wishlist = session()->get('guest_wishlist', []);

            if (($key = array_search($productId, $wishlist)) !== false) {
                unset($wishlist[$key]);
                $action = 'removed';
                $message = 'Removed from temporary wishlist.';
            } else {
                $wishlist[] = $productId;
                $action = 'added';
                // 👇 Yahan Guest wala message set karein
                $message = 'Temporarily saved! Please login to save permanently.';
            }

            session()->put('guest_wishlist', array_values($wishlist));
            $count = count($wishlist);
        }

        // JSON Response bhejo (JS is message ko pakad kar Toast me dikhayega)
        return response()->json([
            'status' => true,
            'action' => $action,
            'count' => $count,
            'message' => $message, // <--- Ye variable JS me jayega
            'is_guest' => $isGuest
        ]);
    }

    // 3. Fetch Wishlist Items for Modal (AJAX)
    public function fetchWishlist()
    {
        // A. Agar User Logged In hai
        if (Auth::check()) {
            $wishlists = Wishlist::where('user_id', Auth::id())
                ->with('product')
                ->latest()
                ->get();

            // Products extract karein
            $products = $wishlists->map(function ($item) {
                return $item->product;
            });
        }
        // B. Agar User Guest hai (Session)
        else {
            $productIds = session()->get('guest_wishlist', []);
            $products = \App\Models\Product::whereIn('id', $productIds)->get();
        }

        // HTML Generate karke return karo (Partial View ki zaroorat nahi, yahi loop chala denge)
        // Aap chaho to alag blade file bhi bana sakte ho, par simple rakhne ke liye yahan logic hai:

        if ($products->isEmpty()) {
            return response()->json(['status' => true, 'html' => '', 'empty' => true]);
        }

        $html = '';
        foreach ($products as $product) {
            $image = asset($product->main_image);
            $route = route('product.detail', $product->slug);
            $price = number_format($product->price, 0);

            $html .= '
        <div class="col-6 col-md-4 wishlist-item-' . $product->id . '">
            <div class="border h-100 position-relative">
                <button onclick="removeFromWishlist(' . $product->id . ')" class="btn-close position-absolute top-0 end-0 m-2 shadow-none" style="font-size: 10px; z-index: 5; background-color: #fff; padding: 5px; opacity: 1;"></button>

                <div class="img-box bg-light" style="height: 180px; overflow: hidden;">
                    <img src="' . $image . '" class="w-100 h-100 object-fit-cover">
                </div>

                <div class="p-3">
                    <h6 class="mb-1 text-truncate" style="font-size: 14px;">
                        <a href="' . $route . '" class="text-dark text-decoration-none">' . $product->name . '</a>
                    </h6>
                    <p class="fw-bold mb-3">₹' . $price . '</p>

                    <button onclick="moveToCart(' . $product->id . ')" class="btn btn-dark w-100 btn-sm rounded-0" style="font-size: 12px;">
                        MOVE TO CART
                    </button>
                </div>
            </div>
        </div>';
        }

        return response()->json(['status' => true, 'html' => $html, 'empty' => false]);
    }
}
