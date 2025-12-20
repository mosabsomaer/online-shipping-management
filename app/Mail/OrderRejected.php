<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderRejected extends Mailable implements ShouldQueue
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
            subject: "Order #{$this->order->tracking_number} Rejected",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $html = <<<HTML
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
                    <p>Dear {$this->order->merchant->name},</p>

                    <p>We regret to inform you that your shipping order has been rejected.</p>

                    <div class="details">
                        <h3 style="margin-top: 0; color: #111827;">Order Details</h3>
                        <p><strong>Tracking Number:</strong> {$this->order->tracking_number}</p>
                        <p><strong>Route:</strong> {$this->order->route->originPort->name} → {$this->order->route->destinationPort->name}</p>
                        <p><strong>Container:</strong> {$this->order->container->name}</p>
                    </div>

                    <div class="reason-box">
                        <h4 style="margin-top: 0; color: #dc2626;">Rejection Reason:</h4>
                        <p style="margin-bottom: 0;">{$this->order->rejection_reason}</p>
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
