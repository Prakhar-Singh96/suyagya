<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Order;
use Razorpay\Api\Api;
use App\Models\Coupon;
use App\Models\Product;
use App\Mail\OrderPlaced;
use App\Models\OrderItem;
use App\Models\UserCoupon;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentSetting;
use App\Models\ProductVariant;
use App\Models\ReferralCoupon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $addresses = UserAddress::where('user_id', $userId)->get();

        // Cart Items bhi chahiye Order Summary ke liye
        $cartItems = Cart::with('product')->where('user_id', $userId)->get();

        // Agar cart khali hai to wapas bhej do (Sirf tab jab Direct Buy na ho raha ho)
        // Note: Direct buy ke liye hum modal use kar rahe hain, isliye ye page load nahi hoga
        if ($cartItems->isEmpty()) {
            return redirect()->route('products.search_listing')->with('error', 'Your cart is empty.');
        }

        // ============================================================
        // 🎮 GAMING COUPON LOGIC START
        // ============================================================

        $luckyCoupon = \App\Models\UserCoupon::where('user_id', $userId)
            ->where('is_used', 0) // Jo use nahi hua
            ->latest()
            ->first();

        $autoApplyDiscount = 0;
        $autoCouponCode = null;

        if ($luckyCoupon) {
            $autoApplyDiscount = $luckyCoupon->amount;
            $autoCouponCode = $luckyCoupon->code;

            // Frontend par message dikhane ke liye flash session set karein
            // Note: View me 'session("success")' check karna padega
            if (!session()->has('coupon_applied')) {
                session()->flash('success', '🎉 Congratulations! Your game reward of ₹' . $autoApplyDiscount . ' OFF has been applied.');
                session()->flash('coupon_applied', true); // Prevent duplicate messages on refresh
            }
        }

        return view('frontend.pages.checkout', compact('addresses', 'cartItems', 'autoApplyDiscount', 'autoCouponCode'));
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'address_line1' => 'required',
            'name' => 'required',
            'phone' => 'required'
        ]);

        UserAddress::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'address_line1' => $request->address_line1,
            'type' => $request->address_type ?? 'home'
        ]);

        return redirect()->back()->with('success', 'Address Saved Successfully');
    }

    // New AJAX Function
    public function saveAddressAjax(Request $request)
    {
        $request->validate([
            'pincode' => 'required',
            'address_line1' => 'required',
            'name' => 'required',
            'email' => 'required|email'
            //'phone' => 'required'
        ]);

        $user = Auth::user();

        // 2. 🔥 Phone Number Logic (Main Fix)
        // Agar form se number aya hai to wo lo, nahi to User Profile se utha lo
        $phoneToSave = $request->phone;

        if (empty($phoneToSave)) {
            $phoneToSave = $user->phone;
        }

        // Hum naye data ko user table me update karenge
        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        $address = UserAddress::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone' => $phoneToSave, // ✅ Ab yahan sahi number jayega
            'email' => $request->email,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'address_line1' => $request->address_line1,
            'type' => $request->type
        ]);

        return response()->json(['status' => true, 'address_id' => $address->id]);
    }

    // 🔥 MAIN ORDER LOGIC (Handles Both Cart & Direct Buy)
    // 🔥 MAIN ORDER LOGIC (Handles Both Cart & Direct Buy)
    public function placeOrder(Request $request)
    {
        $user = Auth::user();

        // -----------------------------
        // 1. ADDRESS HANDLING
        // -----------------------------
        $addressId = $request->address_id;

        if ($request->new_address_flag == '1' || !$addressId) {
            $request->validate([
                'new_pincode' => 'required',
                'new_address' => 'required',
                'new_name' => 'required',
                'new_phone' => 'required',
                'new_email' => 'required'
            ]);

            $newAddress = UserAddress::create([
                'user_id' => $user->id,
                'name' => $request->new_name,
                'phone' => $request->new_phone,
                'email' => $request->new_email,
                'pincode' => $request->new_pincode,
                'city' => $request->new_city,
                'state' => $request->new_state,
                'address_line1' => $request->new_address,
                'type' => $request->addr_type ?? 'home'
            ]);

            $finalAddress = $newAddress;
        } else {
            $finalAddress = UserAddress::findOrFail($addressId);
        }

        // placeOrder() के अंदर
        if ($request->referral_code) {
            // 1. चेक करें कि क्या यह कोड डेटाबेस में है
            $exists = \App\Models\ReferralCoupon::where('code', $request->referral_code)->first();

            if (!$exists) {
                return response()->json(['status' => false, 'message' => 'Invalid Referral Code! Please check again.']);
            }

            // 2. सुरक्षा चेक: खुद का कोड खुद इस्तेमाल नहीं कर सकते
            if ($exists->user_id == Auth::id()) {
                return response()->json(['status' => false, 'message' => 'You cannot use your own referral code.']);
            }
        }

        // -----------------------------
        // 2. PREPARE ORDER ITEMS & CALCULATE TOTAL
        // -----------------------------
        // $orderItemsData = [];
        // $totalAmount = 0;
        // 2. PREPARE ITEMS & CALCULATE TOTALS
        // 2. PREPARE ITEMS & CALCULATE TOTALS
        $orderItemsData = [];
        $subtotal = 0;   // कुल सेलिंग प्राइस (Price * Qty)
        $totalMrp = 0;   // कुल MRP (MRP * Qty) - बिना सिद्धार्थ के
        $totalSiddhCharge = 0; // कुल सिद्धार्थ चार्ज

        // if ($request->buy_mode == 'direct') {
        //     $product = Product::findOrFail($request->product_id);
        //     // 🚀 वजन (Weight) निकालें अगर variant_id भेजा गया है
        //     $weight = null;
        //     if ($request->variant_id) {
        //         $variant = \App\Models\ProductVariant::find($request->variant_id);
        //         if ($variant) {
        //             $weight = $variant->weight . 'g'; // वजन जैसे '50g'
        //         }
        //     }
        //     $qty = $request->quantity;
        //     // 🔥 सिद्धार्थ अमाउंट अलग से कैलकुलेट करें
        //     $isSiddh = $request->is_siddh ?? 0;
        //     $siddhAmountPerItem = ($isSiddh == 1) ? ($product->siddh_price ?? 0) : 0;

        //     $subtotal = round($product->price) * $qty;
        //     $totalMrp = round($product->mrp_price ?? $product->price) * $qty;
        //     $totalSiddhCharge = $siddhAmountPerItem * $qty;
        //     $itemTotalPrice = (round($product->price) + $siddhAmountPerItem) * $qty; // Price + Siddh मिलाकर Total

        //     $orderItemsData[] = [
        //         'product_id'   => $product->id,
        //         'product_name' => $product->name,
        //         'quantity'     => $qty,
        //         'price'        => round($product->price), // 👈 सिर्फ असली सेलिंग प्राइस
        //         'total_price'  => $itemTotalPrice, // 👈 नया कॉलम
        //         'mrp_price'    => round($product->mrp_price ?? $product->price), // 👈 शुद्ध MRP
        //         'is_siddh'     => $isSiddh,
        //         'siddh_amount' => $siddhAmountPerItem, // 👈 अलग से सिद्धार्थ चार्ज
        //         'ring_size'    => $request->ring_size,
        //         'weight'       => $weight // 👈 यहाँ वजन सेव होगा
        //     ];
        // } else {
        //     $cartItems = Cart::with('product', 'variant')->where('user_id', $user->id)->get();
        //     foreach ($cartItems as $item) {
        //         $qty = $item->quantity;
        //         $isSiddh = $item->is_siddh ?? 0;
        //         $siddhAmountPerItem = ($isSiddh == 1) ? ($item->product->siddh_price ?? 0) : 0;

        //         $subtotal += round($item->product->price) * $qty;
        //         $totalMrp += round($item->product->mrp_price ?? $item->product->price) * $qty;
        //         $totalSiddhCharge += $siddhAmountPerItem * $qty;
        //         $itemTotalPrice = (round($item->product->price) + $siddhAmountPerItem) * $qty;

        //         $orderItemsData[] = [
        //             'product_id'   => $item->product_id,
        //             'product_name' => $item->product->name,
        //             'quantity'     => $qty,
        //             'price'        => round($item->product->price ?? $item->product->price),
        //             'total_price'  => $itemTotalPrice, // 👈 नया कॉलम
        //             'mrp_price'    => round($item->product->mrp_price ?? $item->product->price),
        //             'is_siddh'     => $isSiddh,
        //             'siddh_amount' => $siddhAmountPerItem,
        //             'ring_size'    => $item->ring_size,
        //             'weight'       => $item->variant ? $item->variant->weight . 'g' : null // 👈 कार्ट में सेव वजन
        //         ];
        //     }
        // }
        if ($request->buy_mode == 'direct') {
            $product = Product::findOrFail($request->product_id);
            $qty = $request->quantity;

            // 🚀 सुधार 1: मास्टर प्राइस लॉजिक (वजन के हिसाब से असली कीमत उठाएं)
            $baseSellingPrice = round($product->price);
            $baseMrpPrice = round($product->mrp_price ?? $product->price);
            $weight = null;

            if ($request->variant_id) {
                $variant = \App\Models\ProductVariant::find($request->variant_id);
                if ($variant) {
                    $baseSellingPrice = round($variant->selling_price); // वजन वाली असली कीमत
                    $baseMrpPrice = round($variant->mrp_price);
                    $weight = $variant->weight . 'g';
                }
            }

            $isSiddh = $request->is_siddh ?? 0;
            $siddhAmountPerItem = ($isSiddh == 1) ? round($product->siddh_price ?? 0) : 0;

            // फाइनल कैलकुलेशन (वजन की कीमत + सिद्धार्थ चार्ज)
            $subtotal = $baseSellingPrice * $qty;
            $totalMrp = $baseMrpPrice * $qty;
            $totalSiddhCharge = $siddhAmountPerItem * $qty;
            $itemTotalPrice = ($baseSellingPrice + $siddhAmountPerItem) * $qty;

            $orderItemsData[] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => $qty,
                'price'        => $baseSellingPrice,
                'total_price'  => $itemTotalPrice,
                'mrp_price'    => $baseMrpPrice,
                'is_siddh'     => $isSiddh,
                'siddh_amount' => $siddhAmountPerItem,
                'ring_size'    => $request->ring_size,
                'weight'       => $weight
            ];
        } else {
            // 🛒 CART MODE: यहाँ भी वही गलती थी, अब फिक्स है
            $cartItems = Cart::with(['product', 'variant'])->where('user_id', $user->id)->get();

            foreach ($cartItems as $item) {
                $qty = $item->quantity;
                $isSiddh = $item->is_siddh ?? 0;
                $siddhAmountPerItem = ($isSiddh == 1) ? round($item->product->siddh_price ?? 0) : 0;

                // 🚀 सुधार 2: अगर वजन (Variant) है तो उसकी कीमत लो, वरना बेस प्राइस
                $itemUnitPrice = $item->variant ? round($item->variant->selling_price) : round($item->product->price);
                $itemUnitMrp = $item->variant ? round($item->variant->mrp_price) : round($item->product->mrp_price ?? $item->product->price);

                $subtotal += $itemUnitPrice * $qty;
                $totalMrp += $itemUnitMrp * $qty;
                $totalSiddhCharge += $siddhAmountPerItem * $qty;

                $itemTotalPrice = ($itemUnitPrice + $siddhAmountPerItem) * $qty;

                $orderItemsData[] = [
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity'     => $qty,
                    'price'        => $itemUnitPrice,
                    'total_price'  => $itemTotalPrice,
                    'mrp_price'    => $itemUnitMrp,
                    'is_siddh'     => $isSiddh,
                    'siddh_amount' => $siddhAmountPerItem,
                    'ring_size'    => $item->ring_size,
                    'weight'       => $item->variant ? $item->variant->weight . 'g' : null
                ];
            }
        }

        // 3. APPLY DISCOUNTS
        $adminDiscount = 0;
        $gameDiscount = 0;

        $baseForDiscount = $subtotal + $totalSiddhCharge;

        // placeOrder(Request $request) के अंदर:
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->where('status', 1)->first();
            if ($coupon && !($coupon->expires_at && Carbon::now()->gt($coupon->expires_at))) {
                // 🚀 यहाँ बदलाव: डिस्काउंट को round() करें
                $rawDiscount = ($coupon->type == 'fixed') ? $coupon->value : ($baseForDiscount * $coupon->value) / 100;
                $adminDiscount = round($rawDiscount);
            }
        }

        $usedGameCouponId = null;
        // B. Gaming Coupon Discount
        if ($request->gaming_coupon_code) {
            $luckyCoupon = \App\Models\UserCoupon::where('code', $request->gaming_coupon_code)->where('user_id', $user->id)->where('is_used', 0)->first();
            if ($luckyCoupon) {
                $gameDiscount = $luckyCoupon->amount;
                $usedGameCouponId = $luckyCoupon->id; // ✅ यहाँ ID मिल जाएगी
            }
        }

        $prepaidDiscount = ($request->payment_method == 'RAZORPAY') ? 25 : 0;

        // 🚀 फाइनल टोटल में प्रीपेड डिस्काउंट भी घटाएं
        $finalTotal = ($baseForDiscount - ($adminDiscount + $gameDiscount)) - $prepaidDiscount;

        // 💰 WALLET DEDUCTION
        $walletDeduction = 0;
        if ($request->use_coins == '1' && $user->wallet_balance > 0) {
            $walletDeduction = min($user->wallet_balance, max(0, $finalTotal));
            $finalTotal -= $walletDeduction;
        }
        $finalTotal = max(0, round($finalTotal));

        // 4. CREATE ORDER
        $order = Order::create([
            'order_number'     => 'ORD-' . strtoupper(Str::random(10)),
            'user_id'          => $user->id,
            'shipping_address' => $finalAddress->toArray(),
            'mrp_total'        => $totalMrp,         // 👈 शुद्ध MRP का जोड़
            'coupon_discount'  => $adminDiscount,
            'gaming_discount'  => $gameDiscount,
            'prepaid_discount' => $prepaidDiscount, // ✅ नया कॉलम यहाँ सेव होगा
            'wallet_amount'    => $walletDeduction, // Record wallet usage
            'coupon_code'      => $request->coupon_code,
            'refer_code_used'  => $request->referral_code, // 👈 यह नया कॉलम यहाँ आएगा
            'total_amount'     => $finalTotal,      // 👈 शुद्ध पेयबल अमाउंट
            // 'payment_method'   => $request->payment_method,
            'payment_method' => ($finalTotal == 0) ? 'WALLET' : $request->payment_method,
            // 'status'           => 'pending',
            // 'payment_status'   => 'pending'
            'status' => ($finalTotal == 0) ? 'processing' : 'pending',
            'payment_status' => ($finalTotal == 0) ? 'paid' : 'pending'
        ]);

        // 5. SAVE ORDER ITEMS
        foreach ($orderItemsData as $itemData) {
            $order->items()->create($itemData);
        }
        // 💰 DEDUCT FROM WALLET & LOG
        if ($walletDeduction > 0) {
            $user->decrement('wallet_balance', $walletDeduction);
            \App\Models\WalletTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $walletDeduction,
                'type' => 'debit',
                'description' => 'Used Coins for Order #' . $order->order_number
            ]);
        }
        // -----------------------------
        // 5. PAYMENT HANDLING (RAZORPAY FIXED)
        // -----------------------------
        if ($finalTotal == 0) {
            if ($request->buy_mode == 'cart') Cart::where('user_id', $user->id)->delete();
            // 2. 🚀 REFERRAL REWARD: प्रखर को 25 सिक्के दें (अगर अभिषेक ने उसका कोड यूज़ किया है)
            if ($order->refer_code_used) {
                $this->distributeReferralRewards($order);
            }

            // 3. 🚀 NEW COUPON: अभिषेक के लिए उसका खुद का रेफरल कोड बनाएं
            if (!\App\Models\ReferralCoupon::where('user_id', $user->id)->exists()) {
                $newCoupon = \App\Models\ReferralCoupon::create([
                    'user_id' => $user->id,
                    'code'    => 'SUY' . strtoupper(Str::random(5)) . $user->id,
                ]);
                // पॉप-अप दिखाने के लिए सेशन में डालें
                session()->flash('show_referral_popup', $newCoupon->code);
            }

            // 4. ईमेल भेजें
            $this->sendOrderEmail($order->id);

            // 5. फाइनल रिस्पॉन्स
            return response()->json([
                'status'  => 'success',
                'message' => 'Order Successful! Paid via Coins.'
            ]);
        }

        if ($request->payment_method == 'RAZORPAY') {
            $paymentSetting = PaymentSetting::first();
            if (!$paymentSetting || !$paymentSetting->key_id) {
                return response()->json(['status' => false, 'message' => 'Payment Gateway Not Configured']);
            }

            $api = new Api($paymentSetting->key_id, $paymentSetting->key_secret);

            // 🚀 यहाँ सुधार: अब $finalTotal का इस्तेमाल हो रहा है
            $finalAmountInPaise = (int) ($finalTotal * 100);

            $rzpOrder = $api->order->create([
                'receipt'         => (string) $order->id,
                'amount'          => $finalAmountInPaise,
                'currency'        => 'INR',
                'payment_capture' => 1
            ]);

            return response()->json([
                'status' => 'razorpay',
                'key' => $paymentSetting->key_id,
                'amount' => $finalAmountInPaise, // Paises for Frontend JS
                'currency' => 'INR',
                'name' => 'Suyagya Store',
                'description' => 'Order #' . $order->order_number,
                'image' => asset('assets/img/logo.png'),
                'order_id' => $order->id,
                'rzp_order_id' => $rzpOrder['id'],
                'prefill' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact' => $user->phone
                ]
            ]);
        }

        // 🟢 COD LOGIC
        else {
            if ($request->buy_mode == 'cart') {
                Cart::where('user_id', $user->id)->delete();
            }
            if ($order->refer_code_used) {
                $this->distributeReferralRewards($order);
            }
            // 🚀 बदलाव २: आर्डर होते ही नए यूजर के लिए कोड बनाएं (Chain System)
            if (!ReferralCoupon::where('user_id', $user->id)->exists()) {
                $coupon = ReferralCoupon::create([
                    'user_id' => $user->id,
                    'code'    => 'SUY' . strtoupper(Str::random(5)) . $user->id,
                ]);
                // 🚀 प्रोफेशनल पॉप-अप के लिए डेटा फ्लैश करें
                session()->flash('show_referral_popup', $coupon->code);
            }
            if ($usedGameCouponId) {
                \App\Models\UserCoupon::where('id', $usedGameCouponId)->update(['is_used' => 1]);
            }
            $this->sendOrderEmail($order->id);
            return response()->json(['status' => 'success', 'message' => 'Order Placed Successfully via COD!']);
        }
    }

    private function distributeReferralRewards($order)
    {
        if (!$order->refer_code_used) return;

        $referrerCoupon = \App\Models\ReferralCoupon::where('code', $order->refer_code_used)->first();
        if (!$referrerCoupon) return;

        $referrer = $referrerCoupon->user;
        $buyer = $order->user;

        // 🚀 लॉजिक: अगर यूजर अफिलिएट है तो 10%, वरना 5%
        $percentage = ($referrer->user_type == 'affiliate') ? 0.10 : 0.05;
        $rewardAmount = round($order->total_amount * $percentage);

        if ($rewardAmount > 0) {
            // 1. Referrer (पंडित जी/Influencer) को रिवॉर्ड
            $referrer->increment('wallet_balance', $rewardAmount);
            \App\Models\WalletTransaction::create([
                'user_id'     => $referrer->id,
                'order_id'    => $order->id,
                'amount'      => $rewardAmount,
                'type'        => 'credit',
                'description' => ($percentage * 100) . '% Referral Reward from ' . $buyer->name
            ]);

            // 2. Buyer (अभिषेक) को भी रिवॉर्ड (इसे आप हमेशा 5% रखना चाहें तो फिक्स कर सकते हैं)
            $buyerReward = round($order->total_amount * 0.05);
            $buyer->increment('wallet_balance', $buyerReward);
            \App\Models\WalletTransaction::create([
                'user_id'     => $buyer->id,
                'order_id'    => $order->id,
                'amount'      => $buyerReward,
                'type'        => 'credit',
                'description' => '5% Cashback for using Referral Code'
            ]);
        }
    }

    // 🔍 Check Address by Pincode
    public function checkAddressByPincode($pincode)
    {
        $userId = Auth::id();

        // Is user ka koi address hai is pincode par? (Latest wala uthao)
        $existingAddress = UserAddress::where('user_id', $userId)
            ->where('pincode', $pincode)
            ->latest()
            ->first();

        if ($existingAddress) {
            return response()->json([
                'status' => true,
                'found' => true,
                'data' => $existingAddress
            ]);
        }

        return response()->json(['status' => true, 'found' => false]);
    }

    // 🟢 VERIFY PAYMENT API (Updated)
    public function verifyPayment(Request $request)
    {
        $setting = PaymentSetting::first();
        $api = new Api($setting->key_id, $setting->key_secret);

        try {
            // 1. Signature Verify karein
            // Note: Keys ke naam exact yehi hone chahiye
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // 2. Agar Verify ho gaya -> Database Update
            $order = Order::findOrFail($request->order_id);
            $user = Auth::user();

            if ($order->refer_code_used) {
                $this->distributeReferralRewards($order);
            }

            // 🚀 बदलाव २: आर्डर होते ही नए यूजर के लिए कोड बनाएं (Chain System)
            if (!ReferralCoupon::where('user_id', $user->id)->exists()) {
                $coupon = ReferralCoupon::create([
                    'user_id' => $user->id,
                    'code'    => 'SUY' . strtoupper(Str::random(5)) . $user->id,
                ]);
                // 🚀 प्रोफेशनल पॉप-अप के लिए डेटा फ्लैश करें
                session()->flash('show_referral_popup', $coupon->code);
            }

            // 🚀 यहाँ सभी Razorpay IDs को सेव करें ताकि रिफंड किया जा सके
            $order->update([
                'payment_status' => 'paid',
                'status'         => 'processing',
                'rzp_payment_id' => $request->razorpay_payment_id, // 👈 रिफंड के लिए सबसे ज़रूरी
                'rzp_order_id'   => $request->razorpay_order_id,
                'rzp_signature'  => $request->razorpay_signature,
                'transaction_id' => $request->razorpay_payment_id, // बैकअप के लिए
            ]);

            // 3. Empty Cart (Agar pehle nahi kiya tha)
            Cart::where('user_id', Auth::id())->delete();

            // -----------------------------
            // 🔥 MARK GAMING COUPON USED
            // -----------------------------
            $luckyCoupon = \App\Models\UserCoupon::where('user_id', Auth::id())
                ->where('is_used', 0)
                ->first();

            if ($luckyCoupon) {
                $luckyCoupon->update(['is_used' => 1]);
            }

            // 🔥 MAIL SEND KARO (Payment Success hone par)
            $this->sendOrderEmail($order->id);

            return response()->json(['status' => true, 'message' => 'Payment Verified']);
        } catch (\Exception $e) {
            // ❌ Verification Failed
            return response()->json([
                'status' => false,
                'message' => 'Payment Verification Failed: ' . $e->getMessage()
            ]);
        }
    }

    // 🔄 AJAX: Fetch User Data & Addresses after Login
    public function getUserCheckoutData()
    {
        $user = Auth::user();
        $addresses = $user->addresses;

        // Render HTML from the new partial file
        $html = view('frontend.includes.checkout_address_list', compact('addresses'))->render();

        return response()->json([
            'status' => true,
            'user_name' => $user->name,
            'user_phone' => $user->phone,
            'has_address' => $addresses->count() > 0,
            'html' => $html
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $code = strtoupper($request->code); // कोड को हमेशा CAPS में रखें

        // 🔥 FIX: Ensure cartTotal is treated as a float
        $cartTotal = (float) str_replace(',', '', $request->cart_total);
        $user = Auth::user();

        // 1. Coupon Find karo
        $coupon = Coupon::where('code', $code)->where('status', 1)->first();

        // 2. Validation
        if (!$coupon) {
            return response()->json(['status' => false, 'message' => 'Invalid Coupon Code']);
        }

        // 🎯 मुख्य लॉजिक: अगर कूपन WELCOME10 है, तो ऑर्डर चेक करें
        if ($code === 'WELCOME10') {
            $orderCount = \App\Models\Order::where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->count();

            if ($orderCount > 0) {
                return response()->json(['status' => false, 'message' => 'Ye coupon sirf aapke pehle order ke liye hai!']);
            }
        }

        // Expiry Check (Agar NULL nahi hai tabhi check karo)
        if ($coupon->expires_at && Carbon::now()->gt($coupon->expires_at)) {
            return response()->json(['status' => false, 'message' => 'Coupon Expired']);
        }

        // Min Amount Check
        if ($coupon->min_cart_amount && $cartTotal < $coupon->min_cart_amount) {
            return response()->json(['status' => false, 'message' => 'Add more items worth ₹' . ($coupon->min_cart_amount - $cartTotal)]);
        }

        // 3. Calculation Logic (Main Fix)
        $discountAmount = 0;
        $couponValue = (float) $coupon->value;

        // 🔥 FIX: Check lowercase string matches
        $type = strtolower($coupon->type);

        if ($type == 'fixed' || $type == 'flat') {
            $discountAmount = $couponValue;
        } else {
            // Percent Case (10% of 500 = 50)
            $discountAmount = round(($cartTotal * $couponValue) / 100);
        }

        // Discount Total se zyada nahi ho sakta
        if ($discountAmount > $cartTotal) {
            $discountAmount = $cartTotal;
        }

        $newTotal = $cartTotal - $discountAmount;

        return response()->json([
            'status' => true,
            'message' => 'Coupon Applied Successfully!',
            'discount' => number_format($discountAmount, 0), // Format for display
            'new_total' => number_format($newTotal, 0)
        ]);
    }

    public function getCoupons()
    {
        // 1. सिर्फ वो कूपन लाओ जो Active हैं (Status=1)
        // AUR (Expiry future me ho YA Expiry Null ho)
        $coupons = \App\Models\Coupon::where('status', 1)
            ->where(function ($query) {
                $query->whereDate('expires_at', '>', Carbon::now())
                    ->orWhereNull('expires_at');
            })
            ->latest()
            ->get();

        // 2. Value ko Number banao taaki JS me dikkat na aaye
        $coupons->transform(function ($coupon) {
            $coupon->value = (float) $coupon->value;
            return $coupon;
        });

        return response()->json($coupons);
    }

    // 🔥 HELPER: Send Email to User & Admin
    public function sendOrderEmail($orderId)
    {
        try {
            $order = Order::with('items')->find($orderId);

            // 1. User Email (Jo address form se aaya)
            $userEmail = $order->shipping_address['email'];

            // 2. Admin Email (Jaha aapko notification chahiye)
            // Aap chaho to apni personal gmail daal lo taaki turant pata chale
            $adminEmail = 'ram@rammittal.com'; // 👈 Yahan apni personal ID dalein

            // Send to User (From: support@suyagya.com)
            if ($userEmail) {
                Mail::to($userEmail)->send(new OrderPlaced($order, false));
            }

            // Send to Admin (From: support@suyagya.com)
            Mail::to($adminEmail)->send(new OrderPlaced($order, true));
        } catch (\Exception $e) {
            \Log::error('Mail Sending Failed: ' . $e->getMessage());
        }
    }

    // public function cancelOrder(Request $request)
    // {

    //     $order = Order::find($request->order_id);

    //     if ($order && $order->payment_status == 'pending') {
    //         $order->status = 'cancelled';
    //         $order->payment_status = 'failed';
    //         $order->save();

    //         \Log::info('Order Successfully Cancelled'); // Success Log

    //         return response()->json(['status' => true, 'message' => 'Order Cancelled']);
    //     }

    //     \Log::warning('Order Cancel Condition Failed'); // Fail Log
    //     return response()->json(['status' => false]);
    // }

    public function cancelOrder(Request $request)
    {
        // 1. ऑर्डर ढूंढें
        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found']);
        }

        // 🛡️ सुरक्षा क्लॉज: सिर्फ तभी कैंसिल करें जब शिप न हुआ हो
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['status' => false, 'message' => 'This order is already shipped and cannot be cancelled.']);
        }

        try {
            // 💰 2. REFUND LOGIC: अगर पेमेंट 'PAID' है तो Razorpay से रिफंड करें
            if ($order->payment_status == 'paid' && !empty($order->rzp_payment_id)) {

                $setting = PaymentSetting::first();
                $api = new Api($setting->key_id, $setting->key_secret);

                // Razorpay API को रिफंड रिक्वेस्ट भेजें
                $refund = $api->payment->fetch($order->rzp_payment_id)->refund([
                    'amount' => (int)($order->total_amount * 100), // पैसे में (जैसे ₹100 = 10000)
                    'notes'  => [
                        'reason' => 'User cancelled the order',
                        'order_number' => $order->order_number
                    ]
                ]);

                Log::info('Razorpay Refund Processed for Order: ' . $order->order_number);
                $order->payment_status = 'refunded'; // स्टेटस बदलें
            } elseif ($order->payment_status == 'pending') {
                $order->status = 'cancelled';
                $order->payment_status = 'failed';
            }

            // 3. ऑर्डर स्टेटस अपडेट करें
            $order->status = 'cancelled';
            $order->save();

            return response()->json([
                'status' => true,
                'message' => 'Order successfully cancelled and refund initiated (if applicable).'
            ]);
        } catch (\Exception $e) {
            Log::error('Order Cancellation Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Cancellation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
