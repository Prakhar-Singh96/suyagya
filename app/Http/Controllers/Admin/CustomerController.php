<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // ✅ User Model Import करें

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 🔍 Search Logic
        $search = $request->input('search');

        $customers = User::where('user_type', 'customer') // ✅ Sirf 'user' role walon ko layein (Admin ko nahi)
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest() // Newest first
            ->paginate(15); // 15 per page

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return redirect()->back()->with('success', 'Customer deleted successfully.');
        }

        return redirect()->back()->with('error', 'Customer not found.');
    }

    // Baaki methods (create, store, edit, update, show) khali chhod sakte hain agar zarurat nahi hai
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
}
