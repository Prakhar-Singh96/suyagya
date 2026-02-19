<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\BigShipService;

class LogisticController extends Controller
{
    protected $bigShip;

    public function __construct(BigShipService $bigShip)
    {
        $this->bigShip = $bigShip;
    }

    // 1. Dashboard (Orders Ready to Ship)
    public function index()
    {
        // Sirf 'processing' wale orders dikhayenge jo ship karne hain
        $orders = Order::where('status', 'processing')->latest()->paginate(10);
        return view('admin.logistic.index', compact('orders'));
    }

    // 2. Show Weight Form
    public function prepareShipment($id)
    {
        $order = Order::findOrFail($id);
        $warehouses = $this->bigShip->getWarehouses();
        return view('admin.logistic.ship_form', compact('order', 'warehouses'));
    }

    // 3. Create Order & Fetch Rates
    public function createAndFetchRates(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'warehouse_id' => 'required',
            'weight' => 'required|numeric|min:0.1',
            'length' => 'required|numeric',
            'width'  => 'required|numeric',
            'height' => 'required|numeric',
        ]);

        // A. Create Order on BigShip (Agar pehle nahi bana)
        if (!$order->system_order_id) {
            $dimensions = [
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height
            ];

            $createResponse = $this->bigShip->addSingleOrder($order, $request->warehouse_id, $request->weight, $dimensions);

            if (!isset($createResponse['success']) || !$createResponse['success']) {
                return back()->with('error', 'Order Creation Failed: ' . ($createResponse['message'] ?? 'Unknown Error'));
            }

            // System ID Extract
            preg_match('/\d+/', $createResponse['data'], $matches);
            $systemOrderId = $matches[0] ?? null;

            if ($systemOrderId) {
                 $order->system_order_id = $systemOrderId;
                 $order->save();
            }
        }

        // B. Fetch Rates
        $rates = $this->bigShip->getShippingRates($order->system_order_id);

        if (count($rates) == 0) {
            return back()->with('error', 'Order Created but No Courier Rates Found.');
        }

        return view('admin.logistic.select_courier', compact('order', 'rates'));
    }

    // 4. Manifest (Final Ship)
    public function manifest(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $response = $this->bigShip->manifestOrder($order->system_order_id, $request->courier_id);

        if (isset($response['success']) && $response['success']) {

            // Get AWB
            $awbData = $this->bigShip->getShipmentData($order->system_order_id, 1); // 1 = AWB

            if (isset($awbData['data']['master_awb'])) {
                $order->update([
                    'status' => 'shipped',
                    'awb_number' => $awbData['data']['master_awb'],
                    'courier_name' => $awbData['data']['courier_name'] ?? 'BigShip',
                ]);
                return redirect()->route('admin.logistic.index')->with('success', 'Order Manifested & Shipped Successfully!');
            }
        }

        return back()->with('error', 'Manifest Failed: ' . ($response['message'] ?? 'Unknown Error'));
    }

    // 5. Cancel Shipment
    public function cancelShipment($id)
    {
        $order = Order::findOrFail($id);

        if ($order->awb_number) {
            $this->bigShip->cancelOrder($order->awb_number);
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Shipment Cancelled on BigShip & System.');
    }

    // 6. Download Label
    public function downloadLabel($id)
    {
        $order = Order::findOrFail($id);
        if (!$order->system_order_id) return back()->with('error', 'System Order ID missing.');

        // 2 = Label
        $response = $this->bigShip->getShipmentData($order->system_order_id, 2);

        if (isset($response['data']['res_FileContent'])) {
            $pdfContent = base64_decode($response['data']['res_FileContent']);
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="Label_'.$order->awb_number.'.pdf"');
        }

        return back()->with('error', 'Label generation failed.');
    }
}
