<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
    }

    public function getAstroAdvice($prompt)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'model' => 'llama-3.3-70b-versatile', // यह सबसे पावरफुल मॉडल है
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert Vedic Astrologer for Suyagya. Use Hinglish.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
            ]);

            $data = $response->json();

            if (isset($data['error'])) {
                Log::error("Groq API Error:", ['error' => $data['error']]);
                return "क्षमा करें, अभी मैं विश्लेषण नहीं कर पा रहा हूँ।";
            }

            return $data['choices'][0]['message']['content'] ?? 'कोई सलाह नहीं मिल सकी।';

        } catch (\Exception $e) {
            Log::error("Groq Exception: " . $e->getMessage());
            return "तकनीकी समस्या!";
        }
    }
}
