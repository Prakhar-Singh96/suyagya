<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Http\Controllers\Controller;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = ProductReview::with('product')->latest()->paginate(10);
        return view('admin.reviews.index', compact('reviews'));
    }

    // 2. Show Create Form
    public function create()
    {
        // Product select karne ke liye list chahiye
        $products = Product::where('status', 1)->select('id', 'name')->get();
        return view('admin.reviews.create', compact('products'));
    }

    // 3. Store Review
    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'display_name' => 'required|string|max:255',
            'email'        => 'required|email',
            'rating'       => 'required|integer|min:1|max:5',
            'title'        => 'nullable|string|max:255',
            'review'       => 'required|string',
            'media.*'      => 'nullable|file|mimes:jpg,jpeg,png,mp4,webp|max:10240', // Max 10MB per file
            'status'       => 'required|boolean'
        ]);

        $mediaPaths = [];

        // Handle Multiple File Uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reviews'), $filename);
                $mediaPaths[] = 'uploads/reviews/' . $filename;
            }
        }

        ProductReview::create([
            'product_id'   => $request->product_id,
            'user_id'      => auth()->id(), // Admin ki ID (Optional)
            'display_name' => $request->display_name,
            'email'        => $request->email,
            'rating'       => $request->rating,
            'title'        => $request->title,
            'review'       => $request->review,
            'media'        => $mediaPaths, // Store as Array (JSON in DB)
            'status'       => $request->status, // 1 = Approved
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review Added Successfully!');
    }

    // 4. Delete Review
    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();
        return redirect()->back()->with('success', 'Review Deleted Successfully!');
    }

    // 5. Toggle Status (Optional helper for quick approve)
    public function toggleStatus($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->status = !$review->status; // Toggle 0 to 1, 1 to 0
        $review->save();
        return redirect()->back()->with('success', 'Status Updated');
    }

    // 6. Show Edit Form
    public function edit($id)
    {
        $review = ProductReview::findOrFail($id);
        $products = Product::where('status', 1)->select('id', 'name')->get();
        return view('admin.reviews.edit', compact('review', 'products'));
    }

    // 7. Update Review Logic
    public function update(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);

        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'display_name' => 'required|string|max:255',
            'email'        => 'required|email',
            'rating'       => 'required|integer|min:1|max:5',
            'title'        => 'nullable|string|max:255',
            'review'       => 'required|string',
            'media.*'      => 'nullable|file|mimes:jpg,jpeg,png,mp4,webp|max:10240',
            'status'       => 'required|boolean'
        ]);

        // Old media retrieve karein (Model me 'casts' => ['media' => 'array'] hona chahiye)
        $mediaPaths = $review->media ?? [];

        // Agar nayi files upload ki hain to unhe existing list me jod do
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/reviews'), $filename);
                $mediaPaths[] = 'uploads/reviews/' . $filename;
            }
        }

        $review->update([
            'product_id'   => $request->product_id,
            'display_name' => $request->display_name,
            'email'        => $request->email,
            'rating'       => $request->rating,
            'title'        => $request->title,
            'review'       => $request->review,
            'media'        => $mediaPaths, // Updated Media List
            'status'       => $request->status,
        ]);

        return redirect()->route('admin.reviews.index')->with('success', 'Review Updated Successfully!');
    }
}
