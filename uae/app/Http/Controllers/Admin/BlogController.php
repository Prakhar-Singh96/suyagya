<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BlogController extends Controller
{
    // 1. List Blogs
    public function index()
    {
        $blogs = Blog::latest()->get();
        return view('admin.blogs.index', compact('blogs'));
    }

    // 2. Show Create Form
    public function create()
    {
        return view('admin.blogs.create');
    }

    // 3. Store Blog
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'content' => 'required',
        ]);

        $data = $request->all();

        // Image Upload
        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_blog.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/blogs'), $filename);
            $data['main_image'] = 'uploads/blogs/' . $filename;
        }

        // OG Image Upload
        if ($request->hasFile('og_image')) {
            $file = $request->file('og_image');
            $filename = time() . '_og.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/blogs/og'), $filename);
            $data['og_image'] = 'uploads/blogs/og/' . $filename;
        }

        $data['status'] = $request->has('status') ? 1 : 0;

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog Created Successfully!');
    }

    // 4. Show Edit Form
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blogs.edit', compact('blog'));
    }

    // 5. Update Blog
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug,' . $id,
            'content' => 'required',
        ]);

        $data = $request->all();

        // Main Image Update
        if ($request->hasFile('main_image')) {
            // Delete Old
            if ($blog->main_image && File::exists(public_path($blog->main_image))) {
                File::delete(public_path($blog->main_image));
            }
            // Upload New
            $file = $request->file('main_image');
            $filename = time() . '_blog.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/blogs'), $filename);
            $data['main_image'] = 'uploads/blogs/' . $filename;
        }

        // OG Image Update
        if ($request->hasFile('og_image')) {
            if ($blog->og_image && File::exists(public_path($blog->og_image))) {
                File::delete(public_path($blog->og_image));
            }
            $file = $request->file('og_image');
            $filename = time() . '_og.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/blogs/og'), $filename);
            $data['og_image'] = 'uploads/blogs/og/' . $filename;
        }

        $data['status'] = $request->has('status') ? 1 : 0;

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog Updated Successfully!');
    }

    // 6. Delete Blog
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->main_image && File::exists(public_path($blog->main_image))) {
            File::delete(public_path($blog->main_image));
        }
        if ($blog->og_image && File::exists(public_path($blog->og_image))) {
            File::delete(public_path($blog->og_image));
        }

        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog Deleted Successfully!');
    }
}
