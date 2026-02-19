<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order', 'asc')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'desktop_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'mobile_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 🟢 Changed to nullable
            'link'          => 'nullable|string',
            'sort_order'    => 'nullable|integer',
            'status'        => 'boolean'
        ]);

        $data = $request->all();

        // Desktop Image Upload
        if ($request->hasFile('desktop_image')) {
            $file = $request->file('desktop_image');
            $filename = time() . '_desk.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $data['desktop_image'] = 'uploads/banners/' . $filename;
        }

        // Mobile Image Upload
        if ($request->hasFile('mobile_image')) {
            $file = $request->file('mobile_image');
            $filename = time() . '_mob.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $data['mobile_image'] = 'uploads/banners/' . $filename;
        } else {
            // 🟢 Optional: If no mobile image, use desktop image or keep it null
            // $data['mobile_image'] = $data['desktop_image']; // Uncomment if you want fallback
            $data['mobile_image'] = null;
        }

        $data['status'] = $request->has('status') ? 1 : 0;

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner Added Successfully!');
    }

    // 4. Show Edit Form
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    // 5. Update Banner Logic
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'desktop_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Nullable kyu ki image change karna zaroori nahi
            'mobile_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link'          => 'nullable|string',
            'sort_order'    => 'nullable|integer',
            // Status check box se aata hai, validation ki khaas zaroorat nahi par boolean rakh sakte hain
        ]);

        $data = $request->except(['desktop_image', 'mobile_image']); // Images ko alag handle karenge

        // 🟢 Desktop Image Update
        if ($request->hasFile('desktop_image')) {
            // Purani image delete karein
            if (File::exists(public_path($banner->desktop_image))) {
                File::delete(public_path($banner->desktop_image));
            }

            // Nayi upload karein
            $file = $request->file('desktop_image');
            $filename = time() . '_desk.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $data['desktop_image'] = 'uploads/banners/' . $filename;
        }

        // 🟢 Mobile Image Update
        if ($request->hasFile('mobile_image')) {
            // Purani image delete karein
            if (File::exists(public_path($banner->mobile_image))) {
                File::delete(public_path($banner->mobile_image));
            }

            // Nayi upload karein
            $file = $request->file('mobile_image');
            $filename = time() . '_mob.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $data['mobile_image'] = 'uploads/banners/' . $filename;
        }

        // Status Handling
        $data['status'] = $request->has('status') ? 1 : 0;

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner Updated Successfully!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Delete Files
        if(File::exists(public_path($banner->desktop_image))){
            File::delete(public_path($banner->desktop_image));
        }
        if(File::exists(public_path($banner->mobile_image))){
            File::delete(public_path($banner->mobile_image));
        }

        $banner->delete();
        return back()->with('success', 'Banner Deleted!');
    }
}
