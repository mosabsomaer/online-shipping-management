<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;
use Plutu\Services\PlutuLocalBankCards;

class PaymentService
{
    protected string $apiKey;

    protected string $secretKey;

    protected string $accessToken;

    public function __construct()
    {
        $this->apiKey = config('plutu.api_key');
        $this->secretKey = config('plutu.secret_key');
        $this->accessToken = config('plutu.access_token');
    }

    /**
     * Initialize payment with Plutu using LocalBankCards (credit/debit cards)
     */
    public function initiatePayment(Order $order): array
    {
        try {
            Log::info('Initiating Plutu payment', [
                'order_id' => $order->id,
                'amount' => $order->total_cost,
            ]);

            // Initialize Plutu LocalBankCards service
            $plutu = new PlutuLocalBankCards;
            $plutu->setCredentials($this->apiKey, $this->accessToken, $this->secretKey);

            // Prepare payment parameters
            $amount = (float) $order->total_cost;
            $invoiceNo = (string) $order->id;
            $returnUrl = route('payment.callback');

            // Call Plutu API to get payment redirect URL
            $response = $plutu->confirm($amount, $invoiceNo, $returnUrl);

            Log::info('Plutu API Response', [
                'success' => $response->getOriginalResponse()->isSuccessful(),
                'body' => $response->getOriginalResponse()->getBody(),
            ]);

            if ($response->getOriginalResponse()->isSuccessful()) {
                $redirectUrl = $response->getRedirectUrl();

                // Create payment record
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_cost,
                    'payment_method' => 'plutu_cards',
                    'transaction_ref' => $invoiceNo,
                    'status' => 'pending',
                ]);

                return [
                    'success' => true,
                    'payment_url' => $redirectUrl,
                    'transaction_id' => $invoiceNo,
                ];
            }

            $errorMessage = $response->getOriginalResponse()->getErrorMessage() ?? 'Payment initialization failed';
            $errorCode = $response->getOriginalResponse()->getErrorCode() ?? 'UNKNOWN';

            Log::error('Plutu payment initialization failed - API Error', [
                'order_id' => $order->id,
                'error_message' => $errorMessage,
                'error_code' => $errorCode,
            ]);

            return [
                'success' => false,
                'error' => $errorMessage.' (Code: '.$errorCode.')',
            ];
        } catch (\Exception $e) {
            Log::error('Plutu payment initialization failed - Exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Exception: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Handle payment callback from Plutu
     */
    public function handleCallback(array $parameters): array
    {
        try {
            Log::info('Processing Plutu callback', ['parameters' => $parameters]);

            // Initialize Plutu service for callback handling
            $plutu = new PlutuLocalBankCards;
            $plutu->setSecretKey($this->secretKey);

            // Process callback
            $callback = $plutu->callbackHandler($parameters);

            if ($callback->isApprovedTransaction()) {
                $transactionId = $callback->getParameter('transaction_id');
                $invoiceNo = $callback->getParameter('invoice_no');

                // Find order by invoice number (which is the order ID)
                $order = Order::find($invoiceNo);

                if (! $order) {
                    Log::error('Order not found in callback', ['invoice_no' => $invoiceNo]);

                    return ['success' => false, 'error' => 'Order not found'];
                }

                // Find payment record
                $payment = Payment::where('order_id', $order->id)
                    ->where('transaction_ref', $invoiceNo)
                    ->first();

                if (! $payment) {
                    Log::error('Payment record not found', ['order_id' => $order->id]);

                    return ['success' => false, 'error' => 'Payment record not found'];
                }

                // Use database transaction to ensure data consistency
                \DB::transaction(function () use ($payment, $order, $transactionId) {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'transaction_ref' => $transactionId, // Update with actual transaction ID
                    ]);

                    // Update order status
                    $order->update(['status' => 'paid']);

                    // Update existing shipments status to loaded
                    $this->updateShipmentsStatus($order);
                });

                Log::info('Payment completed successfully', [
                    'order_id' => $order->id,
                    'transaction_id' => $transactionId,
                ]);

                return ['success' => true, 'order' => $order->fresh(['shipments', 'route', 'merchant'])];
            }

            Log::warning('Payment transaction not approved', ['parameters' => $callback->getParameters()]);

            return [
                'success' => false,
                'error' => 'Transaction was not approved or was cancelled',
            ];
        } catch (\Exception $e) {
            Log::error('Payment callback handling failed', [
                'parameters' => $parameters,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'error' => 'Callback processing failed: '.$e->getMessage()];
        }
    }

    /**
     * Update existing shipments status when order is paid
     */
    protected function updateShipmentsStatus(Order $order): void
    {
        // Get all shipments for this order
        $shipments = $order->shipments;

        if ($shipments->isEmpty()) {
            Log::warning('No shipments found for paid order', ['order_id' => $order->id]);

            return;
        }

        // Update each shipment's status to 'loaded'
        foreach ($shipments as $shipment) {
            $shipment->update([
                'current_status' => 'loaded',
            ]);

            // Create status history entry
            // Use merchant_id as the updater since they triggered the payment
            $shipment->statusHistory()->create([
                'status' => 'loaded',
                'notes' => 'Payment received - container loaded and ready for shipment',
                'updated_by' => $order->merchant_id,
            ]);
        }

        // Update order status to processing
        $order->update(['status' => 'processing']);

        Log::info('Shipments status updated after payment', [
            'order_id' => $order->id,
            'shipments_count' => $shipments->count(),
        ]);
    }
}
