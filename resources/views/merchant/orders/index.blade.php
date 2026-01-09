@extends('layouts.app')

@section('title', __('orders.title'))

@section('content')
<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('orders.title') }}</h2>
                    <a href="{{ route('merchant.orders.create') }}" class="inline-flex items-center px-4 py-2 font-semibold text-white bg-primary border border-transparent rounded-md hover:opacity-90">
                        <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('dashboard.create_new_order') }}
                    </a>
                </div>

                @if(session('success'))
                    <div class="px-4 py-3 mb-6 border rounded-md bg-emerald-50 border-emerald-200 text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filters Section -->
                <div class="mb-6 p-5 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg border border-gray-200">
                    <form method="GET" action="{{ route('merchant.orders.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('orders.status') }}
                                </label>
                                <select id="status" name="status" class="w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white hover:border-gray-400 transition">
                                    <option value="">{{ __('dashboard.all') }}</option>
                                    <option value="pending_approval" @selected(request('status') === 'pending_approval')>{{ __('status.pending_approval') }}</option>
                                    <option value="approved" @selected(request('status') === 'approved')>{{ __('status.approved') }}</option>
                                    <option value="rejected" @selected(request('status') === 'rejected')>{{ __('status.rejected') }}</option>
                                    <option value="awaiting_payment" @selected(request('status') === 'awaiting_payment')>{{ __('status.awaiting_payment') }}</option>
                                    <option value="paid" @selected(request('status') === 'paid')>{{ __('status.paid') }}</option>
                                    <option value="processing" @selected(request('status') === 'processing')>{{ __('status.processing') }}</option>
                                    <option value="completed" @selected(request('status') === 'completed')>{{ __('status.completed') }}</option>
                                    <option value="cancelled" @selected(request('status') === 'cancelled')>{{ __('status.cancelled') }}</option>
                                </select>
                            </div>

                            <!-- Sort By -->
                            <div>
                                <label for="sort_by" class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('orders.sort_by') }}
                                </label>
                                <select id="sort_by" name="sort_by" class="w-full px-3 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent bg-white hover:border-gray-400 transition">
                                    <option value="created_at" @selected(request('sort_by') === 'created_at' || !request('sort_by'))>{{ __('orders.created') }}</option>
                                    <option value="status" @selected(request('sort_by') === 'status')>{{ __('orders.status') }}</option>
                                    <option value="total_cost" @selected(request('sort_by') === 'total_cost')>{{ __('orders.total_cost') }}</option>
                                </select>
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('orders.sort_order') }}
                                </label>
                                <div class="flex gap-2">
                                    <label class="flex-1 relative cursor-pointer">
                                        <input type="radio" name="sort_order" value="asc" @checked(request('sort_order') === 'asc') class="peer sr-only">
                                        <span class="block w-full px-3 py-2.5 text-center text-sm font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:border-gray-400 peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary transition">
                                            <svg class="w-4 h-4 inline-block mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 16V4m0 0L3 8m4-4l4 4"></path>
                                            </svg>
                                            <span class="ml-1">{{ __('orders.asc') }}</span>
                                        </span>
                                    </label>
                                    <label class="flex-1 relative cursor-pointer">
                                        <input type="radio" name="sort_order" value="desc" @checked(request('sort_order') !== 'asc') class="peer sr-only">
                                        <span class="block w-full px-3 py-2.5 text-center text-sm font-medium border border-gray-300 rounded-md bg-white text-gray-700 hover:border-gray-400 peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary transition">
                                            <svg class="w-4 h-4 inline-block mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 8v8m0 0l-4-4m4 4l4-4"></path>
                                            </svg>
                                            <span class="ml-1">{{ __('orders.desc') }}</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <button type="submit" class="inline-flex items-center px-5 py-2.5 font-semibold text-white bg-primary border border-transparent rounded-md hover:opacity-90 shadow-sm transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                {{ __('dashboard.apply_filters') }}
                            </button>
                            <a href="{{ route('merchant.orders.index') }}" class="inline-flex items-center px-5 py-2.5 font-semibold text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 shadow-sm transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('dashboard.reset_filters') }}
                            </a>
                        </div>
                    </form>
                </div>

                @if($orders->isEmpty())
                    <div class="py-12 text-center">
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
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.route') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.recipient') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.status') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.total_cost') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.created') }}</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-start text-gray-500 uppercase">{{ __('orders.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $order->route->originPort->localized_name }}</div>
                                            <div class="text-xs text-gray-500">→ {{ $order->route->destinationPort->localized_name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $order->recipient_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->recipient_phone }}</div>
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
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                            {{ __('common.currency') }} {{ number_format($order->total_cost, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                            <a href="{{ route('merchant.orders.show', $order) }}" class="text-secondary hover:text-primary">{{ __('orders.view_details') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
