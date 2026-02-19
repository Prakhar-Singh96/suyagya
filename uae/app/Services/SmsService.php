<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send OTP via MSG91 v5 Flow API
     */
    public function sendOtp($phone, $otp)
    {
        // 1. Phone Formatting
        // Remove '+' and ensure country code (91 for India) is present
        $cleanPhone = str_replace('+', '', $phone);

        // If number is 10 digits, prepend '91'
        if (strlen($cleanPhone) == 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        try {
            // 2. Prepare Data for MSG91 v5 API
            // Note: The key 'otp' below must match the variable name in your DLT template (e.g., {#otp#})
            // If your template variable is {#var1#}, change 'otp' => $otp to 'var1' => $otp
            $payload = [
                'template_id' => env('MSG91_TEMPLATE_ID'),
                'short_url' => '0', // Turn off short URL tracking
                'recipients' => [
                    [
                        'mobiles' => $cleanPhone,
                        'OTP'    => (string)$otp
                    ]
                ]
            ];

            // 3. Make API Call with Headers
            $response = Http::withHeaders([
                'authkey' => env('MSG91_AUTH_KEY'),
                'content-type' => 'application/json',
                'accept' => 'application/json'
            ])->post('https://control.msg91.com/api/v5/flow', $payload);

            $result = $response->json();

            // Log response for debugging
            Log::info("MSG91 Request Payload: " . json_encode($payload));
            Log::info("MSG91 Response: " . json_encode($result));

            // 4. Handle Response
            // MSG91 v5 usually returns 'type' => 'success' on successful queueing
            if (isset($result['type']) && $result['type'] == 'success') {
                return [
                    'status' => true,
                    'message' => 'OTP Sent Successfully!'
                ];
            } else {
                $errorMsg = $result['message'] ?? 'Unknown Error from MSG91';
                Log::error("MSG91 API Failure: " . $errorMsg);

                return [
                    'status' => false,
                    'message' => 'SMS Failed: ' . $errorMsg
                ];
            }

        } catch (\Exception $e) {
            Log::error("MSG91 Connection Error: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Server Error: Could not connect to SMS Gateway'
            ];
        }
    }
}
