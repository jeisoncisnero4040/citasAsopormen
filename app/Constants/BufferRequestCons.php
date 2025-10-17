<?php

namespace App\Constants;

class BufferRequestCons
{
    const KEYS_TO_READ_EVO = [
        'cedula',
        'autoriz',
        'historia'
    ];

    const RULES_TO_READ_EVO = [
        'cedula'  => 'required|string',
        'autoriz' => 'required|string',
        'historia'=> 'required|string',
    ];

    const MESSAGES_TO_READ_EVO = [
        'cedula.required'  => 'El campo cédula es obligatorio.',
        'cedula.string'    => 'El campo cédula debe ser una cadena de texto.',
        
        'autoriz.required' => 'El campo autorización es obligatorio.',
        'autoriz.string'   => 'El campo autorización debe ser una cadena de texto.',
        
        'historia.required'=> 'El campo historia es obligatorio.',
        'historia.string'  => 'El campo historia debe ser una cadena de texto.',
    ];
}
