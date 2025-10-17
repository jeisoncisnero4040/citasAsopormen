<?php

namespace App\Constants;

class ConstantsRequest
{

    public const FIELDS_TO_LOGIN = [
        'cedula',
        'password',
        'rol',
    ];

    public const RULES_TO_LOGIN = [
        'cedula' => 'required|string',
        'password' => 'required|string',
        'rol' => 'required|string',
    ];

    public const ERRORS_TO_LOGIN = [
        'cedula.required' => 'El campo cédula es obligatorio.',
        'cedula.string' => 'La cédula debe ser una cadena de texto.',
        'password.required' => 'El campo contraseña es obligatorio.',
        'password.string' => 'La contraseña debe ser una cadena de texto.',
        'rol.required' => 'El campo rol es obligatorio.',
        'rol.string' => 'el rol  debe ser una cadena de texto.',
    ];

    public const FIELDS_TO_GET_DAILY_SCHEDULE=[
        'cedula'
    ];

    public const RULES_TO_GET_DAILY_SCHEDULE=[
        'cedula' => 'required|string'
    ];
    public const ERRORS_RULES_TO_GET_DAILY_SCHEDULE=[
        'cedula.required' => 'El campo cédula es obligatorio.',
        'cedula.string' => 'La cédula debe ser una cadena de texto.',
    ];


    public const FIELDS_TO_GET_SCHEDULE = [
        'cedula',
        'from',
        'to'
    ];

    public const RULES_TO__GET_SCHEDULE = [
        'cedula' => 'required|string|regex:/^\d{6,15}$/',
        'from' => 'required|date',
        'to'=>'required|date'
    ];

    public const ERRORS_TO_GET_SCHEDULE = [
        'cedula.required' => 'El campo cédula es obligatorio.',
        'cedula.regex' => 'La cédula debe contener solo números y tener entre 6 y 15 dígitos.',
        'cedula.digits_between' => 'La cédula debe tener entre 6 y 15 dígitos.',

        'from.required' => 'El campo fecha de inicio es obligatorio.',
        'from.date' => 'La fecha de inicio debe ser una fecha válida.',

        'to.required' => 'El campo fecha de fin es obligatorio.',
        'to.date' => 'La fecha de fin debe ser una fecha válida.',
        'to.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
    ];
    public const FIELDS_TO_CHANGE_PASSWORD = [
        'cedula',
        'password',
        'first_change',
        'oldPassword'
    ];

    public const RULES_TO_CHANGE_PASSWORD = [
        'cedula' => 'required|string|regex:/^\d{6,15}$/',
        'password' => 'required|string|min:8',
        'first_change' => 'required|boolean',
        'oldPassword'  =>'nullable|string'
    ];

    public const ERRORS_TO_CHANGE_PASSWORD = [
        'cedula.required' => 'El campo cédula es obligatorio.',
        'cedula.regex' => 'La cédula debe contener solo números y tener entre 6 y 15 dígitos.',

        'password.required' => 'El campo contraseña es obligatorio.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',

        'first_change.required' => 'Debe indicarse si es el primer cambio de contraseña.',
        'first_change.boolean' => 'El valor del campo debe ser verdadero o falso.',
    ];
}
