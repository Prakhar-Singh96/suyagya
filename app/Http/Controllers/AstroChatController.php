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
        try {
            $astroDetails = null;
            $userRashi = $request->user_rashi ?? 'Unknown';
            $panchangData = "";

            // 💡 पहली बार में कुंडली डेटा निकालें
            if ($request->has('dob') && $request->dob != null) {
                $astroDetails = $prokerala->getFullAstroData($request->all());
                $userRashi = $astroDetails['rashi'] ?? 'Unknown';

                $panchangClean = [
                    'Tithi' => $astroDetails['panchang']['tithi'][0]['name'] ?? 'Not Available',
                    'Nakshatra' => $astroDetails['panchang']['nakshatra'][0]['name'] ?? 'Not Available',
                    'Yog' => $astroDetails['panchang']['yog'][0]['name'] ?? 'Not Available',
                    'Karan' => $astroDetails['panchang']['karan'][0]['name'] ?? 'Not Available',
                ];
                $panchangData = json_encode($panchangClean);
            }

            // 💡 प्रोडक्ट मैपिंग (Vedic to English)
            $rashiMap = ['Mesha'=>'Aries', 'Vrishabha'=>'Taurus', 'Mithuna'=>'Gemini', 'Karka'=>'Cancer', 'Simha'=>'Leo', 'Kanya'=>'Virgo', 'Tula'=>'Libra', 'Vrischika'=>'Scorpio', 'Dhanu'=>'Sagittarius', 'Makara'=>'Capricorn', 'Kumbha'=>'Aquarius', 'Meena'=>'Pisces'];
            $englishRashi = $rashiMap[$userRashi] ?? $userRashi;

            // 🔍 डेटाबेस से 5 प्रोडक्ट्स
            $recommendedProducts = Product::where('status', 1)
                ->where(function ($q) use ($userRashi, $englishRashi) {
                    $q->where('astro_rashi', 'like', "%$userRashi%")->orWhere('astro_rashi', 'like', "%$englishRashi%");
                })->inRandomOrder()->take(5)->get(['name', 'slug', 'price', 'main_image']);

            $productsJson = json_encode($recommendedProducts);

            // 🧠 डाइनैमिक सिस्टम प्रॉम्ट
            $systemPrompt = "You are 'Suyagya Astro AI'.
            - Current User Rashi: $userRashi.
            - Planets: " . json_encode($astroDetails['planets'] ?? []) . "
            - Panchang: $panchangData.
            - Products: $productsJson.

            INSTRUCTIONS:
            1. Agar ye pehla message hai, toh Planet Analysis aur Panchang Insight (Tithi/Nakshatra) zaroor dein.
            2. Recommendations: Har product ko is format mein dikhayein:
               ### **[Product Name]**
               ![Image](https://suyagya.com/[main_image])
               - Price: ₹[price]
               - **Benefit**: [1-line reason]
               - [Buy Now Click Here](https://suyagya.com/product/[slug])
            3. Memory: User ke follow-up sawalon ka jawab unki rashi ($userRashi) ke hisab se dein.
            4. Tone: Spiritual Hinglish.";

            $reply = $groq->getChatResponse($systemPrompt, $request->history ?? [], $request->message);

            return response()->json([
                'status' => 'success',
                'message' => $reply,
                'user_rashi' => $userRashi
            ]);
        } catch (\Exception $e) {
            Log::error("Astro Error: " . $e->getMessage());
            return response()->json(['message' => 'Error!'], 500);
        }
    }
}
