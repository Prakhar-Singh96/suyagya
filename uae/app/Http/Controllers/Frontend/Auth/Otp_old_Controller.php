<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Models\Cart;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Services\SmsService; // 👈 1. Import Service

class OtpController extends Controller
{
    protected $smsService;

    // 👈 2. Constructor Injection (Ye Zaroori Hai)
    // Isse Laravel automatically SmsService ko load kar dega
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // 1. Send OTP
    public function sendOtp(Request $request)
    {
        // Validate Request (Phone is required)
        $request->validate([
            'phone' => 'required'
        ]);

        // Get full international number (e.g. +919876543210)
        $phone = $request->phone;

        // Generate Random OTP
        $otp = rand(1000, 9999);

        // Save OTP in Cache for 5 minutes
        Cache::put('otp_' . $phone, $otp, 300);

        // 👇👇 3. REAL TWILIO SMS SENDING 👇👇
        try {
            // Service ka use karke OTP bhejein
            $isSent = $this->smsService->sendOtp($phone, $otp);

            if($isSent) {
                return response()->json([
                    'status' => true,
                    'message' => 'OTP Sent Successfully!',
                ]);
            } else {
                 return response()->json([
                    'status' => false,
                    'message' => 'Failed to send SMS. Check logs.',
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'SMS Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 2. Verify OTP & Login/Register (No Changes needed here)
    public function loginWithOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required|numeric'
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $cachedOtp = Cache::get('otp_' . $phone);

        if ($cachedOtp && $cachedOtp == $otp) {
            // Find or Create User
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'name' => 'User ' . substr($phone, -4),
                    'email' => null,
                    'password' => null
                ]
            );

            // 🔴 2. FIX: Login se pehle Guest Cart ko User ID assign karein
            $sessionId = Session::getId();

            // Jo bhi items is session ID ke sath cart me hain, unhe is User ke naam kar do
            Cart::where('session_id', $sessionId)
                ->whereNull('user_id') // Sirf wahi jo kisi user ke nahi hain
                ->update(['user_id' => $user->id]);

            // 🟢 STEP 2: Merge Guest Wishlist (Jo abhi naya lagana hai)
            if (session()->has('guest_wishlist')) {
                $guestWishlist = session()->get('guest_wishlist');

                foreach ($guestWishlist as $productId) {
                    // Duplicate check karke database me insert karo
                    Wishlist::firstOrCreate([
                        'user_id' => $user->id,
                        'product_id' => $productId
                    ]);
                }

                // DB me dalne ke baad session clear kar do
                session()->forget('guest_wishlist');
            }

            Auth::login($user);
            Cache::forget('otp_' . $phone);

            return response()->json(['status' => true, 'message' => 'Login Successful!']);
        }

        return response()->json(['status' => false, 'message' => 'Invalid OTP!'], 401);
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
