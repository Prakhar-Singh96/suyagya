<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Redirect;
use Illuminate\Support\Facades\Cache;

class RedirectController extends Controller
{
    public function index()
    {
        $redirects = Redirect::latest()->paginate(10);
        return view('admin.redirects.index', compact('redirects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'old_url' => 'required|unique:redirects,old_url',
            'new_url' => 'required',
        ]);

        // Slash (/) अपने आप लगा दो अगर यूजर भूल जाए
        $oldUrl = '/' . trim($request->old_url, '/');
        $newUrl = '/' . trim($request->new_url, '/');

        Redirect::create([
            'old_url' => $oldUrl,
            'new_url' => $newUrl,
            'status_code' => 301
        ]);

        // Cache साफ़ करो ताकि नया Redirect तुरंत चले
        Cache::forget("redirect_{$oldUrl}");

        return back()->with('success', 'Redirect Created Successfully!');
    }

    public function destroy($id)
    {
        $redirect = Redirect::findOrFail($id);
        Cache::forget("redirect_" . $redirect->old_url);
        $redirect->delete();
        return back()->with('success', 'Redirect Deleted!');
    }
}
