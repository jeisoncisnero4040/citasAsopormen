<?php

namespace App\Constanst;

class RequestsCitasConstans{
    public const RULES_CREATE_CITAS = [

        'profesional' => 'required|string|max:150',
        'cedProf'     => 'required|string|regex:/^[0-9]+$/',


        'nro_hist'            => 'required|string|regex:/^[0-9]+$/',
        'clientName'          => 'required|string|max:150',


        'n_autoriza'        => 'required|string|max:50',
        'procedim'          => 'required|string|max:1000',
        'tiempo'            => 'required|string|max:50',
        'fecha_vencimiento' => 'required|date',
        'codent'            => 'required|string|regex:/^[0-9]+$/',
        'codent2'           => 'required|string|regex:/^[0-9]+$/',


        'procedipro'        => 'required|string|max:150',
        'recordatorio_wsp'  => 'required|boolean',
        'duration_session' => 'required|integer|min:30',


        'regobserva' => 'nullable|integer',
        'copago'     => 'nullable|string|regex:/^[0-9]+$/',
        'start_date' => 'required|date',
        'week_days'  => 'nullable|array',



        'num_sessions_total' => 'required|integer|min:0',

    ];
    public const MESSAGES_CREATE_CITAS = [

        'registro.required' => 'El usuario que registra la cita es obligatorio.',

        'profesional.required' => 'El nombre del profesional es obligatorio.',
        'cedProf.required'     => 'La cédula del profesional es obligatoria.',
        'cedProf.regex'        => 'La cédula del profesional solo debe contener números.',

        'nro_hist.required' => 'El número de historia clínica es obligatorio.',
        'nro_hist.regex'    => 'El número de historia clínica solo debe contener números.',

        'clientName.required' => 'El nombre del cliente es obligatorio.',

        'n_autoriza.required' => 'El número de autorización es obligatorio.',
        'procedim.required'   => 'El código del procedimiento es obligatorio.',
        'procedim.min'        =>  'El procedimiento no debe superar los 1000 caracteres',
        'tiempo.required'     => 'El tiempo autorizado es obligatorio.',

        'fecha_vencimiento.required' => 'La fecha de vencimiento de la autorización es obligatoria.',
        'fecha_vencimiento.date'     => 'La fecha de vencimiento no tiene un formato válido.',

        'codent.required' => 'El código de la EPS es obligatorio.',
        'codent.regex'    => 'El código de la EPS solo debe contener números.',

        'codent2.required' => 'El código del convenio es obligatorio.',
        'codent2.regex'    => 'El código del convenio solo debe contener números.',

        'procedipro.required' => 'El nombre del procedimiento es obligatorio.',
        'recordatorio_wsp.required' => 'Debe indicar si el recordatorio por WhatsApp está habilitado.',

        'duration_session.required' => 'La duración de la sesión es obligatoria.',
        'duration_session.min'      => 'La duración de la sesión debe ser mayor a cero.',

        'regobserva.required' => 'La observación es obligatoria.',

        'copago.required' => 'El valor del copago es obligatorio.',
        'copago.regex'    => 'El copago solo debe contener valores numéricos.',

        'start_date.required' => 'La fecha de inicio es obligatoria.',
        'start_date.date'     => 'La fecha de inicio no tiene un formato válido.',

        'week_days.required' => 'Debe seleccionar al menos un día de la semana.',
        'week_days.array'    => 'Los días de la semana deben enviarse como un arreglo.',


        'num_sessions_total.required' => 'El número total de sesiones es obligatorio.',

    ];
    public const KEYS_ALLOWED_CREATE_CITAS = [
        'profesional',
        'cedProf',
        'nro_hist',
        'clientName',
        'n_autoriza',
        'procedim',
        'tiempo',
        'fecha_vencimiento',
        'codent',
        'codent2',
        'procedipro',
        'recordatorio_wsp',
        'duration_session',
        'regobserva',
        'copago',
        'start_date',
        'week_days',
        'num_sessions_total',

    ];


    public const RULES_DATES_RANGE = [
    'from' => 'required|date|before_or_equal:to',
    'to' => 'required|date|after_or_equal:from',
    'start' => 'required|date|after:to',
    'cedula' => 'required|string',
    'profesional'=>'required|string'
    ];
    public const MESSAGES_ERROR_DATES_RANGE = [
    'from.required' => 'La fecha inicial es obligatoria.',
    'from.date' => 'La fecha inicial no tiene un formato válido.',
    'from.before_or_equal' => 'La fecha inicial debe ser anterior o igual a la fecha final.',

    'to.required' => 'La fecha final es obligatoria.',
    'to.date' => 'La fecha final no tiene un formato válido.',
    'to.after_or_equal' => 'La fecha final debe ser posterior o igual a la fecha inicial.',

    'start.required' => 'La fecha de inicio de clonación es obligatoria.',
    'start.date' => 'La fecha de inicio de clonación no tiene un formato válido.',
    'start.after' => 'La fecha de inicio de clonación debe ser posterior a la fecha final del rango original.',

    'cedula.required' => 'La cédula del profesional es obligatoria.',
    'cedula.string' => 'La cédula del profesional debe ser un texto.',

    'profesional.required' => 'El nombre de profesional es obligatorio.',
    'profesional.string' => 'El nombre de profesional debe ser un texto.',

    ];
    public const KEYS_ALLOWED_DATES_RANGE = [
    'from',
    'to',
    'start',
    'cedula',
    'profesional'
    ];

}