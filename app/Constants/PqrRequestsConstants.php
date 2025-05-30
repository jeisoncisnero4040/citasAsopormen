<?php
namespace App\Constants;

class PqrRequestsConstants{
    public const KEYS_ALLOWED_TO_ADD_PQR =[
        'descripcion',
        'nombre_quien_registra',
        'identificacion_usuario',
        'celular_usuario',
        'email_usuario',
        'canal_id',
        'tipo_id',
        'tipo_usuario',
        'nombre_usuario',
        'referencia',
        'sede_id',
        'area_id',
        'sogcs_id',
        'motivo_id'
    ] ;
    public const RULES_TO_ADD_PQR=[
        'descripcion' => 'required|string',
        'nombre_quien_registra' => 'required|string|max:100',
        'identificacion_usuario' => 'required|string|max:50',
        'celular_usuario' => 'required|string|max:50',
        'email_usuario' => 'required|email|max:100',
        'canal_id' => 'required|integer',
        'tipo_id' => 'required|integer',
        'tipo_usuario' => 'required',
        'nombre_usuario' => 'required|string|max:100',
        'sede_id' => 'required|integer',
        'area_id' => 'required|integer',
        'sogcs_id' => 'required|integer',
        'motivo_id' => 'required|integer'
    ];
    public const MESSAGES_ERROR_ADD_PQR= [
        'descripcion.required' => 'La descripción es obligatoria.',
        'nombre_quien_registra.required' => 'Debe indicar quién registra el PQR.',
        'identificacion_usuario.required' => 'La identificación del usuario es obligatoria.',
        'celular_usuario.required' => 'El celular del usuario es obligatorio.',
        'email_usuario.required' => 'El correo electrónico del usuario es obligatorio.',
        'email_usuario.email' => 'Debe ingresar un correo válido.',
        'canal_id.required' => 'Debe seleccionar un canal.',
        'tipo_id.required' => 'Debe seleccionar un tipo.',
        'tipo_usuario.required' => 'Debe seleccionar un tipo de usuario.',
        'tipo_usuario.in' => 'El tipo de usuario debe ser USUARIO o EPS.',
        'nombre_usuario.required' => 'Debe ingresar el nombre del usuario.',
        'sede_id.required' => 'Debe seleccionar una sede.',
        'area_id.required' => 'Debe seleccionar un área.',
        'sogcs_id.required' => 'Debe seleccionar una característica SOGCS.',
        'motivo_id.required' => 'Debe seleccionar un motivo.'
    ];
    public const  RULES_TO_ANSWER_PQRS_FROM_AREA = [
        'usuario_respuesta_area' => 'required|string|max:255',
        'respuesta' => 'required|string',
        'causas' => 'required|string|max:255',
        'id' => 'required|string',
        'actions' => 'required|array|min:1',
        'actions.*.descripcion' => 'required|string|max:500',
        'actions.*.persona_responsable' => 'required|string|max:255',
        'actions.*.evidence' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
    ];
    public const  MESSAGES_ERROR_ANSWER_PQRS_FROM_AREA = [
        'usuario_respuesta_area.required' => 'El nombre del usuario que responde es obligatorio.',
        'respuesta.required' => 'La respuesta es obligatoria.',
        'causas.required' => 'Debe indicar la causa.',
        'id.required' => 'El ID de PQRS es obligatorio.',
        'actions.required' => 'Debe agregar al menos una acción correctiva.',
        'actions.*.descripcion.required' => 'La descripción de cada acción es obligatoria.',
        'actions.*.persona_responsable.required' => 'Debe indicar la persona responsable en cada acción.',
        'actions.*.evidence.required' => 'Debe adjuntar un archivo como evidencia en cada acción.',
        'actions.*.evidence.mimes' => 'La evidencia debe ser un archivo imagen',
        'actions.*.evidence.max' => 'La evidencia no debe superar los 2MB.',
    ];
    PUBLIC const REQUEST_KEYS_TO_ANSWER_PQRS_FROM_AREA = [
        'usuario_respuesta_area',
        'respuesta',
        'causas',
        'id',
        'actions' 
    ];
    public const RULES_TO_CHANGE_AREA=[
        'sede_id' => 'required|integer',
        'area_id' => 'required|integer',
    ];
    public const MESSAGES_ERROR_CHANGE_AREA=[
        'sede_id.required' => 'Debe seleccionar una sede.',
        'area_id.required' => 'Debe seleccionar un área.',
    ];
    public const KEYS_ALLOWED_TO_CHANGE_AREA=[
        'sede_id',
        'area_id'
    ];
    
    
    public const RULES_DATES_RANGE = [
        'from' => 'required|date|before_or_equal:to',
        'to' => 'required|date|after_or_equal:from',
        'from_tendencie' => 'required|date|before_or_equal:to_tendencie',
        'to_tendencie' => 'required|date|after_or_equal:from_tendencie',
    ];

    public const MESSAGES_ERROR_DATES_RANGE = [

        'from.required' => 'La fecha inicial es obligatoria.',
        'from.date' => 'La fecha inicial no tiene un formato válido.',
        'from.before_or_equal' => 'La fecha inicial debe ser anterior o igual a la fecha final.',
        
        'to.required' => 'La fecha final es obligatoria.',
        'to.date' => 'La fecha final no tiene un formato válido.',
        'to.after_or_equal' => 'La fecha final debe ser posterior o igual a la fecha inicial.',

        'from_tendencie.required' => 'La fecha inicial para la tendencia es obligatoria.',
        'from_tendencie.date' => 'La fecha inicial para la tendencia no tiene un formato válido.',
        'from_tendencie.before_or_equal' => 'La fecha inicial para la tendencia debe ser anterior o igual a la fecha final de la tendencia.',

        'to_tendencie.required' => 'La fecha final para la tendencia es obligatoria.',
        'to_tendencie.date' => 'La fecha final para la tendencia no tiene un formato válido.',
        'to_tendencie.after_or_equal' => 'La fecha final para la tendencia debe ser posterior o igual a la fecha inicial de la tendencia.',
    ];


    public const KEYS_ALLOWED_DATES_RANGE = [
        'from',
        'to',
        'from_tendencie',
        'to_tendencie'
    ];

}