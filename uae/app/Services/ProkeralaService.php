<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProkeralaService
{
    protected $baseUrl = 'https://api.prokerala.com/v2';
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        // ✅ .env फाइल से वैल्यूज उठाना
        $this->clientId = env('PROKERALA_CLIENT_ID');
        $this->clientSecret = env('PROKERALA_CLIENT_SECRET');
    }

    public function getAccessToken()
    {
        // टोकन को कैश में रखें ताकि हर बार API कॉल न करनी पड़े
        return Cache::remember('prokerala_token', 3500, function () {
            // 💡 सुधार: टोकन के लिए URL में 'v2' या 'oauth/token' नहीं होगा
            $tokenUrl = 'https://api.prokerala.com/token';

            $response = Http::asForm()->post($tokenUrl, [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            $data = $response->json();

            // 💡 यहाँ चेक करें कि टोकन मिल रहा है या नहीं
            if (!isset($data['access_token'])) {
                Log::error("Prokerala Auth Failed: ", $data);
                return null;
            }

            return $data['access_token'];
        });
    }

    public function getFullAstroData($data)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            Log::error("Prokerala Error: Access Token is NULL");
            return null;
        }

        $datetime = $data['dob'] . 'T' . $data['tob'] . ':00+05:30';
        $location = $data['lat'] . ',' . $data['lng'];

        $planetsResponse = Http::withoutVerifying()->withToken($token)
            ->get($this->baseUrl . '/astrology/planet-position', [
                'datetime' => $datetime,
                'coordinates' => $location,
                'ayanamsa' => 1
            ])->json();

        // 🔍 STEP 1: Log Raw Response
        Log::info("STEP 1: Raw Planets API Response:", ['res' => $planetsResponse]);

        // 📜 2. Panchang API Call (यह लाइन मिसिंग थी)
        $panchangResponse = Http::withoutVerifying()->withToken($token)
            ->get($this->baseUrl . '/astrology/panchang', [
                'datetime' => $datetime,
                'coordinates' => $location,
                'ayanamsa' => 1
            ])->json();

        Log::info("STEP 2: Raw Panchang Response:", ['res' => $panchangResponse]);

        // प्रोकेराला के अलग-अलग वर्शन्स के लिए 'Deep Extraction'
        $planetsList = $planetsResponse['data']['planet_position'] ??
                    $planetsResponse['data']['planet-position'] ??
                    $planetsResponse['data'] ?? [];

        Log::info("STEP 3: Extracted Planets List Count:", ['count' => count($planetsList)]);

        $userRashi = 'Unknown';

        if (is_array($planetsList)) {
            foreach ($planetsList as $planet) {
                // 🌙 Moon Sign Search
                if (isset($planet['name']) && $planet['name'] === 'Moon') {
                    $userRashi = $planet['rasi']['name'] ?? 'Unknown';
                    Log::info("STEP 4: Found Moon Sign:", ['rashi' => $userRashi]);
                    break;
                }
            }
        }

        // अगर चंद्रमा नहीं मिला तो Ascendant (पहला रिकॉर्ड) ट्राई करें
        if ($userRashi === 'Unknown' && !empty($planetsList)) {
            $firstRecord = reset($planetsList);
            $userRashi = $firstRecord['rasi']['name'] ?? 'Unknown';
            Log::warning("STEP 5: Moon not found, using Fallback Rashi:", ['rashi' => $userRashi]);
        }

        Log::info("STEP 6: Final Rashi Value being returned:", ['rashi' => $userRashi]);

        return [
            'planets'  => $planetsList,
            'panchang' => $panchangResponse['data'] ?? [],
            'rashi'    => $userRashi
        ];
    }
}
