<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShippingStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Pending Approval', Order::where('status', 'pending_approval')->count())
                ->description('Awaiting admin approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Active Shipments', Shipment::whereIn('current_status', ['loaded', 'in_transit'])->count())
                ->description('Currently in transit')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make('Total Revenue', 'LYD ' . number_format(Payment::where('status', 'completed')->sum('amount'), 2))
                ->description('From completed payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
