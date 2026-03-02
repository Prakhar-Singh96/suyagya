<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // 1. Show Cart Page
    public function index()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        // User login hai to User ID se, nahi to Session ID se cart nikalo
        $cartItems = Cart::with('product')
            ->where(function ($q) use ($sessionId, $userId) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('session_id', $sessionId);
                }
            })->get();

        return view('frontend.pages.cart', compact('cartItems'));
    }

    // 2. Add to Cart Logic
    public function addToCart(Request $request)
    {

        // 🛑 1. ADMIN/STAFF/SELLER CHECK
        if (Auth::check()) {
            $user = Auth::user();

            // Blocked Roles ki list banayein
            $blockedRoles = ['superadmin', 'admin', 'seller', 'staff'];

            if (in_array($user->role, $blockedRoles) || in_array($user->user_type, $blockedRoles)) {

                return response()->json([
                    'status' => false,
                    'html' => '<div class="text-center p-5"><i class="las la-user-lock fs-1 text-danger"></i><p class="mt-3">Admins/Sellers cannot use the Cart.</p></div>',
                    'count' => 0,
                    'subtotal' => 0,
                    'savings' => 0,
                    'message' => 'Cart access denied for ' . ucfirst($user->role ?? $user->user_type)
                ]);
            }
        }
        $productId = $request->product_id;
        $variantId = $request->variant_id; // 👈 नया
        $ringSize = $request->ring_size;   // 👈 नया
        $quantityToAdd = $request->quantity;
        $isSiddh = $request->is_siddh;

        // 🚀 सुधार: अगर रिंग साइज मौजूद है, तो variant_id को NULL कर दें
        // ताकि डेटाबेस 'gemstone_variants' की ID को 'product_variants' में न ढूंढे
        if (!empty($ringSize)) {
            $variantId = null;
        }

        $sessionId = Session::getId();
        $userId = Auth::id();

        // 🚀 सुधार 2: सिलेक्टेड वैरिएंट का असली स्टॉक निकालें
        // अगर वजन सिलेक्टेड है तो variant टेबल से, वरना मेन प्रोडक्ट टेबल से स्टॉक लें
        $variant = null;
        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);
            $maxStock = $variant ? $variant->quantity : 0;
        } else {
            $product = Product::find($productId);
            $maxStock = $product ? $product->quantity : 0;
        }

        // Check if product already in cart
        $existingCart = Cart::where('product_id', $productId)
            ->where('is_siddh', $isSiddh)
            ->where('variant_id', $variantId) // 👈 नया
            ->where('ring_size', $ringSize)   // 👈 नया
            ->where(function ($q) use ($sessionId, $userId) {
                if ($userId) $q->where('user_id', $userId);
                else $q->where('session_id', $sessionId);
            })->first();
        $currentInCart = $existingCart ? $existingCart->quantity : 0;

        // 🚀 सुधार 4: फाइनल स्टॉक चेक (कार्ट + नई क्वांटिटी)
        if (($currentInCart + $quantityToAdd) > $maxStock) {
            return response()->json([
                'status' => false,
                'message' => "Cannot add more. You already have $currentInCart items in cart, and total available stock for this weight is $maxStock."
            ]);
        }

        if ($existingCart) {
            // Update Quantity
            $existingCart->increment('quantity', $quantityToAdd);
        } else {
            // Create New Entry
            Cart::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'product_id' => $productId,
                'variant_id' => $variantId, // 👈 नया
                'ring_size'  => $ringSize,  // 👈 नया
                'quantity' => $quantityToAdd,
                'is_siddh' => $isSiddh
            ]);
        }

        // Return updated count for Header Badge
        $count = Cart::where(function ($q) use ($sessionId, $userId) {
            if ($userId) $q->where('user_id', $userId);
            else $q->where('session_id', $sessionId);
        })->count();

        return response()->json(['status' => true, 'message' => 'Added to Cart!', 'cart_count' => $count]);
    }

    // 4. Update Quantity (AJAX)
    public function updateQuantity(Request $request)
    {
        $cartItem = Cart::with(['product', 'variant'])->findOrFail($request->cart_id);
        $newQty = $request->quantity;
        $maxStock = $cartItem->variant ? $cartItem->variant->quantity : $cartItem->product->quantity;

        // 1. Validate Stock
        if ($newQty > $maxStock) {
            return response()->json([
                'status' => false,
                'message' => 'Only ' . $maxStock . ' items left in stock!'
            ]);
        }

        // 2. Minimum 1 hona chahiye
        if ($newQty < 1) {
            return response()->json(['status' => false, 'message' => 'Minimum quantity is 1']);
        }

        // 3. Update Cart
        $cartItem->update(['quantity' => $newQty]);

        // 4. Calculate New Totals (Frontend update ke liye)
        $sessionId = \Illuminate\Support\Facades\Session::getId();
        $userId = \Illuminate\Support\Facades\Auth::id();

        $cartItems = Cart::with('product')->where(function ($q) use ($sessionId, $userId) {
            if ($userId) $q->where('user_id', $userId);
            else $q->where('session_id', $sessionId);
        })->get();

        $total = 0;
        foreach ($cartItems as $item) {
            $price = $item->product->price;
            if ($item->is_siddh) $price += $item->product->siddh_price;
            $total += ($price * $item->quantity);
        }

        // Item ka specific subtotal
        $itemPrice = $cartItem->product->price;
        if ($cartItem->is_siddh) $itemPrice += $cartItem->product->siddh_price;
        $itemSubtotal = $itemPrice * $newQty;

        return response()->json([
            'status' => true,
            'message' => 'Cart updated!',
            'item_subtotal' => number_format($itemSubtotal),
            'cart_total' => number_format($total)
        ]);
    }

    // 3. Remove Item
    public function remove($id)
    {
        Cart::destroy($id);
        return back()->with('success', 'Item removed from cart.');
    }

    // 🖥️ 5. FETCH SIDE CART HTML
    public function getSideCartHtml()
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        $cartItems = Cart::with(['product', 'variant'])->where(function ($q) use ($sessionId, $userId) {
            if ($userId) $q->where('user_id', $userId);
            else $q->where('session_id', $sessionId);
        })->latest()->get();

        $total = 0;
        $totalMrp = 0;

        foreach ($cartItems as $item) {
            // 🚀 मास्टर लॉजिक: अगर वैरिएंट है तो उसकी कीमत लें, वरना प्रोडक्ट की बेस प्राइस
            $basePrice = $item->variant ? round($item->variant->selling_price) : round($item->product->price);
            $mrpPrice = $item->variant ? round($item->variant->mrp_price) : round($item->product->mrp_price ?? $item->product->price);

            $siddhPrice = $item->is_siddh ? round($item->product->siddh_price) : 0;

            $priceWithSiddh = $basePrice + $siddhPrice;
            $mrpWithSiddh = $mrpPrice + $siddhPrice;

            $total += $priceWithSiddh * $item->quantity;
            $totalMrp += $mrpWithSiddh * $item->quantity;
        }

        $savings = $totalMrp - $total;

        // 🔥 3. FETCH RECOMMENDATIONS (New Code)
        // Aap yahan logic change kar sakte hain (e.g. is_bestseller column)
        $bestSellers = Product::where('status', 1)->inRandomOrder()->take(5)->get();
        $youMayLike = Product::where('status', 1)->inRandomOrder()->skip(5)->take(5)->get();

        // View Render with new variables
        $html = view('frontend.includes.side_cart_items', compact('cartItems', 'bestSellers', 'youMayLike'))->render();
        // $html = view('frontend.includes.side_cart_items', compact('cartItems'))->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'count' => $cartItems->count(),
            'subtotal' => number_format($total),
            'savings' => number_format($savings)
        ]);
    }

    public function updateSideCartQty(Request $request)
    {
        // 🚀 सुधार: वैरिएंट को भी लोड करें ताकि उसका स्टॉक (Quantity) मिल सके
        $cartItem = Cart::with(['product', 'variant'])->find($request->cart_id);

        if (!$cartItem) {
            return response()->json(['status' => false, 'message' => 'Item not found']);
        }

        $newQty = $cartItem->quantity;

        // 🚀 मास्टर स्टॉक चेक:
        // अगर वैरिएंट (वजन) सिलेक्टेड है, तो वैरिएंट टेबल से स्टॉक लें, वरना मेन प्रोडक्ट से
        $maxStock = $cartItem->variant ? $cartItem->variant->quantity : $cartItem->product->quantity;

        // Action Check
        if ($request->action == 'plus') {
            if ($newQty < $maxStock) {
                $newQty++;
            } else {
                // 🔥 अब यह हर वजन के हिसाब से अलग मैसेज देगा
                $weightInfo = $cartItem->variant ? " in this weight ({$cartItem->variant->weight}g)" : "";
                return response()->json(['status' => false, 'message' => "Only $maxStock items available$weightInfo."]);
            }
        } elseif ($request->action == 'minus') {
            if ($newQty > 1) {
                $newQty--;
            } else {
                return response()->json(['status' => false, 'message' => 'Minimum quantity is 1']);
            }
        }

        // Update Database
        $cartItem->update(['quantity' => $newQty]);

        return response()->json(['status' => true, 'message' => 'Updated']);
    }
}
