<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomePageSetting;
use Illuminate\Support\Facades\File; // File delete karne ke liye

class HomePageSettingController extends Controller
{
    public function edit()
    {
        // First row fetch karein, agar nahi hai to naya instance bhejein
        $setting = HomePageSetting::first() ?? new HomePageSetting();
        return view('admin.settings.home_page', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'story_title' => 'nullable|string|max:255',
            'story_content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Data prepare karein
        $data = [
            'story_title' => $request->story_title,
            'story_content' => $request->story_content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'faq_content' => $request->has('faqs') ? array_values($request->faqs) : null,
        ];

        // Fetch existing record to handle image deletion
        $setting = HomePageSetting::first();

        // Image Upload Logic
        if ($request->hasFile('og_image')) {
            // Delete old image if exists
            if ($setting && $setting->og_image && File::exists(public_path($setting->og_image))) {
                File::delete(public_path($setting->og_image));
            }

            $file = $request->file('og_image');
            $filename = 'home_og_' . time() . '.' . $file->getClientOriginalExtension();
            $path = 'uploads/home';

            // Move file
            $file->move(public_path($path), $filename);

            // Save path to DB
            $data['og_image'] = $path . '/' . $filename;
        }

        // Update or Create (ID 1)
        HomePageSetting::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Home Page Settings updated successfully!');
    }
}
