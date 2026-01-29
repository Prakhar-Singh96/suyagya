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
                \Log::error("Prokerala Auth Failed: ", $data);
                return null;
            }

            return $data['access_token'];
        });
    }

    public function getFullAstroData($data)
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $datetime = $data['dob'] . 'T' . $data['tob'] . ':00+05:30';
        $location = $data['lat'] . ',' . $data['lng'];

        // 🪐 1. Planets Position - ayanamsa जोड़ें
        $planets = Http::withToken($token)
            ->get($this->baseUrl . '/astrology/planet-position', [
                'datetime' => $datetime,
                'coordinates' => $location,
                'ayanamsa' => 1 // 👈 यह जोड़ना अनिवार्य है
            ])->json();

        // 📅 2. Panchang - ayanamsa जोड़ें
        $panchang = Http::withToken($token)
            ->get($this->baseUrl . '/astrology/panchang', [
                'datetime' => $datetime,
                'coordinates' => $location,
                'ayanamsa' => 1 // 👈 यह जोड़ना अनिवार्य है
            ])->json();

        // 💡 Debugging के लिए: अगर डेटा नहीं आ रहा तो लॉग में चेक करें
        if (!isset($planets['data']) || !isset($panchang['data'])) {
            \Log::error("Prokerala API Error Response: ", ['planets' => $planets, 'panchang' => $panchang]);
            return null;
        }

        return [
            'planets'  => $planets['data'] ?? [],
            'panchang' => $panchang['data'] ?? [],
            'rashi'    => $planets['data'][0]['rashi'] ?? 'Unknown'
        ];
    }
}
