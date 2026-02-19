<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Correct file facade import

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            // Basic
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'status' => 'required|boolean',

            // Images
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // New OG Image

            // Image Alts
            'banner_alt' => 'nullable|string|max:255',
            'icon_alt' => 'nullable|string|max:255',
            'cover_alt' => 'nullable|string|max:255',

            // SEO Meta
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',

            // Open Graph
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
        ]);

        // 2. Prepare Data (exclude file inputs initially)
        $data = $request->except(['icon_image', 'cover_image', 'banner_image', 'og_image']);

        // 3. Handle Image Uploads using helper function
        $data['icon_image'] = uploadImage($request, 'icon_image', 'uploads/categories/icons');
        $data['cover_image'] = uploadImage($request, 'cover_image', 'uploads/categories/covers');
        $data['banner_image'] = uploadImage($request, 'banner_image', 'uploads/categories/banners');
        $data['og_image'] = uploadImage($request, 'og_image', 'uploads/categories/og_images');

        // 4. Save to Database
        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'status' => 'required|boolean',

            // Images validation
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Text fields validation (Alts & SEO)
            'banner_alt' => 'nullable|string|max:255',
            'icon_alt' => 'nullable|string|max:255',
            'cover_alt' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
        ]);

        $data = $request->except(['icon_image', 'cover_image', 'banner_image', 'og_image']);

        // 2. Handle Image Updates
        if ($request->hasFile('icon_image')) {
            deleteImage($category->icon_image);
            $data['icon_image'] = uploadImage($request, 'icon_image', 'uploads/categories/icons');
        }

        if ($request->hasFile('cover_image')) {
            deleteImage($category->cover_image);
            $data['cover_image'] = uploadImage($request, 'cover_image', 'uploads/categories/covers');
        }

        if ($request->hasFile('banner_image')) {
            deleteImage($category->banner_image);
            $data['banner_image'] = uploadImage($request, 'banner_image', 'uploads/categories/banners');
        }

        if ($request->hasFile('og_image')) {
            deleteImage($category->og_image);
            $data['og_image'] = uploadImage($request, 'og_image', 'uploads/categories/og_images');
        }

        // 3. Update
        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Delete all associated images
        deleteImage($category->icon_image);
        deleteImage($category->cover_image);
        deleteImage($category->banner_image);
        deleteImage($category->og_image);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
