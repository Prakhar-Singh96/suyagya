<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BigShipService;

class TrackingController extends Controller
{
    protected $bigShip;

    public function __construct(BigShipService $bigShip)
    {
        $this->bigShip = $bigShip;
    }

    public function index()
    {
        return view('frontend.pages.track_order');
    }

    public function track(Request $request)
    {
        $request->validate([
            'awb_number' => 'required|string'
        ]);

        $trackingInfo = $this->bigShip->trackShipment($request->awb_number);

        if ($trackingInfo['status']) {
            // Data successfully mil gaya
            return view('frontend.pages.track_order', [
                'trackingData' => $trackingInfo['data'], // Poora data bhej rahe hain
                'awb' => $request->awb_number
            ]);
        } else {
            // Error aaya ya data nahi mila
            return back()->with('error', $trackingInfo['message']);
        }
    }
}
