<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function getAstroAdvice($prompt)
    {
        try {
            // 💡 आपके CURL के हिसाब से मॉडल और वर्जन अपडेट कर दिया है
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $this->apiKey, // CURL वाला हेडर तरीका
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            $result = $response->json();

            // एरर चेक
            if (isset($result['error'])) {
                Log::error("Gemini API Error:", ['error' => $result['error']]);
                return "माफ़ करें, अभी मैं विश्लेषण नहीं कर पा रहा हूँ। (Error: " . $result['error']['message'] . ")";
            }

            // रिस्पॉन्स एक्सट्रैक्ट करें
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }

            return "माफ़ करें, सही जवाब नहीं मिल सका।";

        } catch (\Exception $e) {
            Log::error("Gemini Service Exception: " . $e->getMessage());
            return "तकनीकी समस्या के कारण सलाह नहीं दी जा सकी।";
        }
    }
}
