<?php

namespace App\Constanst;

class RequestClientConstants
{
    public const RULES_UPDATE_CLIENT = [
        'celular' => 'required|string|min:7|max:15',
        'email' => 'required|email|max:150',
        'ocupacion' => 'nullable|string|max:10',
        'direccion' => 'required|string|max:255',
        'barrio' => 'required|string|max:150',
        'municipio' => 'nullable|string|max:10',
        'password'  =>'required|string',

        'responsable' => 'required|string|max:150',
        'celular_responsable' => 'required|string|min:7|max:15',
        'parentezco_responsable' => 'required|string|max:50',
    ];

    public const MESSAGES_UPDATE_CLIENT = [
        'celular.required' => 'El número de celular del usuario es obligatorio.',
        'celular.string' => 'El número de celular debe ser una cadena de texto.',
        'celular.min' => 'El número de celular debe tener al menos 7 caracteres.',
        'celular.max' => 'El número de celular no puede exceder los 15 caracteres.',

        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El formato del correo electrónico no es válido.',
        'email.max' => 'El correo electrónico no puede exceder los 150 caracteres.',

        'ocupacion.string' => 'El código de ocupación debe ser texto.',
        'ocupacion.max' => 'El código de ocupación no puede superar los 10 caracteres.',

        'direccion.required' => 'La dirección de residencia es obligatoria.',
        'direccion.string' => 'La dirección debe ser texto.',
        'direccion.max' => 'La dirección no puede superar los 255 caracteres.',

        'barrio.required' => 'El nombre del barrio es obligatorio.',
        'barrio.string' => 'El barrio debe ser texto.',
        'barrio.max' => 'El nombre del barrio no puede superar los 150 caracteres.',

        'municipio.string' => 'El código de municipio debe ser texto.',
        'municipio.max' => 'El código de municipio no puede superar los 10 caracteres.',

        'responsable.required' => 'El nombre del responsable es obligatorio.',
        'responsable.string' => 'El nombre del responsable debe ser texto.',
        'responsable.max' => 'El nombre del responsable no puede superar los 150 caracteres.',

        'celular_responsable.required' => 'El celular del responsable es obligatorio.',
        'celular_responsable.string' => 'El celular del responsable debe ser texto.',
        'celular_responsable.min' => 'El celular del responsable debe tener al menos 7 caracteres.',
        'celular_responsable.max' => 'El celular del responsable no puede exceder los 15 caracteres.',

        'parentezco_responsable.required' => 'El parentesco del responsable es obligatorio.',
        'parentezco_responsable.string' => 'El parentesco del responsable debe ser texto.',
        'parentezco_responsable.max' => 'El parentesco no puede superar los 50 caracteres.',

        'password.required' => 'La contraseña  es obligatoria.',
        'password.string' => 'La contraseña debe ser texto.',
    ];

    public const KEYS_ALLOWED_UPDATE_CLIENT = [
        'celular',
        'email',
        'ocupacion',
        'direccion',
        'barrio',
        'municipio',
        'responsable',
        'celular_responsable',
        'parentezco_responsable',
        'password'
    ];
}
