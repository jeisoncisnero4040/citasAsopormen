<?php

namespace App\Utils;
use App\Utils\DateManager;

class WhatsappTemplates {

    public static function generateLoginTemplate(): string {

        $template = 
            "👋 ¡Hola! Te has comunicado con *Diana*, la asistente virtual de Asopormen.\n\n" .
            "Para continuar, por favor escribe tu nombre completo ✍️😊";

        return $template;
    }

    public static function generateLoginTemplateNewUser(): string {

        $template = 
            "👋 ¡Hola! Veo que no estás registrado, necesitaré algunos datos personales para continuar.\n\n" .
            "Por favor, dime tu nombre completo ✍️😊";

        return $template;
    }

    public static function generateUploadCcImageTemplate() {
        return "📸 *Captura de la cara frontal del documento de identidad*\n\n" .
        "Para continuar, por favor toma una imagen de la parte frontal de tu documento de identidad (donde está la foto). 📄✨\n\n" .
        "✅ *Recomendaciones:*\n" .
        "🔹 Toma la foto en un lugar con buena iluminación.\n" .
        "🔹 Asegúrate de que toda la cédula esté visible y sin reflejos.\n" .
        "🔹 La imagen debe ser clara y sin borrones.\n\n" .
        "¡Gracias por tu ayuda! 😊";
    }

    public static function generateUploadBackCcImageTemplate() {
        return "📸 *Captura de la cara trasera del documento de identidad*\n\n" .
        "Ahora, de igual forma como cargaste la cara frontal, toma una foto de la parte posterior del documento. 📄✅";
    }

    public static function generateUploadMedicalOrderTemplate() {
        return "📄 *Captura de la orden médica*\n\n" .
        "Por favor, sube una imagen de la orden médica que deseas procesar. De igual forma como cargaste la cara frontal del documento, asegúrate de que sea legible. ✨✅";
    }

    public static function generateUploadClinicalHistoryTemplate() {
        return "📄 *Captura de la historia clínica*\n\n" .
        "Por favor, toma una foto o sube un documento con la historia clínica relevante. De igual forma como cargaste la cara frontal del documento, asegúrate de que todos los datos sean legibles. 😊";
    }

    public static function failedUploadImageTemplate(){
        return "❌ La imagen subida no es válida o no es un archivo de imagen. Por favor, inténtalo de nuevo. 📷🔄";
    }

    public static function requestIMageInQueue(){
        return "⏳ Estamos procesando tu solicitud, por favor espera un momento... 😊";
    }

    public static function generateUploadPdfTemplate(){
        return "📄 *Subida de PDF*\n\n" .
        "Por favor, adjunta el PDF que te ha enviado tu Entidad. 📂✅";
    }

    public static function generateUploadAutorizTemplateTemplate(){
        return "📄 *Captura de la autorización médica*\n\n" .
        "Por favor, sube una imagen clara de la autorización médica que deseas procesar. De igual forma como cargaste la cara frontal del documento, asegúrate de que toda la información sea visible. ✅";
    }

    public static function requestAutorizationToSanitasUser() {
        return "📄 *Subida del código de autorización médica*\n\n" .
        "Por favor, sube una imagen clara donde se vea el código de autorización médica que deseas procesar. También puedes escribir el número de autorización manualmente si lo prefieres. ✍️😊";
    }

    public static function generateUploadPreautorizNuevaEpsTemplate(){
        return "📄 *Subida del código de preautorización médica*\n\n" .
        "Por favor, sube una imagen clara donde se vea el código de preautorización médica que deseas procesar. De igual forma como cargaste la cara frontal del documento, asegúrate de que sea legible. ✅";
    }

    public static function generateUploadServiceDetailsTemplate(){
        return "✍️ *Descripción del servicio*\n\n" .
        "Por favor, introduce una breve descripción del servicio que deseas solicitar. 📑😊";
    }
    public static function startClinicalHistory() {
        return "📄 *Captura de la primera página de la historia clínica*\n\n" .
        "Por favor, toma una foto de la *primera página* de la historia clínica. 📷✅\n\n" .
        "De igual forma como cargaste la cara frontal del documento, asegúrate de que todos los datos sean legibles y sin reflejos. 😊";
    }
    public static function sendCodCasoCreated($caseCod) { 
        return "✅ *Orden Guardada Exitosamente*\n\n" .
            "📌 *Código de Orden asignado:* *$caseCod*\n\n" .
            "Puedes consultar el estado de tu orden en el menú, seleccionando la opción *Consultar Órdenes*.\n\n" .
            "⏳ *ASOPORMEN* tiene un plazo de *dos días hábiles* para dar respuesta a tu solicitud.\n\n" .
            "¡Gracias por tu confianza! 😊";
    }
    
    public static function orderRejectedTemplate($order) {
        $date = DateManager::getDshrtDate($order['fecha_rechazo']);
        $day = DateManager::getDayWeekToDate($order['fecha_rechazo']);
    
        return "❌ *¡Tu orden ha sido rechazada!*\n\n"
            . "🔍 *Motivo del rechazo:*\n"
            . "{$order["observaciones_rechazo"]}\n\n"
            . "📅 *Fecha de rechazo:* {$day} {$date}\n\n"
            . "🔁 Puedes intentar hacer de nuevo la solicitud teniendo en cuenta las actuales razones de rechazo.";
    }
    
    public static function orderClosedTemplate($order) {
        $date = DateManager::getDshrtDate($order['fecha_cierre']);
        $day = DateManager::getDayWeekToDate($order['fecha_cierre']);
    
        return "✅ *¡Tu orden ha sido cerrada con éxito!*\n\n"
            . "📌 Ya se gestionaron todas las acciones necesarias y las citas fueron agendadas correctamente.\n\n"
            . "📅 *Fecha de cierre:* {$day} {$date}\n\n"
            . "🗓️ Podés consultar tus citas ingresando en *Consultar Citas* desde el menú principal.";
    }
    
    public static function orderAceptedTemplate($order) {
        $date = DateManager::getDshrtDate($order['fecha_aceptacion']);
        $day = DateManager::getDayWeekToDate($order['fecha_aceptacion']);
    
        return "🟢 *¡Orden aceptada exitosamente!*\n\n"
            . "🛠️ Tu solicitud fue procesada y estamos coordinando las citas correspondientes.\n\n"
            . "📅 *Fecha de aceptación:* {$day} {$date}\n\n"
            . "📲 Te avisaremos apenas las citas estén listas para que las puedas consultar.";
    }
    
    public static function orderInProgres($order) {
        return "⏳ *Tu orden está en espera de ser procesada.*\n\n"
            . "🕐 Pronto comenzaremos con la gestión. Te avisaremos cuando haya novedades.\n\n"
            . "Gracias por tu paciencia 🙌";
    }

}

