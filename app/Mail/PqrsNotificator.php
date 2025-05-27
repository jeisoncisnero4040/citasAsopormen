<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PqrsNotificator extends Mailable
{   
    use Queueable, SerializesModels;

    public $nameArea;
    public $urlFormAnswerPqr;
    public $datePqrs;
    public $userPqrName;
    public $descriptionPqrs;
    public $PqrType;
    public $macromotivePqrs;
    public $generalMotivetivePqrs;
    public $specificMotivetivePqrs;
    public $typeMotivetivePqrs;
    public $causeMotivetivePqrs;

    /**
     * Create a new message instance.
     */
    public function __construct(array $PqrsInfo)
    {
        $this->nameArea = $PqrsInfo['cordinador_area'];
        $this->urlFormAnswerPqr = $PqrsInfo['url_form'];
        $this->userPqrName = $PqrsInfo['nombre_usuario'];
        $this->descriptionPqrs = $PqrsInfo['descripcion'];
        $this->PqrType = $PqrsInfo['tipo_pqr'];
        $this->macromotivePqrs = $PqrsInfo['macromotivo'];
        $this->generalMotivetivePqrs = $PqrsInfo['motivo_general'];
        $this->specificMotivetivePqrs = $PqrsInfo['motivo_especifico'];
        $this->typeMotivetivePqrs = $PqrsInfo['tipo_motivo'];
        $this->causeMotivetivePqrs = $PqrsInfo['causa_motivo'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación Nuevo PQRS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'pqrs-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
