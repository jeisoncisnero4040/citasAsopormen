<?php

namespace App\Requests;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;
use App\Rules\ValidWeekDays;

class CitasRequests
{
    public static function validateCitasClient($request)
    {
        $validator = Validator::make($request, [
            'nro_hist' => 'required|regex:/^\d+$/',
            'regobserva' => 'nullable|string',
            'codent'   => 'required|regex:/^\d+$/',
            'codent2'  => 'required|regex:/^\d+$/',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }

    public static function validateCitasUser($request)
    {
        $validator = Validator::make($request, [
            'ced_usu'  => 'required|regex:/^\d+$/',
            'registro' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }

    public static function validateCitasProfesional($request)
    {
        $validator = Validator::make($request, [
            'cedprof'  => 'required|regex:/^\d+$/',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    public static function validateCitasAuthorization($request){
        $validator = Validator::make($request, [
            'n_autoriza' =>'required|string',
            'procedim' =>'required|string',
            'tiempo'=>'required|string',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }

    public static function validateCitasProcedure($request)
    {
        $validator = Validator::make($request, [
            'procedipro' => 'required|string',
            'duration_session'=>'required|integer|min:15',
            'recordatorio_wsp'=>'required|boolean'
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }

    public static function validateCitasCentral($request)
    {
        $validator = Validator::make($request, [
            'sede'     => 'required|regex:/^\d+$/|max:3',
            'direccion_cita'=>'required|string'
            
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }

    public static function validateCitasSchedule($request)
    {
        $validator = Validator::make($request, [
            'start_date' => 'required|date',
            'week_days' => ['required', 'array', new ValidWeekDays],
            'num_citas' => 'required|integer|between:1,30',
            'num_sessions' => 'required|integer|between:1,8',
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }

    public static function ValidateTineRangeFromCitasClient($request){
        $validator = Validator::make($request, [
            'codigo' => 'required|regex:/^\d+$/',
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    public static function ValidateTineRangeFromCitasProfesional($request){
        $validator = Validator::make($request, [
            'cedula' => 'required|regex:/^\d+$/',
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    public static function vaalidateDateAndCedulaProfesional($request){
        $validator = Validator::make($request, [
            'day' => 'required|date_format:Y-m-d',
            'profesional_identity' => 'required|regex:/^\d+$/',
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    public static function ValidateRealizarField($request){
        $validator = Validator::make($request, [
            'realizar' => 'required|string',
            'id'=>'required|numeric'
        ]);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    public static function ValidateCitaSessionsIds($request, $action){
        $rules = ['ids' => 'required|string'];
        if ($action == 'cancelar') {
            $rules['razon'] = 'required|string';
            $rules['fecha_cita'] = 'required|date_format:Y-m-d H:i';
        }
        
        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            throw new BadRequestException($validator->errors(), 400);
        }
    }
    public static function checkIsReassingCitas($request){
        $validator = Validator::make($request, [
            'id'=>'required|numeric'
        ]);
        return !$validator->fails();
    }
    
}
