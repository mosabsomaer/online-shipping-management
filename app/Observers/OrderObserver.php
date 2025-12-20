<?php

namespace App\Observers;

use App\Events\OrderStatusChanged;
use App\Models\Order;

class OrderObserver
{
    /**
     * Store old status values temporarily (not persisted to database)
     */
    protected static array $oldStatuses = [];

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        if ($order->isDirty('status')) {
            static::$oldStatuses[$order->id] = $order->getOriginal('status');
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $oldStatus = static::$oldStatuses[$order->id] ?? null;
            unset(static::$oldStatuses[$order->id]);
            OrderStatusChanged::dispatch($order, $oldStatus);
        }
    }
}
