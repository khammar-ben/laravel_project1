<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $visitor;
    public $token;
    public $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($visitor, $token)
    {
        $this->visitor = $visitor;
        $this->token = $token;
        $this->resetUrl = "http://localhost:3000/visitor/reset-password?email=" . urlencode($visitor->email) . "&token=" . $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password - Happy Hostel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'visitor-password-reset',
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
