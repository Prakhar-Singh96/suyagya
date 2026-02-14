<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletAdminController extends Controller
{
    // 1. Nazar Suraksha के वो ऑर्डर्स दिखाना जिनकी रील आ चुकी है
    public function pendingCashbacks()
    {
        // 1. पेंडिंग ऑर्डर्स (जो अभी लिस्ट में दिख रहे हैं)
        $pendingOrders = Order::whereNotNull('reel_link')
            ->where('cashback_status', 'pending')
            ->whereHas('items', function ($query) {
                $query->where('product_name', 'like', '%Nazar Suraksha%');
            })
            ->with(['user', 'items'])
            ->latest()
            ->paginate(15, ['*'], 'page_pending'); // 👈 अलग पेज नाम

        // 2. अप्रूव्ड ऑर्डर्स (जो 'credited' हो चुके हैं)
        $approvedOrders = Order::whereNotNull('reel_link')
            ->where('cashback_status', 'credited')
            ->whereHas('items', function ($query) {
                $query->where('product_name', 'like', '%Nazar Suraksha%');
            })
            ->with(['user', 'items'])
            ->latest()
            ->paginate(15, ['*'], 'page_approved'); // 👈 अलग पेज नाम

        return view('admin.wallet.pending_cashbacks', compact('pendingOrders', 'approvedOrders'));
    }

    // public function approvedUser()
    // {
    //     $orders = Order::whereNotNull('reel_link')
    //         ->where('cashback_status', 'credited')
    //         ->whereHas('items', function($query) {
    //             $query->where('product_name', 'like', '%Nazar Suraksha%');
    //         })
    //         ->with(['user', 'items'])
    //         ->latest()
    //         ->paginate(20);

    //     return view('admin.wallet.pending_cashbacks', compact('orders'));
    // }

    // 2. कैशबैक अप्रूव करना और वॉलेट में पैसे डालना
    public function approveCashback(Request $request, $id)
    {
        // ऑर्डर के साथ आइटम्स और यूजर को लोड करें
        $order = Order::with(['user', 'items'])->findOrFail($id);

        if ($order->cashback_status === 'credited') {
            return back()->with('error', 'Cashback already credited!');
        }

        // 1. कुल डिस्काउंट निकालें (Coupon + Gaming + Prepaid)
        $totalDiscounts = ($order->coupon_discount ?? 0) +
            ($order->gaming_discount ?? 0) +
            ($order->prepaid_discount ?? 0);

        // 2. डिस्काउंट से पहले ऑर्डर की कुल वैल्यू निकालें (MRP + Siddh Charges)
        // आपके डेटा के अनुसार यह ₹999 + ₹100 = ₹1099 होना चाहिए, लेकिन हम डेटाबेस से कैलकुलेट करेंगे
        $orderBaseTotal = $order->items->sum('total_price');

        $cashbackAmount = 0;

        foreach ($order->items as $item) {
            if (stripos($item->product_name, 'Nazar Suraksha') !== false) {
                // आइटम की बेस प्राइस (जैसे ₹499)
                $itemBasePrice = $item->total_price;

                // 3. Pro-rata डिस्काउंट कैलकुलेशन:
                // सूत्र: (Item Price / Total Order Price) * Total Discount
                $itemProportion = $itemBasePrice / $orderBaseTotal;
                $itemShareOfDiscount = round($totalDiscounts * $itemProportion);

                // प्रभावी कीमत = बेस प्राइस - उसका डिस्काउंट हिस्सा
                $cashbackAmount += ($itemBasePrice - $itemShareOfDiscount);
            }
        }

        // 4. सुरक्षा चेक: कैशबैक कभी भी यूजर द्वारा दिए गए 'Final Amount' से ज्यादा नहीं हो सकता
        $cashbackAmount = min($cashbackAmount, $order->total_amount);

        DB::transaction(function () use ($order, $cashbackAmount) {
            // वॉलेट बैलेंस बढ़ाएं
            $order->user->increment('wallet_balance', $cashbackAmount);

            // ट्रांजैक्शन रिकॉर्ड करें
            WalletTransaction::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'amount' => $cashbackAmount,
                'type' => 'credit',
                'description' => 'Nazar Suraksha Effective Price Cashback - Order #' . $order->order_number
            ]);

            // स्टेटस अपडेट करें
            $order->update(['cashback_status' => 'credited']);
        });

        return back()->with('success', '₹' . $cashbackAmount . ' credited to wallet!');
    }
}
