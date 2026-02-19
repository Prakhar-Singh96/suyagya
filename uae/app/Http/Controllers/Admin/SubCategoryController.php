<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    // 1. List all Sub-Categories
    public function index()
    {
        $subCategories = SubCategory::with('category')->latest()->get();
        return view('admin.subcategories.index', compact('subCategories'));
    }

    // 2. Show Add Form
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.subcategories.create', compact('categories'));
    }

    // 3. Store Data
    public function store(Request $request)
    {
        // Validation including SEO & OG fields
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sub_categories,slug',
            'description' => 'nullable|string',
            'status' => 'required|boolean',

            // 🟢 Main Image Validation
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_alt' => 'nullable|string|max:255',

            // SEO Meta
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',

            // Open Graph
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Prepare data (exclude file initially)
        $data = $request->except(['image', 'og_image']);

        // 🟢 Handle Main Image Upload
        $data['image'] = uploadImage($request, 'image', 'uploads/subcategories');

        // Handle OG Image Upload (using global helper)
        $data['og_image'] = uploadImage($request, 'og_image', 'uploads/subcategories/og_images');

        SubCategory::create($data);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Sub-Category created successfully!');
    }

    // 4. Show Edit Form
    public function edit($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $categories = Category::where('status', 1)->get();
        return view('admin.subcategories.edit', compact('subCategory', 'categories'));
    }

    // 5. Update Data
    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sub_categories,slug,' . $subCategory->id,
            'status' => 'required|boolean',

            // 🟢 Main Image Validation
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_alt' => 'nullable|string|max:255',

            // SEO Validation
            'meta_title' => 'nullable|string|max:255',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->except(['image', 'og_image']);

        // 🟢 Handle Main Image Update
        if ($request->hasFile('image')) {
            deleteImage($subCategory->image); // Purani image delete
            $data['image'] = uploadImage($request, 'image', 'uploads/subcategories');
        }

        // Handle OG Image Update
        if ($request->hasFile('og_image')) {
            // Purani image delete karein (Global helper)
            deleteImage($subCategory->og_image);
            // Nayi upload karein
            $data['og_image'] = uploadImage($request, 'og_image', 'uploads/subcategories/og_images');
        }

        $subCategory->update($data);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Sub-Category updated successfully!');
    }

    // 6. Delete Data
    public function destroy($id)
    {
        $subCategory = SubCategory::findOrFail($id);

        // Image delete karein
        deleteImage($subCategory->image);
        deleteImage($subCategory->og_image);

        $subCategory->delete();

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Sub-Category deleted successfully!');
    }
}
