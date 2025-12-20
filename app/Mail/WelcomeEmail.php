<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Online Shipping Management!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $dashboardUrl = url(route('merchant.dashboard', absolute: false));
        $ordersUrl = url(route('merchant.orders.index', absolute: false));

        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #10b981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                .button { display: inline-block; padding: 14px 28px; background-color: #10b981; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                .steps { background-color: white; padding: 20px; border-radius: 6px; margin: 20px 0; }
                .step { padding: 15px 0; border-bottom: 1px solid #e5e7eb; }
                .step:last-child { border-bottom: none; }
                .step-number { display: inline-block; width: 30px; height: 30px; background-color: #10b981; color: white; border-radius: 50%; text-align: center; line-height: 30px; font-weight: bold; margin-right: 10px; }
                .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome Aboard!</h1>
                </div>
                <div class="content">
                    <p>Dear {$this->user->name},</p>

                    <p>Congratulations! Your email has been verified and your account is now active.</p>

                    <p>You're all set to start using our Online Shipping Management System. Here's how to get started:</p>

                    <div class="steps">
                        <h3 style="margin-top: 0; color: #111827;">Getting Started Guide</h3>

                        <div class="step">
                            <span class="step-number">1</span>
                            <strong>Complete Your Profile</strong><br>
                            <span style="color: #6b7280; font-size: 14px;">Add your company details and contact information for better service</span>
                        </div>

                        <div class="step">
                            <span class="step-number">2</span>
                            <strong>Create Your First Order</strong><br>
                            <span style="color: #6b7280; font-size: 14px;">Select your route, choose a container, and submit for approval</span>
                        </div>

                        <div class="step">
                            <span class="step-number">3</span>
                            <strong>Track Your Shipments</strong><br>
                            <span style="color: #6b7280; font-size: 14px;">Monitor your orders in real-time from your dashboard</span>
                        </div>

                        <div class="step">
                            <span class="step-number">4</span>
                            <strong>Manage Payments</strong><br>
                            <span style="color: #6b7280; font-size: 14px;">Secure payment processing for approved orders</span>
                        </div>
                    </div>

                    <div style="text-align: center;">
                        <a href="{$dashboardUrl}" class="button">Go to Dashboard</a>
                    </div>

                    <p style="font-size: 14px; color: #6b7280; margin-top: 30px;">
                        <strong>Need Help?</strong><br>
                        Our support team is here to assist you. If you have any questions or need assistance, please don't hesitate to contact us.
                    </p>
                </div>
                <div class="footer">
                    <p>Thank you for choosing Online Shipping Management System.</p>
                    <p>We look forward to serving your shipping needs!</p>
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
