<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Filter;
use App\Models\Product;
use App\Models\Category;
use App\Models\FilterValue;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Services\BigShipService;
use App\Http\Controllers\Controller;

class ProductListingController extends Controller
{
    // // 🟢 HELPER: Get Dynamic Filters & Counts based on context
    // 🟢 1. COMMON QUERY BUILDER (The Engine)
    private function getProductsQuery(Request $request, $context = [])
    {
        $query = Product::where('status', 1);

        // A. Context: Category
        if (isset($context['category_id'])) {
            $query->where('category_id', $context['category_id']);
        }

        // B. Context: SubCategory
        if (isset($context['sub_category_id'])) {
            $query->where('sub_category_id', $context['sub_category_id']);
        }

        // C. Context: Collection/Purpose (FilterValue)
        if (isset($context['filter_value_id'])) {
            $query->whereHas('filterValues', function($q) use ($context) {
                $q->where('filter_values.id', $context['filter_value_id']);
            });
        }

        // D. Apply Sidebar Filters (Price, Attributes)
        return $this->applyFilters($query, $request);
    }

    // 🟢 2. APPLY FILTERS (Helper)
    private function applyFilters($query, $request)
    {
        // 1. ✅ NEW: Handle Simple 'purpose' Parameter
        // (Ye Home Page ke 'Shop By Purpose' links ke liye hai)
        if ($request->filled('purpose')) {
            $purpose = $request->input('purpose');

            // Check in FilterValues table
            $query->whereHas('filterValues', function($q) use ($purpose) {
                $q->where('value', 'like', $purpose);
            });
        }

        // 2. Standard Filters (Sidebar - jo array bhejta hai)
        if ($request->filled('filter')) {
            foreach ($request->filter as $filterId => $valueIds) {
                if (!empty($valueIds)) {
                    $query->whereHas('filterValues', function ($q) use ($valueIds) {
                        $q->whereIn('filter_values.id', $valueIds);
                    });
                }
            }
        }

        // 👇👇 NEW LOGIC FOR HOME PAGE LINKS 👇👇
        if ($request->filled('type')) {
            $type = $request->type;

            if ($type == 'featured') {
                $query->where('is_featured', 1);
            }
            elseif ($type == 'best-selling') {
                $query->where('is_best_seller', 1);
            }
        }

        // 3. Price Filter
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        if ($request->filled('sort') || $request->filled('sort_by')) {
            // Support both 'sort' and 'sort_by'
            $sort = $request->input('sort') ?? $request->input('sort_by');

            switch ($sort) {
                case 'best-selling':
                    $query->where('is_best_seller', 1);
                    break;
                case 'created-descending': // Newest
                case 'newest':
                    $query->orderBy('created_at', 'desc'); // Ensure created_at is used
                    break;
                case 'created-ascending': // Oldest
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'price-ascending': // Low to High
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-descending': // High to Low
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc'); // Default fallback
            }
        } else {
            // 🟢 DEFAULT BEHAVIOR: NEWEST FIRST
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    // 🟢 3. GET DYNAMIC SIDEBAR FILTERS (Helper)
    private function getDynamicFilters($context = [])
    {
        return Filter::whereHas('filterValues.products', function ($q) use ($context) {
            $q->where('status', 1);
            if (isset($context['category_id'])) $q->where('category_id', $context['category_id']);
            if (isset($context['sub_category_id'])) $q->where('sub_category_id', $context['sub_category_id']);
            // Note: For 'Purpose' pages, we might want to show other filters, logic handles it naturally.
        })
        ->with(['filterValues' => function ($q) use ($context) {
            $q->whereHas('products', function ($sq) use ($context) {
                $sq->where('status', 1);
                if (isset($context['category_id'])) $sq->where('category_id', $context['category_id']);
                if (isset($context['sub_category_id'])) $sq->where('sub_category_id', $context['sub_category_id']);
            })
            ->withCount(['products' => function ($sq) use ($context) {
                $sq->where('status', 1);
                if (isset($context['category_id'])) $sq->where('category_id', $context['category_id']);
                if (isset($context['sub_category_id'])) $sq->where('sub_category_id', $context['sub_category_id']);
            }]);
        }])
        ->get();
    }

    // =========================================================
    // 🚀 PUBLIC METHODS (PAGE ROUTES)
    // =========================================================

    // 1. Category Page
    public function categoryProducts(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = $this->getProductsQuery($request, ['category_id' => $category->id]);
        $products = $query->paginate(12)->withQueryString();
        $filters = $this->getDynamicFilters(['category_id' => $category->id]);

        return view('frontend.pages.product_listing', compact('category', 'products', 'filters'));
    }

    // 2. SubCategory Page
    public function subCategoryProducts(Request $request, $cat_slug, $sub_slug)
    {
        $category = Category::where('slug', $cat_slug)->firstOrFail();
        $subCategory = SubCategory::where('slug', $sub_slug)->where('category_id', $category->id)->firstOrFail();

        $query = $this->getProductsQuery($request, ['sub_category_id' => $subCategory->id]);
        $products = $query->paginate(12)->withQueryString();
        $filters = $this->getDynamicFilters(['sub_category_id' => $subCategory->id]);

        return view('frontend.pages.product_listing', compact('category', 'subCategory', 'products', 'filters'));
    }

    // 🟢 3. SHOW ALL COLLECTION (Updated Method)
    public function showAllCollection(Request $request)
    {
        $query = Product::where('status', 1);

        // Default Title
        $pageTitle = "All Products";
        $pageDesc = "Explore our complete collection of spiritual products.";

        // 👇 Dynamic Title Set Karein 👇
        if ($request->has('type')) {
            if ($request->type == 'featured') {
                $pageTitle = "Featured Products";
                $pageDesc = "Handpicked exclusive spiritual items for you.";
            }
            elseif ($request->type == 'best-selling') {
                $pageTitle = "Best Selling Products";
                $pageDesc = "Our most loved and purchased spiritual items.";
            }
        }

        // Apply Logic
        $this->applyFilters($query, $request);

        $products = $query->paginate(12)->withQueryString();

        // Filters load...
        $filters = $this->getDynamicFilters();

        // Object Create karein View ke liye
        $category = (object) [
            'name' => $pageTitle,
            'description' => $pageDesc
        ];

        return view('frontend.pages.product_listing', compact('category', 'products', 'filters'));
    }

    public function productDetail($slug)
    {
        // $product = Product::where('slug', $slug)->where('status', 1)->firstOrFail();
        $product = Product::with(['images', 'category', 'subCategory'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $categories = Category::where('status', 1)->get();

        // Fetch Reviews (Only approved ones)
        $reviews = ProductReview::where('product_id', $product->id)
            ->where('status', 1) // Assuming 1 = Approved
            ->latest()
            ->get();

        // Calculate Stats
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        // Count per star for progress bars
        $starCounts = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        // Related products logic (Optional)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(8)->get();

        return view('frontend.pages.product_detail', compact('product', 'relatedProducts', 'categories', 'reviews', 'totalReviews', 'averageRating', 'starCounts'));
    }

    public function searchListing(Request $request)
    {
        $queryTerm = $request->get('q');

        // Base Query (Search by name)
        $query = Product::where('status', 1)
            ->where('name', 'like', "%{$queryTerm}%");

        // Apply Filters (Existing helper function)
        $this->applyFilters($query, $request);

        $products = $query->paginate(12)->withQueryString();

        // Filters load karne ke liye dummy empty category pass kar sakte hain ya custom logic
        // Yahan hum simply saare filters load kar rahe hain
        $filters = Filter::with('filterValues')->get();

        // Dummy category object for view compatibility
        $category = (object)['name' => "Search Results for: '$queryTerm'", 'description' => ''];

        return view('frontend.pages.product_listing', compact('category', 'products', 'filters'));
    }

    public function checkPincode($pincode)
    {
        // Warehouse Details
        $pickupPin = '110019';
        $weight = 0.5;
        $price = 999;

        $bigship = new BigShipService();
        $result = $bigship->checkServiceability($pickupPin, $pincode, $weight, $price);

        if ($result['status']) {

            // 🔥 TAT (Days) se Date calculate karein
            $days = (int) $result['days'];

            // Date Format: "Dec 15, 2025"
            $date = now()->addDays($days)->format('M d, Y');

            return response()->json([
                'status' => true,
                'date'   => $date,
                'days'   => $days, // Debug ke liye
                'message' => 'Delivery by ' . $date
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Service not available.'
            ]);
        }
    }
}
