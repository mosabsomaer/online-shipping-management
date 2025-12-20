<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Log;

class PlutuService
{
    private string $apiKey;
    private string $accessToken;
    private string $secretKey;

    public function __construct()
    {
        $this->apiKey      = config('plutu.api_key');
        $this->accessToken = config('plutu.access_token');
        $this->secretKey   = config('plutu.secret_key');
    }

    /**
     * Initiate payment process (Send OTP for applicable methods or redirect for others)
     */
    public function initiatePayment(Payment $payment): array
    {
        try {
            $paymentMethod   = $payment->payment_method;
            $serviceClass    = $paymentMethod->getServiceClass();
            $Plutu           = new $serviceClass();

            if ($paymentMethod->requiresOtp()) {
                return $this->handleOtpPayment($Plutu, $payment);
            }
            return $this->handleRedirectPayment($Plutu, $payment);

        } catch (Exception $e) {
            Log::error('Plutu payment initiation failed', [
                'payment_process_id' => $payment->id,
                'error'              => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Confirm OTP payment
     */
    public function confirmPayment(Payment $payment, string $otpCode): array
    {
        try {
            $paymentMethod   = $payment->payment_method;
            $serviceClass    = $paymentMethod->getServiceClass();
            $Plutu           = new $serviceClass();

            $Plutu->setCredentials($this->apiKey, $this->accessToken);

            $invoiceNo   = $payment->id;
            $apiResponse = $Plutu->confirm(
                $payment->process_id,
                $otpCode,
                (float) $payment->amount,
                $invoiceNo
            );
            if ($apiResponse->getOriginalResponse()->isSuccessful()) {
                $transactionId = $apiResponse->getTransactionId();


                Log::info('Payment confirmed successfully', [
                    'payment_id'         => $payment->id,
                    'transaction_id'     => $transactionId,
                    'payment_method'     => $paymentMethod->value,
                ]);

                return [
                    'success'        => true,
                    'transaction_id' => $transactionId,
                ];
            }
            $payment->update(['status' => PaymentStatusEnum::FAILED]);

            return [
                'success'    => false,
                'message'    => $apiResponse->getOriginalResponse()->getErrorMessage(),
                'error_code' => $apiResponse->getOriginalResponse()->getErrorCode(),
            ];

        } catch (Exception $e) {
            $payment->update(['status' => PaymentStatusEnum::FAILED]);

            Log::error('Payment confirmation failed', [
                'payment_process_id' => $payment->id,
                'error'              => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment confirmation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle callback from redirect payment methods
     */
    public function handleCallback(array $parameters, PaymentMethodEnum $paymentMethod): array
    {
        try {
            $serviceClass   = $paymentMethod->getServiceClass();
            $Plutu          = new $serviceClass();
            $Plutu->setSecretKey($this->secretKey);

            $callback = $Plutu->callbackHandler($parameters);

            if ($callback->isApprovedTransaction()) {
                return [
                    'success'        => true,
                    'transaction_id' => $callback->getParameter('transaction_id'),
                    'payment_id'     => $callback->getParameter('invoice_no'),
                    'all_parameters' => $callback->getParameters(),
                ];
            }
            return [
                'success'        => false,
                'message'        => 'Transaction was not approved or was cancelled',
                'all_parameters' => $callback->getParameters(),
            ];

        } catch (Exception $e) {
            Log::error('Callback handling failed', [
                'payment_method' => $paymentMethod->value,
                'error'          => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Callback handling failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle OTP-based payment methods (Adfali, Sadad)
     */
    private function handleOtpPayment($Plutu, Payment $payment): array
    {
        $Plutu->setCredentials($this->apiKey, $this->accessToken);

        if ($payment->payment_method === PaymentMethodEnum::SADAD) {
            $birthYear = $payment->metadata['birth_year'] ?? null;
            if (!$birthYear) {
                throw new Exception('Birth year is required for Sadad payment');
            }

            $apiResponse = $Plutu->verify(
                $payment->phone,
                $birthYear,
                (float) $payment->amount
            );
        } else {
            // Adfali
            $apiResponse = $Plutu->verify(
                $payment->phone,
                (float) $payment->amount
            );
        }

        if ($apiResponse->getOriginalResponse()->isSuccessful()) {
            $processId = $apiResponse->getProcessId();
            $payment->update(['process_id' => $processId, 'status' => PaymentStatusEnum::PENDING]);

            return [
                'success' => true,
                'data'    => [
                    'process_id'   => $processId,
                    'is_otp'       => true,
                ],
                'message' => 'OTP has been sent to your mobile number',
            ];
        }
        return [
            'success'    => false,
            'message'    => $apiResponse->getOriginalResponse()->getErrorMessage(),
            'error_code' => $apiResponse->getOriginalResponse()->getErrorCode(),
        ];
    }

    /**
     * Handle redirect-based payment methods (LocalBankCards, TLync, MPGS)
     */
    private function handleRedirectPayment($Plutu, Payment $payment): array
    {
        $Plutu->setCredentials($this->apiKey, $this->accessToken, $this->secretKey);

        $invoiceNo = $payment->id;
        $returnUrl = route('vendor.subscriptions.callback', ['method' => $payment->payment_method->value]);

        if ($payment->payment_method === PaymentMethodEnum::TLYNC) {
            // $callbackUrl = route('vendor.subscriptions.callback', ['method' => 'tlync']);
            $callbackUrl = config('app.url') . '/api/vendor/subscriptions/callback/tlync';

            $apiResponse = $Plutu->confirm(
                $payment->phone,
                (float) $payment->amount,
                $invoiceNo,
                $returnUrl,
                $callbackUrl,
            );
        } else {
            $apiResponse = $Plutu->confirm(
                (float) $payment->amount,
                $invoiceNo,
                $returnUrl
            );
        }

        if ($apiResponse->getOriginalResponse()->isSuccessful()) {
            $redirectUrl = $apiResponse->getRedirectUrl();

            return [
                'success' => true,
                'data'    => [
                    'redirect_url'      => $redirectUrl,
                    'requires_redirect' => true,
                ],
                'message' => 'Redirect to payment page',
            ];
        }
        $OgResponse = $apiResponse->getOriginalResponse();
        Log::error('Plutu redirect payment failed', [
            'payment_id' => $payment->id,
            'message'    => $OgResponse->getErrorMessage(),
            'error_code' => $OgResponse->getErrorCode(),
            'body'       => $OgResponse->getBody(),
        ]);
        return [
            'success'    => false,
            'message'    => $OgResponse->getErrorMessage(),
            'error_code' => $OgResponse->getErrorCode(),
        ];
    }
}
