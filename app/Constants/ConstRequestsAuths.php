<?php
namespace App\Constants;

class ConstRequestsAuths{

    public const FIELDS_TO_CLOSE_AUTH = [
        'razon',
        'profesional',
        'id',
        'cedula',
        'codent',
        'historia',
        'autorizacion',
    ];

    public const RULES_TO_CLOSE_AUTH = [
        'razon'        => 'required|string|max:5000',
        'profesional'  => 'required|string|max:255',
        'id'           => 'required|integer',
        'cedula'       => 'required|string|max:20',
        'codent'       => 'required|string|max:20',
        'historia'     => 'required|string|max:10',
        'autorizacion' => 'required|string|max:50',
    ];

    public const MESSAGES_TO_CLOSE_AUTH = [
        'razon.required'        => 'La razón del cierre es obligatoria.',
        'razon.string'          => 'La razón debe ser una cadena de texto.',
        'profesional.required'  => 'El nombre del profesional es obligatorio.',
        'profesional.string'    => 'El nombre del profesional debe ser texto.',
        'id.required'           => 'El ID de la autorización es obligatorio.',
        'id.integer'            => 'El ID debe ser un número entero.',
        'cedula.required'       => 'La cédula del profesional es obligatoria.',
        'cedula.string'         => 'La cédula debe ser texto.',
        'codent.required'       => 'El código de la entidad es obligatorio.',
        'codent.string'         => 'El código de la entidad debe ser texto.',
        'historia.required'     => 'El número de historia clínica es obligatorio.',
        'historia.string'       => 'El número de historia debe ser texto.',
        'autorizacion.required' => 'El número de autorización es obligatorio.',
        'autorizacion.string'   => 'El número de autorización debe ser texto.',
    ];


}