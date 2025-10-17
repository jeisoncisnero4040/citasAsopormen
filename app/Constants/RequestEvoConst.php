<?php

namespace App\Constants;

class RequestEvoConst {
    public const RULES_TO_GET_EVO = [
        'procedipro' => 'required|string',
        'from' => 'nullable|date|required_with:to|before_or_equal:to',
        'to'   => 'nullable|date|required_with:from|after_or_equal:from',
        'autoriz'    => 'nullable|string',
        'historia'   => 'required|string',
        'profesional'=> 'required|string',
        'client'     => 'required|string'
    ];

    public const MESSAGES_TO_GET_EVO = [
        'procedipro.required' => 'El campo procedimiento es obligatorio.',
        'procedipro.string'   => 'El campo procedimiento debe ser un texto válido.',
        
        'from.date'           => 'La fecha inicial debe tener un formato válido.',
        'from.before_or_equal'=> 'La fecha inicial debe ser anterior o igual a la fecha final.',
        'from.required_with'  => 'La fecha inicial es obligatoria cuando se proporciona la fecha final.',

        'to.date'             => 'La fecha final debe tener un formato válido.',
        'to.after_or_equal'   => 'La fecha final debe ser posterior o igual a la fecha inicial.',
        'to.required_with'    => 'La fecha final es obligatoria cuando se proporciona la fecha inicial.',

        'autoriz.string'      => 'El campo autorización debe ser un texto válido.',

        'historia.required'   => 'El número de historia clínica es obligatorio.',
        'historia.string'     => 'El número de historia clínica debe ser un texto válido.',

        'profesional.required'   => 'El profesional que solicita las evoluciones  es obligatorio.',
        'profesional.string'     => 'El profesional que solicita las evoluciones debe ser un texto válido.',

        'client.required'   => 'El nombre del cliente  es obligatorio.',
        'client.string'     => 'El nombre del cliente  debe ser un texto válido.',
    ];
}
