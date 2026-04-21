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



    const DATA_CREATE_CLIENT = [

        'documento' => 'required|string|max:50',
        'numDoc' => 'required|numeric|digits_between:5,20',
        
        'entidad' => 'required|numeric|digits:10',
        'convenio' => 'required|numeric|digits:10',
        'regimen' => 'required|string|max:100',
        'tipo_usuario' => 'required|string|max:100',
        'grupo' => 'nullable|alpha_num|size:1',
        
        'primer_nombre' => 'required|string|min:2|max:50',
        'segundo_nombre' => 'nullable|string|max:50',
        'primer_apellido' => 'required|string|min:2|max:50',
        'segundo_apellido' => 'nullable|string|min:2|max:50',
        
        'pais' => 'required|string|max:100',
        'sexo' => 'required|string|max:20',
        'ocupacion' => 'required|string|max:255',
        'estado_civil' => 'nullable|string|max:50',
        'rh' => 'nullable|string|max:5|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        'num_hijos' => 'nullable|integer|min:0|max:20',
        'escolaridad' => 'required|string|max:100',
        'lugar_nac' => 'nullable|string|max:100',
        'fecha_nac' => 'required|date|before:today',
        
        'direccion' => 'required|string|max:150',
        'barrio' => 'required|string|max:100',
        'municipio' => 'required|string|max:100',
        'contacto' => 'required|string|max:22',
        'email' => 'required|email|max:120',
        
        'poblacion' => 'nullable|string|max:100',
        'etnia' => 'nullable|string|max:100',
        'zona' => 'required|string|max:50',
        'firmar' => 'nullable',
        'discapacidad' => 'nullable',
        'discapaciadad_tipo' => 'nullable|string|max:100',
        
        // RESPONSABLE
        'a_documento' => 'required|numeric|digits_between:5,20',
        'a_primer_nombre' => 'required|string|min:2|max:50',
        'a_segundo_nombre' => 'nullable|string|max:50',
        'a_primer_apellido' => 'required|string|min:2|max:50',
        'a_segundo_apellido' => 'nullable|string|min:2|max:50',
        'a_contacto' => 'required|string|max:22',
        'a_parentezco' => 'required|string|max:50',
        
        // OTROS
        'observaciones' => 'nullable|string|max:1000',
        //files
        'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:5120',
        'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        'discapacidad_tipo'=>'nullable',
        'cedula_user'=>'required|string',
        'user'=>'required|string',
        'activo'=>'nullable'

    ];
    const DATA_UPDATE_CLIENT = [...self::DATA_CREATE_CLIENT,'codigo'=>'required|string|min:10'];


    const ERRORES_CREATE_CLIENT = [
        // IDENTIFICACIÓN
        'documento.required' => 'Debe seleccionar el tipo de identificación',
        'numDoc.required' => 'Debe ingresar el número de identificación',
        'numDoc.numeric' => 'El número de identificación solo puede contener números',
        'numDoc.digits_between' => 'El número de identificación debe tener entre :min y :max dígitos',
        // ASEGURAMIENTO
        'regimen.required' => 'Debe seleccionar el régimen',
        'entidad.required' => 'Debe seleccionar la entidad (EPS)',
        'entidad.numeric' => 'El código de la entidad debe ser numérico',
        'entidad.digits' => 'El código de la entidad debe tener :digits dígitos',
        'convenio.required' => 'Debe seleccionar el convenio',
        'convenio.numeric' => 'El código del convenio debe ser numérico',
        'convenio.digits' => 'El código del convenio debe tener :digits dígitos',
        // NOMBRES
        'primer_nombre.required' => 'Debe ingresar el primer nombre',
        'primer_nombre.alpha' => 'El primer nombre solo puede contener letras',
        'primer_nombre.min' => 'El primer nombre debe tener al menos :min caracteres',
        'primer_nombre.max' => 'El primer nombre no puede superar los :max caracteres',
        'segundo_nombre.alpha' => 'El segundo nombre solo puede contener letras',
        'segundo_nombre.max' => 'El segundo nombre no puede superar los :max caracteres',
        'primer_apellido.required' => 'Debe ingresar el primer apellido',
        'primer_apellido.alpha' => 'El primer apellido solo puede contener letras',
        'primer_apellido.min' => 'El primer apellido debe tener al menos :min caracteres',
        'primer_apellido.max' => 'El primer apellido no puede superar los :max caracteres',
        
        'segundo_apellido.alpha' => 'El segundo apellido solo puede contener letras',
        'segundo_apellido.min' => 'El segundo apellido debe tener al menos :min caracteres',
        'segundo_apellido.max' => 'El segundo apellido no puede superar los :max caracteres',
        
        'pais.required' => 'Debe seleccionar el país',
        
        // DATOS PERSONALES
        'estado_civil.alpha' => 'El estado civil solo puede contener letras',
        'estado_civil.max' => 'El estado civil no puede superar los :max caracteres',
        
        'rh.max' => 'El RH no puede superar los :max caracteres',
        'rh.in' => 'El RH debe ser un tipo de sangre válido (A+, A-, B+, B-, AB+, AB-, O+, O-)',
        
        'num_hijos.integer' => 'El número de hijos debe ser un número entero',
        'num_hijos.min' => 'El número de hijos no puede ser menor que :min',
        'num_hijos.max' => 'El número de hijos no puede ser mayor que :max',
        
        'escolaridad.required' => 'Debe seleccionar el nivel educativo',
        
        'zona.required' => 'Debe seleccionar la zona',
        
        'lugar_nac.alpha' => 'El lugar de nacimiento solo puede contener letras',
        'lugar_nac.max' => 'El lugar de nacimiento no puede superar los :max caracteres',
        
        'sexo.required' => 'Debe seleccionar el sexo',
        
        'tipo_usuario.required' => 'Debe seleccionar el tipo de usuario',
        
        'ocupacion.required' => 'Debe seleccionar la ocupación',
        
        'municipio.required' => 'Debe seleccionar el municipio',
        
        'fecha_nac.required' => 'Debe ingresar la fecha de nacimiento',
        'fecha_nac.date' => 'Ingrese una fecha válida',
        'fecha_nac.before' => 'La fecha de nacimiento no puede ser futura',
        
        // CONTACTO
        'direccion.required' => 'Debe ingresar la dirección',
        'direccion.max' => 'La dirección no puede superar los :max caracteres',
        
        'barrio.required' => 'Debe ingresar el barrio',
        'barrio.max' => 'El barrio no puede superar los :max caracteres',
        
        'contacto.required' => 'Debe ingresar el número de contacto',
        'contacto.regex' => 'El campo contacto debe contener dos números de 10 dígitos que inicien en 3, separados por un guion (ej: 3001234567-3019876543).',
        
        'email.required' => 'Debe ingresar el correo electrónico',
        'email.email' => 'Ingrese un correo electrónico válido',
        'email.max' => 'El correo electrónico no puede superar los :max caracteres',
        
        // CONDICIONES SOCIALES
        'firmar.boolean' => 'El valor de firmar debe ser verdadero o falso',
        
        'poblacion.max' => 'El grupo poblacional no puede superar los :max caracteres',
        
        'etnia.max' => 'La etnia no puede superar los :max caracteres',
        
        'discapacidad.boolean' => 'El valor de discapacidad debe ser verdadero o falso',
        
        'discapaciadad_tipo.max' => 'El tipo de discapacidad no puede superar los :max caracteres',
        
        // RESPONSABLE
        'a_documento.required' => 'Debe ingresar el documento del responsable',
        'a_documento.numeric' => 'El documento del responsable solo puede contener números',
        'a_documento.digits_between' => 'El documento del responsable debe tener entre :min y :max dígitos',
        
        'a_primer_nombre.required' => 'Debe ingresar el primer nombre del responsable',
        'a_primer_nombre.alpha' => 'El primer nombre del responsable solo puede contener letras',
        'a_primer_nombre.min' => 'El primer nombre del responsable debe tener al menos :min caracteres',
        'a_primer_nombre.max' => 'El primer nombre del responsable no puede superar los :max caracteres',
        
        'a_segundo_nombre.alpha' => 'El segundo nombre del responsable solo puede contener letras',
        'a_segundo_nombre.max' => 'El segundo nombre del responsable no puede superar los :max caracteres',
        
        'a_primer_apellido.required' => 'Debe ingresar el primer apellido del responsable',
        'a_primer_apellido.alpha' => 'El primer apellido del responsable solo puede contener letras',
        'a_primer_apellido.min' => 'El primer apellido del responsable debe tener al menos :min caracteres',
        'a_primer_apellido.max' => 'El primer apellido del responsable no puede superar los :max caracteres',
        
        'a_segundo_apellido.alpha' => 'El segundo apellido del responsable solo puede contener letras',
        'a_segundo_apellido.min' => 'El segundo apellido del responsable debe tener al menos :min caracteres',
        'a_segundo_apellido.max' => 'El segundo apellido del responsable no puede superar los :max caracteres',
        
        'a_contacto.required' => 'Debe ingresar el teléfono del responsable',
        'a_contacto.numeric' => 'El teléfono del responsable solo puede contener números',
        'a_contacto.digits_between' => 'El teléfono del responsable debe tener :min dígitos',
        
        'a_parentezco.required' => 'Debe seleccionar el parentesco del responsable',
        
        // OBSERVACIONES
        'observaciones.max' => 'Las observaciones no pueden superar los :max caracteres',
        'image.file' => 'El archivo de imagen no es válido',
        'image.image' => 'El archivo debe ser una imagen',
        'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif',
        'image.max' => 'La imagen no puede superar los 5MB',
        
        'document.file' => 'El archivo de documento no es válido',
        'document.mimes' => 'El documento debe ser de tipo: pdf, doc, docx',
        'document.max' => 'El documento no puede superar los 5MB',
    ];
}
