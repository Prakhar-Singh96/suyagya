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
    // 🟢 1. COMMON QUERY BUILDER (Updated Logic)
    private function getProductsQuery(Request $request, $context = [])
    {
        $query = Product::where('status', 1)->with('reviews');

        // A. Context: Category (Primary OR Additional)
        if (isset($context['category_id'])) {
            $catId = $context['category_id'];

            $query->where(function ($q) use ($catId) {
                // 1. Check Primary Category
                $q->where('category_id', $catId)
                    // 2. OR Check Additional Categories (Pivot Table)
                    ->orWhereHas('additionalCategories', function ($subQ) use ($catId) {
                        $subQ->where('categories.id', $catId);
                    });
            });
        }

        // B. Context: SubCategory (Primary OR Additional)
        if (isset($context['sub_category_id'])) {
            $subCatId = $context['sub_category_id'];

            $query->where(function ($q) use ($subCatId) {
                // 1. Check Primary SubCategory
                $q->where('sub_category_id', $subCatId)
                    // 2. OR Check Additional SubCategories (Pivot Table)
                    ->orWhereHas('additionalSubCategories', function ($subQ) use ($subCatId) {
                        $subQ->where('sub_categories.id', $subCatId);
                    });
            });
        }

        // C. Context: Collection/Purpose (FilterValue)
        if (isset($context['filter_value_id'])) {
            $query->whereHas('filterValues', function ($q) use ($context) {
                $q->where('filter_values.id', $context['filter_value_id']);
            });
        }

        // D. Apply Sidebar Filters (Price, Attributes)
        return $this->applyFilters($query, $request);
    }

    // 🟢 2. APPLY FILTERS (Helper)
    private function applyFilters($query, $request)
    {
        // 1. Handle Simple 'purpose' Parameter
        if ($request->filled('purpose')) {
            $purpose = $request->input('purpose');
            $query->whereHas('filterValues', function ($q) use ($purpose) {
                $q->where('value', 'like', $purpose);
            });
        }

        // 2. Standard Filters
        if ($request->filled('filter')) {
            foreach ($request->filter as $filterId => $valueIds) {
                if (!empty($valueIds)) {
                    $query->whereHas('filterValues', function ($q) use ($valueIds) {
                        $q->whereIn('filter_values.id', $valueIds);
                    });
                }
            }
        }

        // 3. Home Page Links Logic
        if ($request->filled('type')) {
            $type = $request->type;
            if ($type == 'featured') {
                $query->where('is_featured', 1);
            } elseif ($type == 'best-selling') {
                $query->where('is_best_seller', 1);
            }
        }

        // 4. Price Filter
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        // 5. Sorting
        if ($request->filled('sort') || $request->filled('sort_by')) {
            $sort = $request->input('sort') ?? $request->input('sort_by');
            switch ($sort) {
                case 'best-selling':
                    $query->where('is_best_seller', 1);
                    break;
                case 'created-descending': // Newest
                case 'newest':
                    $query->orderBy('created_at', 'desc');
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
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    // 🟢 3. GET DYNAMIC SIDEBAR FILTERS (Helper)
    private function getDynamicFilters($context = [])
    {
        return Filter::whereHas('filterValues.products', function ($q) use ($context) {
            $q->where('status', 1);

            // Updated logic for dynamic filters too
            if (isset($context['category_id'])) {
                $catId = $context['category_id'];
                $q->where(function ($subQ) use ($catId) {
                    $subQ->where('category_id', $catId)
                        ->orWhereHas('additionalCategories', function ($deepQ) use ($catId) {
                            $deepQ->where('categories.id', $catId);
                        });
                });
            }
            if (isset($context['sub_category_id'])) {
                $subCatId = $context['sub_category_id'];
                $q->where(function ($subQ) use ($subCatId) {
                    $subQ->where('sub_category_id', $subCatId)
                        ->orWhereHas('additionalSubCategories', function ($deepQ) use ($subCatId) {
                            $deepQ->where('sub_categories.id', $subCatId);
                        });
                });
            }
        })
            ->with(['filterValues' => function ($q) use ($context) {
                $q->withCount(['products' => function ($sq) use ($context) {
                    $sq->where('status', 1);

                    if (isset($context['category_id'])) {
                        $catId = $context['category_id'];
                        $sq->where(function ($subQ) use ($catId) {
                            $subQ->where('category_id', $catId)
                                ->orWhereHas('additionalCategories', function ($deepQ) use ($catId) {
                                    $deepQ->where('categories.id', $catId);
                                });
                        });
                    }

                    if (isset($context['sub_category_id'])) {
                        $subCatId = $context['sub_category_id'];
                        $sq->where(function ($subQ) use ($subCatId) {
                            $subQ->where('sub_category_id', $subCatId)
                                ->orWhereHas('additionalSubCategories', function ($deepQ) use ($subCatId) {
                                    $deepQ->where('sub_categories.id', $subCatId);
                                });
                        });
                    }
                }]);
            }])
            ->get();
    }

    // =========================================================
    // 🚀 PUBLIC METHODS (PAGE ROUTES)
    // =========================================================

    public function categoryProducts(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $query = $this->getProductsQuery($request, ['category_id' => $category->id]);
        $products = $query->paginate(12)->withQueryString();
        $filters = $this->getDynamicFilters(['category_id' => $category->id]);

        return view('frontend.pages.product_listing', compact('category', 'products', 'filters'));
    }

    public function subCategoryProducts(Request $request, $cat_slug, $sub_slug)
    {
        $category = Category::where('slug', $cat_slug)->firstOrFail();
        $subCategory = SubCategory::where('slug', $sub_slug)->where('category_id', $category->id)->firstOrFail();

        $query = $this->getProductsQuery($request, ['sub_category_id' => $subCategory->id]);
        $products = $query->paginate(12)->withQueryString();
        $filters = $this->getDynamicFilters(['sub_category_id' => $subCategory->id]);

        return view('frontend.pages.product_listing', compact('category', 'subCategory', 'products', 'filters'));
    }

    public function showAllCollection(Request $request)
    {
        $query = Product::where('status', 1)->with('reviews');
        $pageTitle = "All Products";
        $pageDesc = "Explore our complete collection of spiritual products.";

        if ($request->has('type')) {
            if ($request->type == 'featured') {
                $pageTitle = "Featured Products";
                $pageDesc = "Handpicked exclusive spiritual items for you.";
            } elseif ($request->type == 'best-selling') {
                $pageTitle = "Best Selling Products";
                $pageDesc = "Our most loved and purchased spiritual items.";
            }
        }

        $this->applyFilters($query, $request);
        $products = $query->paginate(12)->withQueryString();
        $filters = $this->getDynamicFilters();

        $category = (object) [
            'name' => $pageTitle,
            'description' => $pageDesc
        ];

        return view('frontend.pages.product_listing', compact('category', 'products', 'filters'));
    }

    public function productDetail($slug)
    {
        $product = Product::with(['images', 'category', 'subCategory'])
            ->where('slug', $slug)
            ->where('status', 1)
            ->firstOrFail();

        $categories = Category::where('status', 1)->get();

        $reviews = ProductReview::where('product_id', $product->id)
            ->where('status', 1)
            ->latest()
            ->get();

        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        $starCounts = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        // Related Products Logic (Updated to check both categories)
        $relatedProducts = Product::withCount('reviews') // ✅ Total Reviews layega
            ->withAvg('reviews', 'rating') // ✅ Average Rating nikalega (reviews_avg_rating)
            ->where('status', 1)
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id)
                    ->orWhereHas('additionalCategories', function ($sq) use ($product) {
                        $sq->where('categories.id', $product->category_id);
                    });
            })
            ->where('id', '!=', $product->id)
            ->take(8)->get();

        return view('frontend.pages.product_detail', compact('product', 'relatedProducts', 'categories', 'reviews', 'totalReviews', 'averageRating', 'starCounts'));
    }

    public function searchListing(Request $request)
    {
        $queryTerm = $request->get('q');
        $query = Product::where('status', 1)
            ->where('name', 'like', "%{$queryTerm}%");

        $this->applyFilters($query, $request);
        $products = $query->paginate(12)->withQueryString();
        $filters = Filter::with('filterValues')->get();
        $category = (object)['name' => "Search Results for: '$queryTerm'", 'description' => ''];

        return view('frontend.pages.product_listing', compact('category', 'products', 'filters'));
    }

    public function checkPincode($pincode)
    {
        $pickupPin = '110019';
        $weight = 0.5;
        $price = 999;

        $bigship = new BigShipService();
        $result = $bigship->checkServiceability($pickupPin, $pincode, $weight, $price);

        if ($result['status']) {
            $days = (int) $result['days'];
            $date = now()->addDays($days)->format('M d, Y');

            return response()->json([
                'status' => true,
                'date'   => $date,
                'days'   => $days,
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
