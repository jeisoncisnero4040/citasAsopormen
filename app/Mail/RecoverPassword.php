<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecoverPassword extends Mailable
{   
    public $name;
    public $newPassword;
    public $date;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $newPassword,$date)
    {
        $this->name = $name;
        $this->newPassword = $newPassword;
        $this->date=$date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperación de contraseña sistema de citas asopormen',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'RecoverPassword',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // No attachments required for the image
        return [];
    }
}
