<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Order $order,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order #{$this->order->tracking_number} Approved - Payment Required",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $paymentUrl = route('merchant.orders.show', $this->order);
        $total = number_format($this->order->total_cost, 2);
        $origin = $this->order->route ? $this->order->route->originPort->name : 'N/A';
        $destination = $this->order->route ? $this->order->route->destinationPort->name : 'N/A';

        // Get container info from shipments
        $containersList = $this->order->shipments->map(function ($shipment) {
            return $shipment->container ? $shipment->container->name : 'N/A';
        })->join(', ');

        $html = <<<HTML
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
                    <p>Dear {$this->order->merchant->name},</p>

                    <p>Great news! Your shipping order has been approved and is ready for payment.</p>

                    <div class="details">
                        <h3 style="margin-top: 0; color: #111827;">Order Details</h3>
                        <div class="details-row">
                            <span class="label">Tracking Number:</span>
                            <span class="value">{$this->order->tracking_number}</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Route:</span>
                            <span class="value">{$origin} → {$destination}</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Container(s):</span>
                            <span class="value">{$containersList}</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Recipient:</span>
                            <span class="value">{$this->order->recipient_name}</span>
                        </div>
                        <div class="details-row">
                            <span class="label">Customs Fee:</span>
                            <span class="value">LYD {$this->order->customs_fee}</span>
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

        return new Content(
            htmlString: $html,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
