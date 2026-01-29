<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey; // यहाँ अपनी Gemini Key डालें

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function getAstroAdvice($prompt)
    {
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey, [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'माफ़ करें, मैं अभी सलाह नहीं दे पा रहा हूँ।';
    }
}
