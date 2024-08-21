<?php
namespace App\Services;

use App\Exceptions\CustomExceptions\ServerErrorException;
use App\Mail\RecoverPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class EmailService
{
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
