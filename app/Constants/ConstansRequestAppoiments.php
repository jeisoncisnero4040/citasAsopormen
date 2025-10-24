<?php

namespace App\Constants;

class ConstansRequestAppoiments{
    public const FIELDS_TO_CANCEL_APPOIMENT = [
    'profesional',
    'ids',
    'razon',
    'fecha_cita',
    'meanCancel'
    ];

    public const RULES_TO_CANCEL_APPOIMENT = [
        'ids' => 'required|string',  
        'razon' => 'required|string|min:3',
        'profesional' => 'required|string',
    ];

    public const ERRORS_TO_CANCEL_APPOIMENT = [
        
        'ids.required' => 'Debe seleccionar al menos una cita para cancelar.',
        'ids.string' => 'Los IDs deben estar en formato de texto concatenado.',

        'razon.required' => 'Debe ingresar la razón del cancelamiento.',
        'razon.string' => 'La razón debe ser una cadena de texto.',
        'razon.min' => 'La razón debe tener al menos 3 caracteres.',

        'profesional.required' => 'El nombre del profesional es obligatorio.',
        'profesional.string' => 'El nombre del profesional debe ser una cadena de texto.',
    ];
    public const FIELDS_TO_EVO_APPOS = [
        'companion',
        'place',
        'kindred',

        'entryRoute',
        'purpose',
        'externalCause',

        'entry',
        'typeDiagnosisEntry',

        'out',
        'typeDiagnosisOutPut',

        'firstTime',

        'target',
        'descript',
        'result',


        'idsToEvo',

        'profesional',
        'cedula',
        'date',
        'endHour',
        'secuencie'
    ];

    public const RULES_TO_EVO_APPOS = [
        'companion' => 'nullable|string',
        'place' => 'required|string',
        'kindred' => 'required|string',

        'entryRoute' => 'required|string',
        'purpose' => 'required|string',
        'externalCause' => 'required|string',

        'entry' => 'required|string',
        'typeDiagnosisEntry' => 'required|string',

        'out' => 'nullable|string',
        'typeDiagnosisOutPut' => 'nullable|string',

        'firstTime' => 'required|boolean',

        'target' => 'required|string',
        'descript' => 'required|string',
        'result' => 'required|string',
 
        'idsToEvo' => 'required|array|min:1',

        'profesional' => 'required|string',
        'cedula' => 'required|string',
        'date' => 'required|date',
        'endHour' => 'required|date',
        'secuencie'=>'required'
    ];

    public const ERRORS_TO_EVO_APPOS = [

        'companion.string' => 'El acompañante debe ser una cadena de texto.',

        'place.required' => 'Debe indicar el lugar.',
        'place.string' => 'El lugar debe ser una cadena de texto.',

        'kindred.required' => 'Debe indicar el parentesco.',
        'kindred.string' => 'El parentesco debe ser una cadena de texto.',

        'entryRoute.required' => 'Debe indicar la ruta de ingreso.',
        'entryRoute.string' => 'La ruta de ingreso debe ser una cadena de texto.',

        'purpose.required' => 'Debe especificar la finalidad.',
        'purpose.string' => 'La finalidad debe ser una cadena de texto.',

        'externalCause.required' => 'Debe especificar la causa externa.',
        'externalCause.string' => 'La causa externa debe ser una cadena de texto.',

        'entry.required' => 'Debe especificar el diagnóstico de entrada.',
        'entry.string' => 'El diagnóstico de entrada debe ser una cadena de texto.',

        'typeDiagnosisEntry.required' => 'Debe indicar el tipo de diagnóstico de entrada.',
        'typeDiagnosisEntry.string' => 'El tipo de diagnóstico de entrada debe ser una cadena de texto.',

        'out.required' => 'Debe especificar el diagnóstico de salida.',
        'out.string' => 'El diagnóstico de salida debe ser una cadena de texto.',

        'typeDiagnosisOutPut.required' => 'Debe indicar el tipo de diagnóstico de salida.',
        'typeDiagnosisOutPut.string' => 'El tipo de diagnóstico de salida debe ser una cadena de texto.',

        'firstTime.required' => 'Debe indicar si es la primera vez.',
        'firstTime.boolean' => 'El valor de primera vez debe ser verdadero o falso.',

        'target.required' => 'Debe ingresar el objetivo de la atención.',
        'target.string' => 'El objetivo debe ser una cadena de texto.',
        'target.min' => 'El objetivo debe tener al menos 2000 caracteres.',

        'descript.required' => 'Debe ingresar la descripción de la evolución.',
        'descript.string' => 'La descripción debe ser una cadena de texto.',
        'descript.min' => 'La descripción debe tener al menos 200 caracteres.',

        'result.required' => 'Debe ingresar los resultados.',
        'result.string' => 'Los resultados deben ser una cadena de texto.',
        'result.min' => 'Los resultados deben tener al menos 200 caracteres.',



        'idsToEvo.required' => 'Debe seleccionar al menos un elemento para la evolución.',
        'idsToEvo.array' => 'Los elementos seleccionados deben ser un arreglo.',
        'idsToEvo.min' => 'Debe seleccionar al menos un elemento.',

        'profesional.required' => 'Debe indicar el nombre del profesional.',
        'profesional.string' => 'El nombre del profesional debe ser una cadena de texto.',

        'cedula.required' => 'Debe indicar la cédula del profesional.',
        'cedula.string' => 'La cédula del profesional debe ser una cadena de texto.',

        'date.required' => 'Debe indicar la fecha de la cita.',
        'date.date' => 'La fecha de la cita no tiene un formato válido.',

        'endHour.required' => 'Debe indicar la hora de finalización.',
        'endHour.date_format' => 'La hora de finalización debe tener el formato HH:MM.',
    ];

