@extends('layouts.app')

@section('title', 'Create New Order')

@section('content')
<div class="py-12" x-data="orderForm()">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h2 class="mb-6 text-2xl font-bold text-gray-900">Create New Order</h2>

                <form action="{{ route('merchant.orders.store') }}" method="POST" id="orderForm">
                    @csrf

                    <!-- Recipient Information -->
                    <div class="mb-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Recipient Information</h3>

                        <div class="mb-4">
                            <label for="recipient_name" class="block mb-2 text-sm font-medium text-gray-700">
                                Recipient Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('recipient_name') border-red-500 @enderror"
                                placeholder="Full name of the recipient">
                            @error('recipient_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="recipient_phone" class="block mb-2 text-sm font-medium text-gray-700">
                                Recipient Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('recipient_phone') border-red-500 @enderror"
                                placeholder="+218 XX XXX XXXX">
                            @error('recipient_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="recipient_address" class="block mb-2 text-sm font-medium text-gray-700">
                                Recipient Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="recipient_address" id="recipient_address" rows="3" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('recipient_address') border-red-500 @enderror"
                                placeholder="Full delivery address including street, city, and postal code">{{ old('recipient_address') }}</textarea>
                            @error('recipient_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Route Selection -->
                    <div class="mb-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Route</h3>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="origin" class="block mb-2 text-sm font-medium text-gray-700">
                                    Origin <span class="text-red-500">*</span>
                                </label>
                                <select id="origin" x-model="origin" @change="updateDestinationOptions(); updateRouteId()" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select origin</option>
                                    @foreach($routes->unique('origin_port_id') as $route)
                                        <option value="{{ $route->originPort->name }}">{{ $route->originPort->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="destination" class="block mb-2 text-sm font-medium text-gray-700">
                                    Destination <span class="text-red-500">*</span>
                                </label>
                                <select id="destination" x-model="destination" @change="updateRouteId()" required
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select destination</option>
                                    <template x-for="dest in getDestinationsForOrigin(origin)" :key="dest">
                                        <option :value="dest" x-text="dest"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="route_id" x-model="route_id">
                    </div>

                    <!-- Shipments Section -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Shipments</h3>
                            <button type="button" @click="addShipment()"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Add Shipment
                            </button>
                        </div>

                        <template x-for="(shipment, index) in shipments" :key="index">
                            <div class="p-6 mb-6 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-900 text-md" x-text="`Shipment ${index + 1}`"></h4>
                                    <button type="button" x-show="shipments.length > 1" @click="removeShipment(index)"
                                        class="text-sm font-medium text-red-600 hover:text-red-800">
                                        Remove
                                    </button>
                                </div>

                                <!-- Container Selection -->
                                <div class="mb-4">
                                    <label :for="`shipments[${index}][container_id]`" class="block mb-2 text-sm font-medium text-gray-700">
                                        Container Type <span class="text-red-500">*</span>
                                    </label>
                                    <select :name="`shipments[${index}][container_id]`" :id="`shipments[${index}][container_id]`"
                                        x-model="shipment.container_id" @change="calculatePrice(index)" required
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select a container</option>
                                        @foreach($containers as $container)
                                            <option value="{{ $container->id }}" data-price="{{ $container->price }}">
                                                {{ $container->name }} ({{ $container->size }}m³, {{ number_format($container->weight_limit, 0) }}kg, LYD {{ number_format($container->price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Item Description -->
                                <div class="mb-4">
                                    <label :for="`shipments[${index}][item_description]`" class="block mb-2 text-sm font-medium text-gray-700">
                                        Item Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea :name="`shipments[${index}][item_description]`" :id="`shipments[${index}][item_description]`"
                                        x-model="shipment.item_description" rows="3" required
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Describe what you are shipping in this container"></textarea>
                                </div>

                                <!-- Shipment Price Display -->
                                <div class="p-3 bg-white border border-gray-200 rounded-md">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-700">Container Price:</span>
                                        <span class="font-medium" x-text="`LYD ${shipment.price.toFixed(2)}`"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total Price Summary -->
                    <div class="p-4 mb-6 border border-blue-200 rounded-md bg-blue-50">
                        <h3 class="mb-2 text-sm font-medium text-gray-900">Order Summary</h3>
                        <div class="space-y-1 text-sm text-gray-700">
                            <div class="flex justify-between">
                                <span>Number of Shipments:</span>
                                <span class="font-medium" x-text="shipments.length"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-blue-300">
                                <span class="font-semibold">Total Estimated Cost:</span>
                                <span class="font-bold text-blue-600" x-text="`LYD ${totalPrice.toFixed(2)}`"></span>
                            </div>
                            <p class="mt-2 text-xs text-gray-600">Note: Admin may add customs fees during approval</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('merchant.dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 font-semibold text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function orderForm() {
        return {
            origin: '',
            destination: '',
            route_id: '',
            shipments: [
                {
                    container_id: '',
                    item_description: '',
                    price: 0
                }
            ],
            routes: @json($routes),
            containers: @json($containers),

            get totalPrice() {
                return this.shipments.reduce((sum, shipment) => sum + shipment.price, 0);
            },

            addShipment() {
                this.shipments.push({
                    container_id: '',
                    item_description: '',
                    price: 0
                });
            },

            removeShipment(index) {
                this.shipments.splice(index, 1);
            },

            getDestinationsForOrigin(origin) {
                if (!origin) return [];
                const filtered = this.routes.filter(route => {
                    const routeOriginName = route.originPort?.name;
                    const matches = routeOriginName === origin;
                    return matches;
                });
                const destinations = filtered
                    .map(route => route.destinationPort?.name)
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
                        r.originPort && r.destinationPort &&
                        r.originPort.name === this.origin &&
                        r.destinationPort.name === this.destination
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
            }
        }
    }
</script>
@endpush
@endsection
