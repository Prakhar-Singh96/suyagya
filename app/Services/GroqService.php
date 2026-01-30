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

    /**
     * AI को पुरानी बातचीत (Memory) के साथ कॉल करने के लिए
     */
    public function getChatResponse($systemPrompt, $history = [], $currentUserMessage)
    {
        // 1. सबसे पहले System Instruction सेट करें
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // 2. पुरानी बातचीत (History) को एरे में जोड़ें
        // $history में [{role: 'user', content: '...'}, {role: 'assistant', content: '...'}] होगा
        foreach ($history as $chat) {
            $messages[] = [
                'role'    => $chat['role'],
                'content' => $chat['content']
            ];
        }

        // 3. यूज़र का ताज़ा सवाल (Current Message) जोड़ें
        $messages[] = ['role' => 'user', 'content' => $currentUserMessage];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl, [
                'model'    => 'llama-3.3-70b-versatile', // तेज़ और स्मार्ट मॉडल
                'messages' => $messages,
                'temperature' => 0.7, // थोड़ी क्रिएटिविटी के लिए
                'max_tokens'  => 2048,
            ]);

            if ($response->failed()) {
                Log::error("Groq API Error: " . $response->body());
                return "Maafi chahta hoon, main abhi samajh nahi pa raha hoon. Kripya dobara puchein.";
            }

            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? 'No response from AI';

        } catch (\Exception $e) {
            Log::error("Groq Service Exception: " . $e->getMessage());
            return "Technical issue! Dobara try karein.";
        }
    }
}
