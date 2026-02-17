<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\FilterValue;

class WhatsAppController extends Controller
{
    // 1. Meta Verification Logic (Handshake)
    public function verifyWebhook(Request $request)
    {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN');

        if ($request->input('hub_mode') === 'subscribe' &&
            $request->input('hub_verify_token') === $verifyToken) {
            return response($request->input('hub_challenge'), 200);
        }

        return response('Unauthorized', 403);
    }

    // 2. Incoming Message Logic
    public function handleWebhook(Request $request)
    {
        $data = $request->all();

        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $from = $message['from'];
            $text = strtolower($message['text']['body'] ?? '');

            // 🚀 Dynamic Product Search
            $product = Product::where('status', 1)
                        ->where('name', 'LIKE', "%$text%")
                        ->first();

            if ($product) {
                $reply = "Namaste! *" . $product->name . "* की जानकारी:\n\n";
                $reply .= "💰 Price: ₹" . round($product->price) . "\n";
                $reply .= "🔗 Link: " . url('/product/' . $product->slug);
            } else {
                $reply = "Namaste! Suyagya में आपका स्वागत है। आप हमसे किसी भी रुद्राक्ष या रत्न का नाम लिखकर उसकी कीमत पूछ सकते हैं।";
            }

            $this->sendWhatsAppMessage($from, $reply);
        }

        return response('OK', 200);
    }

    // 3. Send Message API Function
    private function sendWhatsAppMessage($to, $message)
    {
        $url = "https://graph.facebook.com/v18.0/" . env('WHATSAPP_PHONE_ID') . "/messages";

        Http::withToken(env('WHATSAPP_TOKEN'))->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $message]
        ]);
    }
}
