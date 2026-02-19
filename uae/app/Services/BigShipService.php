<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BigShipService
{
    protected $baseUrl = 'https://api.bigship.in/';
    protected $email = 'tptcworld@gmail.com'; // .env file se lein
    protected $password = 'PassWord@4321'; // .env file se lein
    protected $access_key = '6e5c54156c2a2b495608f10d8d99ddc76cd0566ac91135608f3b11e0685705be';

    // 1. Login & Get Token
    public function getToken()
    {
        // Cache for 11 hours (Kyuki token 12 hours valid rehta hai)
        return Cache::remember('bigship_token', 39600, function () {

            // 🔥 1. Payload update (user_name instead of email)
            $response = Http::post("{$this->baseUrl}api/login/user", [
                'user_name'  => $this->email, // Doc ke hisab se 'user_name'
                'password'   => $this->password,
                'access_key' => $this->access_key
            ]);

            $data = $response->json();

            // Debugging: Agar ab bhi error aaye to response dekhein
            if (!$response->successful() || !isset($data['data']['token'])) {
                // dd('BigShip Login Error:', $data); // Uncomment for debugging
                return null;
            }

            // 🔥 2. Token ab 'data' key ke andar hai
            return $data['data']['token'];
        });
    }

    // 2. Check Rates & Delivery Time (Updated for api/calculator)
    // 2. Check Rates & Delivery Time
    public function checkServiceability($pickupPin, $destPin, $weight, $price)
    {
        $token = $this->getToken();

        if (!$token) {
            return ['status' => false, 'message' => 'Authentication Failed'];
        }

        // 🔥 Payload (Data Types ko Cast kiya hai taaki API reject na kare)
        $payload = [
            "shipment_category" => "B2C",
            "payment_type"      => "COD",
            "pickup_pincode"    => (int) $pickupPin,      // Integer zaroori hai
            "destination_pincode" => (int) $destPin,      // Integer zaroori hai
            "shipment_invoice_amount" => (float) $price,  // Float/Decimal zaroori hai
            "risk_type"         => "",
            "box_details"       => [
                [
                    "each_box_dead_weight" => (float) $weight,
                    "each_box_length" => 10,
                    "each_box_width"  => 10,
                    "each_box_height" => 10,
                    "box_count"       => 1
                ]
            ]
        ];

        // 🔥 FIX URL: 'api/calculator' sahi endpoint hai
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json'
        ])->post("{$this->baseUrl}api/calculator", $payload);

        if ($response->successful()) {
            $data = $response->json();

            // Check if data exists and is not empty
            if (isset($data['data']) && is_array($data['data']) && count($data['data']) > 0) {

                // Couriers list ko collect karein
                $couriers = collect($data['data']);

                // 🔥 Sort by TAT (Time) - Jiska TAT sabse kam ho (Fastest)
                $fastest = $couriers->sortBy('tat')->first();

                if ($fastest) {
                    return [
                        'status' => true,
                        'days'   => $fastest['tat'], // Return Days (e.g., 3)
                        'courier' => $fastest['courier_name'],
                        'price'  => $fastest['total_shipping_charges'],
                        'message' => 'Available'
                    ];
                }
            }
        }

        // Error checking (Optional: Log response to see why it failed)
        // \Log::error('BigShip Error: ' . $response->body());

        return ['status' => false, 'message' => 'Service not available.'];
    }

    // 3. Track Shipment Function (Updated as per BigShip Docs)
    public function trackShipment($awbNumber)
    {
        $token = $this->getToken();

        if (!$token) {
            return ['status' => false, 'message' => 'Authentication Failed'];
        }

        // 🔥 API Call: /api/tracking
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json'
        ])->get("{$this->baseUrl}api/tracking", [
            'tracking_type' => 'awb',      // Hum AWB use kar rahe hain
            'tracking_id'   => $awbNumber  // User ka input
        ]);

        if ($response->successful()) {
            $result = $response->json();

            // Check if API returned success: true
            if (isset($result['success']) && $result['success'] == true) {
                return [
                    'status' => true,
                    'data' => $result['data'] // Isme 'order_detail' aur 'scan_histories' dono hain
                ];
            }
        }

        return ['status' => false, 'message' => 'No tracking history found for this AWB.'];
    }

    // ================= NEW FUNCTIONS FOR LOGISTIC MODULE =================

    // 4. Get Warehouse List
    public function getWarehouses()
    {
        $token = $this->getToken();
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                        ->get("{$this->baseUrl}api/warehouse/get/list?page_index=1&page_size=50");
        return $response->json()['data']['result_data'] ?? [];
    }

    // 5. Add Single Order (Create Order)
    // 5. Add Single Order (Fixed Product, Name & Document Logic)
    public function addSingleOrder($order, $warehouseId, $weight, $dimensions)
    {
        $token = $this->getToken();

        // 1. Address Parsing & Cleaning
        $addr = is_array($order->shipping_address) ? $order->shipping_address : json_decode($order->shipping_address, true);

        // Name Validation
        $fullName = trim($addr['name']);
        $parts = explode(' ', $fullName, 2);
        $firstName = $parts[0];
        $lastName = isset($parts[1]) ? $parts[1] : 'Customer';
        if (strlen($lastName) < 3) {
            $lastName = $lastName . ' Sr.';
        }

        // Phone Validation (Only 10 digits)
        $phone = preg_replace('/[^0-9]/', '', $addr['phone']);
        if(strlen($phone) > 10) {
            $phone = substr($phone, -10);
        }

        // Address Length Validation
        $addressLine = substr($addr['address_line1'], 0, 80);
        if(strlen($addressLine) < 5) {
            $addressLine .= " " . ($addr['address_line2'] ?? 'Landmark');
        }

        $isCOD = $order->payment_method == 'COD';

        // ✅ Target Total Amount (Discount ke baad wala price, e.g., 900)
        $totalValue = round((float) $order->total_amount, 2);

        // 2. Items Calculation Logic (To match Total Value)
        $productList = [];

        if($order->relationLoaded('items')){
             $items = $order->items;
        } else {
             $items = $order->items()->get();
        }

        // Step A: Calculate Original Sum of Items (without discount)
        $originalSum = 0;
        foreach ($items as $item) {
            $originalSum += ($item->price * $item->quantity);
        }

        // Step B: Loop items and adjust price based on discount ratio
        $currentSum = 0;
        $itemCount = count($items);
        $counter = 0;

        foreach ($items as $item) {
            $counter++;

            // Calculate item's original share
            $itemOriginalTotal = $item->price * $item->quantity;

            if ($originalSum > 0) {
                // Agar last item hai, to bacha hua amount isme daal do (Rounding fix ke liye)
                if ($counter == $itemCount) {
                    $itemNewTotal = $totalValue - $currentSum;
                } else {
                    // Ratio ke hisab se price kam karo
                    $share = $itemOriginalTotal / $originalSum;
                    $itemNewTotal = round($totalValue * $share, 2);
                }
            } else {
                $itemNewTotal = 0;
            }

            // Running Sum update karo
            $currentSum += $itemNewTotal;

            // Per unit price calculate karo
            $unitPrice = ($item->quantity > 0) ? round($itemNewTotal / $item->quantity, 2) : 0;

            $productList[] = [
                "product_category" => "Others",
                "product_sub_category" => "General",
                "product_name" => substr(preg_replace('/[^A-Za-z0-9 ]/', '', $item->product_name ?? 'Item'), 0, 35),
                "product_quantity" => (int) $item->quantity,
                // 🔥 Ye ab Discounted Price hoga (e.g., 900)
                "each_product_invoice_amount" => $unitPrice,
                "each_product_collectable_amount" => $isCOD ? $unitPrice : 0,
                "hsn" => "999999"
            ];
        }

        // 3. Payload Construct
        $payload = [
            "shipment_category" => "b2c",
            "warehouse_detail" => [
                "pickup_location_id" => (int)$warehouseId,
                "return_location_id" => (int)$warehouseId
            ],
            "consignee_detail" => [
                "first_name" => $firstName,
                "last_name" => $lastName,
                "company_name" => "Personal",
                "contact_number_primary" => (string)$phone,
                "email_id" => $order->user->email ?? "support@suyagya.com",
                "consignee_address" => [
                    "address_line1" => $addressLine,
                    "address_line2" => "",
                    "pincode" => (string)$addr['pincode']
                ]
            ],
            "order_detail" => [
                "invoice_date" => now()->format('Y-m-d\TH:i:s.000\Z'),
                "invoice_id" => (string)$order->order_number,
                "payment_type" => $isCOD ? "COD" : "Prepaid",
                // Box Invoice Amount must match Sum of Products
                "shipment_invoice_amount" => $totalValue,
                "total_collectable_amount" => $isCOD ? $totalValue : 0,
                "box_details" => [
                    [
                        "each_box_dead_weight" => (float)$weight,
                        "each_box_length" => (int)$dimensions['length'],
                        "each_box_width" => (int)$dimensions['width'],
                        "each_box_height" => (int)$dimensions['height'],
                        "each_box_invoice_amount" => $totalValue, // 900
                        "each_box_collectable_amount" => $isCOD ? $totalValue : 0,
                        "box_count" => 1,
                        "product_details" => $productList // Sum of these is now guaranteed to be 900
                    ]
                ],
                "ewaybill_number" => "",
                "document_detail" => [
                    "invoice_document_file" => "",
                    "ewaybill_document_file" => ""
                ]
            ]
        ];

        // API Call
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json'])
                        ->post("{$this->baseUrl}api/order/add/single", $payload);

        // Error Logging
        if (!$response->successful()) {
             \Log::error('BigShip API Fail:', ['response' => $response->json(), 'payload' => $payload]);
        }

        return $response->json();
    }

    // 6. Get Shipping Rates (Courier List Fetch)
    public function getShippingRates($systemOrderId)
    {
        $token = $this->getToken();
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                        ->get("{$this->baseUrl}api/order/shipping/rates", [
                            'shipment_category' => 'B2C',
                            'system_order_id'   => $systemOrderId
                        ]);

        return $response->json()['data'] ?? [];
    }

    // 7. Manifest Order (Final Ship)
    public function manifestOrder($systemOrderId, $courierId)
    {
        $token = $this->getToken();
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json'])
                        ->post("{$this->baseUrl}api/order/manifest/single", [
                            "system_order_id" => (int)$systemOrderId,
                            "courier_id" => (int)$courierId
                        ]);
        return $response->json();
    }

    // 8. Get Shipment Data (1=AWB, 2=Label)
    public function getShipmentData($systemOrderId, $type = 1)
    {
        $token = $this->getToken();
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                        ->post("{$this->baseUrl}api/shipment/data?shipment_data_id={$type}&system_order_id={$systemOrderId}");
        return $response->json();
    }

    // 9. Cancel Order
    public function cancelOrder($awbNumber)
    {
        $token = $this->getToken();
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json'])
                        ->put("{$this->baseUrl}api/order/cancel", [(string)$awbNumber]);
        return $response->json();
    }
}
