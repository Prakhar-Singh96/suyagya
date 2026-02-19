<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'review' => 'required|string',
            'display_name' => 'required|string|max:100',
            'email' => 'required|email',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:10240', // 10MB max per file
        ]);

        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                // Determine folder based on type (image or video) if needed, or just dump in reviews
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reviews'), $filename);
                $mediaPaths[] = 'uploads/reviews/' . $filename;
            }
        }

        ProductReview::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id() ?? null, // Optional if logged in
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'display_name' => $request->display_name,
            'email' => $request->email,
            'media' => $mediaPaths, // Will be cast to JSON
            'status' => 0, // Pending
        ]);

        return redirect()->back()->with('success', 'Thank you! Your review has been submitted for approval.');
    }

    public function filterReviews(Request $request)
    {
        $query = ProductReview::where('product_id', $request->product_id)->where('status', 1);

        if ($request->sort == 'recent') {
            $query->latest();
        } elseif ($request->sort == 'highest') {
            $query->orderBy('rating', 'desc');
        } elseif ($request->sort == 'lowest') {
            $query->orderBy('rating', 'asc');
        } elseif ($request->sort == 'media') {
            $query->whereNotNull('media')->where('media', '!=', '[]');
        }

        $reviews = $query->get();

        $html = view('frontend.includes.review_list', compact('reviews'))->render();

        return response()->json(['html' => $html]);
    }
}
