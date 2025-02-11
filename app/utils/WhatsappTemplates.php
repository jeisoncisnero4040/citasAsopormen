<?php 
namespace App\Utils;

use App\Utils\DateManager;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Message;

class WhatsappTemplates {
    
    public static function ConfirmationTemplate() {
        $message = "
¡Hola! Desde Asopormen nos alegra saber que has confirmado tu cita.  
Recuerda que para más información puedes comunicarte a las líneas del contact-center 6076852932 o móvil +57318 2918112.";
        
        return $message;
    }

    public static function SendCitaToWait() {
        $message = "Por favor, indícanos la razón por la que deseas cancelar la cita asignada.";
        return $message;
    }

    public static function citaCanceledMessage() {
        $message = "
Desde Asopormen te informamos que tu cita ha sido cancelada.  
Para más información sobre el proceso de reprogramación de tu cita, por favor comunicarte a las líneas del contact-center 6076852932 o móvil 318 2918112.";
        
        return $message;
    }

    public static function serverErrorTemplate() {
        $message = "Asopormen informa que actualmente estamos experimentando problemas al procesar la información.  
Por favor, inténtalo más tarde.";
        return $message;
    }

    public static function invalidRazonForCancelCitaTemplate() {
        $message = "La razón por la que deseas cancelar la cita no es válida. Por favor, intenta nuevamente proporcionando más detalles.";
        return $message;
    }

    public static function citasOnWaitNotFound() {
        $message = "Te has comunicado con Asopormen IPS.  
Este canal es exclusivo para operaciones de cancelación y confirmación de citas.  
Si deseas información sobre nuestro servicio al cliente, por favor comunicarte a las líneas del contact-center 6076852932 o móvil 318 2918112..";
        return $message;
    }

    public static function citaOutRangeTimeTemplate() {
        $message = "La cita que deseas cancelar no está disponible para esta acción. Por favor, inténtalo de nuevo.";
        return $message;
    }
    public static function CitaAlreadyCanceled(){
        $message = "La cita que deseas confirmar ya ha sido cancelada";
        return $message;
    }
    public static function ObservationTemplate($observations){

       
        $message="desde asopormen agradecemos que confirmes tu cita, por favor ten en cuenta las siguientes *recomedaciones*:

$observations";
        return $message;
    }
}

