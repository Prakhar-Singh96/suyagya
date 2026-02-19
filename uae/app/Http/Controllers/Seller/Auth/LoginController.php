<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 1. Seller Login Form दिखाएं
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->user_type === 'seller') {
            return redirect()->route('seller.dashboard');
        }
        return view('seller.auth.login');
    }

    // 2. Login प्रक्रिया (केवल Sellers के लिए)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {

            $user = Auth::user();

            // 1. चेक करें कि यूजर टाइप 'seller' है या नहीं
            if ($user->user_type === 'seller') {

                // 2. चेक करें कि एडमिन ने अप्रूव किया है या नहीं (Status Check)
                if ($user->status === 'pending') {
                    Auth::logout();
                    return back()->withErrors(['email' => 'आपका अकाउंट अभी एडमिन द्वारा अप्रूव नहीं किया गया है। कृपया प्रतीक्षा करें।']);
                }
                elseif ($user->status === 'inactive') {
                    Auth::logout();
                    return back()->withErrors(['email' => 'आपका अकाउंट सस्पेंड कर दिया गया है।']);
                }

                return redirect()->route('seller.dashboard');
            }
            else {
                // अगर एडमिन या कस्टमर यहाँ लॉगिन करने की कोशिश करे
                Auth::logout();
                return back()->withErrors(['email' => 'यह लॉगिन केवल सेलर्स के लिए है।']);
            }
        }

        return back()->withErrors(['email' => 'ईमेल या पासवर्ड गलत है।']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('seller.login');
    }
}
