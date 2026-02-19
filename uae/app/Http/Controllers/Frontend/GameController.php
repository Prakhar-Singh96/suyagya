<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie; // 🔥 IMPORT THIS

class GameController extends Controller
{
    public function playGame(Request $request)
    {
        // 1. Agar User Guest Hai (Login nahi hai)
        if (!Auth::check()) {
            // Guest ko hamesha chota amount dikhao taaki wo login kare
            return response()->json([
                'status' => 'guest',
                'amount' => 10,
                'message' => 'Login to win up to ₹500!'
            ]);
        }

        // 2. Agar User Logged In Hai
        $user = Auth::user();

        // 🔥 CHANGE HERE: Sirf wo coupon dhundo jo ABHI TAK USE NAHI HUA (is_used = 0)
        // Agar purana coupon use ho chuka hai (is_used = 1), to ye null return karega aur naya game chalega.
        $existingCoupon = UserCoupon::where('user_id', $user->id)
            ->where('is_used', 0)
            ->first();

        if ($existingCoupon) {
            Cookie::queue('lucky_draw_played', 'true', 1440);
            return response()->json([
                'status' => 'already_played',
                'amount' => $existingCoupon->amount,
                'code' => $existingCoupon->code,
                'message' => 'You already have a coupon!'
            ]);
        }

        // 3. Naya Winner Logic (Random Amount)
        // Probability: 100 (High), 200 (Medium), 500 (Low)
        $amounts = [50, 50, 60, 60, 70, 80, 90, 100];
        $wonAmount = $amounts[array_rand($amounts)];
        $code = 'WIN' . strtoupper(Str::random(4)) . $wonAmount;

        // DB me save karo
        UserCoupon::create([
            'user_id' => $user->id,
            'code' => $code,
            'amount' => $wonAmount,
            'is_used' => false
        ]);

        // 🔥 NEW ADDED: Game Success ho gaya, ab 24 Hours ki Cookie set karo
        // 1440 minutes = 24 Hours
        Cookie::queue('lucky_draw_played', 'true', 1440);

        return response()->json([
            'status' => 'success',
            'amount' => $wonAmount,
            'code' => $code,
            'message' => 'Congratulations! Coupon Added.'
        ]);
    }
}
