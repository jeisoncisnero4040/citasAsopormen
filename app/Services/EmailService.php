<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\PqrsNotificator;
use App\Mail\AsnwerPqrsToClient;
use App\utils\ResponseManager;
use Illuminate\Http\UploadedFile;
use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Mail\RecoverPassword;
use Carbon\Carbon;

class EmailService
{
    private ResponseManager $responseManager;
    public function __construct(ResponseManager $responseManager) {
        $this->responseManager=$responseManager;
    }

    public function notifyPqrs(array $data): array
    {
        Mail::to($data['email_cordinador'])->send(new PqrsNotificator($data));
        return $this->responseManager->success("Pqrs Notificado por Email");

    }

    public function answerPqrsClient(array $data,UploadedFile $pdf,array $adjunts){
        $to = $data['to'] ?? null;
        Mail::to($to)->send(new AsnwerPqrsToClient($data,$pdf,$adjunts));
        return $this->responseManager->success("Pqrs Notificado por Email");
    }
    public function sendEmail($userName, $newPassword, $toSend)
    {
        try {
            Mail::to($toSend)->send(new RecoverPassword($userName, $newPassword, Carbon::now()));
            return true;
        } catch (\Exception $e) {
            throw new ServerErrorException("Error al enviar el correo: " . $e->getMessage(), 500);
        }
    }
}