    public const FIELDS_TO_EVO_APPOS_PSICO = [
        'companion',
        'place',
        'kindred',

        'entryRoute',
        'purpose',
        'externalCause',

        'entry',
        'entryDisae',
        'typeDiagnosisEntry',

        'out',
        'outDisae',
        'typeDiagnosisOutPut',

        'firstTime',

        'target',
        'plan',
        'evolution',
        'analisys',


        'idsToEvo',

        'profesional',
        'cedula',
        'date',
        'endHour',
    ];

    public const RULES_TO_EVO_APPOS_PSICO = [
        'companion' => 'nullable|string',
        'place' => 'required|string',
        'kindred' => 'nullable|string',

        'entryRoute' => 'required|string',
        'purpose' => 'required|string',
        'externalCause' => 'required|string',

        'entry' => 'required|string',
        'entryDisae'=>'required|string',
        'typeDiagnosisEntry' => 'required|string',

        'out' => 'nullable|string',
        'outDisae'=>'nullable|string',
        'typeDiagnosisOutPut' => 'nullable|string',

        'firstTime' => 'required|boolean',

        'target' => 'required|string|min:200',
        'plan' => 'required|string|min:200',
        'analisys' => 'required|string|min:200',
        'evolution' => 'required|string|min:200',
 
        'idsToEvo' => 'required|array|min:1',

        'profesional' => 'required|string',
        'cedula' => 'required|string',
        'date' => 'required|date',
        'endHour' => 'required|date',
    ];

