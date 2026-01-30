<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\GeminiService;
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

            if (!$astroDetails) {
                return response()->json(['message' => 'ज्योतिष डेटा प्राप्त नहीं हो सका।'], 500);
            }

            $userRashi = $astroDetails['rashi'];

            // 💡 Vedic to English Mapping
            $rashiMap = [
                'Mesha' => 'Aries', 'Vrishabha' => 'Taurus', 'Mithuna' => 'Gemini',
                'Karka' => 'Cancer', 'Simha' => 'Leo', 'Kanya' => 'Virgo',
                'Tula' => 'Libra', 'Vrischika' => 'Scorpio', 'Dhanu' => 'Sagittarius',
                'Makara' => 'Capricorn', 'Kumbha' => 'Aquarius', 'Meena' => 'Pisces'
            ];

            // English name nikalein, agar map mein nahi hai toh wahi rehne dein
            $englishRashi = $rashiMap[$userRashi] ?? $userRashi;

            // 2. Database Query (Dono naam se search karein taaki error na aaye)
            $recommendedProducts = Product::where('status', 1)
                ->where(function ($q) use ($userRashi, $englishRashi) {
                    $q->where('astro_rashi', 'like', "%$userRashi%")
                    ->orWhere('astro_rashi', 'like', "%$englishRashi%");
                })->get(['name', 'slug', 'astro_planet', 'astro_benefits']);

            $productsData = $recommendedProducts->isNotEmpty()
                ? json_encode($recommendedProducts)
                : "No specific products found for rashi $userRashi. Suggest general spiritual items.";

            // 3. Gemini Prompt
            $prompt = "
            You are an expert Vedic Astrologer for 'Suyagya'.
            Analyze this User Data:
            - Name: {$request->name}
            - Rashi: {$userRashi}
            - Planet Positions: " . json_encode($astroDetails['planets']) . "
            - Panchang: " . json_encode($astroDetails['panchang']) . "

            Store Products: " . $productsData . "

            TASK:
            1. User ko unki rashi ($userRashi) aur grah sthiti 1-2 line mein batayein.
            2. Hamare store se 2 best products suggest karein.
            3. Product link format: https://suyagya.com/product/slug

            Tone: Spiritual & Professional. Language: Hinglish.
        ";

            $reply = $groq->getAstroAdvice($prompt);

            return response()->json([
                'status' => 'success',
                'message' => $reply,
                'debug_info' => ['rashi' => $userRashi]
            ]);
        } catch (\Exception $e) {
            Log::error("AstroBot CRITICAL Error: " . $e->getMessage());
            return response()->json(['message' => 'तकनीकी खराबी! दोबारा प्रयास करें।'], 500);
        }
    }
}
