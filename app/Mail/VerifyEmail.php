<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $verificationUrl,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->user->email],
            subject: 'Verify Your Email Address - Online Shipping Management',
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
                .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                .button { display: inline-block; padding: 14px 28px; background-color: #2563eb; color: black; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Verify Your Email</h1>
                </div>
                <div class="content">
                    <p>Dear {$this->user->name},</p>

                    <p>Thank you for registering with Online Shipping Management System!</p>

                    <p>Please verify your email address by clicking the button below:</p>

                    <div style="text-align: center;">
                        <a href="{$this->verificationUrl}" class="button">Verify Email Address</a>
                    </div>

                    <p style="font-size: 14px; color: #6b7280; margin-top: 30px;">
                        If the button doesn't work, copy and paste this link into your browser:<br>
                        <a href="{$this->verificationUrl}" style="color: #2563eb; word-break: break-all;">{$this->verificationUrl}</a>
                    </p>

                    <p style="font-size: 14px; color: #6b7280; margin-top: 30px;">
                        If you didn't create an account, no further action is required.
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
