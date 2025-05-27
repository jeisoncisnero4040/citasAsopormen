<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AsnwerPqrsToClient extends Mailable
{
    use Queueable, SerializesModels;
    
    public string $user;
    public string $date;
    public string $canal;
    public string $userType;
    public UploadedFile $response;
    public array $customAttachments;  // Cambié nombre

    public function __construct(array $data, UploadedFile $response, array $attachments)
    {
        $this->user = $data['user'];
        $this->date = $data['date'];
        $this->canal = $data['canal'];
        $this->userType = $data['user_type'];
        $this->response = $response;
        $this->customAttachments = $attachments;  // Uso el nombre cambiado
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respuesta Pqrs',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'pqrs-responder-cliente',
        );
    }

    public function attachments(): array
    {
        $allFiles = array_merge([$this->response], $this->customAttachments);

        return array_filter(array_map(function($file) {
            if ($file instanceof UploadedFile) {
                return Attachment::fromData(
                    fn() => file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->withMime($file->getClientMimeType());
            }
            return null; 
        }, $allFiles));
    }
}
