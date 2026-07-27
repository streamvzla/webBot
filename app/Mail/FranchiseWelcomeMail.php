<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FranchiseWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clientName;
    public string $userEmail;
    public string $userPassword;
    public string $panelUrl;
    public string $siteName;

    public function __construct(string $clientName, string $userEmail, string $userPassword, string $panelUrl, string $siteName)
    {
        $this->clientName   = $clientName;
        $this->userEmail    = $userEmail;
        $this->userPassword = $userPassword;
        $this->panelUrl     = $panelUrl;
        $this->siteName     = $siteName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "¡Bienvenido a {$this->siteName}! Tus credenciales de acceso",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.franchise-welcome',
        );
    }
}
