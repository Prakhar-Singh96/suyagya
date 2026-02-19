<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService; // ✅ 1. Import Service
use App\Models\Cart;
use Illuminate\Support\Facades\Session;

class OtpController extends Controller
{
    protected $smsService;

    // ✅ 2. Constructor Injection
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // 1. Send OTP
    public function sendOtp(Request $request)
    {
        // Validate Request
        $request->validate([
            'phone' => 'required'
        ]);

        $phone = $request->phone; // Frontend se "+9198..." aayega

        // Generate Random OTP
        $otp = rand(1000, 9999);

        // 👇👇 3. MSG91 SMS SENDING (Via Service) 👇👇
        // Service ab ek Array return karegi, Boolean nahi
        $response = $this->smsService->sendOtp($phone, $otp);

        if ($response['status']) {
            // ✅ Success: Cache OTP & Return JSON
            Cache::put('otp_' . $phone, $otp, 300); // 5 Minutes Expiry

            return response()->json([
                'status' => true,
                'message' => 'OTP Sent Successfully via MSG91!',
            ]);
        } else {
            // ❌ Fail: Return Error Message
            return response()->json([
                'status' => false,
                'message' => $response['message'], // Service se aaya hua error message
            ], 500);
        }
    }

    // 2. Verify OTP & Login (No Logic Change, Just Cleaned Up)
    public function loginWithOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required' // OTP numeric ya string ho sakta hai, numeric validation optional rakhein
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $cachedOtp = Cache::get('otp_' . $phone);

        // Check if OTP matches
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

            // 🔴 3. FIX: Merge Guest Cart to User
            $sessionId = Session::getId();

            Cart::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);

            // Login User
            Auth::login($user);

            // Clear OTP Cache
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
