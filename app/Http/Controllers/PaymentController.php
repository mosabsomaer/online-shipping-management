<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initiate payment for an order
     */
    public function initiate(Order $order)
    {
        // Check authorization
        if ($order->merchant_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Check if order is awaiting payment
        if ($order->status !== 'awaiting_payment') {
            return redirect()
                ->route('merchant.orders.show', $order)
                ->with('error', 'This order is not awaiting payment.');
        }

        // Check if payment already exists
        if ($order->payment && $order->payment->status === 'completed') {
            return redirect()
                ->route('merchant.orders.show', $order)
                ->with('info', 'Payment already completed for this order.');
        }

        // Initiate payment with Plutu
        $result = $this->paymentService->initiatePayment($order);

        if ($result['success']) {
            // Redirect to Plutu payment page
            return redirect($result['payment_url']);
        }

        return redirect()
            ->route('merchant.orders.show', $order)
            ->with('error', $result['error'] ?? 'Failed to initiate payment');
    }

    /**
     * Handle payment callback from Plutu
     */
    public function callback(Request $request)
    {
        $result = $this->paymentService->handleCallback($request->all());

        if ($result['success']) {
            return redirect()
                ->route('merchant.orders.show', $result['order'])
                ->with('success', 'Payment completed successfully! Your shipment is being prepared.');
        }

        return redirect()
            ->route('merchant.dashboard')
            ->with('error', $result['error'] ?? 'Payment processing failed');
    }

    /**
     * Handle payment cancellation
     */
    public function cancel(Order $order)
    {
        return redirect()
            ->route('merchant.orders.show', $order)
            ->with('warning', 'Payment was cancelled. You can try again when ready.');
    }
}
