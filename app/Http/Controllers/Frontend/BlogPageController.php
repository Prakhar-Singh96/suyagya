<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogPageController extends Controller
{
    // 1. All Blogs Page (Listing)
    public function index()
    {
        // Active blogs fetch karein, latest pehle, paginate(9) matlab ek page par 9 blogs
        $blogs = Blog::where('status', 1)->latest()->paginate(9);

        return view('frontend.pages.blogs.index', compact('blogs'));
    }

    // 2. Single Blog Detail Page
    public function show($slug)
    {
        // Slug se blog dhoondo
        $blog = Blog::where('slug', $slug)->where('status', 1)->firstOrFail();

        // Sidebar ke liye Recent Blogs (Current blog ko chhod kar)
        $recentBlogs = Blog::where('status', 1)
                            ->where('id', '!=', $blog->id)
                            ->latest()
                            ->take(5)
                            ->get();

        // SEO Data (Layout file me pass karne ke liye)
        $meta = [
            'title' => $blog->meta_title ?? $blog->title,
            'description' => $blog->meta_description,
            'keywords' => $blog->meta_keywords,
            'image' => asset($blog->og_image ?? $blog->main_image)
        ];

        return view('frontend.pages.blogs.show', compact('blog', 'recentBlogs', 'meta'));
    }
}
