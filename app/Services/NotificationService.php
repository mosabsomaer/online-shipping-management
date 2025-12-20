<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;

class NotificationService
{
    /**
     * Send order approval notification to merchant
     */
    public function sendOrderApprovalEmail(Order $order): bool
    {
        try {
            $merchant = $order->merchant;
            $paymentUrl = route('merchant.orders.show', $order);

            Resend::emails()->send([
                'from' => 'Shipping Management <onboarding@resend.dev>',
                'to' => [$merchant->email],
                'subject' => "Order #{$order->tracking_number} Approved - Payment Required",
                'html' => $this->getApprovalEmailTemplate($order, $paymentUrl),
            ]);

            Log::info('Order approval email sent', [
                'order_id' => $order->id,
                'merchant_email' => $merchant->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order approval email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send order rejection notification to merchant
     */
    public function sendOrderRejectionEmail(Order $order): bool
    {
        try {
            $merchant = $order->merchant;

            Resend::emails()->send([
                'from' => 'Shipping Management <onboarding@resend.dev>',
                'to' => [$merchant->email],
                'subject' => "Order #{$order->tracking_number} Rejected",
                'html' => $this->getRejectionEmailTemplate($order),
            ]);

            Log::info('Order rejection email sent', [
                'order_id' => $order->id,
                'merchant_email' => $merchant->email,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order rejection email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get approval email HTML template
     */
    protected function getApprovalEmailTemplate(Order $order, string $paymentUrl): string
    {
        $total = number_format($order->total_cost, 2);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                .button { display: inline-block; padding: 14px 28px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                .details { background-color: white; padding: 20px; border-radius: 6px; margin: 20px 0; }
                .details-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
                .details-row:last-child { border-bottom: none; }
                .label { font-weight: bold; color: #6b7280; }
                .value { color: #111827; }
                .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Order Approved!</h1>
                </div>
                <div class="content">
                    <p>Dear {$order->merchant->name},</p>

                    <p>Great news! Your shipping order has been approved and is ready for payment.</p>

                    <div class="details">
                        <h3 style="margin-top: 0; color: #111827;">Order Details</h3>
                        <div class="details-row">
                            <span class="label">Tracking Number:</span>
                            <span class="value">{$order->tracking_number}</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Route:</span>
                            <span class="value">{$order->route->originPort->name} → {$order->route->destinationPort->name}</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Shipments:</span>
                            <span class="value">{$order->shipments->count()} shipment(s)</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Recipient:</span>
                            <span class="value">{$order->recipient_name}</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Customs Fee:</span>
                            <span class="value">LYD {$order->customs_fee}</span>
                        </div>
                        <div class="details-row">
                            <span class="label" style="font-size: 18px;">Total Amount:</span>
                            <span class="value" style="font-size: 18px; font-weight: bold; color: #2563eb;">LYD {$total}</span>
                        </div>
                    </div>

                    <p style="margin-top: 30px;"><strong>Next Step:</strong> Please complete the payment to proceed with your shipment.</p>

                    <div style="text-align: center;">
                        <a href="{$paymentUrl}" class="button">View Order & Pay Now</a>
                    </div>

                    <p style="font-size: 14px; color: #6b7280; margin-top: 30px;">
                        Once payment is received, we will immediately begin preparing your shipment and you'll receive tracking updates.
                    </p>
                </div>
                <div class="footer">
                    <p>This is an automated email from the Online Shipping Management System.</p>
                    <p>If you have any questions, please contact our support team.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Get rejection email HTML template
     */
    protected function getRejectionEmailTemplate(Order $order): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #dc2626; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                .details { background-color: white; padding: 20px; border-radius: 6px; margin: 20px 0; }
                .reason-box { background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Order Rejected</h1>
                </div>
                <div class="content">
                    <p>Dear {$order->merchant->name},</p>

                    <p>We regret to inform you that your shipping order has been rejected.</p>

                    <div class="details">
                        <h3 style="margin-top: 0; color: #111827;">Order Details</h3>
                        <p><strong>Tracking Number:</strong> {$order->tracking_number}</p>
                        <p><strong>Route:</strong> {$order->route->originPort->name} → {$order->route->destinationPort->name}</p>
                        <p><strong>Shipments:</strong> {$order->shipments->count()} shipment(s)</p>
                    </div>

                    <div class="reason-box">
                        <h4 style="margin-top: 0; color: #dc2626;">Rejection Reason:</h4>
                        <p style="margin-bottom: 0;">{$order->rejection_reason}</p>
                    </div>

                    <p style="margin-top: 30px;">
                        If you have any questions or would like to discuss this decision, please contact our support team.
                    </p>
                </div>
                <div class="footer">
                    <p>This is an automated email from the Online Shipping Management System.</p>
                    <p>Thank you for your understanding.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
}
