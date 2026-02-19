<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HomePageSetting;
use App\Models\GeneralFaq;
use App\Models\Product; // 👈 1. Product Model Import karein
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        // 1. Home Page Settings (Brand Story / Brand FAQs)
        $homeSettings = HomePageSetting::first();

        // 2. General FAQs (Shipping, Returns, etc.)
        $generalFaqs = GeneralFaq::where('status', 1)
                        ->orderBy('sort_order', 'asc')
                        ->get();

        // 3. Product FAQs (Only products that have FAQs) 👈 NEW LOGIC
        // Hum sirf wahi products layenge jinka 'faq_content' NULL nahi hai aur Active hain
        $productsWithFaqs = Product::where('status', 1)
                            ->whereNotNull('faq_content') // Jisme FAQ hai
                            ->select('id', 'name', 'slug', 'main_image', 'faq_content') // Optimize query
                            ->get();

        // PHP side filter: Kabhi kabhi database me "[]" (empty array) save ho jata hai, use hatane ke liye
        $productsWithFaqs = $productsWithFaqs->filter(function($product) {
            return !empty($product->faq_content) && count($product->faq_content) > 0;
        });

        return view('frontend.pages.faq', compact('homeSettings', 'generalFaqs', 'productsWithFaqs'));
    }
}
