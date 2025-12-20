<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\Order;
use App\Models\Route;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $merchant = auth()->user();

        $orders = Order::where('merchant_id', $merchant->id)
            ->with(['route', 'shipments.container'])
            ->latest()
            ->paginate(15);

        return view('merchant.orders.index', compact('orders'));
    }

    public function create()
    {
        $routes = Route::where('is_active', true)->with(['originPort', 'destinationPort'])->get();
        $containers = Container::where('is_available', true)->get();

        return view('merchant.orders.create', compact('routes', 'containers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:255',
            'recipient_address' => 'required|string|max:1000',
            'shipments' => 'required|array|min:1',
            'shipments.*.container_id' => 'required|exists:containers,id',
            'shipments.*.item_description' => 'required|string|max:5000',
        ]);

        // Create order with recipient information and route
        $order = Order::create([
            'merchant_id' => auth()->id(),
            'route_id' => $validated['route_id'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'recipient_address' => $validated['recipient_address'],
            'status' => 'pending_approval',
        ]);

        // Create shipments for this order
        foreach ($validated['shipments'] as $shipmentData) {
            $container = Container::findOrFail($shipmentData['container_id']);

            $order->shipments()->create([
                'container_id' => $shipmentData['container_id'],
                'item_description' => $shipmentData['item_description'],
                'container_price' => $container->price,
                'customs_fee' => 0,
                'current_status' => 'pending',
            ]);
        }

        return redirect()
            ->route('merchant.orders.show', $order)
            ->with('success', 'Order created successfully! Tracking number: '.$order->tracking_number);
    }

    public function show(Order $order)
    {
        // Ensure merchant can only view their own orders
        if ($order->merchant_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['route', 'shipments.container', 'shipments.statusHistory', 'payment']);

        return view('merchant.orders.show', compact('order'));
    }
}
