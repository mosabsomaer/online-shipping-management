@extends('layouts.app')

@section('title', __('orders.create_title'))

@section('content')
<div class="py-8" x-data="orderForm()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('merchant.orders.store') }}" method="POST" id="orderForm">
            @csrf
            <input type="hidden" name="route_id" x-model="route_id">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-[800px]">

                <!-- Left Column: Form Inputs -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Section 1: Route Selection -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            <h3 class="font-semibold text-gray-800">{{ __('orders.route_section') }}</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="origin" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    {{ __('orders.origin') }}
                                </label>
                                <select id="origin" x-model="origin" @change="updateDestinationOptions(); updateRouteId()" required
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block p-2.5">
                                    <option value="">{{ __('orders.select_origin') }}</option>
                                    @foreach($routes->unique('origin_port_id') as $route)
                                        <option value="{{ $route->originPort->localized_name }}">{{ $route->originPort->localized_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="destination" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    {{ __('orders.destination') }}
                                </label>
                                <select id="destination" x-model="destination" @change="updateRouteId()" required
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block p-2.5 disabled:opacity-50"
                                    :disabled="!origin">
                                    <option value="">{{ __('orders.select_destination') }}</option>
                                    <template x-for="dest in getDestinationsForOrigin(origin)" :key="dest">
                                        <option :value="dest" x-text="dest"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Recipient Details -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="font-semibold text-gray-800">{{ __('orders.recipient_info') }}</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="recipient_name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    {{ __('orders.recipient_name') }}
                                </label>
                                <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 @error('recipient_name') border-red-500 @enderror"
                                    placeholder="{{ __('orders.placeholder_name') }}">
                                @error('recipient_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="recipient_phone" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    {{ __('orders.recipient_phone') }}
                                </label>
                                <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 @error('recipient_phone') border-red-500 @enderror"
                                    placeholder="{{ __('orders.placeholder_phone') }}">
                                @error('recipient_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="recipient_address" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                    {{ __('orders.recipient_address') }}
                                </label>
                                <input type="text" name="recipient_address" id="recipient_address" value="{{ old('recipient_address') }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block w-full p-2.5 @error('recipient_address') border-red-500 @enderror"
                                    placeholder="{{ __('orders.placeholder_address') }}">
                                @error('recipient_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Cargo & Containers -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <!-- Container Icon -->
                                <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="2" y="7" width="20" height="12" rx="1" stroke-width="2"/>
                                    <line x1="6" y1="7" x2="6" y2="19" stroke-width="1.5"/>
                                    <line x1="10" y1="7" x2="10" y2="19" stroke-width="1.5"/>
                                    <line x1="14" y1="7" x2="14" y2="19" stroke-width="1.5"/>
                                    <line x1="18" y1="7" x2="18" y2="19" stroke-width="1.5"/>
                                    <path d="M2 7L4 4H20L22 7" stroke-width="2"/>
                                </svg>
                                <h3 class="font-semibold text-gray-800">{{ __('orders.shipments') }}</h3>
                            </div>
                            <button type="button" @click="addShipment()"
                                class="text-xs flex items-center gap-1 bg-primary text-white px-3 py-1.5 rounded-full hover:opacity-90 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('orders.add_shipment') }}
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Empty State -->
                            <template x-if="shipments.length === 0">
                                <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                                    <p class="text-gray-400">{{ __('orders.no_shipments') }}</p>
                                    <button type="button" @click="addShipment()" class="mt-2 text-primary font-medium hover:underline text-sm">
                                        {{ __('orders.add_first_container') }}
                                    </button>
                                </div>
                            </template>

                            <!-- Shipment Cards -->
                            <template x-for="(shipment, index) in shipments" :key="index">
                                <div class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-6 relative group bg-white hover:border-secondary transition-colors">
                                    <!-- Remove Button -->
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="shipments.length > 1">
                                        <button type="button" @click="removeShipment(index)" class="text-gray-400 hover:text-red-500 p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Container Visual -->
                                    <div class="w-full md:w-32 flex flex-col items-center justify-center bg-gray-50 rounded-lg p-2 border border-gray-100">
                                        <div x-html="getContainerIcon(shipment.container_id)" class="w-20 h-20"></div>
                                        <span class="text-xs font-bold text-gray-600 mt-2" x-text="`{{ __('common.currency') }} ${shipment.price.toFixed(2)}`"></span>
                                    </div>

                                    <!-- Inputs -->
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">{{ __('orders.container_type') }}</label>
                                            <select :name="`shipments[${index}][container_id]`"
                                                x-model="shipment.container_id" @change="calculatePrice(index)" required
                                                class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block p-2">
                                                <option value="">{{ __('orders.select_container') }}</option>
                                                @foreach($containers as $container)
                                                    <option value="{{ $container->id }}">{{ $container->localized_name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-[10px] text-gray-500 mt-1" x-text="getContainerDescription(shipment.container_id)"></p>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">{{ __('orders.item_description') }}</label>
                                            <textarea :name="`shipments[${index}][item_description]`"
                                                x-model="shipment.item_description" rows="3" required
                                                class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-secondary focus:border-secondary block p-2"
                                                placeholder="{{ __('orders.placeholder_item') }}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sticky Summary -->
                <div class="lg:col-span-4">
                    <div class="sticky top-6 space-y-4">
                        <!-- Order Summary Card -->
                        <div class="bg-primary text-white rounded-xl shadow-lg p-6">
                            <h2 class="text-xl font-bold mb-6">{{ __('orders.order_summary') }}</h2>

                            <div class="space-y-4 mb-8">
                                <!-- Route Info -->
                                <div class="flex justify-between items-start text-sm opacity-80">
                                    <span>{{ __('orders.route_section') }}</span>
                                    <div class="text-right max-w-[150px]">
                                        <template x-if="origin">
                                            <p x-text="origin"></p>
                                        </template>
                                        <template x-if="!origin">
                                            <p class="opacity-50">{{ __('orders.pending') }}</p>
                                        </template>
                                        <template x-if="destination">
                                            <p class="text-xs mt-1" x-text="'{{ __('orders.to') }} ' + destination"></p>
                                        </template>
                                    </div>
                                </div>

                                <!-- Container Count -->
                                <div class="flex justify-between items-center text-sm opacity-80">
                                    <span>{{ __('orders.containers') }}</span>
                                    <span x-text="shipments.length + ' {{ __('orders.units') }}'"></span>
                                </div>

                                <div class="h-px bg-white/20 my-4"></div>

                                <!-- Shipment Breakdown -->
                                <template x-for="(shipment, index) in shipments" :key="index">
                                    <div class="flex justify-between text-xs opacity-70">
                                        <span x-text="'#' + (index + 1) + ' ' + getContainerName(shipment.container_id)"></span>
                                        <span x-text="`{{ __('common.currency') }} ${shipment.price.toFixed(2)}`"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Total -->
                            <div class="flex justify-between items-end mb-6">
                                <span class="text-sm font-medium opacity-70">{{ __('orders.total_estimated_cost') }}</span>
                                <span class="text-3xl font-bold" x-text="`{{ __('common.currency') }} ${totalPrice.toFixed(2)}`"></span>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-secondary hover:opacity-90 text-white py-3 rounded-lg font-semibold transition-colors flex items-center justify-center gap-2">
                                {{ __('orders.create_order') }}
                                <!-- Ship Icon -->
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 20L4 21L6 20L8 21L10 20L12 21L14 20L16 21L18 20L20 21L22 20"/>
                                    <path d="M4 18V14L12 8L20 14V18"/>
                                    <path d="M12 8V4"/>
                                    <path d="M8 6H12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Info Note -->
                        <div class="bg-secondary/10 border border-secondary/20 rounded-xl p-4 flex items-start gap-3">
                            <svg class="w-5 h-5 text-secondary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                {{ __('orders.customs_note') }}
                            </p>
                        </div>

                        <!-- Cancel Link -->
                        <a href="{{ route('merchant.dashboard') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700 transition">
                            {{ __('common.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function orderForm() {
        return {
            origin: '',
            destination: '',
            route_id: '',
            shipments: [],
            routes: @json($routes),
            containers: @json($containers),

            get totalPrice() {
                return this.shipments.reduce((sum, shipment) => sum + shipment.price, 0);
            },

            addShipment() {
                const defaultContainer = this.containers[0];
                this.shipments.push({
                    container_id: defaultContainer ? defaultContainer.id.toString() : '',
                    item_description: '',
                    price: defaultContainer ? parseFloat(defaultContainer.price) : 0
                });
            },

            removeShipment(index) {
                this.shipments.splice(index, 1);
            },

            getDestinationsForOrigin(origin) {
                if (!origin) return [];
                const filtered = this.routes.filter(route => {
                    const routeOriginName = route.origin_port?.localized_name;
                    return routeOriginName === origin;
                });
                const destinations = filtered
                    .map(route => route.destination_port?.localized_name)
                    .filter(Boolean)
                    .filter((value, index, self) => self.indexOf(value) === index);
                return destinations;
            },

            updateDestinationOptions() {
                this.destination = '';
                this.route_id = '';
            },

            updateRouteId() {
                if (this.origin && this.destination) {
                    const route = this.routes.find(r =>
                        r.origin_port && r.destination_port &&
                        r.origin_port.localized_name === this.origin &&
                        r.destination_port.localized_name === this.destination
                    );
                    if (route) {
                        this.route_id = route.id;
                    }
                }
            },

            calculatePrice(index) {
                const shipment = this.shipments[index];
                let price = 0;

                if (shipment.container_id) {
                    const container = this.containers.find(c => c.id == shipment.container_id);
                    if (container) {
                        price = parseFloat(container.price);
                    }
                }

                shipment.price = price;
            },

            getContainerName(containerId) {
                if (!containerId) return '{{ __("orders.select_container") }}';
                const container = this.containers.find(c => c.id == containerId);
                return container ? container.localized_name : '{{ __("orders.select_container") }}';
            },

            getContainerDescription(containerId) {
                if (!containerId) return '';
                const container = this.containers.find(c => c.id == containerId);
                if (!container) return '';
                return `${container.size}m³, ${Number(container.weight_limit).toLocaleString()}kg`;
            },

            getContainerIcon(containerId) {
                const container = this.containers.find(c => c.id == containerId);
                const type = container?.type || 'standard';
                const length = container?.length || 20;

                const is40 = length === 40;
                const width = is40 ? 220 : 120;
                const height = type === 'high_cube' ? 90 : 80;
                const depth = 30;
                const totalWidth = 280;
                const startX = (totalWidth - width) / 2;
                const startY = 50;

                let baseColor, strokeColor, detailColor;
                if (type === 'refrigerated') {
                    baseColor = '#e0f2fe';
                    strokeColor = '#0ea5e9';
                    detailColor = '#38bdf8';
                } else if (type === 'high_cube') {
                    baseColor = '#334155';
                    strokeColor = '#94a3b8';
                    detailColor = '#64748b';
                } else {
                    baseColor = '#475569';
                    strokeColor = '#94a3b8';
                    detailColor = '#64748b';
                }

                const ribCount = is40 ? 12 : 6;
                let ribs = '';
                for (let i = 0; i < ribCount; i++) {
                    const x = startX + depth + 10 + (i * ((width - 20) / (is40 ? 11 : 5)));
                    ribs += `<rect x="${x}" y="${startY + 2}" width="2" height="${height - 4}" fill="${detailColor}" fill-opacity="0.3"/>`;
                }

                let extras = '';
                if (type === 'refrigerated') {
                    extras = `<circle cx="${startX + depth + width - 15}" cy="${startY + 20}" r="8" fill="white" fill-opacity="0.5"/>
                              <rect x="${startX + depth + width - 25}" y="${startY + 35}" width="20" height="30" rx="2" fill="white" fill-opacity="0.5"/>`;
                } else if (type === 'high_cube') {
                    extras = `<path d="M${startX + depth} ${startY} L${startX + depth + width} ${startY}" stroke="#facc15" stroke-width="4" stroke-dasharray="10 10"/>`;
                }

                return `<svg viewBox="0 0 280 180" class="w-full h-full" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M${startX + depth} ${startY + height} L${startX + width + depth} ${startY + height} L${startX + width} ${startY + height + depth/2} L${startX} ${startY + height + depth/2} Z" fill="black" fill-opacity="0.1"/>
                    <path d="M${startX} ${startY + depth/2} L${startX} ${startY + height + depth/2} L${startX + depth} ${startY + height} L${startX + depth} ${startY}" fill="${baseColor}" style="filter: brightness(0.7)"/>
                    <path d="M${startX} ${startY + depth/2} L${startX + depth} ${startY} L${startX + width + depth} ${startY} L${startX + width} ${startY + depth/2} Z" fill="${baseColor}" style="filter: brightness(1.1)" stroke="${strokeColor}" stroke-width="2"/>
                    <rect x="${startX + depth}" y="${startY}" width="${width}" height="${height}" fill="${baseColor}" stroke="${strokeColor}" stroke-width="2"/>
                    ${ribs}
                    <line x1="${startX + depth + width/2}" y1="${startY}" x2="${startX + depth + width/2}" y2="${startY + height}" stroke="${strokeColor}" stroke-width="2"/>
                    ${extras}
                </svg>`;
            }
        }
    }
</script>
@endpush
@endsection
