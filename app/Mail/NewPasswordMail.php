<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $password;
    public string $user;
    public string $date;

    public function __construct(string $password, string $user)
    {
        $this->password = $password;
        $this->user = $user;

        Carbon::setLocale('es');
        $this->date = Carbon::now()->translatedFormat('j \d\e F \a \l\a\s g:i');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperación de contraseña - Sistema Clínico Asopormen',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'forgot-password',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

