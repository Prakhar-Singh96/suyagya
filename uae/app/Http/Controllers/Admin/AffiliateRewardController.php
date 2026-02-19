<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WalletTransaction;

class AffiliateRewardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // 🚀 Normal Customers (सिर्फ देखने के लिए)
        $customers = User::where('user_type', 'customer')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()->paginate(10, ['*'], 'customers_page');

        // 🚀 Affiliate Users (पंडित जी / इन्फ्लुएंसर्स - पेआउट के लिए)
        $affiliates = User::where('user_type', 'affiliate')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()->paginate(10, ['*'], 'affiliates_page');

        return view('admin.rewards.index', compact('customers', 'affiliates'));
    }

    // 💰 Payout Logic: सिर्फ Affiliate के लिए
    public function settlePayout(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->user_type !== 'affiliate') {
            return redirect()->back()->with('error', 'Action not allowed for normal customers.');
        }

        $payoutAmount = $user->wallet_balance;

        if ($payoutAmount <= 0) {
            return redirect()->back()->with('error', 'Balance is already zero.');
        }

        // 1. बैलेंस 0 करें
        $user->update(['wallet_balance' => 0]);

        // 2. डेबिट ट्रांजेक्शन लॉग करें
        WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $payoutAmount,
            'type' => 'debit',
            'description' => 'Affiliate Payout Settled: ' . $payoutAmount . ' coins paid in cash/bank'
        ]);

        return redirect()->back()->with('success', 'Payout successful for ' . $user->name);
    }

    public function referralLogs()
    {
        // हम WalletTransaction मॉडल का इस्तेमाल करेंगे क्योंकि सारा रिवॉर्ड डेटा वहीं है
        $logs = \App\Models\WalletTransaction::with(['user', 'order.user']) // User और Order के साथ Load करें
            ->where('description', 'like', '%Referral%')
            ->orWhere('description', 'like', '%Cashback%')
            ->latest()
            ->paginate(20);

        return view('admin.rewards.referral_logs', compact('logs'));
    }
}
