<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangeVerification extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $newEmail;
    public string $verificationUrl;

    public function __construct(string $clientName, string $newEmail, string $verificationUrl)
    {
        $this->clientName      = $clientName;
        $this->newEmail        = $newEmail;
        $this->verificationUrl = $verificationUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirma tu nuevo correo electrónico',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.email-change-verification',
        );
    }
}
