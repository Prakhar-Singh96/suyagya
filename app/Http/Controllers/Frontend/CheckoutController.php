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
use App\Models\UserAddress;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        // -----------------------------
        // 2. PREPARE ORDER ITEMS & CALCULATE TOTAL
        // -----------------------------
        $orderItemsData = [];
        $totalAmount = 0;

        if ($request->buy_mode == 'direct') {
            $product = Product::findOrFail($request->product_id);
            $qty = $request->quantity;
            $isSiddh = $request->is_siddh ?? 0;

            //$price = $product->price;
            $price = round($product->price);
            if ($isSiddh == 1) {
                $price += $product->siddh_price;
            }

            $totalAmount = $price * $qty;

            $orderItemsData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $qty,
                'price' => $price,
                'is_siddh' => $isSiddh
            ];
        } else {
            $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'Cart is empty!']);
            }

            foreach ($cartItems as $item) {
                //$price = $item->product->price;
                $price = round($item->product->price);
                $isSiddh = $item->is_siddh;

                if ($isSiddh == 1) {
                    $price += $item->product->siddh_price;
                }

                $totalAmount += $price * $item->quantity;

                $orderItemsData[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'is_siddh' => $isSiddh
                ];
            }
        }

        // -----------------------------
        // A. APPLY ADMIN COUPON (Manual)
        // -----------------------------
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->where('status', 1)->first();

            if ($coupon) {
                // Check Expiry
                if (!($coupon->expires_at && Carbon::now()->gt($coupon->expires_at))) {
                    $discount = 0;
                    if ($coupon->type == 'fixed') {
                        $discount = $coupon->value;
                    } else {
                        $discount = ($totalAmount * $coupon->value) / 100;
                    }

                    if ($discount > $totalAmount) {
                        $discount = $totalAmount;
                    }
                    $totalAmount = $totalAmount - $discount;
                }
            }
        }

        // ============================================================
        // 🔥 B. APPLY GAMING COUPON (Automatic) -> YE CODE ADD KIA HAI
        // ============================================================
        $usedGameCouponId = null; // Store ID to mark used later for COD

        if ($request->gaming_coupon_code) {
            $luckyCoupon = \App\Models\UserCoupon::where('code', $request->gaming_coupon_code)
                ->where('user_id', Auth::id())
                ->where('is_used', 0)
                ->first();

            if ($luckyCoupon) {
                $gameDiscount = $luckyCoupon->amount;

                // Total amount minus karo (Ensure negative na ho)
                if ($gameDiscount > $totalAmount) {
                    $gameDiscount = $totalAmount;
                }

                $totalAmount -= $gameDiscount;

                // ID store karlo taaki COD me isko 'used' mark kar sakein
                $usedGameCouponId = $luckyCoupon->id;
            }
        }
        // ============================================================


        // -----------------------------
        // 3. CREATE ORDER IN DATABASE
        // -----------------------------
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => $user->id,
            'shipping_address' => $finalAddress->toArray(),
            'total_amount' => $totalAmount, // ✅ Final amount after both discounts
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        // -----------------------------
        // 4. SAVE ORDER ITEMS
        // -----------------------------
        foreach ($orderItemsData as $itemData) {
            $order->items()->create($itemData);
        }

        // -----------------------------
        // 5. PAYMENT HANDLING
        // -----------------------------

        // ✅ RAZORPAY LOGIC
        if ($request->payment_method == 'RAZORPAY') {

            $paymentSetting = PaymentSetting::first();

            if (!$paymentSetting || !$paymentSetting->key_id) {
                return response()->json(['status' => false, 'message' => 'Payment Gateway Not Configured']);
            }

            $apiKey = $paymentSetting->key_id;
            $apiSecret = $paymentSetting->key_secret;

            $api = new Api($apiKey, $apiSecret);

            $finalAmountInPaise = (int) round($totalAmount * 100);

            $rzpOrder = $api->order->create([
                'receipt'         => (string) $order->id,
                'amount'          => $finalAmountInPaise,
                'currency'        => 'INR',
                'payment_capture' => 1
            ]);

            return response()->json([
                'status' => 'razorpay',
                'key' => $apiKey,
                'amount' => $totalAmount * 100,
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

        // ✅ COD LOGIC
        else {
            // Cart Clear
            if ($request->buy_mode == 'cart') {
                Cart::where('user_id', $user->id)->delete();
            }

            // 🔥 GAMING COUPON MARK USED (Only for COD here)
            // Razorpay ke liye verifyPayment me mark hoga
            if ($usedGameCouponId) {
                \App\Models\UserCoupon::where('id', $usedGameCouponId)->update(['is_used' => 1]);
            }

            $this->sendOrderEmail($order->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Order Placed Successfully via COD!'
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

            // Payment Status Paid Mark karein
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $request->razorpay_payment_id,
                'status' => 'processing' // Optional: Pending se Processing kar dein
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
            $discountAmount = ($cartTotal * $couponValue) / 100;
        }

        // Discount Total se zyada nahi ho sakta
        if ($discountAmount > $cartTotal) {
            $discountAmount = $cartTotal;
        }

        $newTotal = $cartTotal - $discountAmount;

        return response()->json([
            'status' => true,
            'message' => 'Coupon Applied Successfully!',
            'discount' => number_format($discountAmount, 2), // Format for display
            'new_total' => number_format($newTotal, 2)
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

    public function cancelOrder(Request $request)
    {

        $order = Order::find($request->order_id);

        if ($order && $order->payment_status == 'pending') {
            $order->status = 'cancelled';
            $order->payment_status = 'failed';
            $order->save();

            \Log::info('Order Successfully Cancelled'); // Success Log

            return response()->json(['status' => true, 'message' => 'Order Cancelled']);
        }

        \Log::warning('Order Cancel Condition Failed'); // Fail Log
        return response()->json(['status' => false]);
    }

    public function applyWelcomeCoupon(Request $request)
    {
        $user = Auth::user();

        // 1. चेक करें क्या यूज़र ने पहले कभी ऑर्डर किया है
        $orderCount = \App\Models\Order::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled') // कैंसल ऑर्डर को न गिनें
            ->count();

        if ($orderCount == 0) {
            $coupon = \App\Models\Coupon::where('code', 'WELCOME10')
                ->where('status', 1)
                ->first();

            if ($coupon) {
                return response()->json([
                    'status' => 'success',
                    'coupon' => $coupon,
                    'message' => 'Welcome discount auto-applied!'
                ]);
            }
        }
        return response()->json(['status' => 'not_eligible']);
    }
}
