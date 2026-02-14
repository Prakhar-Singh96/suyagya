<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('frontend.pages.user.orders', compact('orders'));
    }

    // 2. ORDER DETAILS PAGE (Single Order)
    // Yahan bracket ($id) aayega kyunki hame ek specific order dekhna hai
    public function orderDetails($id)
    {
        $order = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('frontend.pages.user.order_details', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('frontend.pages.user.profile', compact('user'));
    }

    // 🚀 रील सबमिशन पेज दिखाएँ (सिर्फ प्रीपेड और डिलीवर ऑर्डर्स के लिए)
    public function submitReelForm()
    {
        // सिर्फ वही Delivered ऑर्डर्स उठाएं जिनमें 'Nazar Suraksha' प्रोडक्ट है
        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) {
                $query->where('product_name', 'like', '%Nazar Suraksha%');
            })
            ->latest()
            ->get();

        return view('frontend.pages.user.reel_submit', compact('orders'));
    }

    // 🚀 रील लिंक को डेटाबेस में सेव करें
    public function storeReelLink(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reel_link' => 'required|url'
        ]);

        $order = Order::where('user_id', Auth::id())->where('status', 'delivered')->findOrFail($request->order_id);

        $order->update([
            'reel_link' => $request->reel_link,
            'cashback_status' => 'pending' // एडमिन अप्रूवल का इंतज़ार
        ]);

        return back()->with('success', 'Reel link submitted successfully! Admin will review it soon.');
    }

    // 🚀 वॉलेट बैलेंस और ट्रांजैक्शन दिखाएँ
    public function wallet()
    {
        $user = Auth::user();
        // ट्रांजैक्शन के साथ यूजर का डेटा लें
        $transactions = \App\Models\WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('frontend.pages.user.wallet', compact('user', 'transactions'));
    }
}
