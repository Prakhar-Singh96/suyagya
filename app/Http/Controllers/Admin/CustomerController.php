<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail; // ✅ Import UserDetail model

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // ✅ अब हम 'customer' और 'affiliate' दोनों को लिस्ट में दिखाएंगे
        $customers = User::whereIn('user_type', ['customer', 'affiliate'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function edit(string $id)
    {
        // ✅ User के साथ उसकी details भी लोड करें
        $user = User::with('details')->findOrFail($id);
        return view('admin.customers.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // 1. Basic User Info Update
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_type' => $request->user_type, // 'customer' or 'affiliate'
            'status' => $request->status,
        ]);

        // 2. Affiliate/KYC Details Update
        // अगर user_type 'affiliate' है, तभी ये डिटेल्स सेव करना बेहतर है
        $user->details()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'pan_card_no'    => $request->pan_card_no,
                'aadhar_card_no' => $request->aadhar_card_no,
                'bank_name'      => $request->bank_name,
                'account_no'     => $request->account_no,
                'ifsc_code'      => $request->ifsc_code,
                'shop_name'      => $request->shop_name,
            ]
        );

        return redirect()->route('admin.customers.index')->with('success', 'User and Affiliate details updated successfully.');
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'Customer deleted successfully.');
        }
        return redirect()->back()->with('error', 'Customer not found.');
    }
}
