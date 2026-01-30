<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\GroqService;
use App\Services\ProkeralaService;
use Illuminate\Support\Facades\Log;

class AstroChatController extends Controller
{
    public function getAstroAdvice(Request $request, ProkeralaService $prokerala, GroqService $groq)
    {
        Log::info("Astro Chat Request Started", $request->all());

        try {
            $astroDetails = $prokerala->getFullAstroData($request->all());

            if (!$astroDetails || $astroDetails['rashi'] === 'Unknown') {
                return response()->json(['message' => 'ज्योतिष डेटा प्राप्त नहीं हो सका।'], 500);
            }

            $userRashi = $astroDetails['rashi'];

            // 💡 Vedic to English Mapping
            $rashiMap = [
                'Mesha' => 'Aries',
                'Vrishabha' => 'Taurus',
                'Mithuna' => 'Gemini',
                'Karka' => 'Cancer',
                'Simha' => 'Leo',
                'Kanya' => 'Virgo',
                'Tula' => 'Libra',
                'Vrischika' => 'Scorpio',
                'Dhanu' => 'Sagittarius',
                'Makara' => 'Capricorn',
                'Kumbha' => 'Aquarius',
                'Meena' => 'Pisces'
            ];

            $panchangClean = [
                'Tithi' => $astroDetails['panchang']['tithi'][0]['name'] ?? 'Not Available',
                'Nakshatra' => $astroDetails['panchang']['nakshatra'][0]['name'] ?? 'Not Available',
                'Yog' => $astroDetails['panchang']['yog'][0]['name'] ?? 'Not Available',
                'Karan' => $astroDetails['panchang']['karan'][0]['name'] ?? 'Not Available',
            ];

            $englishRashi = $rashiMap[$userRashi] ?? $userRashi;

            // 🔍 1. Database Query: Ab 5 products aur Images/Price ke saath
            $recommendedProducts = Product::where('status', 1)
                ->where(function ($q) use ($userRashi, $englishRashi) {
                    $q->where('astro_rashi', 'like', "%$userRashi%")
                        ->orWhere('astro_rashi', 'like', "%$englishRashi%");
                })
                ->inRandomOrder()
                ->take(5)
                ->get(['name', 'slug', 'astro_planet', 'astro_benefits', 'sale_price', 'main_image']);

            $productsData = $recommendedProducts->isNotEmpty()
                ? json_encode($recommendedProducts)
                : "Suggest general spiritual items like Rudraksha or Yantras.";

            // 🔍 2. Advanced Groq Prompt for Bold text, Images & Price
            $prompt = "
                You are an expert Vedic Astrologer for 'Suyagya'.

                USER DATA:
                - Name: {$request->name}
                - Rashi: {$userRashi} ({$englishRashi})
                - Planets: " . json_encode($astroDetails['planets']) . "
                - Panchang: " . json_encode($panchangClean) . "

                AVAILABLE PRODUCTS: $productsData

                STRICT TASK:
                1. **Greeting**: Start with 'Namaste {$request->name} ji!'.

                2. **Planet Analysis**: 'planets' data ka upyog karke batayein ki unka Moon sign (Rashi) kya hai aur unke mukhya grah (Sun/Jupiter) unke vyaktitva (personality) ko kaise prabhavit kar rahe hain.

                3. **Panchang Insight**: 'panchang' data se unki Tithi ({$panchangClean['Tithi']}) aur Nakshatra ({$panchangClean['Nakshatra']}) ke bare mein 1-2 line ka vishesh mahatva (significance) batayein.

                4. **Recommendations**: Suggest up to 5 best products. Use this EXACT Markdown format for each:
                Product Name: **[Product Name Here]**
                Image: https://suyagya.com/[main_image]
                Price: ₹[sale_price]
                Benefit: [1-line astro reason]
                Buy Link: https://suyagya.com/product/[slug]

                STRICT RULES:
                - Image URL format must be: https://suyagya.com/[main_image] (Kyuki main_image column me path 'uploads/products/main/...' pehle se hai).
                - Links ko active karne ke liye bina kisi bracket ke pura URL likhein.
                - Tone: Expert Hinglish.
            ";

            $reply = $groq->getAstroAdvice($prompt);

            return response()->json([
                'status' => 'success',
                'message' => $reply,
                'debug_info' => ['rashi' => $userRashi, 'count' => $recommendedProducts->count()]
            ]);
        } catch (\Exception $e) {
            Log::error("AstroBot CRITICAL Error: " . $e->getMessage());
            return response()->json(['message' => 'तकनीकी खराबी! दोबारा प्रयास करें।'], 500);
        }
    }
}