    public const ERRORS_TO_EVO_APPOS_PSICO = [
        'companion.required' => 'Debe indicar el acompañante.',
        'companion.string' => 'El acompañante debe ser una cadena de texto.',

        'place.required' => 'Debe indicar el lugar.',
        'place.string' => 'El lugar debe ser una cadena de texto.',

        'kindred.required' => 'Debe indicar el parentesco.',
        'kindred.string' => 'El parentesco debe ser una cadena de texto.',

        'entryRoute.required' => 'Debe indicar la ruta de ingreso.',
        'entryRoute.string' => 'La ruta de ingreso debe ser una cadena de texto.',

        'purpose.required' => 'Debe especificar la finalidad.',
        'purpose.string' => 'La finalidad debe ser una cadena de texto.',

        'externalCause.required' => 'Debe especificar la causa externa.',
        'externalCause.string' => 'La causa externa debe ser una cadena de texto.',

        'entry.required' => 'Debe especificar el diagnóstico de entrada.',
        'entry.string' => 'El diagnóstico de entrada debe ser una cadena de texto.',

        'entryDisae.required' => 'Debe especificar la enfermedad asociada al diagnóstico de entrada.',
        'entryDisae.string' => 'La enfermedad asociada al diagnóstico de entrada debe ser una cadena de texto.',

        'typeDiagnosisEntry.required' => 'Debe indicar el tipo de diagnóstico de entrada.',
        'typeDiagnosisEntry.string' => 'El tipo de diagnóstico de entrada debe ser una cadena de texto.',

        'out.string' => 'El diagnóstico de salida debe ser una cadena de texto.',
        'outDisae.string' => 'La enfermedad asociada al diagnóstico de salida debe ser una cadena de texto.',

        'typeDiagnosisOutPut.string' => 'El tipo de diagnóstico de salida debe ser una cadena de texto.',

        'firstTime.required' => 'Debe indicar si es la primera vez.',
        'firstTime.boolean' => 'El valor de primera vez debe ser verdadero o falso.',

        'target.required' => 'Debe ingresar el objetivo de la atención.',
        'target.string' => 'El objetivo debe ser una cadena de texto.',
        'target.min' => 'El objetivo debe tener al menos 200 caracteres.',

        'plan.required' => 'Debe ingresar el plan terapéutico.',
        'plan.string' => 'El plan terapéutico debe ser una cadena de texto.',
        'plan.min' => 'El plan terapéutico debe tener al menos 200 caracteres.',

        'analisys.required' => 'Debe ingresar el análisis clínico.',
        'analisys.string' => 'El análisis clínico debe ser una cadena de texto.',
        'analisys.min' => 'El análisis clínico debe tener al menos 200 caracteres.',

        'evolution.required' => 'Debe ingresar la evolución del paciente.',
        'evolution.string' => 'La evolución debe ser una cadena de texto.',
        'evolution.min' => 'La evolución debe tener al menos 200 caracteres.',

        'idsToEvo.required' => 'Debe seleccionar al menos un elemento para la evolución.',
        'idsToEvo.array' => 'Los elementos seleccionados deben ser un arreglo.',
        'idsToEvo.min' => 'Debe seleccionar al menos un elemento.',

        'profesional.required' => 'Debe indicar el nombre del profesional.',
        'profesional.string' => 'El nombre del profesional debe ser una cadena de texto.',

        'cedula.required' => 'Debe indicar la cédula del profesional.',
        'cedula.string' => 'La cédula del profesional debe ser una cadena de texto.',

        'date.required' => 'Debe indicar la fecha de la cita.',
        'date.date' => 'La fecha de la cita no tiene un formato válido.',

        'endHour.required' => 'Debe indicar la hora de finalización.',
        'endHour.date' => 'La hora de finalización debe ser una fecha/hora válida.',
    ];
    public const FIELDS_TO_EVO_APPOS_ABA = [
        'companion',
        'place',
        'kindred',

        'entryRoute',
        'purpose',
        'externalCause',

        'entry',
        'entryDisae',
        'typeDiagnosisEntry',

        'out',
        'outDisae',
        'typeDiagnosisOutPut',

        'firstTime',

        'target',
        'plan',
        'evolution',
        'analisys',
        'observations',

        'idsToEvo',

        'profesional',
        'cedula',
        'date',
        'endHour',
    ];

    public const RULES_TO_EVO_APPOS_ABA = [
        'companion' => 'required|string',
        'place' => 'required|string',
        'kindred' => 'required|string',

        'entryRoute' => 'required|string',
        'purpose' => 'required|string',
        'externalCause' => 'required|string',

        'entry' => 'required|string',
        'entryDisae'=>'required|string',
        'typeDiagnosisEntry' => 'required|string',

        'out' => 'nullable|string',
        'outDisae'=>'nullable|string',
        'typeDiagnosisOutPut' => 'nullable|string',

        'firstTime' => 'required|boolean',

        'target' => 'required|string|min:200',
        'plan' => 'required|string|min:200',
        'analisys' => 'required|string|min:200',
        'evolution' => 'required|string|min:200',
        'observations'=>'required|string|min:200',
 
        'idsToEvo' => 'required|array|min:1',

        'profesional' => 'required|string',
        'cedula' => 'required|string',
        'date' => 'required|date',
        'endHour' => 'required|date',
    ];

