@extends('layouts.app')

@section('title', __('dashboard.title'))

@section('content')
<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
            <!-- Total Orders -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-primary rounded-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">{{ __('dashboard.total_orders') }}</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalOrders }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 rounded-md bg-amber-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">{{ __('dashboard.pending_orders') }}</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $pendingOrders }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Shipments -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 rounded-md bg-emerald-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">{{ __('dashboard.active_shipments') }}</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $activeShipments }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8 overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">{{ __('dashboard.quick_actions') }}</h3>
                <div class="flex gap-4">
                    <a href="{{ route('merchant.orders.create') }}" class="inline-flex items-center px-4 py-2 font-semibold text-white bg-primary border border-transparent rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('dashboard.create_new_order') }}
                    </a>
                    <a href="{{ route('merchant.orders.index') }}" class="inline-flex items-center px-4 py-2 font-semibold text-white border border-transparent rounded-md bg-secondary hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary">
                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('dashboard.view_all_orders') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-medium text-gray-900">{{ __('dashboard.recent_orders') }}</h3>
                
                @if($recentOrders->isEmpty())
                    <div class="py-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('dashboard.no_orders') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('dashboard.get_started') }}</p>
                        <div class="mt-6">
                            <a href="{{ route('merchant.orders.create') }}" class="inline-flex items-center px-4 py-2 font-semibold text-white bg-primary border border-transparent rounded-md hover:opacity-90">
                                {{ __('orders.create_order') }}
                            </a>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.tracking_number') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.route') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.container') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.status') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.total_cost') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.created') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ $order->tracking_number }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $order->route->originPort->name }} → {{ $order->route->destinationPort->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ __('orders.shipments_count', ['count' => $order->shipments->count()]) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($order->status === 'pending_approval') bg-amber-100 text-amber-800
                                                @elseif($order->status === 'approved') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'rejected') bg-red-100 text-red-800
                                                @elseif($order->status === 'awaiting_payment') bg-amber-100 text-amber-800
                                                @elseif($order->status === 'paid') bg-emerald-100 text-emerald-800
                                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'completed') bg-emerald-100 text-emerald-800
                                                @elseif($order->status === 'cancelled') bg-slate-100 text-slate-800
                                                @endif">
                                                {{ __('status.' . $order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                            {{ __('common.currency') }} {{ number_format($order->total_cost, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                            <a href="{{ route('merchant.orders.show', $order) }}" class="text-secondary hover:text-primary">{{ __('orders.view') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
