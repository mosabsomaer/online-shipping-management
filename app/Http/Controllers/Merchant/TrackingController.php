<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Terminal49Service;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct(private Terminal49Service $terminal49Service) {}

    public function search()
    {
        return view('merchant.tracking.search');
    }

    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
        ]);

        $trackingNumber = $validated['tracking_number'];

        // First, try to find the order in our system
        $order = Order::where('tracking_number', $trackingNumber)
            ->with(['merchant', 'shipments', 'route.originPort', 'route.destinationPort'])
            ->first();

        // If order found, get the first shipment to fetch tracking data
        $trackingData = null;
        $syncResult = null;

        if ($order && $order->shipments()->exists()) {
            $shipment = $order->shipments()->first();
            $syncResult = $this->terminal49Service->syncShipment($shipment);

            if ($syncResult['success']) {
                $trackingData = $syncResult['data'];
            }
        }

        return view('merchant.tracking.result', [
            'trackingNumber' => $trackingNumber,
            'order' => $order,
            'trackingData' => $trackingData,
            'syncResult' => $syncResult,
        ]);
    }
}
