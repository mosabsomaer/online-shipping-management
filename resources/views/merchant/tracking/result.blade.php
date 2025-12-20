@extends('layouts.app')

@section('title', 'Tracking Result - ' . $trackingNumber)

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Back button -->
        <div class="mb-6">
            <a href="{{ route('merchant.tracking.search') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Search
            </a>
        </div>

        <!-- Tracking Number Display -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tracking Number</p>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $trackingNumber }}</h2>
                </div>
                @if($order)
                    <div class="text-right">
                        <p class="text-sm text-gray-600 mb-1">Order Status</p>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                            @if($order->status === 'completed') bg-green-100 text-green-800
                            @elseif($order->status === 'cancelled' || $order->status === 'rejected') bg-red-100 text-red-800
                            @elseif($order->status === 'paid' || $order->status === 'processing') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif
                        ">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Information (if found) -->
        @if($order)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Recipient Name</p>
                        <p class="font-medium text-gray-900">{{ $order->recipient_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Recipient Phone</p>
                        <p class="font-medium text-gray-900">{{ $order->recipient_phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Route</p>
                        <p class="font-medium text-gray-900">
                            {{ $order->route->originPort->name }} → {{ $order->route->destinationPort->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Order Date</p>
                        <p class="font-medium text-gray-900">{{ $order->created_at->format('M d, Y - g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Delivery Address</p>
                        <p class="font-medium text-gray-900">{{ $order->recipient_address }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Cost</p>
                        <p class="font-medium text-gray-900">LYD {{ number_format($order->total_cost, 2) }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-medium text-yellow-900">Order Not Found</h3>
                        <p class="text-sm text-yellow-700 mt-1">This tracking number is not associated with any order in our system. It may be a tracking number from an external carrier.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tracking Data Section -->
        @if($syncResult)
            @if($syncResult['success'])
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Tracking Timeline</h3>
                        <div class="text-sm text-gray-600">
                            @if($syncResult['cached'] ?? false)
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                    Last Updated: {{ $syncResult['synced_at']->diffForHumans() }}
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    Just Now
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline display -->
                    @if(isset($trackingData['events']) && !empty($trackingData['events']))
                        <div class="space-y-4">
                            @foreach($trackingData['events'] as $event)
                                <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-b-0">
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 bg-blue-600 rounded-full border-4 border-blue-100"></div>
                                        <div class="w-0.5 h-12 bg-gray-200 my-2"></div>
                                    </div>
                                    <div class="flex-1 pb-4">
                                        <p class="font-semibold text-gray-900">
                                            {{ $event['status'] ?? 'Unknown Status' }}
                                        </p>
                                        @if(isset($event['location']))
                                            <p class="text-sm text-gray-600 mt-1">
                                                📍 {{ $event['location'] }}
                                            </p>
                                        @endif
                                        @if(isset($event['timestamp']))
                                            <p class="text-xs text-gray-500 mt-2">
                                                {{ $event['timestamp'] }}
                                            </p>
                                        @endif
                                        @if(isset($event['description']))
                                            <p class="text-sm text-gray-700 mt-2">
                                                {{ $event['description'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-900">No tracking events available yet. Your shipment may be in preparation for dispatch.</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-red-900">Unable to Fetch Tracking Data</h3>
                            <p class="text-sm text-red-700 mt-1">{{ $syncResult['error'] ?? 'An error occurred while fetching tracking information.' }}</p>
                            @if($order)
                                <p class="text-sm text-red-600 mt-2">Please try again later or contact support if the problem persists.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <p class="text-center text-gray-600">No tracking information available for this shipment yet.</p>
            </div>
        @endif

        <!-- New Search Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('merchant.tracking.search') }}" class="inline-block px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                Search Another Tracking Number
            </a>
        </div>
    </div>
</div>
@endsection
