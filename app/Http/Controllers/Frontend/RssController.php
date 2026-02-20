<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;

class RssController extends Controller
{
    public function blogs()
    {
        $blogs = Blog::where('status', 1)
            ->latest()
            ->take(20)
            ->get();

        return response()
            ->view('frontend.rss.blogs', compact('blogs'))
            ->header('Content-Type', 'application/xml');
    }
}
