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
                                            <div class="text-sm text-gray-900">{{ $order->route->originPort->name }}</div>
                                            <div class="text-xs text-gray-500">→ {{ $order->route->destinationPort->name }}</div>
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
