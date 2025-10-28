<?php
namespace App\Constants;

/**
 * Clase de constantes que define los diferentes estados en el flujo de un chat.
 */
class StatusChatConstans {
    /**
     * Estado inicial del chatbot cuando el usuario envía un mensaje desde WhatsApp.
     * Se encarga de verificar si el usuario ya está registrado o si es un usuario nuevo.
     */
    public const INDEXLOGIN = 0.0;

    /**
     * Estado en el que se captura el usuario seleccionado en caso de que haya más de un usuario registrado con el mismo número de teléfono.
     */
    public const HANDLEUSER = 0.1;

    /**
     * Estado en el que se solicita y captura el nombre del nuevo usuario cuando no está registrado en el sistema.
     */
    public const HANDLENAMENEWUSER = 0.2;

    /**
     * Estado en el que se solicita y captura el número de cédula del nuevo usuario para su registro en el sistema.
     */
    public const HANDLENUMCEDULANEWUSER = 0.3;

    /**
     * Estado en el que se solicita y captura la EPS (Entidad Prestadora de Salud) del nuevo usuario.
     */
    public const HANDLEEPSNEWUSER = 0.4;

    /**
     * Estado en el que se solicita y captura la dirección de residencia del nuevo usuario.
     */
    public const HANDLEDIRECTIONNEWUSER = 0.5;

    /**
     * Estado en el que se solicita y captura el correo electrónico del nuevo usuario.
     */
    public const HANDLEEMAILNEWUSER = 0.6;
    
    /** 
     * Estado que que presenta el menu de navegacion 
     */
    public const MENUSTATUS=1.0;

    public const INDEXUPLOADORDER =2.0 ;
    public const HANDLEFRONTCC = 2.1;
    public const HANDLEBACKCC = 2.2;
    public const QUESTIONPARTICULARUSERHAVEHISTORYCLINICAL=2.25;
    public const HANDLEORDERMEDICAL=2.3;
    public const HANDLEAUTORIZ=2.4;
    public const HANDLECLINICALHISTORY=2.5;
    public const HANDLEPREAUTORIZ=2.6;
    public const HANDLESERVICEDETAILSTOREQUESTUSER=2.65;
    public const HANDLEOBSERVATIONS=2.7;
    public const HANDLEREQUESTPARTICULARUSER=2.8;
    
    public const HANDLEORDERDATAINPDF=2.9;


    public const INDEXCONSULTORDERS=3.0;
    public const HANDLECASESELECTED=3.1;
    /**
     * Estado inicial del proceso de consultar citas.
     * Se usa cuando el usuario inicia el flujo para consultar citas.
     */
    public const INDEXCONSULTCITASPROCESS=4.0;

    /**
     * Estado para especificar que el usuario no tiene citas para cancelar.
     */
    public const CLIENTCITASNOTFOUND=4.1;
    
    /**
     * Estado inicial del proceso de cancelación de cita.
     * Se usa cuando el usuario inicia el flujo para cancelar una cita.
     */
    public const INDEXCANCELCITAPROCESS = 5.0;

    /**
     * Estado en el que se manejan los IDs de las citas a cancelar.
     * Se usa después de que el usuario selecciona una o varias citas a cancelar.
     */
    public const HANDLEIDSTOCANCEL = 5.1;

    /**
     * Estado en el que sMANEJA QUE EL USUARIO NO TIENE CITAS A CANCELAR.
     */
    public const CITASTOCANCELNOTFOUD=5.3;
    /**
     * Estado en el que se maneja la razón de cancelación de la cita.
     * Se activa cuando el usuario proporciona un motivo para cancelar su cita.
     */
    public const HANDLERAZONTOCANCELCITA = 5.2;

    /**
     * Estado que indica que la rama del chat ha llagado a su fin.
     * Se usa cuando el flujo de conversación ha finalizado pidiendole al usuario cerrar el chat o regresar al menu.
     */
    public const INDEXCLOSEDCHAT = 9.0;

    /**
     * Estado especial de espera.
     * Se usa cuando el sistema está esperando una respuesta de un trabajo en segundo plano .
     */
    public const ESPECIALSTATUSONWHATING = 10.0;

    /**
     * Estado especial de error.
     * Se activa cuando ocurre un error inesperado dentro del flujo del chat, cuando se entra en este estado se 
     * activa un callback de notificacion a la administracion de la asistente
     */
    public const ESPECIALSTATUSONERROR = 10.1;

    /**
     * Estado especial de regresar al menu
     * Se activa cuando ocurre el usuario  pide regresar al menu  */
    public const ESPECIALSTATUSRETURNMENU = 10.2;
}
