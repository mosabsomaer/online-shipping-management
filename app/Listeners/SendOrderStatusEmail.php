<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Mail\OrderApproved;
use App\Mail\OrderCancelled;
use App\Mail\OrderCompleted;
use App\Mail\OrderPaid;
use App\Mail\OrderProcessing;
use App\Mail\OrderRejected;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusEmail
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $merchant = $order->merchant;

        $mailable = match ($order->status) {
            'approved', 'awaiting_payment' => new OrderApproved($order),
            'rejected' => new OrderRejected($order),
            'paid' => new OrderPaid($order),
            'processing' => new OrderProcessing($order),
            'completed' => new OrderCompleted($order),
            'cancelled' => new OrderCancelled($order),
            default => null,
        };

        if ($mailable) {
            Mail::to($merchant->email)->send($mailable);
        }
    }
}
