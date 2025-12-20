<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;

class DashboardController extends Controller
{
    public function index()
    {
        $merchant = auth()->user();

        // Get merchant's order statistics
        $totalOrders = Order::where('merchant_id', $merchant->id)->count();
        $pendingOrders = Order::where('merchant_id', $merchant->id)
            ->whereIn('status', ['pending_approval', 'awaiting_payment'])
            ->count();
        $activeShipments = Shipment::whereHas('order', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })->whereIn('current_status', ['loaded', 'in_transit'])->count();

        // Get recent orders with relationships
        $recentOrders = Order::where('merchant_id', $merchant->id)
            ->with(['route', 'shipments.container'])
            ->latest()
            ->take(5)
            ->get();

        return view('merchant.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'activeShipments',
            'recentOrders'
        ));
    }
}