    public const ERRORS_TO_EVO_APPOS_ABA = [
        'companion.required' => 'Debe indicar el acompañante.',
        'companion.string' => 'El acompañante debe ser una cadena de texto.',

        'place.required' => 'Debe indicar el lugar.',
        'place.string' => 'El lugar debe ser una cadena de texto.',

        'kindred.required' => 'Debe indicar el parentesco.',
        'kindred.string' => 'El parentesco debe ser una cadena de texto.',

        'entryRoute.required' => 'Debe indicar la ruta de ingreso.',
        'entryRoute.string' => 'La ruta de ingreso debe ser una cadena de texto.',

        'purpose.required' => 'Debe especificar la finalidad.',
        'purpose.string' => 'La finalidad debe ser una cadena de texto.',

        'externalCause.required' => 'Debe especificar la causa externa.',
        'externalCause.string' => 'La causa externa debe ser una cadena de texto.',

        'entry.required' => 'Debe especificar el diagnóstico de entrada.',
        'entry.string' => 'El diagnóstico de entrada debe ser una cadena de texto.',

        'entryDisae.required' => 'Debe especificar la enfermedad asociada al diagnóstico de entrada.',
        'entryDisae.string' => 'La enfermedad asociada al diagnóstico de entrada debe ser una cadena de texto.',

        'typeDiagnosisEntry.required' => 'Debe indicar el tipo de diagnóstico de entrada.',
        'typeDiagnosisEntry.string' => 'El tipo de diagnóstico de entrada debe ser una cadena de texto.',

        'out.string' => 'El diagnóstico de salida debe ser una cadena de texto.',

        'outDisae.string' => 'La enfermedad asociada al diagnóstico de salida debe ser una cadena de texto.',

        'typeDiagnosisOutPut.string' => 'El tipo de diagnóstico de salida debe ser una cadena de texto.',

        'firstTime.required' => 'Debe indicar si es la primera vez.',
        'firstTime.boolean' => 'El valor de primera vez debe ser verdadero o falso.',

        'target.required' => 'Debe ingresar el objetivo de la atención.',
        'target.string' => 'El objetivo debe ser una cadena de texto.',
        'target.min' => 'El objetivo debe tener al menos 200 caracteres.',

        'plan.required' => 'Debe ingresar el plan terapéutico.',
        'plan.string' => 'El plan terapéutico debe ser una cadena de texto.',
        'plan.min' => 'El plan terapéutico debe tener al menos 200 caracteres.',

        'analisys.required' => 'Debe ingresar el análisis clínico.',
        'analisys.string' => 'El análisis clínico debe ser una cadena de texto.',
        'analisys.min' => 'El análisis clínico debe tener al menos 200 caracteres.',

        'observations.required' => 'Debe ingresar el observaciones.',
        'observations.string' => 'Las observaciones debe ser una cadena de texto.',
        'observations.min' => 'las observaciones debe tener al menos 200 caracteres.',

        'evolution.required' => 'Debe ingresar la evolución del paciente.',
        'evolution.string' => 'La evolución debe ser una cadena de texto.',
        'evolution.min' => 'La evolución debe tener al menos 200 caracteres.',

        'idsToEvo.required' => 'Debe seleccionar al menos un elemento para la evolución.',
        'idsToEvo.array' => 'Los elementos seleccionados deben ser un arreglo.',
        'idsToEvo.min' => 'Debe seleccionar al menos un elemento.',

        'profesional.required' => 'Debe indicar el nombre del profesional.',
        'profesional.string' => 'El nombre del profesional debe ser una cadena de texto.',

        'cedula.required' => 'Debe indicar la cédula del profesional.',
        'cedula.string' => 'La cédula del profesional debe ser una cadena de texto.',

        'date.required' => 'Debe indicar la fecha de la cita.',
        'date.date' => 'La fecha de la cita no tiene un formato válido.',

        'endHour.required' => 'Debe indicar la hora de finalización.',
        'endHour.date' => 'La hora de finalización debe ser una fecha/hora válida.',
    ];


}