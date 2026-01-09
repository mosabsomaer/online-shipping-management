@php
    use App\Helpers\ArabicHelper;
    $isArabic = app()->getLocale() === 'ar';

    // Helper function to reshape Arabic text
    $ar = fn($text) => $isArabic ? ArabicHelper::reshape($text) : $text;
@endphp
<!DOCTYPE html>
<html dir="{{ $isArabic ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('orders.download_receipt') }} - {{ $order->tracking_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        @page {
            size: A4;
            margin: 20px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Cairo', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1f2937;
            padding: 20px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #112344;
            padding-bottom: 15px;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
            text-align: {{ $isArabic ? 'left' : 'right' }};
        }
        .logo {
            max-width: 120px;
            max-height: 50px;
        }
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            color: #112344;
        }
        .section {
            margin-bottom: 12px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #112344;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: {{ $isArabic ? 'right' : 'left' }};
        }
        .info-label {
            color: #6b7280;
            font-size: 10px;
            text-align: {{ $isArabic ? 'right' : 'left' }};
        }
        .info-value {
            color: #1f2937;
            font-weight: 600;
            text-align: {{ $isArabic ? 'right' : 'left' }};
        }
        .two-col {
            display: table;
            width: 100%;
        }
        .col-half {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-{{ $isArabic ? 'left' : 'right' }}: 10px;
        }
        .col-half:last-child {
            padding-{{ $isArabic ? 'left' : 'right' }}: 0;
            padding-{{ $isArabic ? 'right' : 'left' }}: 10px;
        }
        .shipment-box {
            border: 1px solid #e5e7eb;
            padding: 10px;
            margin-bottom: 8px;
            background-color: #fafafa;
        }
        .shipment-header {
            font-weight: bold;
            font-size: 11px;
            color: #112344;
            margin-bottom: 6px;
            text-align: {{ $isArabic ? 'right' : 'left' }};
        }
        .price-table {
            width: 100%;
            border-collapse: collapse;
        }
        .price-table td {
            padding: 2px 0;
        }
        .price-table .label {
            color: #6b7280;
            text-align: {{ $isArabic ? 'right' : 'left' }};
        }
        .price-table .value {
            text-align: {{ $isArabic ? 'left' : 'right' }};
            color: #1f2937;
        }
        .price-table .total-row td {
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
            font-weight: bold;
        }
        .grand-total {
            background-color: #112344;
            color: white;
            padding: 12px;
            margin-top: 12px;
            display: table;
            width: 100%;
        }
        .grand-total-label {
            display: table-cell;
            font-size: 14px;
            font-weight: bold;
            text-align: {{ $isArabic ? 'right' : 'left' }};
        }
        .grand-total-value {
            display: table-cell;
            text-align: {{ $isArabic ? 'left' : 'right' }};
            font-size: 16px;
            font-weight: bold;
        }
        .compact-info {
            font-size: 10px;
            width: 100%;
        }
        .compact-info td {
            padding: 2px 8px 2px 0;
        }
        .text-right {
            text-align: {{ $isArabic ? 'left' : 'right' }};
        }
        .text-left {
            text-align: {{ $isArabic ? 'right' : 'left' }};
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            @php
                $logoPath = public_path('logo_white_background.png');
                $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
            @endphp
            @if($logoData)
                <img src="data:image/png;base64,{{ $logoData }}" alt="Logo" class="logo">
            @else
                <div style="font-size: 16px; font-weight: bold; color: #112344;">{{ config('app.name') }}</div>
            @endif
        </div>
        <div class="header-right">
            <div class="receipt-title">{{ $ar(__('orders.download_receipt')) }}</div>
        </div>
    </div>

    <!-- Order & Recipient Info Side by Side -->
    <div class="two-col">
        @if($isArabic)
            {{-- Arabic: Recipient on right, Order on left --}}
            <div class="col-half">
                <div class="section">
                    <div class="section-title">{{ $ar(__('orders.recipient')) }}</div>
                    <table class="compact-info">
                        <tr>
                            <td class="info-value">{{ $order->recipient_name }}</td>
                            <td class="info-label">:{{ $ar(__('orders.name')) }}</td>
                        </tr>
                        <tr>
                            <td class="info-value">{{ $order->recipient_phone }}</td>
                            <td class="info-label">:{{ $ar(__('orders.phone')) }}</td>
                        </tr>
                        <tr>
                            <td class="info-value">{{ $order->recipient_address }}</td>
                            <td class="info-label">:{{ $ar(__('orders.address')) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-half">
                <div class="section">
                    <div class="section-title">{{ $ar(__('orders.details_title')) }}</div>
                    <table class="compact-info">
                        <tr>
                            <td class="info-value">{{ $order->tracking_number }}</td>
                            <td class="info-label">:{{ $ar(__('orders.tracking_number')) }}</td>
                        </tr>
                        <tr>
                            <td class="info-value">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="info-label">:{{ $ar(__('orders.created')) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @else
            {{-- English: Order on left, Recipient on right --}}
            <div class="col-half">
                <div class="section">
                    <div class="section-title">{{ __('orders.details_title') }}</div>
                    <table class="compact-info">
                        <tr>
                            <td class="info-label">{{ __('orders.tracking_number') }}:</td>
                            <td class="info-value">{{ $order->tracking_number }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('orders.created') }}:</td>
                            <td class="info-value">{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-half">
                <div class="section">
                    <div class="section-title">{{ __('orders.recipient') }}</div>
                    <table class="compact-info">
                        <tr>
                            <td class="info-label">{{ __('orders.name') }}:</td>
                            <td class="info-value">{{ $order->recipient_name }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('orders.phone') }}:</td>
                            <td class="info-value">{{ $order->recipient_phone }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('orders.address') }}:</td>
                            <td class="info-value">{{ $order->recipient_address }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Route Information -->
    @if($order->route)
        <div class="section">
            <div class="section-title">{{ $ar(__('orders.route')) }}</div>
            <table class="compact-info">
                @if($isArabic)
                    <tr>
                        <td class="info-value">{{ $ar($order->route->destinationPort->name_ar ?? $order->route->destinationPort->name) }}</td>
                        <td class="info-label">:{{ $ar(__('orders.destination')) }}</td>
                        <td style="width: 30px;"></td>
                        <td class="info-value">{{ $ar($order->route->originPort->name_ar ?? $order->route->originPort->name) }}</td>
                        <td class="info-label">:{{ $ar(__('orders.origin')) }}</td>
                    </tr>
                    <tr>
                        <td class="info-value">{{ $ar(__('orders.transit_days', ['days' => $order->route->duration_days])) }}</td>
                        <td class="info-label">:{{ $ar(__('orders.transit_time')) }}</td>
                        <td style="width: 30px;"></td>
                        <td class="info-value">{{ ucfirst($order->route->schedule) }}</td>
                        <td class="info-label">:{{ $ar(__('orders.schedule')) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="info-label">{{ __('orders.origin') }}:</td>
                        <td class="info-value">{{ $order->route->originPort->name }}</td>
                        <td style="width: 30px;"></td>
                        <td class="info-label">{{ __('orders.destination') }}:</td>
                        <td class="info-value">{{ $order->route->destinationPort->name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('orders.schedule') }}:</td>
                        <td class="info-value">{{ ucfirst($order->route->schedule) }}</td>
                        <td style="width: 30px;"></td>
                        <td class="info-label">{{ __('orders.transit_time') }}:</td>
                        <td class="info-value">{{ __('orders.transit_days', ['days' => $order->route->duration_days]) }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    <!-- Shipments -->
    <div class="section">
        <div class="section-title">{{ $ar(__('orders.shipments')) }}</div>
        @foreach($order->shipments as $index => $shipment)
            <div class="shipment-box">
                <table style="width: 100%;">
                    <tr>
                        @if($isArabic)
                            <td style="vertical-align: top; width: 40%;">
                                <table class="price-table">
                                    <tr>
                                        <td class="value">{{ $ar(__('common.currency')) }} {{ number_format($shipment->container_price, 2) }}</td>
                                        <td class="label">:{{ $ar(__('orders.container_price')) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="value">{{ $ar(__('common.currency')) }} {{ number_format($shipment->customs_fee, 2) }}</td>
                                        <td class="label">:{{ $ar(__('orders.customs_fee')) }}</td>
                                    </tr>
                                    <tr class="total-row">
                                        <td class="value">{{ $ar(__('common.currency')) }} {{ number_format($shipment->total_cost, 2) }}</td>
                                        <td class="label">:{{ $ar(__('orders.shipment_total')) }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="vertical-align: top; width: 60%;">
                                <div class="shipment-header">{{ $ar(__('orders.shipment_number', ['number' => $index + 1])) }}</div>
                                @if($shipment->container)
                                    <div style="font-size: 10px; color: #6b7280; text-align: right;">
                                        {{ $ar($shipment->container->name_ar ?? $shipment->container->name) }} ({{ $shipment->container->size }} m³)
                                    </div>
                                @endif
                                <div style="font-size: 10px; margin-top: 4px; text-align: right;">
                                    {{ $ar(Str::limit($shipment->item_description, 80)) }}
                                </div>
                            </td>
                        @else
                            <td style="vertical-align: top; width: 60%;">
                                <div class="shipment-header">{{ __('orders.shipment_number', ['number' => $index + 1]) }}</div>
                                @if($shipment->container)
                                    <div style="font-size: 10px; color: #6b7280; text-align: left;">
                                        {{ $shipment->container->name }} ({{ $shipment->container->size }} m³)
                                    </div>
                                @endif
                                <div style="font-size: 10px; margin-top: 4px; text-align: left;">
                                    {{ Str::limit($shipment->item_description, 80) }}
                                </div>
                            </td>
                            <td style="vertical-align: top; width: 40%;">
                                <table class="price-table">
                                    <tr>
                                        <td class="label">{{ __('orders.container_price') }}:</td>
                                        <td class="value">{{ __('common.currency') }} {{ number_format($shipment->container_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">{{ __('orders.customs_fee') }}:</td>
                                        <td class="value">{{ __('common.currency') }} {{ number_format($shipment->customs_fee, 2) }}</td>
                                    </tr>
                                    <tr class="total-row">
                                        <td class="label">{{ __('orders.shipment_total') }}:</td>
                                        <td class="value">{{ __('common.currency') }} {{ number_format($shipment->total_cost, 2) }}</td>
                                    </tr>
                                </table>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>
        @endforeach
    </div>

    <!-- Payment Information -->
    @if($order->payment)
        <div class="section">
            <div class="section-title">{{ $ar(__('orders.payment_info')) }}</div>
            <table class="compact-info">
                @if($isArabic)
                    <tr>
                        @if($order->payment->paid_at)
                            <td class="info-value">{{ $order->payment->paid_at->format('M d, Y') }}</td>
                            <td class="info-label">:{{ $ar(__('orders.paid_at')) }}</td>
                            <td style="width: 20px;"></td>
                        @endif
                        <td class="info-value">{{ $order->payment->transaction_ref }}</td>
                        <td class="info-label">:{{ $ar(__('orders.transaction_ref')) }}</td>
                        <td style="width: 20px;"></td>
                        <td class="info-value">{{ ucfirst($order->payment->payment_method) }}</td>
                        <td class="info-label">:{{ $ar(__('orders.payment_method')) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="info-label">{{ __('orders.payment_method') }}:</td>
                        <td class="info-value">{{ ucfirst($order->payment->payment_method) }}</td>
                        <td style="width: 20px;"></td>
                        <td class="info-label">{{ __('orders.transaction_ref') }}:</td>
                        <td class="info-value">{{ $order->payment->transaction_ref }}</td>
                        @if($order->payment->paid_at)
                            <td style="width: 20px;"></td>
                            <td class="info-label">{{ __('orders.paid_at') }}:</td>
                            <td class="info-value">{{ $order->payment->paid_at->format('M d, Y') }}</td>
                        @endif
                    </tr>
                @endif
            </table>
        </div>
    @endif

    <!-- Grand Total -->
    <div class="grand-total">
        @if($isArabic)
            <span class="grand-total-value">{{ $ar(__('common.currency')) }} {{ number_format($order->total_cost, 2) }}</span>
            <span class="grand-total-label">{{ $ar(__('orders.total_cost')) }}</span>
        @else
            <span class="grand-total-label">{{ __('orders.total_cost') }}</span>
            <span class="grand-total-value">{{ __('common.currency') }} {{ number_format($order->total_cost, 2) }}</span>
        @endif
    </div>
</body>
</html>
