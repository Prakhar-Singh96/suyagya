<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    // 1. List Page
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    // ✅ 2. Show Create Form (Ye Missing Tha)
    public function create()
    {
        return view('admin.coupons.create');
    }

    // 3. Store Logic
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'expires_at' => 'nullable|date',
            'status' => 'nullable|boolean' // Optional: Status field agar ho
        ]);

        // Status checkbox handling
        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Created Successfully');
    }

    // ✅ 4. Show Edit Form (Ye bhi Missing Tha)
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    // 5. Update Logic
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:coupons,code,' . $id, // Ignore current ID
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'expires_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status') ? 1 : 0;

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Updated Successfully');
    }

    // 6. Delete Logic
    public function destroy($id)
    {
        Coupon::findOrFail($id)->delete();
        return back()->with('success', 'Coupon Deleted Successfully');
    }
}
