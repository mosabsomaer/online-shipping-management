@extends('layouts.app')

@section('title', 'Track Shipment')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900">Track Your Shipment</h2>
                    <p class="mt-2 text-gray-600">Enter your tracking number to get real-time updates on your shipment</p>
                </div>

                <form action="{{ route('merchant.tracking.lookup') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Tracking Number <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-4">
                            <input
                                type="text"
                                name="tracking_number"
                                id="tracking_number"
                                value="{{ old('tracking_number') }}"
                                required
                                placeholder="Enter your tracking number (e.g., TRK-2025-12345)"
                                class="flex-1 block px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('tracking_number') border-red-500 @enderror"
                            />
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Search
                            </button>
                        </div>
                        @error('tracking_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </form>

                <!-- Help section -->
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">How to Track Your Shipment</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-600 text-white text-sm font-semibold">
                                        1
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Find Your Tracking Number</h4>
                                    <p class="mt-1 text-sm text-gray-600">Your tracking number was provided in your order confirmation email or is visible on your dashboard</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-600 text-white text-sm font-semibold">
                                        2
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Enter & Search</h4>
                                    <p class="mt-1 text-sm text-gray-600">Paste your tracking number in the search field and click the Search button</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-600 text-white text-sm font-semibold">
                                        3
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">View Real-Time Updates</h4>
                                    <p class="mt-1 text-sm text-gray-600">See detailed information about your shipment's current status and location</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-600 text-white text-sm font-semibold">
                                        4
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Track Anytime</h4>
                                    <p class="mt-1 text-sm text-gray-600">Check your shipment status multiple times throughout delivery. Data updates every hour</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent orders section -->
                @auth
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Recent Orders</h3>
                        @if($recentOrders = auth()->user()->orders()->latest()->limit(5)->get())
                            <div class="space-y-2">
                                @forelse($recentOrders as $order)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $order->tracking_number }}</p>
                                            <p class="text-xs text-gray-600">{{ $order->created_at->format('M d, Y - g:i A') }}</p>
                                        </div>
                                        <form action="{{ route('merchant.tracking.lookup') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="tracking_number" value="{{ $order->tracking_number }}">
                                            <button type="submit" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                                Track
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-600">No recent orders found</p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
