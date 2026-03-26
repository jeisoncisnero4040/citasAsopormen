<?php

namespace App\Requests;

use App\Constanst\RequestsCitasConstans;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\CustomExceptions\BadRequestException;
use Carbon\Carbon;

class CitasRequests extends BaseRequest
{
    public static function validateCreateApposRequest(array $data){
        $rules = RequestsCitasConstans::RULES_CREATE_CITAS;
        $messages = RequestsCitasConstans::MESSAGES_CREATE_CITAS;
        $keysAllowed = RequestsCitasConstans::KEYS_ALLOWED_CREATE_CITAS;

        self::validateRequest(
            request: $data,
            rules: $rules,
            errors: $messages,
            keysAllowed: $keysAllowed
        );
        if($data['num_sessions_total']==0 && !empty($data['week_days'])){
            throw new BadRequestException("No se ha Seleccionado El numero de semanas a replicar",400);
        }
    }
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
            'week_days' => ['required', 'array'],
            'num_sessions_total' => 'required|integer',
            'numWeeks'=>'required|integer'
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
            $rules['fecha_cita'] = 'date_format:Y-m-d H:i';
            $rules['meanCancel']='required|string';
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
    public static function validateDataToChangeProfesional($request){
        $validator=Validator::make($request,[
            'ids'=>'required|array',
            'cedprof'=>'required|regex:/^\d+$/'
        ]);
        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400);
        }
    }
    public static function validataDataToSendNotifyOrderProgramed($request){
        $validator=Validator::make($request,[
            'cel'=>'required|regex:/^\d+$/',
            'client_name'=>'required|string',
            'tiempo'=>'required|string',
            'codigo_client'=>'required',
            'autorizacion'=>'required'
        ]);
        if ($validator->fails()){
            throw new BadRequestException($validator->errors(),400);
        }
    }
    public static function validateDataToCloneSchedule($request)
    {
        $rules = RequestsCitasConstans::RULES_DATES_RANGE;
        $messages = RequestsCitasConstans::MESSAGES_ERROR_DATES_RANGE;
        $keysAllowed = RequestsCitasConstans::KEYS_ALLOWED_DATES_RANGE;

        self::validateRequest(
            request: $request,
            rules: $rules,
            errors: $messages,
            keysAllowed: $keysAllowed
        );

        if (Carbon::parse($request['from'])->dayOfWeek !== Carbon::parse($request['start'])->dayOfWeek) {
            throw new BadRequestException(
                "Las fechas de inicio  deben caer en el mismo día de la semana.",
                400
            );
        }
    }
    
}
