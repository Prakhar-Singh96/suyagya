<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 1. Admin Login Form दिखाएं
    public function showLoginForm()
    {
        if (Auth::check()) {

            $user = Auth::user();

            // agar already logged in hai aur superadmin hai
            if ($user->user_type === 'admin' && $user->hasRole('superadmin')) {
                return redirect()->route('admin.dashboard');
            }

            // staff ho to future me staff dashboard
            if ($user->user_type === 'staff' && $user->hasRole('staff')) {
                return redirect()->route('admin.dashboard'); // ya staff.dashboard
            }

            Auth::logout();
        }

        return view('admin.auth.login');
    }

    // 2. Login प्रक्रिया
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->filled('remember'))
        ) {

            $user = Auth::user();
            //dd($user);

            // ✅ CHECK: user_type must be admin
            if ($user->user_type !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'आप Admin नहीं हैं।'
                ]);
            }
            //dd($user->user_type);

            // ✅ CHECK: Only SUPERADMIN allowed
            if (! $user->hasRole('superadmin')) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'आपके पास Admin Panel का अधिकार नहीं है।'
                ]);
            }

            // ✅ CHECK: status active
            if (isset($user->status) && $user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'आपका अकाउंट अभी Active नहीं है।'
                ]);
            }

            return redirect()->route('admin.dashboard');

        }

        return back()->withErrors([
            'email' => 'ईमेल या पासवर्ड गलत है।'
        ]);
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
