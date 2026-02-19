<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentSetting; // Model import karna na bhulein

class PaymentController extends Controller
{
    // 1. Show Payment Settings Page
    public function index()
    {
        // Pehli row uthao, agar nahi hai to nayi banao (Error se bachne ke liye)
        $setting = PaymentSetting::first();

        if(!$setting) {
            $setting = PaymentSetting::create([
                'key_id' => '',
                'key_secret' => '',
                'is_active' => 0
            ]);
        }

        return view('admin.payment.settings', compact('setting'));
    }

    // 2. Update Keys
    public function update(Request $request)
    {
        $request->validate([
            'razor_key' => 'required|string',
            'razor_secret' => 'required|string',
        ]);

        $setting = PaymentSetting::first();

        $setting->key_id = $request->razor_key;
        $setting->key_secret = $request->razor_secret;

        // Checkbox handling: Agar checked hai to 1, nahi to 0
        $setting->is_active = $request->has('is_active') ? 1 : 0;

        $setting->save();

        return back()->with('success', 'Razorpay Settings Updated Successfully!');
    }
}
