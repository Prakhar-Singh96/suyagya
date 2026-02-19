<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Blog;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class SitemapController extends Controller
{
    // 1. MAIN INDEX (जो Prinjal जैसा दिखेगा)
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // यहाँ हम छोटे sitemaps का लिंक दे रहे हैं
        $routes = [
            'sitemap.pages',
            'sitemap.blogs',
            'sitemap.collections',
            'sitemap.categories',
            'sitemap.products',
        ];

        foreach ($routes as $route) {
            $sitemap .= '<sitemap>';
            $sitemap .= '<loc>' . route($route) . '</loc>';
            $sitemap .= '<lastmod>' . now()->toAtomString() . '</lastmod>';
            $sitemap .= '</sitemap>';
        }

        $sitemap .= '</sitemapindex>';
        return response($sitemap, 200)->header('Content-Type', 'text/xml');
    }

    // 2. STATIC PAGES (Home, FAQ, Track Order)
    // 2. STATIC PAGES (Updated with Policy & Contact Pages)
    public function pages()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // ✅ All Static Routes (Manually Added)
        $staticUrls = [
            url('/'),                   // Home
            route('frontend.faq'),      // FAQs
            url('/track-order'),        // Track Order
            route('about'),             // About Us
            route('contact'),           // Contact Us
            route('terms.conditions'),  // Terms & Conditions
            route('refund.policy'),     // Refund Policy
            route('privacy.policy'),    // Privacy Policy
            route('support.policy'),    // Support Policy
        ];

        foreach ($staticUrls as $url) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . $url . '</loc>';
            $sitemap .= '<changefreq>monthly</changefreq>'; // Policies roz change nahi hoti
            $sitemap .= '<priority>0.7</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';
        return response($sitemap, 200)->header('Content-Type', 'text/xml');
    }

    // 3. BLOGS SITEMAP (New Function)
    public function blogs()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // 1. Blog Listing Page
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . route('blogs.index') . '</loc>';
        $sitemap .= '<changefreq>daily</changefreq>';
        $sitemap .= '<priority>0.8</priority>';
        $sitemap .= '</url>';

        // 2. Single Blog Posts
        $blogs = Blog::where('status', 1)->latest()->get(); // Status check zaroori hai

        foreach ($blogs as $blog) {
            $sitemap .= '<url>';
            // Note: Route name 'blogs.show' use kiya hai jo apne bheja tha
            $sitemap .= '<loc>' . route('blogs.show', $blog->slug) . '</loc>';
            $sitemap .= '<lastmod>' . $blog->updated_at->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.8</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';
        return response($sitemap, 200)->header('Content-Type', 'text/xml');
    }

    // 3. COLLECTIONS (वो Wealth, Health वाला पुराना लॉजिक)
    public function collections()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Main Collection
        $sitemap .= '<url><loc>' . url('/collections/all') . '</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>';

        // आपके स्पेशल कीवर्ड्स
        $purposes = ['Wealth', 'Health', 'Love', 'Luck', 'Protection', 'Peace', 'Courage', 'Balance'];

        foreach ($purposes as $purpose) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . url("/collections/all?purpose={$purpose}") . '</loc>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.7</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';
        return response($sitemap, 200)->header('Content-Type', 'text/xml');
    }

    // 4. CATEGORIES & SUB-CATEGORIES
    public function categories()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Main Categories
        $categories = Category::where('status', 1)->get();
        foreach ($categories as $cat) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . url("/category/{$cat->slug}") . '</loc>';
            $sitemap .= '<lastmod>' . $cat->updated_at->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.8</priority>';
            $sitemap .= '</url>';
        }

        // Sub Categories (पुराने कमांड वाला लॉजिक)
        $subCats = SubCategory::with('category')->where('status', 1)->get();
        foreach ($subCats as $sub) {
            if ($sub->category) {
                $sitemap .= '<url>';
                $sitemap .= '<loc>' . url("/category/{$sub->category->slug}/{$sub->slug}") . '</loc>';
                $sitemap .= '<lastmod>' . $sub->updated_at->toAtomString() . '</lastmod>';
                $sitemap .= '<changefreq>weekly</changefreq>';
                $sitemap .= '<priority>0.8</priority>';
                $sitemap .= '</url>';
            }
        }

        $sitemap .= '</urlset>';
        return response($sitemap, 200)->header('Content-Type', 'text/xml');
    }

    // 5. PRODUCTS
    public function products()
    {
        // यहाँ Chunk की जरूरत नहीं है क्योंकि हम सीधा XML बना रहे हैं (फास्ट है)
        $products = Product::select('slug', 'updated_at')->where('status', 1)->latest()->get();

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($products as $product) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . url("/product/{$product->slug}") . '</loc>';
            $sitemap .= '<lastmod>' . $product->updated_at->toAtomString() . '</lastmod>';
            $sitemap .= '<changefreq>weekly</changefreq>';
            $sitemap .= '<priority>0.9</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';
        return response($sitemap, 200)->header('Content-Type', 'text/xml');
    }
}
