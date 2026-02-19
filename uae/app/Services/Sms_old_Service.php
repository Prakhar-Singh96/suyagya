<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function sendOtp($phone, $otp)
    {
        $sid    = env('TWILIO_SID');
        $token  = env('TWILIO_TOKEN');
        $from   = env('TWILIO_FROM');

        // 🔥 Safety Check: Ensure phone starts with '+'
        // Twilio requires E.164 format (e.g., +919876543210)
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        try {
            $client = new Client($sid, $token);

            $message = "Your Suyagya Login OTP is: $otp";

            $client->messages->create(
                $phone,
                [
                    'from' => $from,
                    'body' => $message
                ]
            );

            Log::info("Global SMS Sent Successfully to $phone");
            return true;

        } catch (\Exception $e) {
            Log::error("Twilio SMS Failed: " . $e->getMessage());
            // Fail hone par false return karein taaki controller ko pata chale
            return false;
        }
    }
}
