<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory; // 👈 Import SubCategory Model

class SearchController extends Controller
{
    public function ajaxSearch(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) return '';

        // 1. PRODUCTS
        $products = Product::where('status', 1)
                        ->where(function($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%")
                              ->orWhere('description', 'like', "%{$query}%");
                        })
                        ->select('id', 'name', 'slug', 'main_image', 'price', 'mrp_price')
                        ->latest()
                        ->take(3)
                        ->get();

        // 2. SEARCH CATEGORIES
        $cats = Category::where('status', 1)
                    ->where('name', 'like', "%{$query}%")
                    ->select('id', 'name', 'slug', 'icon_image')
                    ->take(3)
                    ->get();

        // 3. SEARCH SUB-CATEGORIES
        $subs = SubCategory::where('status', 1)
                    ->where('name', 'like', "%{$query}%")
                    ->with('category') // Parent Category relation zaroori hai URL ke liye
                    ->take(3)
                    ->get();

        // 4. MERGE BOTH INTO 'COLLECTIONS'
        // Hum ek custom structure banayenge taaki view me loop lagana aasan ho
        $collections = collect();

        // Add Categories
        foreach($cats as $cat) {
            $collections->push((object)[
                'name' => $cat->name,
                'image' => $cat->icon_image,
                'url' => route('products.category', $cat->slug),
                'type' => 'Category'
            ]);
        }

        // Add SubCategories
        foreach($subs as $sub) {
            if($sub->category) { // Parent category honi chahiye
                $collections->push((object)[
                    'name' => $sub->name,
                    'image' => $sub->image, // SubCategory ka icon nahi hai to null
                    'url' => route('products.subcategory', [$sub->category->slug, $sub->slug]),
                    'type' => 'Sub Category'
                ]);
            }
        }

        // 5. PAGES & SUGGESTIONS (Same as before)
        $pages = collect([]);
        $suggestions = Product::where('name', 'like', "%{$query}%")->select('name')->distinct()->take(5)->pluck('name');

        return view('frontend.includes.search_results', compact('products', 'collections', 'pages', 'suggestions', 'query'));
    }
}
