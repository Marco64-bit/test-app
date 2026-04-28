<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code; // Create a public variable to hold the code

    public function __construct($code)
    {
        $this->code = $code; // Assign it when the class is called
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Login Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification_code', // We will create this view next
        );
    }
}
