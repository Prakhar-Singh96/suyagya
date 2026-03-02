<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Models\ReferralCoupon;
use App\Models\WalletTransaction;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Saare orders latest pehle layenge
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Admin side se order create karne ki zarurat kam padti hai
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not needed for now
    }

    /**
     * Display the specified resource.
     * Isme hum Order ki details aur Update Form dikhayenge
     */
    public function show(string $id)
    {
        $order = Order::with(['items.product', 'user'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Hum 'show' page par hi edit ka option denge, alag page ki zarurat nahi
        return abort(404);
    }

    /**
     * Update the specified resource in storage.
     * 🟢 MAIN LOGIC: Status Update + Tracking Info
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $oldStatus = $order->status; // पुराना स्टेटस याद रखें

        // 1. Basic Validation
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $data = [
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ];

        // 2. 🚚 Tracking Logic: Agar Status 'Shipped' hai, to details zaroori hain
        if ($request->status == 'shipped') {

            $request->validate([
                'awb_number' => 'required|string',
                'courier_name' => 'required|string',
            ], [
                'awb_number.required' => 'Tracking ID (AWB) is required when marking as Shipped.',
                'courier_name.required' => 'Courier Name is required when marking as Shipped.'
            ]);

            $data['awb_number'] = $request->awb_number;
            $data['courier_name'] = $request->courier_name;
            $data['tracking_url'] = $request->tracking_url;
            $data['expected_delivery_date'] = $request->expected_delivery_date;
        }

        // 🚀 REFERRAL REWARD LOGIC: अगर स्टेटस 'delivered' हो रहा है
        if ($request->status == 'delivered' && $oldStatus != 'delivered') {
            // चेक करें कि क्या इस आर्डर में कोई रेफरल कोड इस्तेमाल हुआ है
            if ($order->refer_code_used && $order->cashback_status != 'referral_paid') {
                $refCoupon = ReferralCoupon::where('code', $order->refer_code_used)->first();

                // सुरक्षा: खुद का कोड खुद इस्तेमाल करने पर रिवॉर्ड नहीं मिलेगा
                if ($refCoupon && $refCoupon->user_id != $order->user_id) {
                    $referrer = $refCoupon->user;

                    DB::transaction(function () use ($referrer, $order) {
                        // 1. रेफर करने वाले के वॉलेट में 25 Coins डालें
                        $referrer->increment('wallet_balance', 25);

                        // 2. ट्रांजैक्शन हिस्ट्री रिकॉर्ड करें
                        WalletTransaction::create([
                            'user_id' => $referrer->id,
                            'order_id' => $order->id,
                            'amount' => 25,
                            'type' => 'credit',
                            'description' => 'Referral Bonus for Order #' . $order->order_number
                        ]);

                        // दोबारा रिवॉर्ड न मिले इसलिए मार्क करें
                        $order->cashback_status = 'referral_paid';
                    });
                }
            }
        }

        // 3. Update Database
        $order->update($data);

        return redirect()->back()->with('success', 'Order Status Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        // Pehle items delete karein (Agar cascade delete DB me nahi hai to)
        $order->items()->delete();

        // Phir order delete karein
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order Deleted Successfully!');
    }
}
