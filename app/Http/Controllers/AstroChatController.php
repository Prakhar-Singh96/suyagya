<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\GeminiService;
use App\Services\ProkeralaService;
use Illuminate\Support\Facades\Log;

class AstroChatController extends Controller
{
    public function getAstroAdvice(Request $request, ProkeralaService $prokerala, GeminiService $gemini)
    {
        // 💡 Debugging: Har request ko log karein taaki error pakda ja sake
        Log::info("Astro Chat Request Started", $request->all());

        try {
            // 1. Prokerala API call
            $astroDetails = $prokerala->getFullAstroData($request->all());

            if (!$astroDetails) {
                Log::error("AstroBot Error: Prokerala returned no data.");
                return response()->json(['message' => 'माफ़ करें, ज्योतिष डेटा प्राप्त नहीं हो सका।'], 500);
            }

            $userRashi = $astroDetails['rashi'] ?? 'Unknown';
            $planets = json_encode($astroDetails['planets'] ?? []);
            $panchang = json_encode($astroDetails['panchang'] ?? []);

            // 2. डेटाबेस से प्रोडक्ट्स उठाएं (Isse Error nahi aayega agar data khali ho)
            $recommendedProducts = Product::where('status', 1)
                ->where(function ($q) use ($userRashi) {
                    $q->where('astro_rashi', 'like', "%$userRashi%")
                        ->orWhereNull('astro_rashi')
                        ->orWhere('astro_rashi', '');
                })->get(['name', 'slug', 'astro_planet', 'astro_benefits']);

            // 💡 Empty check taaki Gemini crash na ho
            $productsData = $recommendedProducts->isNotEmpty()
                ? json_encode($recommendedProducts)
                : "No specific products found. Suggest general spiritual items.";

            // 3. Gemini Prompt
            $prompt = "
                You are an expert Vedic Astrologer for 'Suyagya'.
                Analyze this User Data:
                - Name: {$request->name}
                - Rashi: {$userRashi}
                - Planet Positions: {$planets}
                - Panchang: {$panchang}

                Store Products: " . $productsData . "

                TASK:
                1. User ko unki rashi aur grah sthiti 1-2 line mein batayein.
                2. Hamare store se 2 best products suggest karein.
                3. Har product ka link format: https://suyagya.com/product/slug

                Tone: Spiritual & Professional. Language: Hinglish.
            ";

            $reply = $gemini->getAstroAdvice($prompt);

            return response()->json([
                'status' => 'success',
                'message' => $reply,
                'debug_info' => ['rashi' => $userRashi]
            ]);

        } catch (\Exception $e) {
            // 🚨 CRITICAL: Asli error yahan record hoga
            Log::error("AstroBot CRITICAL Error: " . $e->getMessage());
            return response()->json(['message' => 'तकनीकी खराबी! कृपया दोबारा प्रयास करें।'], 500);
        }
    }
}
