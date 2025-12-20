@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="px-4 py-3 mb-6 border rounded-md bg-emerald-50 border-emerald-200 text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="px-4 py-3 mb-6 text-red-700 border border-red-200 rounded-md bg-red-50">
                {{ session('error') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="px-4 py-3 mb-6 border rounded-md bg-amber-50 border-amber-200 text-amber-700">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('info'))
            <div class="px-4 py-3 mb-6 text-blue-700 border border-blue-200 rounded-md bg-blue-50">
                {{ session('info') }}
            </div>
        @endif

        <!-- Order Header -->
        <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Order Details</h2>
                        <p class="mt-1 text-sm text-gray-500">Created on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        @if($order->status === 'pending_approval') bg-amber-100 text-amber-800
                        @elseif($order->status === 'approved') bg-blue-100 text-blue-800
                        @elseif($order->status === 'rejected') bg-red-100 text-red-800
                        @elseif($order->status === 'awaiting_payment') bg-amber-100 text-amber-800
                        @elseif($order->status === 'paid') bg-emerald-100 text-emerald-800
                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status === 'completed') bg-emerald-100 text-emerald-800
                        @elseif($order->status === 'cancelled') bg-slate-100 text-slate-800
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>

                <div class="pt-4 border-t">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tracking Number</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $order->tracking_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Cost</p>
                            <p class="text-lg font-semibold text-gray-900">LYD {{ number_format($order->total_cost, 2) }}</p>
                        </div>
                    </div>
                </div>

                @if($order->status === 'rejected' && $order->rejection_reason)
                    <div class="p-4 mt-4 border border-red-200 rounded-md bg-red-50">
                        <p class="text-sm font-medium text-red-800">Rejection Reason:</p>
                        <p class="mt-1 text-sm text-red-700">{{ $order->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recipient Information -->
        <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-900">Recipient Information</h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="text-sm text-gray-900">{{ $order->recipient_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone</p>
                        <p class="text-sm text-gray-900">{{ $order->recipient_phone }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Address</p>
                        <p class="text-sm text-gray-900">{{ $order->recipient_address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Route Information -->
        @if($order->route)
            <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Route Information</h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Route</p>
                            <p class="text-sm text-gray-900">{{ $order->route->originPort->name }} → {{ $order->route->destinationPort->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Schedule</p>
                            <p class="text-sm text-gray-900">{{ ucfirst($order->route->schedule) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Transit Time</p>
                            <p class="text-sm text-gray-900">{{ $order->route->duration_days }} days</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Shipments -->
        @foreach($order->shipments as $shipmentIndex => $shipment)
            <div class="mt-6 overflow-hidden bg-white border-2 border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-4 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Shipment #{{ $shipmentIndex + 1 }}
                    </h3>
                </div>

                <div class="p-6">
                    <!-- Shipping Information -->
                    <div class="mb-6">
                        <h4 class="mb-4 text-base font-semibold text-gray-900">Shipping Information</h4>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @if($shipment->container)
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Container Type</p>
                                    <p class="text-sm text-gray-900">{{ $shipment->container->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $shipment->container->size }} m³ • Max {{ number_format($shipment->container->weight_limit, 0) }} kg</p>
                                </div>
                            @endif

                            <div>
                                <p class="text-sm font-medium text-gray-500">Item Description</p>
                                <p class="text-sm text-gray-900">{{ $shipment->item_description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipment Price Breakdown -->
                    <div class="pt-4 mt-6 border-t border-gray-200">
                        <h4 class="mb-3 text-base font-semibold text-gray-900">Price Breakdown</h4>

                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Container Price:</span>
                                <span class="text-gray-900">LYD {{ number_format($shipment->container_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Customs Fee:</span>
                                @if($order->status === 'pending_approval')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Waiting for approval
                                    </span>
                                @else
                                    <span class="text-gray-900">LYD {{ number_format($shipment->customs_fee, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex justify-between pt-2 text-base font-semibold border-t">
                                <span class="text-gray-900">Shipment Total:</span>
                                <span class="text-gray-900">LYD {{ number_format($shipment->total_cost, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipment Tracking -->
                    @if($shipment->current_status)
                        <div class="pt-4 mt-6 border-t border-gray-200">
                            <h4 class="mb-3 text-base font-semibold text-gray-900">Shipment Status</h4>

                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Current Status</p>
                                <span class="mt-1 px-3 py-1 inline-flex text-sm font-semibold rounded-full
                                    @if($shipment->current_status === 'pending') bg-gray-100 text-gray-800
                                    @elseif($shipment->current_status === 'loaded') bg-blue-100 text-blue-800
                                    @elseif($shipment->current_status === 'in_transit') bg-blue-100 text-blue-800
                                    @elseif($shipment->current_status === 'arrived') bg-blue-100 text-blue-800
                                    @elseif($shipment->current_status === 'delivered') bg-emerald-100 text-emerald-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $shipment->current_status)) }}
                                </span>
                            </div>

                            @if($shipment->statusHistory && $shipment->statusHistory->isNotEmpty())
                                <div class="mt-4">
                                    <p class="mb-3 text-sm font-medium text-gray-900">Status History</p>
                                    <div class="flow-root">
                                        <ul class="-mb-8">
                                            @foreach($shipment->statusHistory as $historyIndex => $history)
                                                <li>
                                                    <div class="relative pb-8">
                                                        @if($historyIndex < count($shipment->statusHistory) - 1)
                                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                        @endif
                                                        <div class="relative flex space-x-3">
                                                            <div>
                                                                <span class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-full ring-8 ring-white">
                                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <div class="min-w-0 flex-1 pt-1.5">
                                                                <div>
                                                                    <p class="text-sm font-medium text-gray-900">
                                                                        {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-500">
                                                                        {{ $history->created_at->format('M d, Y \a\t h:i A') }}
                                                                    </p>
                                                                    @if($history->notes)
                                                                        <p class="mt-1 text-sm text-gray-700">{{ $history->notes }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Payment Information -->
        @if($order->payment)
            <div class="mt-6 overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Payment Information</h3>

                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="text-gray-900">{{ ucfirst($order->payment->payment_method) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Amount:</span>
                            <span class="text-gray-900">LYD {{ number_format($order->payment->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Transaction Reference:</span>
                            <span class="text-gray-900">{{ $order->payment->transaction_ref }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($order->payment->status === 'completed') bg-emerald-100 text-emerald-800
                                @elseif($order->payment->status === 'pending') bg-amber-100 text-amber-800
                                @elseif($order->payment->status === 'failed') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        </div>
                        @if($order->payment->paid_at)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Paid At:</span>
                                <span class="text-gray-900">{{ $order->payment->paid_at->format('M d, Y \a\t h:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('merchant.orders.index') }}" class="inline-flex items-center px-4 py-2 font-semibold text-white border border-transparent rounded-md bg-slate-700 hover:bg-slate-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Orders
            </a>

            <div class="flex space-x-3">
                @if($order->status === 'awaiting_payment')
                    <form action="{{ route('payment.initiate', $order) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-8 py-4 text-lg font-bold text-white transition transform bg-blue-600 border-2 border-blue-600 rounded-lg shadow-xl hover:bg-blue-700 hover:border-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 hover:scale-105">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span>Pay Now - LYD {{ number_format($order->total_cost, 2) }}</span>
                        </button>
                    </form>
                @endif

                @if($order->status === 'paid' || $order->status === 'completed')
                    <button class="inline-flex items-center px-4 py-2 font-semibold text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Download Receipt
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
